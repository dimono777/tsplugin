<?php

namespace tradersoft\helpers\captcha;

use tradersoft\helpers\TS_Setting;
use tradersoft\inc\ReCaptcha\ReCaptcha;
use tradersoft\inc\ReCaptcha\ReCaptchaResponse;

/**
 * Class ReCaptcha_Abstract - abstract class used to add Google reCAPTCHAs
 *
 * @author Alexander Shpak <alexander.shpak@tstechpro.com>
 */
abstract class ReCaptcha_Abstract
{
    const siteKeySettingName = '';

    const secretKeySettingName = '';

    /**
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return boolean $result
     *
     */
    public static function isEnabled()
    {

        return (TS_Setting::get(static::siteKeySettingName)
                && TS_Setting::get(static::secretKeySettingName));
    }

    /**
     * Verify ReCaptcha response
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param string $response
     * @param string $remoteIp
     *
     * @return boolean $result
     *
     */
    public static function verifyResponse($remoteIp, $response)
    {

        $result = false;
        if (!($recaptchaSecretKey = TS_Setting::get(static::secretKeySettingName))) {
            return $result;
        }
        /** @var ReCaptcha $reCaptcha */
        $reCaptcha = new ReCaptcha($recaptchaSecretKey);
        /** @var ReCaptchaResponse $verifyResponse */
        $verifyResponse = $reCaptcha->verifyResponse(
            $remoteIp,
            $response
        );
        if (
            $verifyResponse != null
            && $verifyResponse->success
        ) {
            $result = true;
        }

        return $result;
    }
}