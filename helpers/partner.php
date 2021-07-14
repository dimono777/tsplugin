<?php
namespace tradersoft\helpers;

/**
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Partner
{
    public static function getPartnerForgotLink()
    {
        return Link::getForPageWithKey('[TS-PARTNERS-FORGOT-PASSWORD]');
    }

    public static function getPartnerAuthLink()
    {
        return Link::getForPageWithKey('[TS-PARTNERS-AUTHORIZATION]');
    }

    /**
     * Function getPartnerRegLink
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return string
     */
    public static function getPartnerRegLink()
    {
        return Link::getForPageWithKey('[TS-PARTNERS-REGISTRATION]');
    }

    public static function getPartnerTermsLink()
    {
        return Link::getForPageWithKey('[TS-PARTNERS-TERMS-AND-CONDITIONS]');
    }

    public static function getPartnerDomain() {
        return TS_Setting::get('partner_domain');
    }

    /**
     * Function getPartnerIsAuthJSLink
     *
     *
     *
     * @return string
     */
    public static function getPartnerIsAuthJSLink()
    {
        return Link::updateProtocol(
            self::makePartnerMultiLanguageUrl('/site/isauth')
        );
    }

    public static function redirectToPartners($url = '')
    {
        \TS_Functions::redirect(self::makePartnerMultiLanguageUrl($url));
        return true;
    }

    /**
     * Function redirectToPartnersJS
     *
     *
     * @param string $url
     *
     * @return bool
     */
    public static function redirectToPartnersJS($url = '')
    {
        \TS_Functions::redirectJS(
            Link::updateProtocol(
                self::makePartnerMultiLanguageUrl($url)
            )
        );
        return true;
    }

    public static function makePartnerMultiLanguageUrl($url)
    {
        $partnerLanguages = Config::get('partner_languages', []);
        if (empty($partnerLanguages)) {
            return self::getPartnerDomain(). $url;
        }

        $defaultLanguage = Arr::get($partnerLanguages, 'default', '');
        $lang = Arr::get($partnerLanguages, \TS_Functions::getCurrentLanguage(), $defaultLanguage);
        return self::getPartnerDomain() . "/$lang" . $url;
    }
}