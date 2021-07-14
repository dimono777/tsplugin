<?php

namespace tradersoft\model\account_details\saasic;

use tradersoft\helpers\ExternalFormValidationRule;
use tradersoft\model\account_details\GBG;
use TSInit;

class Base extends GBG
{
    public $address;
    public $nationalId;

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['fname', 'lname', 'nationalId', 'gender', 'phone', 'town', 'postalCode', 'address'], 'required'],
            [
                [
                    'yearNumber',
                    'monthNumber',
                    'dayNumber',
                ],
                'threeFieldsDate',
                [
                    'yearField' => 'yearNumber',
                    'monthField' => 'monthNumber',
                    'dayField' => 'dayNumber',
                    'msg' => 'Birthday is not valid',
                    'skipOnEmpty' => false,
                ],
            ],
            [['fname', 'lname', 'phone', 'cellphone', 'town', 'postalCode', 'address', 'nationalId'], 'stripTags'],
            ['fname', 'maxLength', ExternalFormValidationRule::getFieldRuleParams('firstName', 'maxLength', 100)],
            ['lname', 'maxLength', ExternalFormValidationRule::getFieldRuleParams('lastName', 'maxLength', 100)],
            [['address', 'town', 'nationalId'], 'maxLength', 255],
            ['gender', 'inArray', ['array' => ['male', 'female']]],
            ['postalCode', 'maxLength', 20],
            ['phone', 'phone'],
            ['cellphone', 'phone', ['skipOnEmpty' => true]],
        ];

        $rules = $this->_addAgreedReceiveNewslettersRule($rules);

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
            'gender' => \TS_Functions::__('Gender'),
            'nationalId' => \TS_Functions::__('National ID'),
            'phone' => \TS_Functions::__('Phone'),
            'cellphone' => \TS_Functions::__('Mobile phone'),
            'address' => \TS_Functions::__('Address'),
            'town' => \TS_Functions::__('City/Town'),
            'country' => \TS_Functions::__('Country'),
            'state' => \TS_Functions::__('State'),
            'postalCode' => \TS_Functions::__('Postal code'),
            'dayNumber' => \TS_Functions::__('Day'),
            'monthNumber' => \TS_Functions::__('Month'),
            'yearNumber' => \TS_Functions::__('Year'),
            'email' => \TS_Functions::__('Email'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function _loadModelAttributes()
    {
        parent::_loadModelAttributes();
        $this->address = TSInit::$app->trader->get('address');
        $this->nationalId = TSInit::$app->trader->get('nationalId');
    }

    /**
     * @inheritdoc
     */
    protected function _prepareError($data)
    {
        if (isset($data['validationErrors'])) {
            $validationErrors = (array)$data['validationErrors'];
            if (isset($validationErrors['address'])) {
                $this->addError('address', \TS_Functions::__($validationErrors['address']));
            }
            if (isset($validationErrors['nationalId'])) {
                $this->addError('nationalId', \TS_Functions::__($validationErrors['nationalId']));
            }
        }
        parent::_prepareError($data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareAccountData()
    {
        return array_merge(
            parent::_prepareAccountData(),
            [
                'town'          => $this->town,
                'address'       => $this->address,
                'postalCode'    => $this->postalCode,
            ]
        );
    }
}