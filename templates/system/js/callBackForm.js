function callBackForm() {

    var callBackBlock = jQuery('#callBack'),
        callBackMessage = jQuery('#callBackMessage'),
        callBackButton = jQuery('#callBackButton'),
        callBackSuccessMessageBlock = jQuery('#callBackSuccessMessage'),
        callBackSuccessMessageButton = jQuery('#callBackSuccessMessageButton'),
        reCaptchaEnabled = false;

    /**
     * Get call back block
     * @returns {jQuery|HTMLElement}
     */
    this.getCallBackBlock = function () {
        return callBackBlock;
    };

    /**
     * Get call back message
     * @returns {jQuery|HTMLElement}
     */
    this.getCallBackMessage = function () {
        return callBackMessage;
    };

    /**
     * Get call back button
     * @returns {jQuery|HTMLElement}
     */
    this.getCallBackButton = function () {
        return callBackButton;
    };

    /**
     * Get call back success message block
     * @returns {jQuery|HTMLElement}
     */
    this.getCallBackSuccessMessageBlock = function () {
        return callBackSuccessMessageBlock;
    };

    /**
     * Get call back success message button
     * @returns {jQuery|HTMLElement}
     */
    this.getCallBackSuccessMessageButton = function () {
        return callBackSuccessMessageButton;
    };

    /**
     * Call back init
     */
    this.init = function () {
        if (!this.checkReCaptchaEnabled()) {
            this.callBackButtonOn();
        }
    };

    /**
     * Check reCaptcha enabled
     */
    this.checkReCaptchaEnabled = function () {
        if (jQuery('#reCaptcha').length) {
            reCaptchaEnabled = true;
        }

        return reCaptchaEnabled;
    };

    /**
     * Validate form data before send
     * @returns {{}}
     */
    this.validate = function () {
        var errors = {},
            dataValues = {
                email: this.getCallBackBlock().find('input[name=email]').val(),
                phone_code: this.getCallBackBlock().find('input[name=phone_code]').val(),
                phone: this.getCallBackBlock().find('input[name=phone]').val()
            };

        if (
            !dataValues.email
            || (dataValues.email.indexOf("@") < 1)
        ) {
            errors.email = true;
        }

        if (
            !dataValues.phone_code.length
            || !dataValues.phone.length
        ) {
            errors.phone = true;
        }

        return errors;
    };

    /**
     * Send call back form
     */
    this.send = function () {

        var t = this,
            errors = t.validate();

        t.hideCallBackMessage();
        t.cleanFormErrors();

        if (jQuery.isEmptyObject(errors)) {

            t.callBackButtonOff();

            jQuery.post(
                '',
                this.getDataForSendRequest(),
                function (response) {
                    if (response.ok) {
                        t.showSuccessBlock(response.messages.main);
                    } else {
                        t.showErrors(response.messages);
                    }

                    t.callBackButtonOn();
                    t.reCaptchaReset();
                },
                'json'
            );
        } else {
            t.showErrors(errors);
        }

        return false;
    };

    /**
     * Get data for send request
     * @returns {{}}
     */
    this.getDataForSendRequest = function () {
        var callBackBlock = this.getCallBackBlock();

        return {
            ajax: 1,
            tradersoft_submit: 'call_back',
            name: callBackBlock.find('input[name=fullname]').val(),
            email: callBackBlock.find('input[name=email]').val(),
            phone_code: callBackBlock.find('input[name=phone_code]').val(),
            phone: callBackBlock.find('input[name=phone]').val(),
            country: callBackBlock.find('select[name=country]').val(),
            g_recaptcha_response: callBackBlock.find('textarea[name=g-recaptcha-response]').val()
        }
    };

    /**
     * Show validation errors in form
     * @param errors
     */
    this.showErrors = function(errors) {
        var callBackBlock = this.getCallBackBlock();

        if ('main' in errors) {
            this.showCallbackMessage(errors.main);
        }

        if ('g_recaptcha_response' in errors) {
            jQuery('#reCaptcha').parents('div.form-row').addClass('form-error');
        }

        jQuery.each(errors, function(key, value) {
            callBackBlock.find("input[name='" + key + "']").parents('div.form-row').addClass('form-error');
        });
    };

    /**
     * Show call back message
     * @param message
     */
    this.showCallbackMessage = function (message) {
        this.getCallBackMessage().append('<span>' + message + '</span>');
        this.getCallBackMessage().show();
    };

    /**
     * Hide call back message
     */
    this.hideCallBackMessage = function () {
        this.getCallBackMessage().hide();
        this.getCallBackMessage().empty();
    };

    /**
     * Clean form errors
     */
    this.cleanFormErrors = function () {
        callBackBlock.find('div.form-error').removeClass('form-error');
    };

    /**
     * Show block with success message
     * @param successMessage
     */
    this.showSuccessBlock = function(successMessage) {
        this.setCallBackSuccessMessage(successMessage);
        this.getCallBackBlock().hide();
        this.getCallBackSuccessMessageBlock().show();
    };

    /**
     * Reset form
     * @returns {boolean}
     */
    this.resetForm = function () {
        this.resetFieldsValue();
        this.getCallBackSuccessMessageBlock().hide();
        this.getCallBackBlock().show();
        this.setCallBackSuccessMessage('');
        return false;
    };

    /**
     * Set call back success message
     * @param message
     */
    this.setCallBackSuccessMessage = function (message) {
        this.getCallBackSuccessMessageBlock().find('h3').text(message);
    };

    /**
     * Reset form fields
     * Set default values
     */
    this.resetFieldsValue = function () {
        var callBackBlock = this.getCallBackBlock(),
            fullname = callBackBlock.find('input[name=fullname]'),
            email = callBackBlock.find('input[name=email]'),
            phone_code = callBackBlock.find('input[name=phone_code]'),
            phone = callBackBlock.find('input[name=phone]'),
            country = callBackBlock.find('select[name=country]');

        fullname.val(fullname.data('default'));
        email.val(email.data('default'));
        phone_code.val(phone_code.data('default'));
        phone.val(phone.data('default'));
        country.val(country.data('default'));
    };

    /**
     * Callback function to be executed when the user submits
     * a successful CAPTCHA response.
     * @returns {boolean}
     */
    this.reCaptchaCallBack = function () {
        var v = grecaptcha.getResponse();
        if (v.length == 0) {
            this.callBackButtonOff();
            return false;
        } else {
            this.callBackButtonOn();
            return true;
        }
    };

    /**
     * Callback function to be executed when the recaptcha response
     * expires and the user needs to solve a new CAPTCHA.
     * @returns {boolean}
     */
    this.reCaptchaExpiredCallBack = function () {
        this.callBackButtonOff();
        return true;
    };

    /**
     * Resets the reCAPTCHA widget.
     * @returns {boolean}
     */
    this.reCaptchaReset = function () {
        if (!reCaptchaEnabled) {
            return false;
        }

        grecaptcha.reset();
        this.callBackButtonOff();
        return true;
    };

    /**
     * CallBack button > activate
     * @returns {boolean}
     */
    this.callBackButtonOn = function () {
        this.getCallBackButton().removeAttr('disabled');
        return true;
    };

    /**
     * CallBack button > deactivate
     * @returns {boolean}
     */
    this.callBackButtonOff = function () {
        this.getCallBackButton().attr('disabled', 'disabled');
        return true;
    };
}