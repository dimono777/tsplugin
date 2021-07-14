<?php
/** JUST TO DECEIVE IDE  */
if (false) { ?>
<script>
<?php } ?>
(function($) {
  document.addEventListener('DOMContentLoaded', function() {
    listen_assetsrates = function(c) {
        for (var k in c)
        {
            // RSS Feed
            var class_name;
            
            if (c[k].direction == 1)
            {
                class_name = 'up';
            }
            else 
            {
                class_name = 'down';
            }

            $('#asset_id_is_' + c[k].id).html(c[k].sell_rate);
            $('#asset_id_is_' + c[k].id).next('span').attr('class', class_name);
            $('#asset_id_is_' + c[k].id).next('span').html(c[k].percent + '%');

            // Asset Index
            $('#ass_'+ c[k].id +'_sell').html(c[k].sell_rate);
            $('#ass_'+ c[k].id +'_buy').html(c[k].buy_rate);
            $('#ass_'+ c[k].id +'_change').html(c[k].percent + '%');
        }
    };
    // WLConnections('assetsRates', [13, 14, 15, 19, 41, 52, 110, 112, 17, 18, 33, 2, 42, 24, 6, 29, 7, 8, 5, 37, 3552, 103, 3348, 62, 53, 121, 125, 1074, 3, 44, 54, 105, 109], listen_assetsrates);
  });
})(window.jQuery);
