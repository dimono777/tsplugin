<?php
namespace tradersoft\controllers;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Request;
use tradersoft\helpers\Session as HelperSession;
use tradersoft\helpers\Link as HelperLink;
use tradersoft\model\Base_Registration;
use tradersoft\model\Registration;
use tradersoft\model\Registration_Demo;
use tradersoft\model\Registration_Mini;
use tradersoft\model\Registration_Islamic;
use tradersoft\model\redirect_after_action\actions\Registration as ActionRegistration;

/**
 * Partners controller
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Registration_Controller extends Base_Controller
{
    public function rules()
    {
        return [
            'actionRegistration' => [
                'roles' => '?', //Only foe not authorization user
            ],
            'actionRegistrationDemo' => [
                'roles' => '?', //Only foe not authorization user
            ],
            'actionRegistrationIslamic' => [
                'roles' => '?', //Only foe not authorization user
            ],
        ];
    }

    /**
     * Action for trader registration
     */
    public function actionRegistration()
    {
        try {
            $this->_commonRegistration(
                new Registration($this->params),
                $this->_getFormData('registration')
            );
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    public function actionRegistrationIslamic()
    {
        try {
            $this->_commonRegistration(
                new Registration_Islamic($this->params),
                $this->_getFormData('registration')
            );
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    /**
     * Action for trader demo registration
     */
    public function actionRegistrationDemo()
    {
        try {
            $this->_commonRegistration(
                new Registration_Demo($this->params),
                $this->_getFormData('registration')
            );
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    /**
     * Action for trader registration mini
     */
    public function actionRegistrationMini()
    {
        try {
            $this->_commonRegistration(
                new Registration_Mini($this->params),
                $this->_getFormData('registration_mini')
            );
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    /**
     * @param Base_Registration $model
     * @param $data array
     */
    protected function _commonRegistration($model, array $data)
    {
        $model->setWithCurrency(Arr::get($this->params, 'withCurrency'));
        $model->setWithNotUSReportablePerson(Arr::get($this->params, 'notUSReportablePerson'));
        $model->setWithReceiveEmailNewslettersAgreement(Arr::get($this->params, 'receiveEmailNewslettersAgreement'));
        $model->setWithCreateExternalAccount(Arr::get($this->params, 'createExternalAccount'));
        $model->setWithPrivacyPolicy(Arr::get($this->params, 'withPrivacyPolicy'));

        $this->_setVar('registrationModel', $model);
        $this->_setVar('showAllCountries', (bool)Arr::get($this->params, 'showAllCountries'));
        $this->_setVar('filterByCountryType', (bool)Arr::get($this->params, 'filterByCountryType'));
        $this->_setVar('allowPromoCode', Arr::get($this->params, 'allowPromoCode'));
        $this->_setVar('enableCaptcha', Arr::get($this->params, 'enableCaptcha'));

        if (!empty($data)) {
            $this->_registration($model, $data);
        }
    }

    /**
     * @param $model Base_Registration
     * @param $data array
     */
    protected function _registration(Base_Registration &$model, array $data)
    {
        Arr::stripSlashes($data);
        $model->load($data);

        if (!$model->validate()) {
            return;
        }

        /** @var array $result */
        $result = $model->save();

        if ($model->hasErrors()) {
            return;
        }

        /** @var HelperSession $session */
        $session = \TSInit::$app->session;

        if (!$result) {
            $session->setFlash('error_registration', \TS_Functions::__('Unknown Error'));
            return;
        }

        if (!$result['isRegister']) {
            /** @var string $description */
            $description = Arr::get($result['data'], 'description', 'Unknown Error');
            $session->setFlash('error_registration', \TS_Functions::__($description));
            return;
        }

        /** @var string $redirectUrl */
        $redirectUrl = Arr::get($result['data'], 'redirectUrl', '');

        // If redirect link (force login page) does not belong to the current domain
        if ($redirectUrl && !HelperLink::hasCurrentDomain($redirectUrl)) {
            // Redirect to force login page of another site
            $this->_redirectAfterToForceLogin($redirectUrl);
        }

        $this->_authAfterRegistration($model);
    }

    /**
     * Redirect to force login page of another site
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param string $link
     */
    protected function _redirectAfterToForceLogin($link)
    {
        $link = $this->_addFlagToForceLoginLink($link);
        $this->redirect($link);
    }

    /**
     * Add a flag to the force login link, so that the force login page, on another site,
     * would understand that they came to her after registration
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param string $link
     *
     * @return string
     */
    protected function _addFlagToForceLoginLink($link)
    {
        return add_query_arg([Base_Registration::CAME_AFTER_ACTION => ActionRegistration::ID], $link);
    }

    /**
     * @param $model Base_Registration
     */
    protected function _authAfterRegistration(Base_Registration &$model)
    {
        $session = \TSInit::$app->session;
        $resultAuth = $model->auth();
        if ($resultAuth) {
            $authData = $resultAuth['data'];
            if ($resultAuth['isAuth'] && isset($authData->{'leadInfo'})) {
                \TSInit::$app->trader->login(Arr::stdToArr($authData->{'leadInfo'}), true);
                $this->redirect(
                    $this->_getRedirectUrlAfter($model)
                );
            } else {
                if (isset($authData->{'description'})) {
                    $session->setFlash('error_registration', \TS_Functions::__($authData->{'description'}));
                }
                if ($resultAuth['redirectTo']) {
                    $this->redirect($resultAuth['redirectTo']);
                }
            }
        } else {
            $session->setFlash('error_registration', \TS_Functions::__('Unknown Error'));
        }
    }

    /**
     * Redirect after registration
     * @param $model Base_Registration
     * @return $url
     */
    protected function _getRedirectUrlAfter(Base_Registration &$model)
    {
        /** @var string $url */
        $url = $model->getRedirectUrlAfter();

        if (!$url) {
            $url = \TSInit::$app->request->getPath(); // get current page uri
        }

        return $url;
    }

    private function _getFormData($form)
    {
        return \TS_Functions::isFormSubmit($form) ? $_POST : [];
    }
}