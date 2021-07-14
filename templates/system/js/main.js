jQuery(document).ready(function() {

    reCaptchaConnectJSFile();

    callBackForm = new callBackForm();
    callBackForm.init();

    callBackForm.getCallBackButton().click(function () {
        return callBackForm.send();
    });

    /*callBackForm.getCallBackSuccessMessageButton().click(function () {
        return callBackForm.resetForm();
    });*/

    jQuery('#auth_form #auth_submit').click(function() {
         var email    = jQuery('#auth_email').val();
         var password = jQuery('#auth_password').val();

         jQuery('#auth_success_msg').html('');
         jQuery('#auth_error_msg').html('');

         jQuery.post('/', {tradersoft_submit: 'authorization', email: email, password: password, ajax: '1'}, function(data) {
             var response = jQuery.parseJSON(data);
             var view_response;
             if (typeof response.code !== 'undefined') 
             {
                 if (response.code == 1)
                 {
                     if (typeof response.redirectUrl !== 'undefined') {
                         window.location = response.redirectUrl;
                         return
                     }

                     view_response = 'Successful';
                     jQuery('#auth_success_msg').html(view_response);
                     
                     var hostname = window.location.hostname.split('.');
                     var main_domain = hostname[hostname.length-2]+'.'+hostname[hostname.length-1];
    
                     if (main_domain.indexOf('.local') > 0) 
                     {
                         window.location = window.location.href;
                     }
                     else 
                     {
                         //window.location = '//trade.' + main_domain;
                         window.location = window.location.href;
                     }
                 }
                 else 
                 {
                     view_response = response.reason;
                     jQuery('#auth_error_msg').html(view_response);
                 }
             }
             else 
             {
                 view_response = 'Something is wrong. Please try again later';
                 jQuery('#auth_error_msg').html(view_response);
             }
         });
    });
});

function reCaptchaConnectJSFile() {
    if (jQuery('#reCaptcha').length && !jQuery('#capScript').length) {
        jQuery('body').append('<script id="capScript" src="https://www.google.com/recaptcha/api.js?hl=' + GLOBAL.language + '"></script>');
    }
}

function reCaptchaCallBack() {
    callBackForm.reCaptchaCallBack();
}

function reCaptchaExpiredCallBack() {
    callBackForm.reCaptchaExpiredCallBack();
}