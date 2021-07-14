window.callBackCallBack = (function ($) {
    return function (response) {

        var $callBackBlock = $('#callBack');
        var $callBackMessageError = $('#callBackMessage');
        var $callBackMessageSuccess = $('#callBackSuccessMessage');
        var $callBackSuccessButton = $('#callBackSuccessButton');
        var $errorEmail     = $('.field-call_back-email .error-text-js');
        var $errorPhone  = $('.field-call_back-phone .error-text-js');

        $callBackMessageSuccess.hide().find('h3').text('');
        $callBackMessageError.html('');

        $callBackSuccessButton.click(function () {
            $callBackMessageSuccess.hide();
            $callBackBlock.show();
            return false;
        });

        if ($('#captcha').length) {
            grecaptcha.reset();
            $('#captchaButton').attr('disabled', 'disabled');
        }

        if (typeof response.success !== 'undefined') {
            if (response.success) {
                $callBackMessageSuccess.show().find('h3').text(response.message);
                $callBackBlock.hide();
            } else {
                $callBackMessageError.html(response.message);
            }
            if (typeof response.validationErrors !== 'undefined') {
                if (typeof response.validationErrors.email !== 'undefined') {
                    $errorEmail.html(response.validationErrors.email);
                }
                if (typeof response.validationErrors.phone !== 'undefined') {
                    $errorPhone.html(response.validationErrors.phone);
                }
            }
        } else {
            $callBackMessageError.html('Something is wrong. Please try again later');
        }
    };
})(jQuery);