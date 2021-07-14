<?php
namespace tradersoft\model\account_details;

use tradersoft\helpers\Config;
use tradersoft\helpers\ExternalFormValidationRule;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Session;
use tradersoft\helpers\system\Translate;
use tradersoft\model\validator\AbstractValidator;
use TSInit;

/**
 * Account details model
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Native extends Base
{
    public $address;
    public $address2;
    public $country;

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['fname', 'lname', 'phone', 'cellphone', 'address', 'address2', 'town', 'postalCode'], 'stripTags'],
            [['fname', 'lname', 'phone'], 'required'],
            ['fname', 'maxLength', ExternalFormValidationRule::getFieldRuleParams('firstName', 'maxLength', 100)],
            ['lname', 'maxLength', ExternalFormValidationRule::getFieldRuleParams('lastName', 'maxLength', 100)],
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
            [['email'], 'email'],
            ['postalCode', 'maxLength', 20],
            [['address', 'address2', 'town'], 'maxLength', 255],
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
            'phone' => \TS_Functions::__('Phone'),
            'cellphone' => \TS_Functions::__('Phone (optional)'),
            'address' => \TS_Functions::__('Address'),
            'address2' => \TS_Functions::__('Address 2 (optional)'),
            'town' => \TS_Functions::__('Town'),
            'postalCode' => \TS_Functions::__('Zip code'),
            'dayNumber' => \TS_Functions::__('Day'),
            'monthNumber' => \TS_Functions::__('Month'),
            'yearNumber' => \TS_Functions::__('Year'),
            'email' => \TS_Functions::__('Email'),
        ];
    }

    /**
     * Save account details
     * @return bool
     */
    public function save()
    {
        $data = Interlayer_Crm::updateAccount(
            $this->_prepareAccountData()
        );

        $session = new Session();
        if ($data) {
            if (isset($data['returnCode']) AND $data['returnCode'] == Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
                $session->setFlash('success_account_details', \TS_Functions::__('The information was successfully updated'));

                // Update session info with CRM data
                TSInit::$app->trader->updateTraderInfo();

                return true;
            } else {
                if (isset($data['description'])) {
                    $session->setFlash('error_account_details', \TS_Functions::__($data['description']));
                }

                $this->_prepareError($data);
            }
        }

        return false;
    }

    /**
     * Prepare account data for saving
     * @return array
     */
    protected function _prepareAccountData()
    {
        $leadData = [
            'address'    => $this->address,
            'address2'   => $this->address2,
            'town'       => $this->town,
            'postalCode' => $this->postalCode,
            'phone'      => $this->phone,
            'cellphone'  => $this->cellphone,
            'firstName'  => $this->fname,
            'lastName'   => $this->lname,
            'birthday'   => $this->yearNumber . '-' . $this->monthNumber . '-' . $this->dayNumber,
            'traderID'   => TSInit::$app->trader->get('username', ''),
        ];

        if ($this->withReceiveEmailNewslettersAgreement()) {
            $leadData['agreedReceiveNewsletters'] = $this->agreedReceiveNewsletters;
        }

        return $leadData;
    }

    /**
     * Load model
     */
    protected function _loadModelAttributes()
    {
        parent::_loadModelAttributes();

        if (!TSInit::$app->trader->isGuest) {
            $countryList = Interlayer_Crm::getCountriesAll(null, 1);
            $countryCode = TSInit::$app->trader->get('country');
            if (!empty($countryList[$countryCode])) {
                $this->country = $countryList[$countryCode];
            }
            $this->address = TSInit::$app->trader->get('address');
            $this->address2 = TSInit::$app->trader->get('address2');

            if ($this->withReceiveEmailNewslettersAgreement()) {
                $this->agreedReceiveNewsletters = TSInit::$app->trader->get('agreedReceiveNewsletters');
            }
        }
    }

    /**
     * Prepare error model from API response
     * @param $data array
     */
    protected function _prepareError($data)
    {
        if (isset($data['validationErrors'])) {
            $validationErrors = (array)$data['validationErrors'];

            if (isset($validationErrors['firstName'])) {
                $this->_addErrorForField('fname', $validationErrors['firstName']);
            }
            if (isset($validationErrors['lastName'])) {
                $this->_addErrorForField('lname', $validationErrors['lastName']);
            }
            if (isset($validationErrors['gender'])) {
                $this->_addErrorForField('gender', $validationErrors['gender']);
            }
            if (isset($validationErrors['birthday'])) {
                $this->_addErrorForField('yearNumber', $validationErrors['birthday']);
            }
            if (isset($validationErrors['country'])) {
                $this->_addErrorForField('country', $validationErrors['country']);
            }
            if (isset($validationErrors['phone'])) {
                $this->_addErrorForField('phone', $validationErrors['phone']);
            }
            if (isset($validationErrors['cellphone'])) {
                $this->_addErrorForField('cellphone', $validationErrors['cellphone']);
            }
            if (isset($validationErrors['town'])) {
                $this->_addErrorForField('town', $validationErrors['town']);
            }
            if (isset($validationErrors['state'])) {
                $this->_addErrorForField('state', $validationErrors['state']);
            }
            if (isset($validationErrors['postalCode'])) {
                $this->_addErrorForField('postalCode', $validationErrors['postalCode']);
            }
        }
    }

    /**
     * @param $fieldName
     * @param $error
     */
    protected function _addErrorForField($fieldName, $error)
    {
        $templateOld = Config::get("crm_validation_errors." . $error, $error);

        $fieldTranslate = Translate::__($this->getAttributeLabel($fieldName));

        $oldTranslate = Translate::__($templateOld);

        if ($oldTranslate && $oldTranslate != $templateOld) {
            $this->addError($fieldName, strtr($oldTranslate, [':attribute' => $fieldTranslate]));

            return;
        }

        $templateNew  = strtr($templateOld, [':attribute' => AbstractValidator::VARIABLE_KEY_FIELD_LABEL]);

        $newTranslate = Translate::__(
            $templateNew,
            [AbstractValidator::VARIABLE_KEY_FIELD_LABEL => Translate::__($this->getAttributeLabel($fieldName))]
        );

        $this->addError($fieldName, $newTranslate);
    }

}