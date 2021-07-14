<?php
namespace tradersoft\controllers;

use tradersoft\helpers\Currency;
use tradersoft\helpers\RecentWithdrawalsSettings;
use tradersoft\model\Withdrawal_Cancel;
use tradersoft\model\Withdrawal_Request;
use TS_Functions;

/**
 * Withdrawal controller
 * @author Bogdan Medvedev <bogdan.medvedev@tstechpro.com>
 */
class Withdrawal_Controller extends Base_Controller
{
    /**  @var $_modelWithdrawalRequest Withdrawal_Request */
    protected $_modelWithdrawalRequest;

    public function rules()
    {
        return [
            'actionWithdrawalRequest' => [
                'roles' => '@', //Only for authorization user
            ]
        ];
    }

    protected function _beforeExecute()
    {
        $this->_modelWithdrawalRequest = new Withdrawal_Request();
    }

    public function actionWithdrawalRequest()
    {
        try {
            $this->_request();
            $this->_cancel();

            $this->_modelWithdrawalRequest->initRequests();
            $this->_setVar('traderWithdrawalModel', $this->_modelWithdrawalRequest);
            $this->_setVar('messages', Withdrawal_Request::getValidationMessages());
            $this->_setVar('currency', Currency::getInstance());
            $this->_setVar(RecentWithdrawalsSettings::SETTING_NAME, RecentWithdrawalsSettings::getSettingsValue());
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    protected function _request()
    {
        $data = TS_Functions::isFormSubmit('withdrawal') ? $_POST : [];

        if (!empty($data)) {
            $this->_modelWithdrawalRequest->load($data);
            if ($this->_modelWithdrawalRequest->validate()) {
                $this->_modelWithdrawalRequest->send();
            }
        }
    }

    protected function _cancel()
    {
        $model = new Withdrawal_Cancel();
        $this->_setVar('traderWithdrawalCancelModel', $model);

        $data = TS_Functions::isFormSubmit('withdrawal_cancel') ? $_POST : [];

        if (!empty($data)) {
            $model->load($data);
            if ($model->validate()) {
                $model->send();
            }
        }
    }
}