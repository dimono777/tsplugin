<?php
namespace tradersoft\controllers;
use TSInit;

/**
 * Platform controller
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Platform_Controller extends Base_Controller
{
    public function rules()
    {
        return [
            'actionTradingRoom' => [
                'roles' => '@', //Only for authorization user
            ]
        ];
    }

    /**
     * Action for redirect to platform
     */
    public function actionTradingRoom()
    {
        $this->redirect('//trade.' . TSInit::$app->request->getMainDomain());
    }
}