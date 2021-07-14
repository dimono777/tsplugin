<?php

namespace tradersoft\controllers;

use tradersoft\helpers\Arr;
use tradersoft\model\DynamicValidationModel;
use TSInit;

class Ajax_Validation_Controller extends Base_Controller
{

    public function actionValidate()
    {
        if (!TSInit::$app->request->isPost) {
            return;
        }

        $post = TSInit::$app->request->post();

        if (!($modelData = Arr::get($post, 'modelData'))) {
            $this->renderAjax(false);
        }
        if (!($rule = $this->_getValidationData($post))) {
            $this->renderAjax(false);
        }

        try {
            $model = new DynamicValidationModel();
            $model->load($modelData);
            $model->setRules([$rule]);
            $model->validate();

            $this->renderAjax(!$model->hasErrors(), $model->getErrors());
        } catch (\Exception $e) {
            $this->renderAjax(false);
        }
    }

    /**
     * @param bool  $isSuccess
     * @param array $errors
     * @param array $data
     */
    public function renderAjax($isSuccess, array $errors = [], array $data = [])
    {
        http_response_code(200);
        die(json_encode(['success' => (bool)$isSuccess, 'errors' => $errors, 'data' => $data]));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function _getValidationData(array $data)
    {
        if (!($validatorName = Arr::get($data, 'validatorName'))) {
            return [];
        }
        if (!($validationFields = (array)Arr::get($data, 'validationFields'))) {
            return [];
        }

        return [$validationFields, $validatorName, Arr::get($data, 'validatorParams')];
    }

}