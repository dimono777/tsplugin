<?php

namespace tradersoft\controllers;

use DateTime, TSInit, TS_Functions;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Form;
use tradersoft\helpers\Link;
use tradersoft\model\Model;

/**
 * Webinar calendar controller
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
class Webinar_Calendar_Controller extends Base_Controller
{
    /** @var int */
    protected $_showMonthsBefore = 3;

    /** @var int */
    protected $_showMonthsAfter = 3;

    /**
     * Preparing the data for displaying the calendar
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     */
    public function actionIndex()
    {
        /** @var DateTime */
        $currentDate = new DateTime(date('Y-m-d'));

        /** @var DateTime */
        $firstDay = new DateTime(
            ($currentDate->format('w')) ? 'last sunday' : 'now'
        );

        /** @var DateTime */
        $lastDay = new DateTime(
            ($currentDate->format('w') != 6) ? 'next saturday' : 'now'
        );

        /** @var array */
        $currentTitles = array_unique(
            [
                \TS_Functions::__($firstDay->format('M')) . ' ' . $firstDay->format('Y'),
                \TS_Functions::__($lastDay->format('M')) . ' ' . $lastDay->format('Y'),
            ]
        );

        /** @var array $monthsForSelect */
        $monthsForSelect = [
            '' => \TS_Functions::__('Choose month'),
        ];

        /** @var array */
        $months = [
            \TS_Functions::__('January'),
            \TS_Functions::__('February'),
            \TS_Functions::__('March'),
            \TS_Functions::__('April'),
            \TS_Functions::__('May'),
            \TS_Functions::__('June'),
            \TS_Functions::__('July'),
            \TS_Functions::__('August'),
            \TS_Functions::__('September'),
            \TS_Functions::__('October'),
            \TS_Functions::__('November'),
            \TS_Functions::__('December'),
        ];

        for ($i = -1 * $this->_showMonthsBefore; $i <= $this->_showMonthsAfter; $i++) {
            /** @var string */
            $dateSearchString = ($i)
                ? (
                    "first day of this month" . (($i > 0) ? "+" : "") . "$i month"
                )
                : "first day of this month";

            /** @var DateTime */
            $date = new DateTime($dateSearchString);

            /** @var array */
            $monthsForSelect[$date->format('Y-m')] = $months[$date->format('m') - 1] . ' ' . $date->format('Y');
        }

        /** @var array */
        $days = [
            \TS_Functions::__('Mon'),
            \TS_Functions::__('Tue'),
            \TS_Functions::__('Wed'),
            \TS_Functions::__('Thu'),
            \TS_Functions::__('Fri'),
            \TS_Functions::__('Sat'),
            \TS_Functions::__('Sun'),
        ];

        $this->_setVar('webinarCalendarData', [
            'currentTitles' => $currentTitles,
            'form' => Form::begin(new Model),
            'monthsForSelect' => $monthsForSelect,
            'registerLink' => Link::getTraderRegistrationLink(),
            'months' => $months,
            'days' => $days,
        ]);
    }

    /**
     * Get webinars by time interval
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * params post ['start', 'end'] in timestamp format
     * return array [array $webinars, bool $loggedIn]
     */
    public function actionGetWebinars()
    {
        if (!TSInit::$app->request->isAjax) {
            $this->_jsonResponse([]);
        }
        
        /** @var int */
        $startTimestamp = (int) Arr::get($_POST, 'start');
        
        /** @var int */
        $endTimestamp = (int) Arr::get($_POST, 'end');

        /** @var array */
        $response = Interlayer_Crm::getWebinarsForTraderByDateRange(
            $startTimestamp,
            $endTimestamp,
            TS_Functions::getCurrentLanguage(),
            TSInit::$app->trader->get('crmId')
        );

        $this->_jsonResponse(
            [
                'webinars' => $this->_groupWebinarsByDay($response),
                'loggedIn' => !TSInit::$app->trader->isGuest,
            ]
        );
    }

    /**
     * Join trader to webinar
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @author Mykhailo Chaban <mykhailo.chaban@tstechpro.com>
     */
    public function actionJoin()
    {
        if (!TSInit::$app->request->isAjax) {
            $this->_jsonResponse(['result' => false, 'message' => '']);
        }

        if (TSInit::$app->trader->isGuest) {
            $this->_jsonResponse(['result' => false, 'message' => 'Not logged in']);
        }

        $webinarId = Arr::get($_POST, 'webinarId', 0);
        if (!$webinarId) {
            $this->_jsonResponse(['result' => false, 'message' => 'Webinar not selected']);
        }

        /** @var array */
        $response = Interlayer_Crm::joinLeadToWebinar(
            TSInit::$app->trader->get('crmId'),
            $webinarId
        );

        if (!$response) {
            $this->_jsonResponse(
                [
                    'result' => false,
                    'message' => '<span class="error">' . __('Something went wrong. Please contact support for further assistance.') . '</span>',
                ]
            );
        }

        $this->_jsonResponse($response);
    }


    /**
     * Return list of webinars grouped by days
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param array $webinarsList
     * @return array
     */
    protected function _groupWebinarsByDay(array $webinarsList)
    {
        $groupedWebinars = [];
        
        foreach ($webinarsList as $key => $webinar) {
            $day = gmdate('Y-m-j', $webinar['start']);
            $groupedWebinars[$day][$key] = $webinar;
        }
        
        return $groupedWebinars;
    }
}