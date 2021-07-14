<?php

namespace tradersoft\controllers;

use tradersoft\components\Security;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Config;
use tradersoft\helpers\Link;
use tradersoft\helpers\system\Upload_File;
use tradersoft\model\account_details\factory\AccountDetailsFactory;
use tradersoft\model\system\AutoVerificationStatuses;
use tradersoft\model\Trader_Change_Password;
use tradersoft\model\Trader_Forgot_Password;
use tradersoft\model\Trader_Login;
use tradersoft\model\Verification_Upload;
use TS_Functions;
use TSInit;

/**
 * Trader controller
 * @author Bogdan Medvedev <bogdan.medvedev@tstechpro.com>
 */
class Trader_Controller extends Base_Controller
{
    public function rules()
    {
        return [
            'actionForgotPassword' => [
                'roles' => '?', //Only for not authorization user
            ],
            'actionChangePassword' => [
                'roles' => '@', //Only for authorization user
            ],
            'actionAccountDetails' => [
                'roles' => '@', //Only for authorization user
            ],
            'actionLogin' => [
                'roles' => '?', //Only for authorization user
            ],
            'actionVerificationUpload' => [
                'roles' => '@', //Only for authorization user
            ],
            'actionLogout' => [
                'roles' => '@', //Only for authorization user
            ],
            'actionUpdateTraderInfo' => [
                'roles' => '@', //Only for authorization user
            ],
        ];
    }

    /**
     * Updating (getting from CRM) trader info
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function actionUpdateTraderInfo()
    {
        TSInit::$app->trader->updateTraderInfo();
        if ($returnUrl = TSInit::$app->request->get('returnUrl')) {
            $this->redirect($returnUrl);
        }
    }

    public function actionAccountDetails()
    {
        try {
            TSInit::$app->trader->updateTraderInfo();

            $model = AccountDetailsFactory::createModel();
            $model->setWithReceiveEmailNewslettersAgreement(
                Arr::get($this->params, 'receiveEmailNewslettersAgreement')
            );
            if ($model->withReceiveEmailNewslettersAgreement()) {
                $model->agreedReceiveNewsletters = (int) TSInit::$app->trader->get('agreedReceiveNewsletters');
            }
            if ($model->hasView()) {
                $this->view = $model->getView();
            }

            $data = TS_Functions::isFormSubmit('account_details') ? $_POST : [];
            if (!empty($data)) {
                Arr::stripSlashes($data);
                $model->load($data);
                if ($model->validate()) {
                    $model->save();
                }
            }

            if (AutoVerificationStatuses::isProcessVerification()) {
                $this->wpPost->post_title = 'VERIFY DETAILS';
            }
            $this->_setVar('accountDetailsModel', $model);
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    /**
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function actionLogin()
    {
        $response['success'] = false;
        $response['message'] = '';
        $model = new Trader_Login();

        $data = \TS_Functions::isFormSubmit('trader_login') ? $_POST : [];
        if (!empty($data)) {
            if ($model->load($data) && $model->validate()) {
                $result = $model->auth();
                if ($result['isOk']) {
                    if (!\TSInit::$app->request->isAjax) {
                        if (!$result['redirectUrl']) {
                            $result['redirectUrl'] = \TSInit::$app->request->getPath(); // get current page uri
                        }

                        $this->redirect($result['redirectUrl']);
                    }
                    $response['success'] = true;
                    $response['redirectUrl'] = $result['redirectUrl'];
                } else {
                    if (isset($result['redirectUrl'])) {
                        if (!\TSInit::$app->request->isAjax) {
                            if (!$result['redirectUrl']) {
                                $result['redirectUrl'] = \TSInit::$app->request->getPath(); // get current page uri
                            }

                            $this->redirect($result['redirectUrl']);
                        }
                        $response['redirectUrl'] = $result['redirectUrl'];
                    }
                    if (isset($result['message'])) {
                        $response['message'] = $result['message'];
                    }
                }
            } elseif ($model->hasErrors()) {
                $response['message'] = \TS_Functions::__('Not auth');
                if ($model->hasErrors('email')) {
                    $response['errors']['email'] = $model->getFirstError('email');
                }
                if ($model->hasErrors('password')) {
                    $response['errors']['password'] = $model->getFirstError('password');
                }
            } else {
                $response['message'] = \TS_Functions::__('Unknown error');
            }
        }

        if (\TSInit::$app->request->isAjax) {
            http_response_code(200);
            die(json_encode($response));
        } else {
            $this->_setVar('modelTraderLogin', $model);
            $this->_setVar('response', $response);
        }
    }

    public function actionForgotPassword()
    {
        try {
            $this->_setVar(
                'captchaType',
                (Arr::get($this->params, 'enableCaptcha') == 1)
                    ? 'invisible recaptcha'
                    : ''
            );
            $model = new Trader_Forgot_Password($this->params);
            $data = TS_Functions::isFormSubmit('trader_forgot_password') ? $_POST : [];

            if (!empty($data)) {
                $model->load($data);
                if ($model->validate() && $model->send()) {
                    $this->redirect(Link::getForPageWithKey('[TS-AFTER-FORGOT-PASSWORD]'));
                }
            }

            $this->_setVar('traderForgotPasswordModel', $model);
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }

    }

    public function actionChangePassword()
    {
        try {
            $model = new Trader_Change_Password();
            $data = TS_Functions::isFormSubmit('trader_change_password') ? $_POST : [];

            if (!empty($data)) {
                $model->load($data);
                if ($model->validate() && $model->send()) {
                    $this->redirect(
                        Link::getForPageWithKey('[TS-AFTER-CHANGE-PASSWORD]')
                    );
                }
            }

            $this->_setVar('traderChangePasswordModel', $model);

        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    /**
     * Action for trader account verification upload file
     */
    public function actionVerificationUpload()
    {
        try {
            $types = isset($this->params['types']) ? explode(',', $this->params['types']) : [];
            $comments = isset($this->params['mandatorycomments']) ? explode(',', $this->params['mandatorycomments']) : [];
            $commentLabel = isset($this->params['commentlabel']) ? $this->params['commentlabel'] : '';
            $btnLabel = isset($this->params['uploadbuttonlabel']) ? $this->params['uploadbuttonlabel'] : 'Upload';
            $anotherBtnLabel = isset($this->params['uploadanotherbuttonlabel']) ? $this->params['uploadanotherbuttonlabel'] : 'Upload another document';

            $allTypes = Verification_Upload::getCategoriesTypesList();
            // excludes unavailable types
            $filteredTypes = [];
            foreach ($types as $typeId) {
                if (isset($allTypes[$typeId])) {
                    $filteredTypes[$typeId] = $allTypes[$typeId];
                }
            }
            // excludes unavailable comments by types
            $filteredComments = array_intersect($comments, array_keys($allTypes));

            $secureParams = json_encode(
                [
                    'types' => $filteredTypes,
                    'mandatorycomments' => $filteredComments,
                    'microtime' => microtime(true),
                ]
            );
            $security = new Security();
            $signature = $security->getSignature($secureParams);

            $result = [
                'success' => false,
                'errors' => [],
            ];
            $messageBlock = '';

            $traderId = \TSInit::$app->trader->get('crmId', 0);

            if (\TSInit::$app->request->isPost) {
                $csrfData = base64_decode(\TSInit::$app->request->post('csrf'));
                if ($security->getSignature($csrfData) !== \TSInit::$app->request->post('signature')) {
                    $result['errors'][] = 'Signature is wrong!';
                    die(json_encode($result));
                }
                $data = json_decode($csrfData, true);
                $model = new Verification_Upload($traderId, $data['types'], $data['mandatorycomments']);

                $model->file = Upload_File::getInstance('file');
                $model->setCategoryTypeId(\TSInit::$app->request->post('categoryTypeId', 0));
                $model->comment = esc_html(\TSInit::$app->request->post('comment', ''));
                if ($model->validate()) {
                    $result = $model->save();
                } else {
                    $result['errors'] += $model->getErrors('file');
                    $result['errors'] += $model->getErrors('categoryTypeId');
                    $result['errors'] += $model->getErrors('comment');
                }

                if (\TSInit::$app->request->isAjax) {
                    $response = [
                        'isSuccess' => $result['success'],
                        'validationErrors' => $result['errors'],
                    ];
                    die(json_encode($response));
                }

                if ($result['errors']) {
                    $this->_setVar('modalVerificationUploadModel', new Verification_Upload($traderId));
                    $messageBlock .= \tradersoft\View::load('modal/varification-upload-error', ['messages' => $result['errors']]);
                } elseif ($result['success']) {
                    $this->_setVar('modalVerificationUploadModel', new Verification_Upload($traderId));
                    $messageBlock .= \tradersoft\View::load('modal/varification-upload-success');
                }
            }

            $verificationUploadModel = new Verification_Upload($traderId);
            $uploadFileDetails = $verificationUploadModel->getUploadFileDetails();

            $this->_setVar('csrf', base64_encode($secureParams));
            $this->_setVar('signature', $signature);
            $this->_setVar('types', $filteredTypes);
            $this->_setVar('mandatorycomments', $filteredComments);
            $this->_setVar('commentlabel', $commentLabel);
            $this->_setVar('uploadbuttonlabel', $btnLabel);
            $this->_setVar('uploadanotherbuttonlabel', $anotherBtnLabel);
            $this->_setVar('verificationUploadModel', $verificationUploadModel);
            $this->_setVar('maxUploadFileSize', Config::get('verification_upload.img-validation.size.max'));
            $this->_setVar('messageBlock', $messageBlock);
            $this->_setVar('canUploadDocuments', Arr::get($uploadFileDetails, 'canUploadDocuments', true));
            $this->_setVar('forbidUploadDocumentsReason', Arr::get($uploadFileDetails, 'forbidUploadDocumentsReason', ''));
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    /**
     * Action for trader logout
     */
    public function actionLogout()
    {
        TSInit::$app->trader->logout();
        $this->goHome();
    }
}
