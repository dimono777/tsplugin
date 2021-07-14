<?php
namespace tradersoft\model;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;

use TSInit;
/**
 * Withdrawal model
 * @author Bogdan Medvedev <bogdan.medvedev@tstechpro.com>
 */
class Withdrawal_Cancel extends Model
{
    public $requestId;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['requestId', 'required'],
            ['requestId', 'number']
        ];
    }

    /**
     * Save account details
     * @return bool
     */
    public function send()
    {
        $data = Interlayer_Crm::cancelWithdrawalRequest(
            TSInit::$app->trader->get('crmId'),
            $this->requestId
        );

        $result = false;

        if ($data) {
            $returnCode = Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);

            if ($returnCode == Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
                $result = true;
                TSInit::$app->session->setFlash(
                    'success_message',
                    \TS_Functions::__('Withdrawal request has been cancelled')
                );
            } else {
                TSInit::$app->session->setFlash(
                    'error_message',
                    \TS_Functions::__(Arr::get($data, 'description'))
                );
            }
        }
        return $result;
    }
}