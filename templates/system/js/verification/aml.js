(function($) {
    
    $(document).ready(function() {
        var skipField = {
            $control: $(".js-skip-field-control"),
            $fieldToSkip: $(".js-field-to-skip"),
            $alternative: $(".js-skipped-field-alternative"),
            $requiredCheckboxes: $(".js-required-field"),
            $submitButton: $(".form-submit-button"),
            
            init: function () {
                var that = this;
                
                that.display();
                that.$control.change(function (event) {
                    event.preventDefault();
                    that.display();
                });
                that.$alternative.find('.textarea-show').hide();
                that.checkedRadio(that.$alternative);
                
                that.requiredCheckboxHandler(that.$requiredCheckboxes);
                that.$requiredCheckboxes.change(function () {
                    that.requiredCheckboxHandler(that.$requiredCheckboxes);
                });
            },
            display: function () {
                var that = this;
                
                if (that.$control.is(':checked')) {
                    that.$fieldToSkip.hide();
                    that.$alternative.show();
                    that.radioButtonHandler(that.$alternative);
                } else {
                    that.$fieldToSkip.show();
                    that.$alternative.hide();
                }
            },
            radioButtonHandler: function (elem) {
                var elemAlternative = elem.find("input[type='radio']");
                
                elemAlternative.each(function() {
                    $(this).on("click", function(){
                        var positionNumber = $(this).val();
                        elem.find('.textarea-show').hide();
                        $('.field-aml-verification-tinmissingreasoncomment-' + positionNumber).show();
                    });
                });
                
            },
            checkedRadio: function (el) {
                var elemAlternative = el.find("input[type='radio']");
                
                elemAlternative.each(function() {
                    var positionNumber = $(this).attr('checked') ? $(this).val() : null;
                    if(positionNumber !== null) {
                        $('.field-aml-verification-tinmissingreasoncomment-' + positionNumber).show();
                    }
                })
            },
            requiredCheckboxHandler: function (el) {
                var allChecked = true;
                el.each(function() {
                    if (!$(this).prop('checked')) {
                        return allChecked = false;
                    }
                });
                
                this.$submitButton.prop('disabled', !allChecked);
            }
        };
        
        skipField.init();
    });
})(window.jQuery);
