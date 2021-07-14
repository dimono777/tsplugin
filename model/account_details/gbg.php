<?php
namespace tradersoft\model\account_details;

use tradersoft\model\system\AutoVerificationStatuses;
use tradersoft\model\ModelOption;
use tradersoft\helpers\system\Translate;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Config;
use tradersoft\helpers\Platform;
use TSInit;

abstract class GBG extends Base
{
    use ModelOption;

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    public $country;
    public $state;
    public $gender;

    protected $_view = 'trader/account-details-gbg';

    /**
     * @inheritdoc
     */
    public function attributeOptions()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Get countries list
     * @return array
     */
    public function getCountriesList()
    {
        return Interlayer_Crm::getCountriesAll(null,1);
    }

    /**
     * Get states list
     * @return array
     */
    public function getStatesList()
    {
        return Arr::get(Interlayer_Crm::getStates(), $this->country, []);
    }

    /**
     * Get genders list
     * @return array
     */
    public function getGenderList()
    {
        return [
            self::GENDER_MALE => \TS_Functions::__('Male'),
            self::GENDER_FEMALE => \TS_Functions::__('Female'),
        ];
    }

    public function isStateEnabled()
    {
        return !empty($this->getStatesList());
    }

    protected function _loadModelAttributes()
    {
        parent::_loadModelAttributes();
        $this->country = TSInit::$app->trader->get('country');
        $this->state = TSInit::$app->trader->get('state');
        $this->gender = TSInit::$app->trader->get('gender');
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
                $this->addError('fname', \TS_Functions::__($validationErrors['firstName']));
            }
            if (isset($validationErrors['lastName'])) {
                $this->addError('lname', \TS_Functions::__($validationErrors['lastName']));
            }
            if (isset($validationErrors['gender'])) {
                $this->addError('gender', \TS_Functions::__($validationErrors['gender']));
            }
            if (isset($validationErrors['birthday'])) {
                $this->addError('yearNumber', \TS_Functions::__($validationErrors['birthday']));
            }
            if (isset($validationErrors['country'])) {
                $this->addError('country', \TS_Functions::__($validationErrors['country']));
            }
            if (isset($validationErrors['phone'])) {
                $this->addError('phone', \TS_Functions::__($validationErrors['phone']));
            }
            if (isset($validationErrors['cellphone'])) {
                $this->addError('cellphone', \TS_Functions::__($validationErrors['cellphone']));
            }
            if (isset($validationErrors['town'])) {
                $this->addError('town', \TS_Functions::__($validationErrors['town']));
            }
            if (isset($validationErrors['state'])) {
                $this->addError('state', \TS_Functions::__($validationErrors['state']));
            }
            if (isset($validationErrors['postalCode'])) {
                $this->addError('postalCode', \TS_Functions::__($validationErrors['postalCode']));
            }
        }
    }

    /**
     * Set auto verification status message
     */
    protected function _setStatusMessage()
    {
        if (AutoVerificationStatuses::isStatusVerify()) {
            TSInit::$app->session->setFlash('autoVerificationStatus', \TS_Functions::__('Account details are verified'));
        } else {
            TSInit::$app->session->setFlash('autoVerificationStatus', \TS_Functions::__('Account details are not verified'));
        }
    }

    /**
     * Set error message
     * @param $key string
     * @param $message string
     */
    protected function _setFlashError($key, $message)
    {
        TSInit::$app->session->setFlash($key, Translate::__($message, [
            ':support' => '<span class="to-support">' . \TS_Functions::__('support') . '</span>'
        ]));
    }

    /**
     * Prepare account data for saving
     * @return array
     */
    protected function _prepareAccountData()
    {
        $data =  [
            'phone'      => $this->phone,
            'cellphone'  => $this->cellphone,
            'traderID'   => TSInit::$app->trader->get('username', ''),
        ];

        if ($this->withReceiveEmailNewslettersAgreement()) {
            $data['agreedReceiveNewsletters'] = $this->agreedReceiveNewsletters;
        }

        return $data;
    }

    /**
     * Before verification saving
     * @return bool
     */
    protected function _beforeVerificationSaving()
    {
        $data = Interlayer_Crm::updateAccount($this->_prepareAccountData());

        if (!$data) {
            TSInit::$app->session->setFlash('error_account_details', \TS_Functions::__('Unknown error'));
            return false;
        }

        $returnCode = Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);
        if ($returnCode == Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
            TSInit::$app->session->setFlash('success_account_details', \TS_Functions::__('The information was successfully updated'));
            // Update session info with CRM data
            TSInit::$app->trader->updateTraderInfo();
            return true;
        } else {
            $errorDescription = Config::get('gbg_errors.default', Arr::get($data, 'description'));
            TSInit::$app->session->setFlash('error_account_details', \TS_Functions::__($errorDescription));
            $this->_prepareError($data);
        }

        return false;
    }

    /**
     * In verification process saving
     * @return bool
     */
    protected function _processVerificationSaving()
    {
        $data = Interlayer_Crm::updateAccount($this->_prepareAccountData());

        // Update session info with CRM data
        TSInit::$app->trader->updateTraderInfo();
        $this->_setStatusMessage();

        if (!$data) {
            TSInit::$app->session->setFlash('error_account_details', \TS_Functions::__('Unknown error'));
            return false;
        }

        $returnCode = Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);
        if ($returnCode == Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
            TSInit::$app->session->setFlash('success_account_details', \TS_Functions::__('The information was successfully updated'));
            if (TSInit::$app->trader->autoVerificationBeforeFtd) {
                TSInit::$app->request->redirect(Platform::getURL(Platform::URL_DEPOSIT_ID));
            }
            return true;
        } else {
            $errorDescription = Config::get('gbg_errors.default', Arr::get($data, 'description'));
            if ($returnCode == Interlayer_Crm::RESPONSE_CODE_GBG_VERIFICATION_FAIL) {
                $errorDescription = Config::get('gbg_errors.fail', $errorDescription);
            } elseif ($returnCode == Interlayer_Crm::RESPONSE_CODE_GBG_VERIFICATION_ATTEMPTS_EXCEEDED) {
                $this->_setFlashError('error_account_details', Config::get('gbg_errors.finish', $errorDescription));
                TSInit::$app->request->refresh();
            } elseif ($returnCode == Interlayer_Crm::RESPONSE_CODE_GBG_DISALLOWED_CURRENT_STATUS) {
                $errorDescription = Arr::get($data, 'description');
            }
            $this->_setFlashError('error_account_details', $errorDescription);
            $this->_prepareError($data);
        }

        return false;
    }

    /**
     * After verification saving
     * @return bool
     */
    protected function _afterVerificationSaving()
    {
        $data = Interlayer_Crm::updateAccount($this->_prepareAccountData());

        // Update session info with CRM data
        TSInit::$app->trader->updateTraderInfo();
        $this->_setStatusMessage();

        if (!$data) {
            TSInit::$app->session->setFlash('error_account_details', \TS_Functions::__('Unknown error'));
            return false;
        }

        $returnCode = Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);
        if ($returnCode == Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
            TSInit::$app->session->setFlash('success_account_details', \TS_Functions::__('The information was successfully updated'));
            return true;
        } else {
            $errorDescription = Config::get('gbg_errors.default', Arr::get($data, 'description'));
            if ($returnCode == Interlayer_Crm::RESPONSE_CODE_GBG_VERIFICATION_FAIL) {
                $errorDescription = Config::get('gbg_errors.fail', $errorDescription);
            } elseif ($returnCode == Interlayer_Crm::RESPONSE_CODE_GBG_VERIFICATION_ATTEMPTS_EXCEEDED) {
                $errorDescription = Config::get('gbg_errors.finish', $errorDescription);
            } elseif ($returnCode == Interlayer_Crm::RESPONSE_CODE_GBG_DISALLOWED_CURRENT_STATUS) {
                $errorDescription = Config::get('gbg_errors.finish', $errorDescription);
            }
            $this->_setFlashError('error_account_details', $errorDescription);
            $this->_prepareError($data);
        }

        return false;
    }
}