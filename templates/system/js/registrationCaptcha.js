(function($) {
    $(document).ready(function() {
        if (!$("#capScript").length) {
            var language = (typeof GLOBAL == "undefined"
                            || typeof GLOBAL.language == "undefined")
                           ? "en"
                           : GLOBAL.language;
            $("body").append(
                '<script id="capScript" src="https://www.google.com/recaptcha/api.js?hl='
                + language + '"></script>');
        }
    
        $("#regFormSubmit").click(function(event) {
            // prevent form submit before captcha is completed
            event.preventDefault();

            var $form = $(this).closest('form');

            $form.data('activeForm').form.processValidation();

            if ($form.find(".error-text-js").text().length) {
                // on error - reset google reCaptcha, as it is already submitted
                grecaptcha.reset();
            } else {
                // all OK, process reCaptcha
                grecaptcha.execute();
            }
            
        });
    });
})(window.jQuery);

var submitForm = (function($){
    return function(data) {
        $("#reg_form").submit();
    }
})(window.jQuery);
