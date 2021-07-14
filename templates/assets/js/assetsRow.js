// callback function for processing data provided by node
var assetsRatesCallback = (function($) {
    return function(c) {
        for (var k in c) {
            var className = (c[k].direction == 1) ? 'up' : 'down';
            var assetSelector = $('.assetId_' + c[k].id);
            assetSelector.children('.assetName').html(c[k].name);
            assetSelector.children('.assetSellRate').html(c[k].sell_rate);
            assetSelector.children('.assetPercent').
                removeClass('up down').
                addClass(className).
                html(c[k].percent + '%');
            
            // ==== OLD CODE ==== Asset Index (left the code because do not know about its usage)
            $('#ass_' + c[k].id + '_sell').html(c[k].sell_rate);
            $('#ass_' + c[k].id + '_buy').html(c[k].buy_rate);
            $('#ass_' + c[k].id + '_change').html(c[k].percent + '%');
            // ==== OLD CODE ====
        }
    };
})(window.jQuery);

(function($) {
    if ($("#tradestrip").length > 0) {
        // init jQuery.marquee plugin
        $("#tradestrip").marquee({
            startVisible: true,
            duration: 70000,
            duplicated: true
        });
    }
})(window.jQuery);