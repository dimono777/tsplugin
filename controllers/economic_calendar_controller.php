<?php
namespace tradersoft\controllers;

use tradersoft\helpers\Interlayer_Crm;

/**
 * Economic calendar controller
 * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Economic_Calendar_Controller extends Base_Controller
{
    public function rules()
    {
        return [];
    }

    /**
     * show economic calendar action
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     */
    public function actionIndex()
    {
        $calendarData = Interlayer_Crm::getEconomicCalendarData();
        $calendarData = isset($calendarData['data']) ? $calendarData['data'] : [];

        $this->_setVar('economicCalendarData', [
            'calendarData' => $calendarData,
            'daysList' => $this->_getDaysList()
        ]);
    }

    /**
     * get days list for economic calendar
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     *
     * @return string
     */
    private function _getDaysList()
    {
        $daysList = ['MON', 'TUE', 'WED', 'THU', 'FRI'];

        return array_map(function($day) { return \TS_Functions::__($day); }, $daysList);
    }
}