var tooltip = (function($) {
    return  {
        tooltip: $('<div id="tooltip"></div>'),
            targets: '',
        // objects which use the tooltips
        init: function ($targets) {
            this.targets = $targets;
            
            //Elements with tooltip must have "data-tooltip" attribute
            $.each(this.targets, function (index, value) {
                var tooltipData = $(value).attr('data-tooltip');
                if (!tooltipData) {
                    throw 'Target block must have "data-tooltip" attribute.';
                }
            });
            
            var tooltipObj = this;
            this.targets.on('mouseenter', function () {
                tooltipObj.showContent($(this));
                tooltipObj.updatePosition($(this));
            });
            this.targets.on('mouseleave', function () {
                tooltipObj.remove();
            });
            
            tooltipObj.tooltip.on('click', function () {
                tooltipObj.remove();
            });
        },
        
        showContent: function (target) {
            var tooltipContent = target.attr('data-tooltip');
            
            this.tooltip
                .css('opacity', 0)
                .html(tooltipContent)
                .appendTo('body');
        },
        
        remove: function () {
            this.tooltip.animate(
                {opacity: 0},
                0,
                function () {
                    $(this).remove();
                }
            );
        },
        
        //Get position and max-width
        updatePosition: function (target) {
            const   targetEl = target[0].getBoundingClientRect(),
                iconSpace = 10,
                iconWidth = targetEl.width,
                iconHeight = targetEl.height;
            
            let tooltipTransform = 'translateX(-50%) translateY(-100%)',
                iconTop = targetEl.top + window.scrollY - iconSpace,
                iconLeft = targetEl.left + window.scrollX + iconWidth / 2,
                clientWidth = document.documentElement.clientWidth;
            
            this.tooltip.css('transform', '');
            
            if (targetEl.top < iconHeight + this.tooltip.outerHeight()) {
                iconTop = targetEl.top + window.scrollY + iconHeight + iconSpace;
                tooltipTransform = 'translateX(-50%)';
                this.tooltip.addClass('top');
            } else {
                this.tooltip.removeClass('top');
            }
            
            if (targetEl.left + this.tooltip.outerWidth() > clientWidth) {
                tooltipTransform = 'translateX(calc(-100% + 20px)) translateY(-100%)';
                this.tooltip.addClass('right');
            } else {
                this.tooltip.removeClass('right');
            }
            
            if (targetEl.left - this.tooltip.outerWidth() < 0) {
                tooltipTransform = 'translateX(-20px) translateY(-100%)';
                this.tooltip.addClass('left');
            } else {
                this.tooltip.removeClass('left');
            }
            
            if (clientWidth < this.tooltip.outerWidth() * 1.5) {
                this.tooltip.css('max-width', clientWidth / 2);
            } else {
                this.tooltip.css('max-width', 220);
            }
            
            this.tooltip
                .css({left: iconLeft, top: iconTop, transform: tooltipTransform})
                .animate({opacity: 1}, 0);
        }
    }
})(window.jQuery);

tooltip.init(
    window.jQuery('a[data-tooltip]')
);