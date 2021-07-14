<?php
namespace tradersoft\model;

use Redis;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\ExternalFormValidationRule;
use TS_Functions;

class Contact_Us extends Model
{
    public $fullName;
    public $email;
    public $phone;
    public $topic;
    public $message;

    public $successes = false;
    public $responseMessage = '';

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['fullName','phone', 'email', 'message'], 'stripTags'],
            [['fullName','phone', 'topic', 'email', 'message'], 'required'],
            ['fullName', 'maxLength', ExternalFormValidationRule::getFullNameMaxSize()],
            ['topic', 'maxLength', 255],
            ['message', 'maxLength', 32768],
            ['email', 'minLength', 5],
            ['phone', 'phone'],
            ['email', 'email'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'fullName' => \TS_Functions::__('Full Name'),
            'email' => \TS_Functions::__('Email Address'),
            'phone' => \TS_Functions::__('Phone number'),
            'topic' => \TS_Functions::__('Topic'),
            'message' => \TS_Functions::__('Message'),
        ];
    }

    public function init()
    {
        $this->_setDefaultValue();
    }

    public function send()
    {
        $result = [
            'success' => false,
            'message' => '',
        ];

        $data = Interlayer_crm::contactUsRequest([
            'fullName' => $this->fullName,
            'phone'    => $this->phone,
            'email'    => $this->email,
            'topic'    => $this->topic,
            'message'  => $this->message,
            'url'      => \TSInit::$app->request->getHostName() . \TSInit::$app->request->getPath(),
            'language' => Interlayer_Crm::getCurrentLanguage(),
        ]);

        if (!$data) {
            $this->responseMessage = $result['message'] = \TS_Functions::__('Unknown error');
            return $result;
        }

        $returnCode = Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);
        $description = Arr::get($data, 'description', 'Unknown error');
        if ($returnCode != Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
            $this->responseMessage = $result['message'] = \TS_Functions::__($description);
            $this->_prepareError($data);
            return $result;
        }

        $this->successes = true;
        $result['success'] = true;
        $this->responseMessage = $result['message'] = \TS_Functions::__('The information was successfully sent');
        return $result;
    }

    /**
     * Getting list of `contact us` topics
     *
     * @return array
     */
    public function getContactUsTopics()
    {
        $return = [];

        $redisConnect = false;
        $redisKey = 'contactus_topics_v202002071000';

        if (class_exists('Redis')) {
            $redis = new Redis();
            $redisConnect = $redis->connect('localhost', 6379);
            if ($redisConnect === true) {
                $return = json_decode($redis->get($redisKey));
            }
        }

        if ($redisConnect !== true || !$return) {
            $data = Interlayer_Crm::getContactUsTopics();
            if ($data) {
                $topics = isset($data['topics']) ? $data['topics'] : [];
                foreach ($topics as $key => $top) {
                    $return[$key] = ucfirst(str_replace('-', ' ', $key));
                }

                if (isset($redis) && $redisConnect === true) {
                    $redis->set($redisKey, json_encode($return), 3600);
                }
            }
        }

        foreach ($return = Arr::stdToArr($return) as $key => $topic) {
            $return[$key] = TS_Functions::__($topic);
        }

        return $return;
    }

    protected function _setDefaultValue()
    {
        if (!\TSInit::$app->request->isPost) {
            $trader = \TSInit::$app->trader;
            if (!$trader->isGuest) {
                $this->fullName = $trader->fullName;
                $this->email = $trader->get('email', '');
                $this->phone = $trader->get('nationalPhone', '');
            }
        }
    }

    /**
     * Prepare error model from API response
     * @param $data mixed
     */
    protected function _prepareError($data)
    {
        if (isset($data['validationErrors'])) {
            $validationErrors = $data['validationErrors'];
            if (isset($validationErrors['fullName'])) {
                $this->addError('fullName', \TS_Functions::__($validationErrors['fullName']));
            }
            if (isset($validationErrors['phone'])) {
                $this->addError('phone', \TS_Functions::__($validationErrors['phone']));
            }
            if (isset($validationErrors['email'])) {
                $this->addError('email', \TS_Functions::__($validationErrors['email']));
            }
        }
    }
}