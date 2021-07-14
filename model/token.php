<?php

namespace tradersoft\model;

use Exception;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Link;
use tradersoft\helpers\Platform;
use tradersoft\model\Base_Registration as ModelBaseRegistration;
use tradersoft\model\redirect_after_action\Init as RedirectAfterActionInit;
use TSInit;

/**
 * Class Token
 *
 * @package tradersoft\model
 */
class Token
{
    const TOKEN_GET_KEY = 'force_token';

    const FORCE_REDIRECT_GET_KEY = 'fr';
    const PASSWORD_RECOVERY_VIA_LINK_GET_KEY = 'password_recovery_via_link';
    const PASSWORD_RECOVERY_FLASH_KEY = 'password_recovery_lead_id';

    /** @var array  */
    const RESPONSE_CODES_WITH_POSSIBLE_REDIRECTS = [
        Interlayer_Crm::RESPONSE_CODE_WRONG_DOMAIN,
    ];

    const FORCE_REDIRECT_PARAM_PLATFORM = 'platform';
    const FORCE_REDIRECT_PARAM_DEPOSIT = 'deposit';
    const FORCE_REDIRECT_PARAM_VERIFICATION = 'account-verification';
    const FORCE_REDIRECT_PARAM_PROFESSIONAL_FORM = 'professional-request-form';
    const FORCE_REDIRECT_PARAM_QUESTIONNAIRE = 'investor-questionnaire';
    const FORCE_REDIRECT_PARAM_AML = 'aml-verification';
    const FORCE_REDIRECT_PARAM_LINK = 'link';

    /** @var array */
    private $_data;

    /** @var string|null */
    private $_token;

    /** @var bool */
    private $_isPasswordRecoveryToken;

    /** @var string|null */
    private $_forceRedirect;

    /** @var int */
    private $_afterAction;

    /** @var string */
    private $_redirectUrl = '';

    /**
     * Token constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->_data = $data;

        $this->_token = $this->_getParam(static::TOKEN_GET_KEY);
        $this->_forceRedirect = $this->_getParam(static::FORCE_REDIRECT_GET_KEY, '');
        $this->_afterAction = (int)$this->_getParam(ModelBaseRegistration::CAME_AFTER_ACTION, 0);
        $this->_isPasswordRecoveryToken = (bool)$this->_getParam(static::PASSWORD_RECOVERY_VIA_LINK_GET_KEY);
    }

    /**
     * Return token
     *
     * @return string|null
     */
    public function getRedirectUrl()
    {
        return $this->_redirectUrl;
    }

    /**
     * Process token and return false or redirect url
     *
     * @return bool
     * @throws Exception
     */
    public function processToken()
    {
        if (!$this->_token) {
            return false;
        }

        if ($this->_isPasswordRecoveryToken) {
            $this->_processPasswordRecoveryToken();
        } else {
            $this->_processAuthToken();
        }

        /**
         * just for case. e.g. some chosen page not exist anymore etc.
         * Lead should not stay on page with token, so move lead to 404
         */
        if (!$this->_redirectUrl) {
            $this->_redirectUrl = '/404';
        }

        return true;
    }

    /**
     * Process auth token
     *
     * @throws Exception
     */
    private function _processAuthToken()
    {
        $responseData = Interlayer_crm::loginByToken($this->_token);


        $this->_loadRedirectUrlByResponse($responseData);

        if (!$this->_isResponseCodeSuccess($responseData)) {

            return;
        }

        TSInit::$app->trader->login(
                Arr::stdToArr($responseData['leadInfo']),
                true
            );

        switch ($this->_forceRedirect) {

            case static::FORCE_REDIRECT_PARAM_PLATFORM:
                $this->_redirectUrl = Platform::getURL();
                break;

            case static::FORCE_REDIRECT_PARAM_DEPOSIT:
                $this->_redirectUrl = Platform::getURL(Platform::URL_DEPOSIT_ID);
                break;

            case static::FORCE_REDIRECT_PARAM_VERIFICATION:
                $this->_redirectUrl = Link::getForPageWithKey('[TS-ACCOUNT-VERIFICATION-UPLOAD]');
                break;

            case static::FORCE_REDIRECT_PARAM_PROFESSIONAL_FORM:
                $this->_redirectUrl = Link::getForPageWithKey('[TS-PROFESSIONAL-REQUEST-FORM]');
                break;

            case static::FORCE_REDIRECT_PARAM_QUESTIONNAIRE:
                $this->_redirectUrl = Link::getForPageWithKey('[TS-SURVEY]');
                break;

            case static::FORCE_REDIRECT_PARAM_AML:
                $this->_redirectUrl = Link::getAmlVerificationPage();
                break;

            case static::FORCE_REDIRECT_PARAM_LINK:
                if ($link = $this->_getParam('link')) {
                    $this->_redirectUrl = $link;
                }
                break;

            default:
                if (!$this->_afterAction) {
                    break;
                }

                $redirectUrlAfter = (new RedirectAfterActionInit($this->_afterAction))
                    ->getUrl();

                if ($redirectUrlAfter) {
                    $this->_redirectUrl = $redirectUrlAfter;
                }
                break;
        }
    }

    /**
     * Process password recovery token
     *
     * @throws Exception
     */
    private function _processPasswordRecoveryToken()
    {
        TSInit::$app->session->remove(static::PASSWORD_RECOVERY_FLASH_KEY);

        $responseData = Interlayer_crm::getLeadIdByPasswordRecoveryToken($this->_token);

        $this->_loadRedirectUrlByResponse($responseData);

        if (
            $this->_isResponseCodeSuccess($responseData)
            && ($leadId = Arr::get($responseData, 'leadId'))
        ) {
            TSInit::$app->session->set(static::PASSWORD_RECOVERY_FLASH_KEY, $leadId);
            $this->_redirectUrl = Link::getPasswordRecoveryPage();
        }
    }

    /**
     * Check is response code success
     *
     * @param array $responseData
     *
     * @return bool
     */
    private function _isResponseCodeSuccess(array $responseData)
    {
        return is_array($responseData)
               && isset($responseData['returnCode'])
               && $responseData['returnCode'] == Interlayer_Crm::RESPONSE_CODE_SUCCESS;
    }

    /**
     * Check is response code in list of responses with redirect url - so try to get redirect url it from response param
     * else - use home page as redirect url
     * Also we don't need to add any additional _GET params to this url.
     * If we get this url - it means that some thing not well in lead's flow and clearing of additional params is OK
     *
     * @param array $responseData
     *
     * @return void
     */
    private function _loadRedirectUrlByResponse(array $responseData)
    {
        $redirectFromApi = '';
        if (!is_array($responseData)) {
            $responseData = [];
        }
        $responseCode = (int) Arr::get($responseData, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);
        if (in_array($responseCode, static::RESPONSE_CODES_WITH_POSSIBLE_REDIRECTS)) {

            $redirectFromApi = Arr::get($responseData, 'leadInfo.redirectTo', '');
        }

        $this->_redirectUrl =  $redirectFromApi ?: TSInit::$app->request->getHomeUrl();
    }

    /**
     * Get parameter value from data array
     *
     * @param string $name
     * @param null   $default
     *
     * @return mixed|null
     */
    private function _getParam($name, $default = null)
    {
        return Arr::get($this->_data, $name, $default);
    }
}