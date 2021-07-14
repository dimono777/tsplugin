<?php

namespace tradersoft\helpers\captcha;

use tradersoft\helpers\TS_Setting;
use tradersoft\inc\ReCaptcha\ReCaptcha;
use tradersoft\inc\ReCaptcha\ReCaptchaResponse;

class InvisibleRecaptcha
{
    const SETTING_NAME_SITE_KEY = 'invisible_recaptcha_site_key';
    const SETTING_NAME_SECRET_KEY = 'invisible_recaptcha_secret_key';

    /**
     * @return string|null
     */
    public function getSecretKey()
    {
        return TS_Setting::get(static::SETTING_NAME_SECRET_KEY);
    }

    /**
     * @return string|null
     */
    public function getSiteKey()
    {
        return TS_Setting::get(static::SETTING_NAME_SITE_KEY);
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return !empty($this->getSiteKey()) && !empty($this->getSecretKey());
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
    public function verifyResponse($remoteIp, $response)
    {
        if (!$this->isAvailable()) {
            return false;
        }

        /** @var ReCaptcha $reCaptcha */
        $reCaptcha = new ReCaptcha($this->getSecretKey());
        /** @var ReCaptchaResponse $verifyResponse */
        $verifyResponse = $reCaptcha->verifyResponse($remoteIp, $response);

        return $verifyResponse != null
            && $verifyResponse->success;
    }
}