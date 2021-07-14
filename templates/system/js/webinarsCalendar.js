/**
 *
 * @param settings
 * @constructor
 */
var webinarsCalendar;

var WebinarsCalendar = (function($){
    return function (settings) {
        
        var that = this;
        settings = $.extend(that.defaults, settings || {});
        
        that.days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        that.months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
        
        that.year = settings['year'];
        that.month = settings['month'];
        that.day = settings['day'];
        that.weekDay = settings['weekDay'];
        
        that.webinarsURL = settings['webinarsURL'];
        that.joinURL = settings['joinURL'];
        
        
        that.calendarContainer = $(settings['calendarContainer']);
        that.titleElement = $(settings['calendarContainer'] + ' ' + settings['titleElement']);
        that.weekDays = $(settings['calendarContainer'] + ' ' + settings['weekDays']);
        that.weeksContainer = $(settings['calendarContainer'] + ' ' + settings['weeksContainer']);
        that.eventsContainer = $(settings['calendarContainer'] + ' ' + settings['eventsContainer']);
        that.weekNavigation = $(settings['calendarContainer'] + ' ' + settings['weekNavigation']);
        that.monthNavigation = $(settings['calendarContainer'] + ' ' + settings['monthNavigation']);
        that.monthSelect = $(settings['calendarContainer'] + ' ' + settings['monthSelect']);
        
        that.webinars = settings['webinars'];
        that.isLoading = settings['isLoading'];
        that.loggedIn = settings['loggedIn'];
        
        that.startDay = null;
        that.endDay = null;
        
        that.translations = settings['translations'];
        
        $.each(that.months, function (key) {
            
            if (typeof that.translations.months[key] !== undefined) {
                that.months[key] = that.translations.months[key];
            }
            
        });
        
        $.each(that.days, function (key) {
            
            if (typeof that.translations.days[key] !== undefined) {
                that.days[key] = that.translations.days[key];
            }
            
        });
        
        that.titles = [];
        
        that.init();
        that.initWeekNavigation();
        that.initMonthsNavigation();
        
    }
})(window.jQuery);

(function($) {
    WebinarsCalendar.prototype = {
        
        /** Default settings */
        defaults: {
            year: (new Date).getFullYear(),
            month: (new Date).getMonth(),
            day: (new Date).getDate(),
            weekDay: (new Date).getDay(),
            calendarContainer: '.calendar',
            eventsContainer: '.calendar-day-events',
            titleElement: '.calendar-title',
            weeksContainer: '.calendar-wrap',
            weekDays: '.week-days',
            monthSelect: '.month-select',
            monthNavigation: '.change-month .nav-btn',
            weekNavigation: '.change-week',
            webinarsURL: '/' + GLOBAL.language
                         + '/webinar-calendar/get-webinars/',
            joinURL: '/webinar-calendar/join/',
            isLoading: false,
            loggedIn: false,
            translations: GLOBAL.webinarsTranslations,
            webinars: {}
        },
        
        /** Init general render function that invokes others rendering functions */
        init: function() {
            this.renderCalendar();
            this.renderTitle();
            this.initMonthSelect();
            this.loadWebinars();
        },
        
        /** Render week title ("{monthFullName} {yearFull}") */
        renderTitle: function() {
            this.titleElement.find('.current-month').text(
                this.titles.join(' - ')
            );
            
        },
        
        /** calendar's body render */
        renderCalendar: function() {
            
            this.weekDays.empty();
            
            this.startDay = new Date(
                Date.UTC(
                    this.year,
                    this.month,
                    this.day - (this.weekDay + 6) % 7
                )
            );
            
            this.endDay = new Date(
                Date.UTC(
                    this.year,
                    this.month,
                    this.day - (this.weekDay + 6) % 7 + 7
                )
            );
            
            /** get each day of current week */
            for (var dayOfWeek = 0; dayOfWeek <= 6; dayOfWeek++) {
                
                var dateInWeek = new Date(
                    this.year,
                    this.month,
                    (this.day - ((this.weekDay) ? this.weekDay : 7)) + dayOfWeek
                    + 1
                );
                
                /** build each day of week and add it to calendar. also will start render of webinars list for current choosen day */
                this.weekDays.append(
                    this.buildDayOfWeek(dayOfWeek, dateInWeek));
                
            }
            /** If title array of week is empty */
            if (this.titles.length <= 0) {
                /** add current month name and fuul year to title */
                this.titles.push(this.months[this.month] + ' ' + this.year);
            }
            
        },
        
        /**
         * Get webinars for chosen week and set them in this.webinars which are gouped by months (keys has  format "Y-m-d").
         * Also get trader auth status and set it in this.loggedIn
         * */
        loadWebinars: function() {
            
            var that = this;
            
            that.webinars = {};
            
            that.showLoader(true);
            
            /**
             * @type {{start: number, end: number}} Params for request.
             * Should contain "start" and "end" properties wich contains unix timstamp values.
             * Do not forget - JS use unix microtimstamp, PHP use usual unix timpstmp.
             */
            var params = {
                start: this.startDay.getTime() / 1000 | 0,
                end: this.endDay.getTime() / 1000 | 0
            };
            
            $.post(this.webinarsURL, params, function(data) {
                
                that.showLoader(false);
                
                that.loggedIn = data.loggedIn;
                
                that.webinars = data.webinars;
                
                /** Get date of current coosen day */
                var dateForSearch = new Date(that.year, that.month, that.day);
                
                /** Render webinars list for current day */
                that.renderWebinars(dateForSearch);
                
                that.updateWeekDaysWithWebinarsCount();
                
            }, 'json');
            
        },
        
        updateWeekDaysWithWebinarsCount: function() {
            var that = this;
            $(that.weekDays).find('.day').each(function() {
                var dateStr = $(this).data('date');
                var webinarsCount = Object.keys(
                    (that.webinars[dateStr] || {})).length;
                
                if (webinarsCount) {
                    $(this).append(
                        $('<i>').addClass('webinars-count').text(webinarsCount)
                    );
                }
                else {
                    $(this).addClass('empty');
                }
            });
            
        },
        /**
         * Render webinars list for current day
         * @param currentDate
         * @returns {boolean}
         */
        renderWebinars: function(currentDate) {
            
            var that = this;
            
            /** @type {string} Get string of current date in format "Y-m-d" */
            var currentDateStr = currentDate.getFullYear() + '-'
                                 + that.addZeroBefore(
                                 currentDate.getMonth() + 1) + '-'
                                 + currentDate.getDate();
            
            /** @type {*|Array} Try to get webinars for current day by currentDateStr */
            var webinarsForDay = that.webinars[currentDateStr] || [];
            
            /** Clear exist shown webinars list */
            that.eventsContainer.empty();
            
            if (webinarsForDay.length <= 0) {
                return false;
            }
            
            /** Prepare head of webinars list for showing */
            var webinarsHead = $('<div>').
                addClass('row').
                append(
                    $('<div>').addClass('general-info').text(
                        that.days[(currentDate.getDay() + 6) % 7]
                        + ' '
                        + that.months[currentDate.getMonth()]
                        + ' '
                        + currentDate.getFullYear()
                    )
                ).
                append($('<div>').addClass('start-date').text(
                    that.translations.start)).
                append($('<div>').addClass('end-date').text(
                    that.translations.end)).
                append($('<div>').addClass('actions'));
            
            /** Show head of webinars list */
            that.eventsContainer.append(webinarsHead);
            
            /** get each webinar for current day */
            $.each(webinarsForDay, function(webinarId, webinar) {
                
                var actionButton = $();
                
                if (!webinar.hasOwnProperty('isPast')) {
                    webinar.isPast = false;
                }
                
                /** @type {string} Prepare webinar's classes for  already registrated and past */
                var webinarClass = webinar.alreadyRegistrated
                                   ? ' registrated'
                                   : '';
                webinarClass +=
                    (webinar.isPast) ? ' historical past-event' : '';
                
                /** Create title object */
                var titleElement = $('<h4>').text(webinar.subject);
                
                /** Create message holder object */
                var messageElement = $('<div>').addClass('webinar-message');
                
                /** Create description object */
                var descriptionElement = $('<p>').addClass('webinar-desc').text(
                    webinar.description);
                
                /** Create buttons for upcoming only */
                if (!webinar.isPast) {
                    
                    /** If not logged in -  */
                    if (!that.loggedIn) {
                        
                        /** @type {*|jQuery} Get element of registration link (looks like a button) */
                        actionButton =
                            that.getWebinarButtonTag(webinar, 'notLogged');
                        
                        /** If already registered for trader */
                    }
                    else if (webinar.joinAllowed
                             && webinar.alreadyRegistrated) {
                        
                        /** @type {*|jQuery} Get link for webinar opening (looks like a button) */
                        actionButton =
                            that.getWebinarButtonTag(webinar, 'registered');
                        
                        /** If not registered and need request */
                    }
                    else if (webinar.joinAllowed && !webinar.alreadyRequested) {
                        
                        /** @type {*|jQuery} Get request button */
                        actionButton =
                            that.getWebinarButtonTag(webinar, 'join');
                        
                        /** If not registered and  request has already sent */
                    }
                    else if (webinar.joinAllowed) {
                        
                        /** add message That webinar has been requested already */
                        messageElement.text(that.translations.requestSentText);
                        
                    }
                    
                }
                
                /** @type {*|jQuery} Combine general webinar info data in single element */
                var prepareGeneralInfoBlock = $('<div>').
                    addClass('general-info').
                    append(titleElement).
                    append(messageElement).
                    append(descriptionElement);
                
                /** @type {*|jQuery} Combine  webinar start date data in single element */
                var prepareStartBlock = $('<div>').addClass('start-date').text(
                    that.timestampToDate(webinar.start, true) + ' '
                    + that.translations.UTC);
                
                /** @type {*|jQuery} Combine  webinar end date data in single element */
                var prepareEndBlock = $('<div>').addClass('end-date').text(
                    that.timestampToDate(webinar.end, true) + ' '
                    + that.translations.UTC);
                
                /** @type {*|jQuery} Create Actions block */
                var prepareActionsBlock = $('<div>').addClass('actions');
                
                /** If button/link was gotten - add it to webinar actions block */
                if (actionButton.length) {
                    prepareActionsBlock.append(actionButton);
                }
                
                /** @type {*|jQuery} Combine all webinar data in single element */
                var webinarElement = $('<div>').addClass('row').addClass(
                    'event-item').addClass(webinarClass).append(
                    prepareGeneralInfoBlock).append(prepareStartBlock).append(
                    prepareEndBlock).append(prepareActionsBlock);
                
                /** Show created webinar element */
                that.eventsContainer.append(webinarElement);
            });
            return true;
        },
        
        /**
         * Build Jquery element for current day of week
         *
         * @param {integer} dayNumber
         * @param {Date} dateFull
         * @returns {*|jQuery}
         */
        buildDayOfWeek: function(dayNumber, dateFull) {
            var that = this;
            
            /** @type {*|jQuery} Get day of week name */
            var weekDayElement = $('<span>').addClass('day-name').html(
                this.days[dayNumber]);
            
            /** @type {*|jQuery} Get day of month number */
            var dayNumberElement = $('<i>').html(dateFull.getDate());
            
            var selectedDay = new Date(that.year, that.month, that.day);
            
            var dayClasses = 'day';
            
            if (
                that.year == dateFull.getFullYear()
                && that.month == dateFull.getMonth()
                && that.day == dateFull.getDate()
            ) {
                dayClasses += ' selected';
            }
            
            /**
             * Return element which collect all day's data and has callback function onclick
             */
            return $('<span>').
                addClass(dayClasses).
                data(
                    'date', dateFull.getFullYear() + '-' + that.addZeroBefore(
                            dateFull.getMonth() + 1) + '-'
                            + dateFull.getDate()).
                click(function() {
                    
                    if ($(that.weekDays).find('span.day.selected').length) {
                        $(that.weekDays).find('span.day.selected').removeClass(
                            'selected');
                    }
                    $(this).addClass('selected');
                    
                    that.year = dateFull.getFullYear();
                    that.month = dateFull.getMonth();
                    that.day = dateFull.getDate();
                    that.weekDay = dateFull.getDay();
                    that.renderWebinars(dateFull);
                }).
                append(weekDayElement).
                append(dayNumberElement);
            
        },
        
        /** Init buttons for next and prev weeks choosing */
        initWeekNavigation: function() {
            
            var that = this;
            
            $(that.weekNavigation).click(function() {
                
                /** Stop initiation if loading isn't done yet */
                if (that.isLoading) {
                    return false;
                }
                
                // var direction = $(this).hasClass('prev') ? 0 : 1;
                
                /** TODO: Fix setting of 1st day of week */
                /** Set Current date as a Monday of choosen week */
                
                if ($(this).hasClass('prev')) {
                    that.setDate(
                        that.year, that.month,
                        that.day - (7 + (that.weekDay == 0
                                         ? 7
                                         : that.weekDay) - 1)
                    );
                }
                else {
                    that.setDate(
                        that.year, that.month,
                        that.day + (7 - (that.weekDay == 0
                                         ? 7
                                         : that.weekDay) + 1)
                    );
                }
                // that.setDate(that.year, that.month,  that.day + (direction * (7 - that.weekDay + 1)));
                
                return true;
                
            });
            
        },
        
        /** Init months selector. will choose 1st week of month even if 1st day of month isn't Monday */
        initMonthSelect: function() {
            var that = this;
            /** If month selector will checnged */
            that.monthSelect.change(function() {
                
                /** Stop month selection if loading isn't done yet */
                if (that.isLoading) {
                    return false;
                }
                
                var newMonth = $(this).val();
                if (newMonth) {
                    
                    /** get full year value and month number from value of choosen option and get Date object for 1st day of this month */
                    var dateArray = newMonth.split('-');
                    /** if value contain al less 2 year and month divided by hyphen */
                    if (dateArray.length >= 2) {
                        /**
                         * @type {Date} Get new Date object.
                         * Notice: month number which we will get from select - will start from 1 (Jan - 1, Feb -2 , and etc.)
                         * And JS Date should get mointh numbers which from zero (Jan - 0, Feb - 1 and etc.)
                         */
                        var newMonthStart = new Date(
                            dateArray[0], dateArray[1] - 1, 1);
                        
                        /** TODO: Fix setting of 1st day of week */
                        /** For now it use Monday as a 1st day of a week, and JS use Sunday as a first day(in real as 0 day)
                         * For day value will get last Monday(or itself if it is a Monday)
                         */
                        that.setDate(
                            newMonthStart.getFullYear(),
                            newMonthStart.getMonth(),
                            newMonthStart.getDate()
                        );
                    }
                    
                    /** reset select value for next use */
                    $(this).find('option').removeAttr('selected');
                    $(this).val('');
                }
            });
        },
        
        /** Init buttons for next and prev weeks choosing */
        initMonthsNavigation: function() {
            
            var that = this;
            
            /** TODO: Add month navigation buttons processing */
            $(that.monthNavigation).click(function() {
                that.setDate(
                    that.year,
                    (($(this).hasClass('prev')) ? (that.month - 1) : (that.month
                                                                      + 1)),
                    1
                );
                
            });
            
        },
        
        /**
         * Init Join button
         *
         * @param {*|jQuery} btn
         * @param webinar
         * @returns {boolean}
         */
        initJoinButton: function(btn, webinar) {
            
            var that = this;
            
            /** Stop initiation if loading isn't done yet */
            if (that.isLoading) {
                return false;
            }
            
            var $calendarContainer = $(this.calendarContainer);
            that.showLoader(true, $calendarContainer);
            
            /** @type {{webinarId: *}} Prepare params for Join request. Must contain webinarId */
            var params = {
                webinarId: webinar.webinarId
            };
            
            /** @type {*|jQuery} Find */
            var msgBlock = btn.parents('.row.event-item').find(
                '.webinar-message');
            
            /** @type {string} Get string of current date in format "Y-m-d" (months  should start from 1, not  from 0) */
            // var currentDayString = that.year + '-' + that.addZeroBefore(
            //                        that.month + 1) + '-' + that.day;
            
            $.post(that.joinURL, params, function(data) {
                
                /** If response has "result" and it's 1 */
                if (data.hasOwnProperty('result') && data.result == 1) {
                    
                    /** For webinars which need request */
                    if (webinar.needRequest == true) {
                        
                        /** Set that is already requested, show message about it and remove button */
                        webinar.alreadyRequested = true;
                        msgBlock.text(that.translations.requestSentText);
                        btn.remove();
                        
                        /** If webinar no need to reauest */
                    }
                    else {
                        
                        /** Set as allowed for open */
                        webinar.alreadyRegistrated = true;
                        
                        /** If response contain link */
                        if (data.hasOwnProperty('link')) {
                            /** set gotten link as webinar's property "url" and save webinar in all webinars list too */
                            webinar.url = data.link;
                            // that.webinars[currentDayString][webinar.webinarId] = webinar;
                        }
                        
                        /** Replace exist webinar's button to link for opening */
                        btn.replaceWith(
                            that.getWebinarButtonTag(webinar, 'registered'));

                        msgBlock.text(GLOBAL.webinarsTranslations.joinSuccessfulText);
                    }
                    
                    /** If request is failed or response result hasn't success flag */
                }
                else {
                    
                    /** Show "Request Failed" message and add gotten error message(if exist) */
                    msgBlock.html(
                        that.translations.joinFailedText
                        + (data.hasOwnProperty('message') && data.message)
                        ? '<span> ' + data.message + '</span>'
                        : ''
                    );
                    
                    /** Remove Join/Request button */
                    btn.remove();
                    
                }
                
                that.showLoader(false, $calendarContainer);
            }, 'json');
            
            return false;
            
        },
        
        /**
         * Set year, month and day as current and process rendering
         *
         * @param {string} year
         * @param {string} month
         * @param {string} day
         */
        setDate: function(year, month, day) {
            
            var newDate = new Date(year, month, day);
            
            this.year = newDate.getFullYear();
            this.month = newDate.getMonth();
            this.day = newDate.getDate();
            this.weekDay = newDate.getDay();
            
            /** Clear titles of week array for rerendering it */
            this.titles = [];
            
            /** reInit calendar */
            this.init();
            
        },
        
        /**
         * Convert Unix timestamp to date string in format "Y-m-d H:i". As default will convert in URC
         *
         * @param {integer} timestamp
         * @param {boolean} convertToUTC Convert in local user timezone instead of UTC
         * @returns {string}
         */
        timestampToDate: function(timestamp, convertToUTC) {
            
            var date = new Date(timestamp * 1000);
            
            var year = convertToUTC
                       ? date.getUTCFullYear()
                       : date.getFullYear();
            var month = this.addZeroBefore(
                (convertToUTC ? date.getUTCMonth() : date.getMonth()) + 1
            );
            var day = this.addZeroBefore(
                convertToUTC ? date.getUTCDate() : date.getDate());
            
            var hour = this.addZeroBefore(
                convertToUTC ? date.getUTCHours() : date.getHours());
            var minutes = this.addZeroBefore(
                convertToUTC ? date.getUTCMinutes() : date.getMinutes());
            
            return year + '-' + month + '-' + day + ' ' + hour + ':' + minutes;
            
        },
        
        /**
         *
         *
         * @param webinar
         * @param state
         * @returns {*|jQuery}
         */
        getWebinarButtonTag: function(webinar, state) {
            var that = this;
            /** If need to create link for rigistred webinars */
            if (state == 'registered') {
                
                var link = '#';
                if (webinar.hasOwnProperty('url')) {
                    link = webinar.url;
                }
                
                return $('<a>').
                    attr('href', link).
                    attr('target', '_blank').
                    addClass('green-button open-webinar small-button').
                    text(this.translations.link);
                
                /** If need to create Join button for webinar */
            }
            else if (state == 'join') {
                
                return $('<a>').addClass(
                    'green-button join-now small-button').data(
                    'webinarid', webinar.webinarId).text(
                    this.translations.join).click(function() {
                    that.initJoinButton($(this), webinar);
                    return false;
                });
                
                /** If need to create link for not autorized users */
            }
            else if (state == 'notLogged') {
                
                return $('<a>').
                    attr('href', this.translations.registerLink).
                    addClass('green-button create-account small-button').
                    text(this.translations.register);
                
                /** If some unknown state gotten */
            }
            else {
                return $();
            }
            
        },
        
        /**
         * Enable/disable ajax loader and "isLoading" mode.
         * Week select, month select and Join Button initiating won't be done while this.isLoading == true
         *
         * @param {boolean} doShow
         * @param {*|jQuery} loaderContainer Need for enable only
         */
        showLoader: function(doShow, loaderContainer) {
            
            /** Set isLoading mode value */
            var doShow = this.isLoading = doShow || false;
            
            /** If container wasn't gotten - get all calendar as container */
            if (!loaderContainer) {
                loaderContainer = $(this.calendarContainer);
            }

            var $overlay = loaderContainer.find('.js-overlay');
            var $spinner = loaderContainer.find('.js-spinner');

            if (doShow) {
                
                /** Show loader */
                $overlay.addClass('block-loader block-loader-absolute-position');
                $spinner.addClass('cssload-speeding-wheel');

            }
            else {
                
                /** hide loader */
                $overlay.removeClass('block-loader block-loader-absolute-position');
                $spinner.removeClass('cssload-speeding-wheel');
                
            }
            
        },
        
        /**
         * If gotten string length less then 2 symbols - add zero into begin of this string
         *
         * @param {string} string
         * @returns {string}
         */
        addZeroBefore: function(string) {
            return string.toString().length < 2 ? ('0' + string) : string;
            
        },
        
    };
    
    $(document).ready(function() {
        /**
         * Get Webinar Calendar and initiate it
         *
         * @type {WebinarsCalendar}
         */
        webinarsCalendar = new WebinarsCalendar();
    });
    
})(window.jQuery);

