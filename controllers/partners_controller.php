<?php
namespace tradersoft\controllers;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Partner;
use tradersoft\helpers\system\Cookie;
use tradersoft\model\Partners_Authorization;
use tradersoft\model\Partners_Forgot_Password;
use tradersoft\model\Partners_Registration;

/**
 * Partners controller
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Partners_Controller extends Base_Controller
{
    public function actionAuthorization()
    {
        try {
            $model = new Partners_Authorization();
            $data = \TS_Functions::isFormSubmit('partner-authorization') ? $_POST : [];
            if (!empty($data)) {
                $model->load($data);
                if($model->validate()){
                    $result = $model->auth();
                    $session = \TSInit::$app->session;
                    if ($result) {
                        $data = $result['data'];
                        $description = isset($data->{'description'}) ? $data->{'description'} : 'Unknown Error';

                        if ($result['isAuth']) {
                            $token = isset($data->{'token'}) ? $data->{'token'} : null;
                            if ($token) {
                                Partner::redirectToPartners('/login/token/' . $token . '?remember=' . (int)$model->keep);
                            } else {
                                $session->setFlash('error_partners_authorization', \TS_Functions::__($description));
                            }
                        } else {
                            $error = isset($data->{'error'}) ? $data->{'error'} : null;
                            if (!empty($error->{'username'})) {
                                $model->addError('email', $error->{'username'});
                                $description = $error->{'username'};
                            }
                            if (!empty($error->{'password'})) {
                                $model->addError('password', $error->{'password'});
                                $description = $error->{'password'};
                            }

                            $session->setFlash('error_partners_authorization', \TS_Functions::__($description));
                        }

                    } else {
                        $session->setFlash('error_partners_authorization', \TS_Functions::__('Unknown Error'));
                    }
                }
            }

            $this->_setVar('partnersAuthorizationModel', $model);
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    public function actionRegistration()
    {
        try {
            if (!empty($_GET['ref'])) {
                Cookie::set('ref', $_GET['ref'], 3600*7);
            }

            $model = new Partners_Registration();
            $data = \TS_Functions::isFormSubmit('partner_registration') ? $_POST : [];
            if (!empty($data)) {
                $model->load($data);
                if($model->validate()) {
                    $model->validateTerms();
                    if (!$model->hasErrors()) {
                        $result = $model->register();
                        $session = \TSInit::$app->session;
                        if ($result) {
                            $data = $result['data'];
                            $description = isset($data->{'description'}) ? $data->{'description'} : 'Unknown Error';

                            if ($result['isRegister']) {
                                \TS_Functions::deleteCookie('ref');
                                $session->setFlash('after-registration', 1);
                            } else {
                                $error = isset($data->{'error'}) ? $data->{'error'} : null;
                                if (!empty($error->{'fullName'})) {
                                    $model->addError('fname', $error->{'fullName'});
                                    $model->addError('lname', $error->{'fullName'});
                                }
                                if (!empty($error->{'email'})) {
                                    $model->addError('email', $error->{'email'});
                                }
                                if (!empty($error->{'phone'})) {
                                    $model->addError('phone', $error->{'phone'});
                                }
                                if (!empty($error->{'language'})) {
                                    $model->addError('language', $error->{'language'});
                                }
                                if (!empty($error->{'password'})) {
                                    $model->addError('password', $error->{'password'});
                                }
                                if (!empty($error->{'agreeWithTerms'})) {
                                    $model->addError('accept', $error->{'agreeWithTerms'});
                                }
                                if (!empty($error->{'message'})) {
                                    $session->setFlash('error_partners_registration', \TS_Functions::__($error->{'message'}));
                                } else {
                                    $session->setFlash('error_partners_registration', \TS_Functions::__($description));
                                }
                            }
                        } else {
                            $session->setFlash('error_partners_registration', \TS_Functions::__('Unknown Error'));
                        }
                    }
                }
            }

            $this->_setVar('partnersRegistrationModel', $model);
        } catch (\Exception $e) {
            wp_die($e->getMessage());
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
            $model = new Partners_Forgot_Password($this->params);
            $data = \TS_Functions::isFormSubmit('partner_forgot_password') ? $_POST : [];
            if (!empty($data)) {
                $model->load($data);
                if($model->validate()) {
                    $result = $model->send();
                    $session = \TSInit::$app->session;
                    if ($result) {
                        $data = $result['data'];
                        $description = isset($data->{'description'}) ? $data->{'description'} : 'Unknown Error';

                        if ($result['isSend']) {
                            $session->setFlash('after_forgot_password', 1);
                        } else {
                            $error = isset($data->{'error'}) ? $data->{'error'} : null;
                            if (!empty($error->{'email'})) {
                                $model->addError('email', $error->{'email'});
                            }

                            if (!empty($error->{'message'})) {
                                $session->setFlash('error_partners_forgot_password', \TS_Functions::__($error->{'message'}));
                            } else {
                                $session->setFlash('error_partners_forgot_password', \TS_Functions::__($description));
                            }
                        }
                    } else {
                        $session->setFlash('error_partners_forgot_password', \TS_Functions::__('Unknown Error'));
                    }
                }
            }

            $this->_setVar('partnersForgotPasswordModel', $model);
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }
}