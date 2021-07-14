<?php
namespace tradersoft\model;

use tradersoft\helpers\Arr;
use tradersoft\helpers\captcha\ReCaptcha;
use tradersoft\helpers\ExternalFormValidationRule;
use tradersoft\helpers\Interlayer_Crm;
use TS_Functions;
use TSInit;

/**
 * Call back form model
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Call_Back extends Model
{
    public $fullName;
    public $email;
    public $phoneCode;
    public $phone;
    public $country;
    public $captcha;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['fullName', 'phoneCode', 'phone', 'email', 'country'], 'stripTags'],
            [['fullName', 'phoneCode', 'phone', 'email', 'country'], 'required', ['msg' => 'Empty Field'],],
            ['fullName', 'maxLength', ExternalFormValidationRule::getFullNameMaxSize()],

            [
                'country',
                'exact_length',
                [2, 'skipOnEmpty' => false, 'msg' => 'Invalid country code'],
            ],

            ['email', 'email'],
            ['email', 'minLength', 5],

            ['phone', 'phone', ['fields' => ['phoneCode' => $this->phoneCode]]],
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
            'phoneCode' => \TS_Functions::__('Field'),
            'phone' => \TS_Functions::__('Field'),
            'country' => \TS_Functions::__('Country'),
        ];
    }

    public function init()
    {
        $this->_setDefaultValue();
    }

    public function afterLoad()
    {
        $this->captcha = Arr::get($_POST, 'g-recaptcha-response');
        if (
            ReCaptcha::isEnabled()
            && !ReCaptcha::verifyResponse(TSInit::$app->request->userIP, $this->captcha)
        ) {
            $this->addError('captcha', \TS_Functions::__('Captcha invalid'));
        }
    }

    public function send()
    {
        $result = [
            'success' => false,
            'message' => '',
        ];
        $data = Interlayer_Crm::callBackRequest(
            [
                'fullname' => $this->fullName,
                'phone' => $this->phoneCode . $this->phone,
                'country' => $this->country,
                'email' => $this->email,
                'language' => TS_Functions::getCurrentLanguage(),
                'url'      => TSInit::$app->request->getHostName() . TSInit::$app->request->getPath(),
            ]
        );

        if (!$data) {
            $result['message'] = \TS_Functions::__('Unknown error');
            return $result;
        }

        $returnCode = Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);
        $description = Arr::get($data, 'description', 'Unknown error');
        if ($returnCode != Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
            $result['message'] = \TS_Functions::__($description);
            $this->_prepareError($data, $result);
            return $result;
        }

        $result['success'] = true;
        $result['message'] = \TS_Functions::__('Your request has been successfully sent. Our manager will contact you in the nearest time.');
        return $result;
    }

    /**
     * Prepare error model from API response
     * @param $data mixed
     * @param $result array
     */
    protected function _prepareError($data, array &$result)
    {
        if (isset($data['validationErrors'])) {
            $result['validationErrors'] = $validationErrors = $data['validationErrors'];
            if (isset($validationErrors['fullName'])) {
                $this->addError('fullName', \TS_Functions::__($validationErrors['fullName']));
            }
            if (isset($validationErrors['country'])) {
                $this->addError('country', \TS_Functions::__($validationErrors['country']));
            }
            if (isset($validationErrors['phone'])) {
                $this->addError('phone', \TS_Functions::__($validationErrors['phone']));
            }
            if (isset($validationErrors['email'])) {
                $this->addError('email', \TS_Functions::__($validationErrors['email']));
            }
        }
    }

    /**
     * Get countries list
     * @return array
     */
    public function getCountries()
    {
        return Interlayer_Crm::getCountriesAll();
    }

    protected function _setDefaultValue()
    {
        if (!TSInit::$app->request->isPost) {
            $this->phoneCode = Interlayer_Crm::getPhoneCodeByIP();
            $trader = TSInit::$app->trader;
            if (!$trader->isGuest) {
                $this->fullName = $trader->fullName;
                $this->email = $trader->get('email', '');
                $this->phoneCode = $trader->get('phoneCode', $this->phoneCode);
                $this->phone = $trader->get('nationalPhone', '');
                $this->country = $trader->get('country');
            } else {
                $country = Interlayer_Crm::getCountryByIP(TSInit::$app->request->userIP);

                $this->country = isset($country['country_code']) ? $country['country_code'] : '';
            }
        }
    }

}