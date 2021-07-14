<?php
namespace tradersoft\helpers;

/**
 * Interlayer partner helper for affiliate project.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Interlayer_Partner extends Interlayer
{
    const RESPONSE_CODE_SUCCESS = 0;
    const RESPONSE_CODE_WRONG_SIGNATURE = 1;
    const RESPONSE_CODE_WRONG_REQUEST_METHOD = 2;
    const RESPONSE_CODE_INVALID_DATA = 3;
    const RESPONSE_CODE_INVALID_AUTH = 4;
    const RESPONSE_CODE_INVALID_REG = 5;
    const RESPONSE_CODE_INVALID_FORGOT_PASS = 6;
    const RESPONSE_CODE_UNSPECIFIED_ERROR = 100;

    public static function auth($params)
    {
        return self::sendRequest('/api/partner-auth/', $params);
    }

    public static function registration($params)
    {
        return self::sendRequest('/api/partner-registration', $params);
    }

    public static function forgotPassword($params)
    {
        return self::sendRequest('/api/partner-forgot-password', $params);
    }

    public static function getLanguages()
    {
        return self::sendRequest('/api/partner-get-languages', []);
    }
}