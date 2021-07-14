/**
 * Created by roman.lazarskiy on 6/23/2017.
 */

Vue.component('economic-calendar-item', {
    template: '#economic-calendar-item-template',
    props: ['data'],

    data: function () {
        return {
            infoActive: false
        }
    },

    computed: {
        up: function() {
            return parseFloat(this.data.actual) > parseFloat(this.data.previous);
        },

        down: function() {
            return parseFloat(this.data.actual) < parseFloat(this.data.previous);
        },
    }
});

Vue.component('economic-calendar', {
    template: '#economic-calendar-template',


    data: function () {
        return {
            data: calendarData,
            daysList: daysListData,
            currencyList: {},

            selectedDay: '',
            currencyActive: [],
            starsList: {
                1: { isActive: true },
                2: { isActive: true },
                3: { isActive: true }
            },

            additionalFiltersActive: true,
            currencyFiltersActive: false,
            showTimeZoneList: false,
            timezoneList: [],
            selectedTimeZone: '',

            timeByTimeZone: moment().format('HH:mm:ss'),
            dateFormat: 'MM/DD HH:mm'
        }
    },

    created: function() {
        this.initTimeZoneList();

        this.initCurrencyList();

        //activate currency filter by default
        this.currencyFiltersActive = true;
    },

    watch: {
        /**
         * currency filters toggle
         */
        currencyFiltersActive: function(val) {
            this.currencyActive = val ? this.currencyList : [];
        },

        selectedTimeZone: function() {
            this.timeByTimeZone = this.getCurrentTime();
        },

    },

    computed: {
        computedList: function () {
            var that = this;

            return this.data.filter(function (item) {
                var haveDay = true;
                // filter by selected day
                if (that.selectedDay) {
                    var itemDay = moment(item.date, that.dateFormat).format('ddd').toUpperCase();
                    haveDay = that.selectedDay.toUpperCase() == itemDay;
                }

                // currency filter
                var haveCurrency = (that.currencyActive.indexOf(item.currency.toUpperCase()) !== -1);

                // stars filter
                var haveStar = that.starsList[parseInt(item.impact.toUpperCase())].isActive;

                return haveCurrency && haveStar && haveDay;
            });
        }
    },

    methods: {

        getCurrentTime: function() {
            return moment().tz(this.selectedTimeZone.name).format('HH:mm:ss');
        },

        selectTimeZone: function(timezone) {
            var oldTimeZone = this.selectedTimeZone.name;

            this.selectedTimeZone = timezone;
            this.showTimeZoneList = false;

            var that = this;

            /**
             * change date for selected timezone
             */
            if (timezone.name !== oldTimeZone) {
                this.data.map(function(element) {
                    var timeMoment = !oldTimeZone
                        ? moment(element.date)
                        : moment.tz(element.date, that.dateFormat, oldTimeZone);

                    element.date = timeMoment.tz(timezone.name).format(that.dateFormat);
                    return element;
                });
            }
        },

        toggleStarActivity: function(starId) {
            var star = this.starsList[starId];
            star.isActive = !star.isActive;
        },

        initTimeZoneList: function() {
            var that = this;
            var userTimeZone = moment.tz.guess();

            //prepare timezone list
            this.timezoneList = moment.tz.names()
                .sort(function(a, b) {
                    return parseInt(moment().tz(a).format('Z')) - parseInt(moment().tz(b).format('Z')) ;
                })
                .map(function(element) {
                    var obj = moment().tz(element);
                    var timeZoneInfo = { name: element, offset: obj.format('Z'), offsetLabel: obj.format('z') };

                    if (userTimeZone == element) {
                        that.selectTimeZone(timeZoneInfo);
                    }

                    return timeZoneInfo;
                });

            // init timer
            window.setInterval(function() {
                that.timeByTimeZone = that.getCurrentTime()
            }, 1000);
        },

        initCurrencyList: function() {
            var allCurrency = this.data.map(function(element){
                return element.currency;
            });

            /** unique only */
            this.currencyList = allCurrency.filter(function(value, index, self) {
                return self.indexOf(value) === index;
            });

            this.currencyList.sort();
        }
    }
});

new Vue({ el: '#app' });