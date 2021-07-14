<?php

namespace tradersoft\helpers\captcha;

use tradersoft\helpers\Assets;
use tradersoft\helpers\TS_Setting;
use tradersoft\helpers\Html;
use tradersoft\model\Media_Queue;

/**
 * Class Captcha - to add Google reCAPTCHA
 *
 * @author Alexander Shpak <alexander.shpak@tstechpro.com>
 */
class ReCaptcha extends ReCaptcha_Abstract
{
    const siteKeySettingName = 'recaptcha_site_key';
    const secretKeySettingName = 'recaptcha_secret_key';

    /**
     * @var array $_htmlOptions
     */
    protected static $_htmlOptions = [
        'id' => 'captcha',
        'class' => 'g-recaptcha',
        'data-callback' => 'captchaCallBack',
        'data-expired-callback' => 'captchaExpiredCallBack',
        'captchaButton' => 'captchaButton'
    ];

    /**
     * Render html block with reCAPTCHA
     *
     * @param array $options
     * @return string
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     */
    public static function render(array $options = [])
    {
        $reCaptchaBlock = '';
        $reCaptchaSiteKey = TS_Setting::get(static::siteKeySettingName);
        if (static::isEnabled() && $reCaptchaSiteKey) {
            $options = static::_setOptions($options, $reCaptchaSiteKey);$reCaptchaBlock = Html::script("var captchaButton='{$options['captchaButton']}';", ['type'=>'text/javascript']);
            $reCaptchaBlock .= Html::script('', ['src' => Assets::findUrl('/js/captcha.js', 'system') . '?v=2']);

            $reCaptchaBlock .= Html::tag('div', '', $options);
        }

        return $reCaptchaBlock;
    }

    /**
     * Merge default and provided options for reCAPTCHA
     *
     * @param array $options
     * @param string $siteKey
     * @return array
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     */
    protected static function _setOptions(array $options, $siteKey)
    {
        $options = array_merge(self::$_htmlOptions, $options);
        $options['data-sitekey'] = $siteKey;

        return $options;
    }
}