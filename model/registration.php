<?php
namespace tradersoft\model;

use tradersoft\helpers\ExternalFormValidationRule;

/**
 * Registration model
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Registration extends Base_Registration
{
    /**
     * @return array
     * @throws \Exception
     */
    public function rules()
    {
        $rules = [
            [['fname', 'lname', 'email', 'country', 'phone', 'password', 'confirmPassword',], 'required'],
            ['phone', 'phoneFull', ['fields' => ['phoneCode' => $this->phoneCode]]],
            ['password', 'compare', [
                'skipOnEmpty' => false,
                'operator' => '==',
                'compareAttribute' => 'confirmPassword',
            ]],
            ['accept', 'boolean', ['value' => true, 'msg' => 'You must read and agree to the above T&C']],
        ];

        $rules = array_merge(
            $rules,
            ExternalFormValidationRule::getFieldsRules(
                [
                    'fname' => ExternalFormValidationRule::FIELD_FIRST_NAME,
                    'lname' => ExternalFormValidationRule::FIELD_LAST_NAME,
                    'email' => ExternalFormValidationRule::FIELD_EMAIL,
                    'password' => ExternalFormValidationRule::FIELD_PASSWORD,
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

        if ($this->withCurrency()) {
            $rules = array_merge(
                $rules,
                ExternalFormValidationRule::getFieldsRules([
                    'currency' => ExternalFormValidationRule::FIELD_CURRENCY,
                ])
            );
        }

        if ($this->withNotUSReportablePerson()) {
            array_push(
                $rules,
                [
                    'notUSReportablePerson',
                    'boolean',
                    ['value' => true, 'msg'=>'You must agree to the above declaration']
                ]
            );
        }

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
            'fname' => \TS_Functions::__('First name'),
            'lname' => \TS_Functions::__('Last name'),
            'phone' => \TS_Functions::__('Phone number'),
            'phoneCode' => \TS_Functions::__('Code'),
            'country' => \TS_Functions::__('Country'),
            'email' => \TS_Functions::__('Email address'),
            'password' => \TS_Functions::__('Password'),
            'confirmPassword' => \TS_Functions::__('Re-enter password'),
            'accept' => \TS_Functions::__('Terms & Conditions'),
            'promoCode' => \TS_Functions::__('Promotion Code'),
            'currency' => \TS_Functions::__('Currency'),
        ];
    }

    public function init()
    {
        $this->_setCountryByIP();
        $this->_setPhoneCode();
        $this->_setCurrency();
        parent::init();
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
                $this->addError('fname', \TS_Functions::__($validationErrors['firstName']));
            }
            if (isset($validationErrors['lastName'])) {
                $this->addError('lname', \TS_Functions::__($validationErrors['lastName']));
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
            if (isset($validationErrors['password'])) {
                $this->addError('password', \TS_Functions::__($validationErrors['password']));
            }
        }
    }
}