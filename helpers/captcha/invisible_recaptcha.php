<?php

namespace tradersoft\helpers\captcha;

use tradersoft\helpers\Assets;
use tradersoft\helpers\Html;
use tradersoft\helpers\TS_Setting;
use tradersoft\model\Media_Queue;

/**
 * Class Invisible_ReCaptcha - to add Google invisible reCAPTCHA
 *
 * @author Alexander Shpak <alexander.shpak@tstechpro.com>
 */
class Invisible_ReCaptcha extends ReCaptcha_Abstract
{
    const siteKeySettingName = 'invisible_recaptcha_site_key';

    const secretKeySettingName = 'invisible_recaptcha_secret_key';

    /**
     * @var array $_htmlOptions
     */
    protected static $_htmlOptions = [
        'id' => 'recaptcha',
        'class' => 'g-recaptcha',
        'data-size' => 'invisible',
    ];

    /**
     * Render html block with reCAPTCHA
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     *
     * @param array $options
     *
     * @return string
     *
     */
    public static function render(array $options = [])
    {

        $captchaBlock = '';
        $reCaptchaSiteKey = TS_Setting::get(static::siteKeySettingName);
        if (static::isEnabled() && $reCaptchaSiteKey) {
            $options = static::_setOptions($options, $reCaptchaSiteKey);
            $captchaBlock .= Html::script(
                '',
                [
                    'src' => Assets::findUrl(
                        '/js/registrationCaptcha.js',
                        'system',
                        ['v' => '201912110657']
                    ),
                ]
            );
            $captchaBlock .= Html::tag('div', '', $options);
        }

        return $captchaBlock;
    }

    /**
     * Merge default and provided options for reCAPTCHA
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     *
     * @param string $siteKey
     * @param array $options
     *
     * @return array
     *
     */
    protected static function _setOptions(array $options, $siteKey)
    {

        $options = array_merge(self::$_htmlOptions, $options);
        $options['data-sitekey'] = $siteKey;
        $options['data-callback'] = 'submitForm';
        $options['data-badge'] = 'inline';

        return $options;
    }
}