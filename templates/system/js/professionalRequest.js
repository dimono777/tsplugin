(function($) {
    $(document).ready(function () {
        
        $.fn.isEnoughMarkedCheckboxes = function () {
            return this.filter(':checked').length
                   && this.filter(':checked').length >= suitabilityApplicationSettings.minNumberAnsweredQuestions;
        };
        
        var stopSubmit = false;
        
        $('form#professional-request-form')
            .on('change', 'input[type="checkbox"]', function () {
                stopSubmit = false;
                $('#submitButton').prop('disabled', !$('input[type="checkbox"]').isEnoughMarkedCheckboxes());
            })
            .on('submit', function () {
                if (!$('input[type="checkbox"]').isEnoughMarkedCheckboxes()) {
                    return false;
                }
                
                if (stopSubmit) {
                    return false;
                }
                
                stopSubmit = true;
            });
    });
})(window.jQuery);
