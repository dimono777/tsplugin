<?php
namespace tradersoft\model;

use tradersoft\helpers\Arr;
use tradersoft\helpers\ExternalFormValidationRule;
use tradersoft\helpers\captcha\Invisible_ReCaptcha;

/**
 * Registration mini model
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Registration_Mini extends Base_Registration
{
    public $fullname;

    /**
     * @return array
     * @throws \Exception
     */
    public function rules()
    {
        $rules = [
            [['fullname', 'phone', 'email', 'country'], 'required'],
            ['phone', 'phoneFull'],
            ['accept', 'boolean', ['value' => true, 'msg'=>'You must read and agree to the above T&C']],
        ];

        $rules = array_merge(
            $rules,
            ExternalFormValidationRule::getFieldsRules(
                [
                    'fullname' => ExternalFormValidationRule::FIELD_FULL_NAME,
                    'email' => ExternalFormValidationRule::FIELD_EMAIL,
                    'country' => ExternalFormValidationRule::FIELD_COUNTRY,
                ],
                [
                    ExternalFormValidationRule::FIELD_COUNTRY => [
                        ExternalFormValidationRule::VALIDATOR_EXACT_LENGTH => [
                            'msg' => 'Invalid country code',
                        ],
                    ],
                ]
            )
        );

        if ($this->withReceiveEmailNewslettersAgreement()) {
            array_push(
                $rules,
                [
                    'agreedReceiveNewsletters',
                    'inArray',
                    ['array' => ['0', '1']]
                ]
            );
        }

        if ($this->withPrivacyPolicy()) {
            array_push(
                $rules,
                [
                    'agreedPrivacyPolicy',
                    'boolean',
                    ['value' => true, 'msg'=>'You must read and agree to the above Privacy Policy']
                ]
            );
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'fullname' => \TS_Functions::__('Full Name'),
            'phone' => \TS_Functions::__('Phone number'),
            'country' => \TS_Functions::__('Country'),
            'email' => \TS_Functions::__('Email address'),
            'accept' => \TS_Functions::__('Terms & Conditions'),
        ];
    }

    public function init()
    {
        $this->_setCountryByIP();

        parent::init();
    }

    public function afterLoad()
    {
        if (!Arr::get($_POST, 'g-recaptcha-response')) {
            return;
        }

        $this->captcha = Arr::get($_POST, 'g-recaptcha-response');

        if (
            Invisible_ReCaptcha::isEnabled()
            && !Invisible_ReCaptcha::verifyResponse(\TSInit::$app->request->userIP, $this->captcha)
        ) {
            $this->addError(
                'captcha',
                \TS_Functions::__('Suspicious activity has been detected. Please try again or contact support.')
            );
        }
    }

    protected function _setAttribute()
    {
        $names = explode(' ', $this->fullname);
        $this->fname = $names[0];
        unset($names[0]);
        $this->lname = implode(' ', $names);
        if ($this->lname == '') {
            $this->lname = $this->fname;
        }

        $this->phoneCode = '';
        $this->password = $this->confirmPassword = \TS_Functions::generatePassword();
    }

    /**
     * Prepare error model from API response
     * @param $data mixed
     */
    protected function _prepareError($data)
    {
        if (isset($data->{'validationErrors'})) {
            $validationErrors = (array)$data->{'validationErrors'};
            if (isset($validationErrors['firstName'])) {
                $this->addError('fullname', \TS_Functions::__($validationErrors['firstName']));
            }
            if (isset($validationErrors['lastName'])) {
                $this->addError('fullname', \TS_Functions::__($validationErrors['lastName']));
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

    protected function _beforeSave()
    {
        $this->_setAttribute();

        parent::_beforeSave();
    }
}