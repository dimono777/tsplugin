window.callBackLogin = (function ($) {
    return function (response) {
        var $successMsgElem = $('#auth_success_msg');
        var $errorMsgElem   = $('#auth_error_msg');
        var $errorEmail     = $('.field-trader_login-email .error-text-js');
        var $errorPassword  = $('.field-trader_login-password .error-text-js');

        $successMsgElem.html('');
        $errorMsgElem.html('');

        if (typeof response.success !== 'undefined') {
            if (response.success) {
                $successMsgElem.html('Successful');
                window.location = window.location.href;
            } else {
                $errorMsgElem.html(response.message);
                if (typeof response.errors !== 'undefined') {
                    if (typeof response.errors.email !== 'undefined') {
                        $errorEmail.html(response.errors.email);
                    }
                    if (typeof response.errors.password !== 'undefined') {
                        $errorPassword.html(response.errors.password);
                    }
                }
            }
            if (typeof response.redirectUrl !== 'undefined') {
                window.location = response.redirectUrl;
            }
        } else {
            $errorMsgElem.html('Something is wrong. Please try again later');
        }
    };
})(jQuery);