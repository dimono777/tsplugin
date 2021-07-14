<?php

namespace tradersoft\components;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Link;
use tradersoft\helpers\multi_language\Multi_Language;
use tradersoft\helpers\system\Cookie;
use tradersoft\helpers\TS_Setting;
use TS_Functions;
use TSInit;

/**
 * Class allows you to switch to another country type for registration
 *
 * Class DataPolicyRegistration
 */
class DataPolicyRegistration
{
    /* If there is this parameter in URL or Cookie, disallow redirect to another label country domain */
    const IGNORE_REDIRECT_PARAMETER_NAME = 'ignoreR';

    const REGISTRATION_FORM_COUNTRY_TYPE = 'registration_form_country_type';
    const REGISTRATION_FORM_REDIRECT_LINK_DOMAIN = 'registration_form_redirect_link_domain';
    const REGISTRATION_FORM_REDIRECT_LINK_PAGE_ID = 'registration_form_redirect_link_page_id';

    /** If URL contains parameter in $_GET which prohibits redirect user by country ip,
     * save parameter in cookie and redirect user without $_GET parameter
     */
    public static function applyIgnoreRedirectByIp()
    {
        $ignoreRedirectInGetParam = (int)Arr::get($_GET, static::IGNORE_REDIRECT_PARAMETER_NAME, 0);

        if ($ignoreRedirectInGetParam) {
            $destinationUrl = Link::removeGetParamFromUrl(
                static::IGNORE_REDIRECT_PARAMETER_NAME, TS_Functions::getCurrentUrl()
            );

            static::_saveIgnoreRedirectCookie();
            TSInit::$app->request->redirect($destinationUrl);
        }
    }

    /**
     * Return true if exist cookie with name static::IGNORE_REDIRECT_PARAMETER_NAME
     *
     * @return bool
     */
    public static function ignoreRedirectByIp()
    {
        return (bool)Cookie::get(static::IGNORE_REDIRECT_PARAMETER_NAME, false);
    }

    /**
     * Build link for registration in another country type
     *
     * @return string
     */
    public static function getRegistrationAnotherCountryTypeLink()
    {
        $language = Multi_Language::getInstance()->getCurrentLanguage();
        $domain = TS_Setting::get(static::REGISTRATION_FORM_REDIRECT_LINK_DOMAIN);
        $pageId = TS_Setting::get(static::REGISTRATION_FORM_REDIRECT_LINK_PAGE_ID);

        return implode('/', [$domain, $language])
            . '/?page_id=' . $pageId . '&' . static::IGNORE_REDIRECT_PARAMETER_NAME . '=1';
    }

    /**
     * Get country type setting for registration form
     *
     * @return string|null
     */
    public static function getRegistrationFormCountryTypeSetting()
    {
        return TS_Setting::get(static::REGISTRATION_FORM_COUNTRY_TYPE);
    }

    /**
     * Save attribute static::IGNORE_REDIRECT_PARAMETER_NAME in cookie.
     * If is exist this attribute, ignored redirecting user by country ip
     */
    private static function _saveIgnoreRedirectCookie()
    {
        Cookie::set(
            static::IGNORE_REDIRECT_PARAMETER_NAME,
            1,
            0,
            '/',
            TSInit::$app->request->getMainDomain()
        );
    }
}