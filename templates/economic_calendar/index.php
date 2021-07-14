<?php
/**
 * @var $calendarData string
 * @var $daysList string
 */

$economicCalendarData = TSInit::$app->getVar('economicCalendarData');
extract($economicCalendarData);
?>
<script>
    var calendarData = <?php echo json_encode($calendarData); ?>;
    var daysListData = <?php echo json_encode($daysList); ?>;
</script>

<div class="container" id="app">
    <economic-calendar></economic-calendar>
</div>

<script type="text/x-template" id="economic-calendar-template">
    <section id="economic-calendar" class="section-calendar">
        <ul class="days-list">
            <li :class="{'active': !selectedDay}"><a @click="selectedDay = ''" title=""><?php echo \TS_Functions::__('ALL'); ?></a></li>

            <li v-for="day in daysList" :class="{'active': selectedDay == day}"><a @click="selectedDay = day">{{ day }}</a></li>

            <li class="settings-icon">
                <a title="" @click="additionalFiltersActive = !additionalFiltersActive">
                    <img src="<?php echo \tradersoft\helpers\Assets::findUrl('/images/settings.png', 'system'); ?>" alt="" />
                </a>
            </li>
        </ul>

        <transition name="fade-body">
            <div class="calendar-filter" v-show="additionalFiltersActive">
                <div class="top-part" >

                    <!-- time filter -->
                    <div class="time-block">
                        <div class="timer">{{ timeByTimeZone }}</div>
                        <div class="country">{{ selectedTimeZone.name }}</div>
                        <span class="icon-expand" @click="showTimeZoneList = !showTimeZoneList">
                            <svg viewBox="0 0 24 24" fill="#fad11d" preserveAspectRatio="xMidYMid meet"><g><path d="M7 10l5 5 5-5z"></path></g></svg>
                        </span>

                        <transition name="fade-body">
                            <div class="select" v-show="showTimeZoneList">
                                <div class="item" v-for="timezone in timezoneList" @click="selectTimeZone(timezone)">
                                    <span>{{ timezone.offsetLabel }} {{ timezone.offset }}</span> <span>{{ timezone.name }}</span>
                                </div>
                            </div>
                        </transition>
                    </div>

                    <!-- stars filter -->
                    <a :class="['star', {'inactive': !starsList[3].isActive }]" @click="toggleStarActivity(3)">
                        <svg viewBox="0 0 24 24" fill="#fad11d" preserveAspectRatio="xMidYMid meet">
                            <g>
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path>
                            </g>
                        </svg>
                    </a>
                    <a :class="['star', {'inactive': !starsList[2].isActive }]" @click="toggleStarActivity(2)">
                        <svg viewBox="0 0 24 24" fill="#fad11d" preserveAspectRatio="xMidYMid meet"><g>
                                <path d="M22 9.74l-7.19-.62L12 2.5 9.19 9.13 2 9.74l5.46 4.73-1.64 7.03L12 17.77l6.18 3.73-1.63-7.03L22 9.74zM12 15.9V6.6l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.9z"></path></g>
                        </svg>
                    </a>
                    <a :class="['star', {'inactive': !starsList[1].isActive }]" @click="toggleStarActivity(1)">
                        <svg viewBox="0 0 24 24" fill="#fad11d" preserveAspectRatio="xMidYMid meet"><g>
                                <path d="M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z"></path></g>
                        </svg>
                    </a>
                </div>

                <!-- currency filter-->
                <div class="btm-part">
                    <div id="currencyFlags" class="style-scope mte-economic-calendar">
                        <div class="filter-countries">
                            <div class="item" v-for="filterItem in currencyList">
                                <div class="checkbox" >
                                    <input type="checkbox" :id="filterItem" :value="filterItem" v-model="currencyActive"/>
                                    <label :for="filterItem"></label>
                                </div>

                                <span :class="['flag-icon', 'flag-icon-' + filterItem.toLowerCase()]"></span>
                                <div class="currency-name">{{ filterItem }}</div>
                            </div>
                        </div>
                        <div class="switch">
                            <input id="toggle-1" class="toggle-button" type="checkbox" v-model="currencyFiltersActive">
                            <label for="toggle-1"></label>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <div>
            <!--  start calendar data-->
            <div class="row-ecalendar row-ecalendar-head">
                <div class="col-ecalendar col-date"><?php echo \TS_Functions::__('Date'); ?></div>
                <div class="col-ecalendar col-flag-currency"><?php echo \TS_Functions::__('Currency'); ?></div>
                <div class="col-ecalendar col-event"><?php echo \TS_Functions::__('Message'); ?></div>
                <div class="col-ecalendar col-importace"><?php echo \TS_Functions::__('Impact'); ?></div>
                <div class="col-ecalendar col-actual"><?php echo \TS_Functions::__('Actual'); ?></div>
                <div class="col-ecalendar col-forcast"><?php echo \TS_Functions::__('Forecast'); ?></div>
                <div class="col-ecalendar col-previous"><?php echo \TS_Functions::__('Previous'); ?></div>
            </div>

            <economic-calendar-item v-for="item in computedList" :data="item" class="list-group-item" :key="item"></economic-calendar-item>
        </div>

    </section>
</script>

<script type="text/x-template" id="economic-calendar-item-template">
    <div>

        <div class="row-ecalendar row-ecalendar-body" @click="infoActive = !infoActive">
            <div class="col-ecalendar col-date">{{ data.date }}</div>
            <div class="col-ecalendar col-currency">
                <div :class="['flag-icon', 'flag-icon-' + data.currency.toLowerCase()]"></div>
                <span>{{ data.currency }}</span>
            </div>
            <div class="col-ecalendar col-event">{{ data.msg }}</div>

            <!-- full star -->
            <div class="col-ecalendar col-importace" v-if="data.impact == 3">
                <svg viewBox="0 0 24 24" fill="#fad11d" preserveAspectRatio="xMidYMid meet"><g>
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></g>
                </svg>
            </div>

            <!-- 0.5 star -->
            <div class="col-ecalendar col-importace" v-if="data.impact == 2">
                <svg viewBox="0 0 24 24" fill="#fad11d" preserveAspectRatio="xMidYMid meet"><g>
                        <path d="M22 9.74l-7.19-.62L12 2.5 9.19 9.13 2 9.74l5.46 4.73-1.64 7.03L12 17.77l6.18 3.73-1.63-7.03L22 9.74zM12 15.9V6.6l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.9z"></path></g>
                </svg>
            </div>

            <!-- empty star -->
            <div class="col-ecalendar col-importace" v-if="data.impact == 1">
                <svg viewBox="0 0 24 24" fill="#fad11d" preserveAspectRatio="xMidYMid meet"><g>
                        <path d="M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z"></path></g>
                </svg>
            </div>

            <div class="col-ecalendar col-actual">{{ data.actual }}</div>
            <div class="col-ecalendar col-forcast">{{ data.forcast }}</div>
            <div class="col-ecalendar col-previous">{{ data.previous }}</div>
        </div>

        <transition name="fade-body">
            <div class="collapse-content" v-if="infoActive">
                <div class="col-detail-results">
                    <div class="col-detail-arrow" v-if="up">
                        <svg viewBox="0 0 24 24" fill="#008000" preserveAspectRatio="xMidYMid meet">
                            <g><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"></path></g>
                        </svg>
                    </div>

                    <div class="col-detail-arrow" v-if="down">
                        <svg viewBox="0 0 24 24" fill="#ff0000" preserveAspectRatio="xMidYMid meet">
                            <g><path d="M16 18l2.29-2.29-4.88-4.88-4 4L2 7.41 3.41 6l6 6 4-4 6.3 6.29L22 12v6z"></path></g>
                        </svg>
                    </div>

                    <div class="row-ecalendar">
                        <div class="col-ecalendar">
                            <div class="col-detail-head"><?php echo \TS_Functions::__('Actual'); ?></div>
                            <div class="col-detail-result" v-if="data.actual">
                                <span :class="{ 'detail-actual-up': up, 'detail-actual-down': down }">{{ data.actual }}</span>
                            </div>
                        </div>
                        <div class="col-ecalendar">
                            <div class="col-detail-head"><?php echo \TS_Functions::__('Forecast'); ?></div>
                            <div class="col-detail-result">{{ data.forcast }}</div>
                        </div>
                        <div class="col-ecalendar">
                            <div class="col-detail-head"><?php echo \TS_Functions::__('Previous'); ?></div>
                            <div class="col-detail-result">{{ data.previous }}</div>
                        </div>
                    </div>
                    <a class="btn btn-default btn-small" href="<?php echo \tradersoft\helpers\Platform::getURL(); ?>"><?php echo \TS_Functions::__('Trade'); ?></a>
                </div>
                <div class="col-detail-details">
                    <div class="row-ecalendar">
                        <div class="col-ecalendar">
                            <div class="col-detail-head"><?php echo \TS_Functions::__('Measures'); ?></div>
                            <div class="col-detail-detail">{{ data.measures }}</div>
                        </div>
                        <div class="col-ecalendar">
                            <div class="col-detail-head"><?php echo \TS_Functions::__('Usual effect'); ?></div>
                            <div class="col-detail-detail">{{ data.usualeffect }}</div>
                        </div>
                        <div class="col-ecalendar">
                            <div class="col-detail-head"><?php echo \TS_Functions::__('Frequency of publication'); ?></div>
                            <div class="col-detail-detail">{{ data.frequency }}</div>
                        </div>
                        <div class="col-ecalendar">
                            <div class="col-detail-head"><?php echo \TS_Functions::__('Note'); ?></div>
                            <div class="col-detail-detail">{{ data.notes }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>

</script>
