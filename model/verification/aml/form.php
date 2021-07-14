<?php

namespace tradersoft\model\verification\aml;

use tradersoft\exceptions\VerificationDataSavingException;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\model\Model;
use tradersoft\model\verification\helpers\ErrorMessages;
use TS_Functions;
use TSInit;

class Form extends Model
{
    /** @var */
    public $nationalIdentificationNumber;

    /** @var */
    public $TIN;

    /** @var */
    public $TINMissingReasonId;

    /** @var */
    public $TINMissingReasonComment;

    /** @var */
    public $doNotHaveTIN;

    /** @var */
    public $countryOfTax;

    /** @var */
    public $companyName;

    /** @var */
    public $declarationOfKnowledgeBelief;

    /** @var */
    public $consentCooperationTaxAuthorities;

    /** @var */
    private $_leadId;

    /** @var array */
    private $_leadData = [];

    /** @var bool */
    private $_canBeEdited = false;

    /** @var array */
    private $_taxCountries = [];

    /** @var array */
    private $_TINMissingReasons = [];

    /** @var array */
    private $_formAttributes = [];

    /**
     *
     *
     * @return array
     *
     */
    public function rules()
    {

        return [
            [
                [
                    'nationalIdentificationNumber',
                    'TIN',
                    'countryOfTax',
                    'companyName',
                    'declarationOfKnowledgeBelief',
                    'consentCooperationTaxAuthorities',
                ],
                'stripTags',
            ],
        ];
    }

    /**
     * @return void
     *
     */
    public function init()
    {

        $this->_leadId = TSInit::$app->trader->get('crmId');
        $this->_loadExternalData();
    }

    /**
     * Get attributes titles from CRM and pass them through translations
     *
     * @return array
     *
     */
    public function attributeLabels()
    {

        $result = [];
        foreach ($this->_formAttributes as $entityName => $attribute) {
            $result[$entityName] = TS_Functions::__(
                Arr::get($attribute, 'titleForLead', $entityName)
            );


        }
        return $result;
    }

    /**
     * @return string
     */
    public function formName()
    {

        return 'aml-verification';
    }

    /**
     * @return bool
     */
    public function canBeEdited()
    {

        return $this->_canBeEdited;
    }

    /**
     * @return array
     */
    public function getTaxCountries()
    {

        return $this->_taxCountries;
    }

    /**
     * @return TINMissingReason[]
     */
    public function getTINMissingReasons()
    {

        return $this->_TINMissingReasons;
    }

    /**
     * @inheritDoc
     */
    public function afterLoad()
    {

        foreach ($this->attributes() as $attribute) {
            if (is_array($this->$attribute)) {
                array_walk(
                    $this->$attribute,
                    function(&$value) {

                        $value = trim(strip_tags(stripcslashes($value)));
                    }
                );
            } else {
                $this->$attribute = trim(strip_tags(stripcslashes($this->$attribute)));
            }

        }
        parent::afterLoad();
    }

    /**
     * @return bool
     * @throws VerificationDataSavingException
     */
    public function save()
    {

        $dataToSave = $this->_prepareDataToSave();
        $result = Interlayer_Crm::saveAMLVerificationFormData($this->_leadId, $dataToSave);
        if (Arr::get($result, 'returnCode') == Interlayer_Crm::RESPONSE_CODE_FORM_FIELD_NOT_VALID) {
            $this->_displayErrors(Arr::get($result, 'validationErrors', []));

            return false;
        } elseif (Arr::get($result, 'returnCode') == Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
            return true;
        }
        throw new VerificationDataSavingException('Unknown error');
    }

    /**
     *
     *
     * @return void
     *
     */
    protected function _loadExternalData()
    {

        $result = Interlayer_Crm::getAMLVerificationFormData($this->_leadId);
        $verificationData = Arr::get($result, 'verificationData', []);
        $this->_canBeEdited = Arr::get($verificationData, 'canEdit', false);
        $this->_leadData = Arr::get($verificationData, 'leadData');
        $this->_formAttributes = Arr::get($verificationData, 'formAttributes', []);
        $this->_taxCountries = Arr::get($verificationData, 'taxCountries', []);
        $this->_TINMissingReasons = TINMissingReason::getListFromData(
            Arr::get($verificationData, 'TINMissingReasons', [])
        );
    }

    /**
     *
     * @return array
     *
     */
    protected function _prepareDataToSave()
    {

        $dataToSave = [
            'nationalIdentificationNumber' => $this->nationalIdentificationNumber,
            'countryOfTax' => $this->countryOfTax,
            'companyName' => $this->companyName,
        ];
        if ($this->doNotHaveTIN) {
            $dataToSave['TINMissingReasonId'] = $this->TINMissingReasonId;
            $dataToSave['TINMissingReasonComment'] = Arr::get(
                $this->TINMissingReasonComment,
                $this->TINMissingReasonId
            );
        } else {
            $dataToSave['TIN'] = $this->TIN;
        }
        if ($this->declarationOfKnowledgeBelief && $this->declarationOfKnowledgeBelief !== '0') {
            $dataToSave['declarationOfKnowledgeBelief'] = $this->declarationOfKnowledgeBelief;
        }
        if ($this->consentCooperationTaxAuthorities && $this->consentCooperationTaxAuthorities !== '0') {
            $dataToSave['consentCooperationTaxAuthorities'] = $this->consentCooperationTaxAuthorities;
        }

        return $dataToSave;
    }

    /**
     * @param $validationErrors
     */
    protected function _displayErrors($validationErrors)
    {

        foreach ($validationErrors as $field => $error) {
            if ($field == 'TINMissingReasonComment') {
                $field = "TINMissingReasonComment[$this->TINMissingReasonId]";
            } elseif (in_array($field, ['declarationOfKnowledgeBelief', 'consentCooperationTaxAuthorities'])) {
                $error = 'amlVerification_requiredProofCheckbox';
            }
            $this->addError($field, ErrorMessages::getMessage($error));
        }
    }

    /**
     * @return void
     */
    protected function _initLeadData()
    {

        if ($this->_leadData) {
            $this->load($this->_leadData);
        }
        if (empty($this->TIN) && !empty($this->TINMissingReasonId)) {
            $this->doNotHaveTIN = true;
            $this->TINMissingReasonComment = [
                $this->TINMissingReasonId => $this->TINMissingReasonComment,
            ];
        }
    }
}