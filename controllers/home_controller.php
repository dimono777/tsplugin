<?php
namespace tradersoft\controllers;

use tradersoft\model\Call_Back;
use tradersoft\model\Contact_Us;
use tradersoft\helpers\Arr;
use TS_Functions;

/**
 * Main controller
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Home_Controller extends Base_Controller
{
    public function actionCallBack()
    {
        $response['success'] = false;
        $response['message'] = '';

        $model = new Call_Back();
        $data = TS_Functions::isFormSubmit('call_back') ? $_POST : [];

        if (!empty($data)) {
            Arr::stripSlashes($data);
            if ($model->load($data) && $model->validate()) {
                $result = $model->send();
                $response = array_merge($response, $result);
            } else {
                $response['message'] = \TS_Functions::__('Unknown error');
            }
        }

        $this->_setVar('modelCallBack', $model);
        $this->_setVar('responseCallBack', $response);

        if (\TSInit::$app->request->isAjax) {
            $this->_jsonResponse($response);
        }
    }

    public function actionContactUs()
    {
        try {
            $model = new Contact_Us();
            $data = TS_Functions::isFormSubmit('contact_us') ? $_POST : [];

            if (!empty($data)) {
                Arr::stripSlashes($data);
                if ($model->load($data) && $model->validate()) {
                    $model->send();
                } else {
                    $model->addError('main', \TS_Functions::__('Unknown error'));
                }
            }

            $this->_setVar('modelContactUs', $model);
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    public function actionSkipPopuperAssets()
    {
        wp_dequeue_script( 'ts-popuper' );
    }
}