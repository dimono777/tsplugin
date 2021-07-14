<?php
namespace tradersoft\model\account_details\asic;

use TSInit;
use tradersoft\helpers\ExternalFormValidationRule;

class ProcessVerification extends Base
{
    public $street;
    public $building;

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['fname', 'lname', 'gender', 'street', 'building', 'town', 'country', 'phone', 'postalCode'], 'required'],
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
            [['fname', 'middleName', 'lname', 'phone', 'cellphone', 'street', 'building', 'town', 'postalCode'], 'stripTags'],
            ['fname', 'maxLength', ExternalFormValidationRule::getFieldRuleParams('firstName', 'maxLength', 100)],
            ['lname', 'maxLength', ExternalFormValidationRule::getFieldRuleParams('lastName', 'maxLength', 100)],
            [['street', 'building', 'town', 'state', 'middleName'], 'maxLength', 255],
            [['email'], 'email'],
            ['gender', 'inArray', ['array' => ['male', 'female']]],
            [['country'], 'exact_length', [2, 'skipOnEmpty' => false, 'msg' => 'Invalid country code']],
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
            'middleName' => \TS_Functions::__('Middle name'),
            'lname' => \TS_Functions::__('Last name'),
            'gender' => \TS_Functions::__('Gender'),
            'phone' => \TS_Functions::__('Phone'),
            'cellphone' => \TS_Functions::__('Mobile phone'),
            'street' => \TS_Functions::__('Street'),
            'building' => \TS_Functions::__('Building number'),
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
    public function attributeOptions()
    {
        return [
            'gender' => [
                'prompt' => \TS_Functions::__('Gender'),
            ],
            'email' => ['disabled'=>'disabled'],
            'country' => ['disabled'=>'disabled'],
            'submit' => ['value' => 'Verify'],
        ];
    }

    public function init()
    {
        parent::init();
        $this->_setStatusMessage();
    }

    /**
     * Save account details
     * @return bool
     */
    public function save()
    {
        return $this->_processVerificationSaving();
    }

    /**
     * @inheritdoc
     */
    protected function _loadModelAttributes()
    {
        parent::_loadModelAttributes();
        $this->street = TSInit::$app->trader->get('street');
        $this->building = TSInit::$app->trader->get('buildingNumber');
    }

    /**
     * @inheritdoc
     */
    protected function _prepareError($data)
    {
        if (isset($data['validationErrors'])) {
            $validationErrors = (array)$data['validationErrors'];
            if (isset($validationErrors['street'])) {
                $this->addError('street', \TS_Functions::__($validationErrors['street']));
            }
            if (isset($validationErrors['buildingNumber'])) {
                $this->addError('building', \TS_Functions::__($validationErrors['buildingNumber']));
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
                'firstName'     => $this->fname,
                'middleName'    => $this->middleName,
                'lastName'      => $this->lname,
                'gender'        => $this->gender,
                'birthday'      => $this->yearNumber . '-' . $this->monthNumber . '-' . $this->dayNumber,
                'street'        => $this->street,
                'buildingNumber' => $this->building,
                'town'          => $this->town,
                'state'         => $this->state,
                'postalCode'    => $this->postalCode,
            ]
        );
    }
}