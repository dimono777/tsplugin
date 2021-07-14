<?php
namespace tradersoft\model;

use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Interlayer_Partner;
use tradersoft\helpers\Session;

/**
 * Partners registration model
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Partners_Registration extends Model
{

    public $fname;
    public $lname;
    public $language;
    public $phone;
    public $email;
    public $password;
    public $confirmPassword;
    public $accept = 1;

    private static $cache;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['fname', 'lname', 'phone', 'email', 'language'], 'stripTags'],
            [['fname', 'lname', 'email', 'password', 'confirmPassword', 'language'], 'required'],
            [['fname', 'lname'], 'minLength', 2],
            ['email', 'minLength', 5],
            ['phone', 'minLength', ['skipOnEmpty'=>true, 'min'=>9]],
            [['password', 'confirmPassword'], 'minLength', 5],
            [['password', 'confirmPassword'], 'maxLength', 15],
            ['email', 'email'],
            ['password', 'compare', [
                'skipOnEmpty' => false,
                'operator' => '==',
                'compareAttribute' => 'confirmPassword',
            ]]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'fname' => \TS_Functions::__('First name'),
            'lname' => \TS_Functions::__('Last name'),
            'phone' => \TS_Functions::__('Phone'),
            'language' => \TS_Functions::__('Language'),
            'email' => \TS_Functions::__('Email address'),
            'password' => \TS_Functions::__('Password'),
            'confirmPassword' => \TS_Functions::__('Re-enter password'),
            'accept' => \TS_Functions::__('Terms & Conditions')
        ];
    }

    public function validateTerms()
    {
        if ($this->accept != 1) {
            $this->addError('accept', \TS_Functions::__('You must agree to the Terms and Conditions'));
        }
    }

    public function init()
    {
        parent::init();
    }

    public function register()
    {
        if ($this->hasErrors()) {
            return false;
        }
        $fromUserId = !empty($_GET['ref']) ?  $_GET['ref'] : \TS_Functions::getCookie('ref');
        $data = Interlayer_Partner::registration([
            'fullName' => $this->fname . ' ' . $this->lname,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
            'language' => $this->language,
            'agreeWithTerms' => $this->accept,
            'fromUserId' => $fromUserId
        ]);
        if (!$data) {
            return false;
        }

        $data = json_decode($data);
        $returnCode = isset($data->{'returnCode'}) ?
            $data->{'returnCode'}
            : Interlayer_Partner::RESPONSE_CODE_UNSPECIFIED_ERROR;

        $result['data'] = $data;
        $result['isRegister'] = $returnCode == Interlayer_Partner::RESPONSE_CODE_SUCCESS ? true : false;

        return $result;
    }

    public function getLanguages()
    {
        $languages = [1 => 'English'];

        if (isset(self::$cache['languages'])) {
            return self::$cache['languages'];
        }

        $data = Interlayer_Partner::getLanguages();
        if ($data) {
            $data = json_decode($data, true);
            if (isset($data['languages'])) {
                $languages = [];
                foreach ($data['languages'] as $lang) {
                    $languages[$lang['id']] = $lang['title'];
                }
            }
        }
        self::$cache['languages'] = $languages;

        return $languages;
    }

    protected function _setPhoneCode()
    {
        $this->phone = '+' . Interlayer_Crm::getPhoneCodeByIP();
    }
}