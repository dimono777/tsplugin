<?php
namespace tradersoft\model\account_details\asic;

use TSInit;

class AfterVerification extends Base
{
    public $address;

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['phone'], 'required'],
            [['phone', 'cellphone'], 'stripTags'],
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
            'address' => \TS_Functions::__('Address'),
            'town' => \TS_Functions::__('City/Town '),
            'country' => \TS_Functions::__('Country'),
            'state' => \TS_Functions::__('State'),
            'postalCode' => \TS_Functions::__('Postal code'),
            'dayNumber' => \TS_Functions::__('Day number'),
            'monthNumber' => \TS_Functions::__('Month number'),
            'yearNumber' => \TS_Functions::__('Year number'),
            'email' => \TS_Functions::__('Email'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeOptions()
    {
        return [
            'fname'     => ['disabled'=>'disabled'],
            'lname'     => ['disabled'=>'disabled'],
            'gender'    => [
                'prompt' => \TS_Functions::__('Gender'),
                'disabled' => 'disabled',
            ],
            'middleName' => ['disabled'=>'disabled'],
            'address'    => ['disabled'=>'disabled'],
            'town'       => ['disabled'=>'disabled'],
            'postalCode' => ['disabled'=>'disabled'],
            'dayNumber'  => ['disabled'=>'disabled'],
            'monthNumber' => ['disabled'=>'disabled'],
            'yearNumber' => ['disabled'=>'disabled'],
            'email'     => ['disabled'=>'disabled'],
            'country'   => ['disabled'=>'disabled'],
            'state'   => ['disabled'=>'disabled'],
            'submit'    => ['value' => 'Save'],
        ];
    }

    public function init()
    {
        parent::init();
        $this->_setStatusMessage();
    }

    public function load(array $data, $formName = null)
    {
        if (isset($data['street']) && isset($data['building'])){
            $this->address = $data['street'] . ', ' . $data['building'];
        }
        parent::load($data, $formName);
    }

    /**
     * Save account details
     * @return bool
     */
    public function save()
    {
        return $this->_afterVerificationSaving();
    }

    protected function _loadModelAttributes()
    {
        parent::_loadModelAttributes();
        $this->address = TSInit::$app->trader->get('address');
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
            if (isset($validationErrors['buildingNumber'])) {
                $this->addError('address', \TS_Functions::__($validationErrors['buildingNumber']));
            }
            if (isset($validationErrors['street'])) {
                $this->addError('address', \TS_Functions::__($validationErrors['street']));
            }
        }
        parent::_prepareError($data);
    }
}