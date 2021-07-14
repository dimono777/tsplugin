<?php

namespace tradersoft\helpers\captcha;

use Exception;

/**
 * Class Captcha_Abstract - abstract class used to create new captcha instances
 *
 * @author Alexander Shpak <alexander.shpak@tstechpro.com>
 */
abstract class Captcha_Abstract
{
    /**
     * @var array $_captchaTypes
     */
    protected static $_captchaTypes = [
        'recaptcha' => ReCaptcha::class,
        'invisible recaptcha' => Invisible_ReCaptcha::class,
    ];

    /**
     * Create a new captcha instance
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     *
     * @param string $type
     *
     * @return static
     * @throws Exception
     *
     */
    public static function factory($type)
    {

        if (!isset(static::$_captchaTypes[$type])) {
            throw new Exception('There is no such type of captcha');
        }
        $className = static::$_captchaTypes[$type];

        return new $className();
    }
}