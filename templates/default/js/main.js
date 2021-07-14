(function($) {
  $(document).ready(function() {
    $('#auth_form #auth_submit').click(function() {
      var email = $('#auth_email').val();
      var password = $('#auth_password').val();
      
      $('#auth_success_msg').html('');
      $('#auth_error_msg').html('');
      
      $.post('/', {
        tradersoft_submit: 'authorization',
        email: email,
        password: password,
        ajax: '1'
      }, function(data) {
        var response = $.parseJSON(data);
        var view_response;
        if (typeof response.code !== 'undefined') {
          if (response.code == 1) {
            if (typeof response.redirectUrl !== 'undefined') {
              window.location = response.redirectUrl;
              return
            }
            
            view_response = 'Successful';
            $('#auth_success_msg').html(view_response);
            window.location = '/';
          }
          else {
            view_response = response.reason;
            $('#auth_error_msg').html(view_response);
          }
        }
        else {
          view_response = 'Something is wrong. Please try again later';
          $('#auth_error_msg').html(view_response);
        }
      });
    });
  });
})(window.jQuery);