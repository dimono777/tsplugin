<?php
namespace tradersoft\model;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;

use TSInit;
use TS_Functions;

/**
 * Withdrawal model
 * @author Bogdan Medvedev <bogdan.medvedev@tstechpro.com>
 */
class Withdrawal_Request extends Model
{
    const DEFAULT_MIN_AMOUNT = 0;

    public $amount;
    public $fees = [];
    public $requests = [];
    public $statuses = [];

    public function init()
    {
        $this->_initFees();

        $this->amount = Arr::get($this->fees, 'minAmount', self::DEFAULT_MIN_AMOUNT);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['amount', 'required'],
            ['amount', 'number']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'amount' => \TS_Functions::__('Amount')
        ];
    }

    /**
     * Save account details
     * @return bool
     */
    public function send()
    {
        $data = Interlayer_Crm::withdrawalRequest(
            \TSInit::$app->trader->get('username'),
            $this->amount
        );

        $result = false;

        if ($data) {
            $returnCode = Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);

            if ($returnCode == Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
                $result = true;
                TSInit::$app->session->setFlash(
                    'success_message',
                    \TS_Functions::__('Your account manager will contact you shortly to finalize your withdrawal request.')
                );
            } else {
                TSInit::$app->session->setFlash(
                    'error_message',
                    \TS_Functions::__($data['description'])
                );
            }
        }
        return $result;
    }

    public function initRequests()
    {
        $data = Interlayer_crm::getWithdrawalRequestsByLeadId(
            TSInit::$app->trader->get('crmId')
        );

        if ($data) {
            if (Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR) == 0) {
                $this->requests = $data['data'];

                foreach ($data['statuses'] as $wd_k => $wd_v) {
                    foreach ($wd_v as $w_v) {
                        $this->statuses[$w_v] = $wd_k;
                    }
                }
            } else {
                TSInit::$app->session->setFlash(
                    'error_message',
                    \TS_Functions::__($data['description'])
                );
            }
        }
    }

    protected function _initFees()
    {
        $data = Interlayer_Crm::getFeeIndexes(
            TSInit::$app->trader->get('crmId')
        );

        if ($data) {
            if (Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR) == 0) {
                $this->fees = $data['data'];
            } else {
                TSInit::$app->session->setFlash(
                    'error_message',
                    \TS_Functions::__($data['description'])
                );
            }
        }
    }

    public static function getValidationMessages()
    {
        return [
            'notValidAmount' => \TS_Functions::__("Note: Withdrawal amount not valid."),
            'minAmount' => \TS_Functions::__("Withdrawal minimum is "),
            'maxAmount' => \TS_Functions::__("Withdrawal maximum is "),
        ];
    }
}