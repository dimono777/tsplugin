(function($) {
    $(document).bind('invalidCountriesChanged', function(){
        $('#ts-popuper-main').css('display', 'flex');
        $('#btn-popup-close').on('click', function(){
            $('#ts-popuper-main').hide();
        });
    });
})(window.jQuery);