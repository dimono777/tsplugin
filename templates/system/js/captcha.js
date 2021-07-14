var captcha;

(function($) {
    $(document).ready(function() {
        if (!$('#capScript').length) {
            jQuery('body').append(
                '<script id="capScript" src="https://www.google.com/recaptcha/api.js?hl='
                + GLOBAL.language + '"></script>');
        }
        
        captcha = new Captcha();
        captcha.init();
    });
})(window.jQuery);

function Captcha()
{
    var callBackButton = jQuery('#' + captchaButton);

    this.init = function() {
        this.callBackButtonOff();
    };

    this.callBack = function()
    {
        var v = grecaptcha.getResponse();
        if (v.length == 0) {
            this.callBackButtonOff();
            return false;
        } else {
            this.callBackButtonOn();
            return true;
        }
    };

    this.expiredCallBack = function()
    {
        this.callBackButtonOff();
        return true;
    };

    this.callBackButtonOn = function ()
    {
        callBackButton.removeAttr('disabled');
        return true;
    };

    this.callBackButtonOff = function ()
    {
        callBackButton.attr('disabled', 'disabled');
        return true;
    };
}

function captchaCallBack() {

    captcha.callBack();
}

function captchaExpiredCallBack() {
    captcha.expiredCallBack();
}