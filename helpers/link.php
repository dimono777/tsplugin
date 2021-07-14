<?php
namespace tradersoft\helpers;

use tradersoft\helpers\system\PageKey;
use TSInit;
use TS_Functions;

/**
 * @author Bogdan Medvedev <bogdan.medvedev@tstechpro.com>
 */
class Link
{
    public static function getTraderForgotLink()
    {
        $pageId = Page::getFieldValueByKey(
            PageKey::getPageShortCode(PageKey::KEY_FORMS),
            'ID',
            FormBuilder::prepareShortCodeParams(FormBuilder::TYPE_EMAIL_FOR_PASSWORD_RECOVERY, [])
        );

        if ($pageId) {
            return get_permalink($pageId);
        }

        return self::getForPageWithKey('[TS-FORGOT-PASSWORD]');
    }

    /**
     * @param string $key
     * @param array $params
     * @return mixed|string
     */
    public static function getForPageWithKey($key, array $params = [])
    {
        return get_permalink(Page::getFieldValueByKey($key, 'ID', $params));
    }

    /**
     * @param array $params
     *
     * @return false|string
     * @throws \Exception
     */
    public static function getAmlVerificationPage(array $params = [])
    {
        $pageId = Page::getFieldValueByKey(
            PageKey::getPageShortCode(PageKey::KEY_FORMS),
            'ID',
            FormBuilder::prepareShortCodeParams(FormBuilder::TYPE_AML_VERIFICATION, $params)
        );

        if (is_null($pageId)) {
            $pageId = Page::getFieldValueByKey(
                PageKey::getPageShortCode(PageKey::KEY_AML_VERIFICATION_FORM),
                'ID'
            );
        }

        return get_permalink($pageId);
    }

    /**
     * @param array $params
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getPasswordRecoveryPage(array $params = [])
    {
        $pageId = Page::getFieldValueByKey(
            PageKey::getPageShortCode(PageKey::KEY_FORMS),
            'ID',
            FormBuilder::prepareShortCodeParams(FormBuilder::TYPE_PASSWORD_RECOVERY, $params)
        );

        return get_permalink($pageId);
    }

    /**
     * @param array $params
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getContactUsPage(array $params = [])
    {
        $pageId = Page::getFieldValueByKey(
            PageKey::getPageShortCode(PageKey::KEY_FORMS),
            'ID',
            FormBuilder::prepareShortCodeParams(FormBuilder::TYPE_CONTACT_US, $params)
        );

        if (is_null($pageId)) {
            $pageId = Page::getFieldValueByKey(
                PageKey::getPageShortCode(PageKey::KEY_CONTACT_US),
                'ID'
            );
        }

        return get_permalink($pageId);
    }

    /**
     * Getting professional form page link
     *
     * @return bool|mixed|string
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public static function getTraderProfessionalForm()
    {
        return TSInit::$app->trader->professionalSuitabilityAvailable && TS_Functions::issetLink('[TS-PROFESSIONAL-REQUEST-FORM]')
            ? static::getForPageWithKey('[TS-PROFESSIONAL-REQUEST-FORM]')
            : false;
    }

    /**
     * The current domain exists in the link
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param string $link
     *
     * @return bool
     */
    public static function hasCurrentDomain($link)
    {
        return (wp_parse_url($link, PHP_URL_HOST) == \TS_Functions::getHostName());
    }

    /**
     * Function httpBuildUrl
     * Build url from parts. Parts names are similar to result of function parse_url
     * Almost alias of function http_build_url from PECL pecl_http >= 0.21.0
     * but without additional modes and replacing of getting url
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $parts
     *
     * @return string
     */
    public static function httpBuildUrl(array $parts)
    {
        return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
               ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
               (isset($parts['user']) ? "{$parts['user']}" : '') .
               (isset($parts['user']) && isset($parts['pass']) ? ":{$parts['pass']}" : '') .
               (isset($parts['user']) ? '@' : '') .
               (isset($parts['host']) ? "{$parts['host']}" : '') .
               (isset($parts['port']) ? ":{$parts['port']}" : '') .
               (isset($parts['path']) ? "{$parts['path']}" : '') .
               (isset($parts['query']) ? "?{$parts['query']}" : '') .
               (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
    }

    /**
     * Function sanitizeURI
     *
     * Path URI part throw parse_str and throw http_build_query to decode symbols in _GET params
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string $uri
     *
     * @return string
     */
    public static function sanitizeURI($uri)
    {
        $uriParts = wp_parse_url($uri);

        if (!Arr::get($uriParts, 'query')) {
            return $uri;
        }

        parse_str($uriParts['query'], $urlParams);
        $uriParts['query'] = http_build_query($urlParams);

        $uri = static::httpBuildUrl($uriParts);


        return $uri;
    }

    /**
     * merge $_GET parameters to url
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    public static function addParams($url, array $params)
    {
        if (!$params) {
            return $url;
        }

        $urlParts = explode('#', $url, 2);

        $delimiter = wp_parse_url($urlParts[0], PHP_URL_QUERY) ? '&' : '?';

        $urlParts[0] = implode(
            $delimiter,
            [rtrim($urlParts[0], $delimiter), http_build_query($params)]
        );

        return implode('#', $urlParts);
    }

    /**
     * Function updateProtocol
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $url
     *
     * @return string
     */
    public static function updateProtocol($url)
    {

        $uriParts = wp_parse_url($url);
        $currentProtocol = \TS_Functions::getProtocol();
        if (
            $currentProtocol == $uriParts['scheme']
            || !isset($uriParts['host'])
        ) {
            return $url;
        }

        $uriParts['scheme'] = \TS_Functions::getProtocol();

        return static::httpBuildUrl($uriParts);

    }

    /**
     * Removing get parameter from url string
     *
     * @param mixed $key
     * @param string $url
     *
     * @return string
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public static function removeGetParamFromUrl($key, $url)
    {
        $queryString = parse_url($url, PHP_URL_QUERY);
        if (empty($queryString)) {
            return $url;
        }

        parse_str($queryString, $query);
        if (empty($query[$key])) {
            return $url;
        }

        unset($query[$key]);

        $newQueryString = http_build_query($query);
        $cleanUrl = explode('?', $url);
        $hashPartUrl = explode('#', $url, 2);

        return $cleanUrl[0]
            . (!empty($newQueryString) ? '?' . $newQueryString : '')
            . (!empty($hashPartUrl[1]) ? '#' . $hashPartUrl[1] : '');
    }

    public static function getTraderRegistrationLink()
    {
        $pageId = Page::getFieldValueByKey(
            PageKey::getPageShortCode(PageKey::KEY_FORMS),
            'ID',
            FormBuilder::prepareShortCodeParams(FormBuilder::TYPE_REGISTRATION, [])
        );

        if ($pageId) {
            return get_permalink($pageId);
        }

        return Link::getForPageWithKey("[TS-REGISTRATION]");
    }
}