<?php

use \tradersoft\helpers\TS_Setting;
use tradersoft\helpers\captcha\ReCaptcha;

if (
    ReCaptcha::isEnabled()
    && ($recaptchaSiteKey = TS_Setting::get(ReCaptcha::siteKeySettingName))
) {
    ?><div id="reCaptcha" class="g-recaptcha" data-sitekey="<?php echo $recaptchaSiteKey; ?>" data-callback="reCaptchaCallBack" data-expired-callback="reCaptchaExpiredCallBack"></div><?php
}

?>