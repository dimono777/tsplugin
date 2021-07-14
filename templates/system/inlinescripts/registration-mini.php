<?php
/** JUST TO DECEIVE IDE  */
if (false) { ?>
<script>
    <?php } ?>
(function ($) {
    $(document).ready(function () {
        $('#registration-mini-country').on('change', function () {
            if ($('option:selected', this).data('invalid') == 1) {
                $('option:selected', this).each(function () {
                    this.selected = false;
                });
                //for invalid countries popup
                $(document).trigger("invalidCountriesChanged");
            }
        });
    });
})(window.jQuery);