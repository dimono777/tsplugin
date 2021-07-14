<?php

namespace tradersoft\controllers;

use tradersoft\exceptions\VerificationDataSavingException;
use tradersoft\model\verification\aml\Form as AMLVerificationForm;
use TS_Functions;
use TSInit;
use Exception;

class Verification_Controller extends Base_Controller
{
    public function rules()
    {
        return [
            'actionAml' => [
                'roles' => '@', //Only for authorized users
            ],
        ];
    }

    public function actionAml()
    {
        try {
            if (!TSInit::$app->request->isPost) {
                if ($redirectUrl = TSInit::$app->request->getReferer()) {
                    TSInit::$app->session->set('redirectUrl', $redirectUrl);
                }
            }

            $model = new AMLVerificationForm();

            if (!$model->canBeEdited()) {
                $this->redirect(TSInit::$app->request->getHomeUrl());
            }

            try {
                $data = $this->_getFormData($model->formName());
                if (!empty($data)) {
                    if (
                        $model->load($data)
                        && $model->validate()
                        && $model->save()
                    ) {
                        $redirectUrl = TSInit::$app->session->get('redirectUrl', TSInit::$app->request->getHomeUrl());
                        TSInit::$app->session->remove('redirectUrl');

                        $this->redirect($redirectUrl);
                    }
                }
                $this->_setVar('errorMessage', '');
            } catch (VerificationDataSavingException $e) {
                $this->_setVar('errorMessage', $e->getMessage());
            }

            $this->_setVar('model', $model);
        } catch (Exception $e) {
            wp_die($e->getMessage());
        }
    }

    /**
     * @param string $form
     * @return array
     */
    private function _getFormData($form)
    {
        return TS_Functions::isFormSubmit($form) ? $_POST : [];
    }
}