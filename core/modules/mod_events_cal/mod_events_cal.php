<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\EventsCal;

use Hubzero\Module\Module;
use Components\Events\Helpers\Html;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;

/**
 * Class for events calendar module
 */
class EventsCal extends Module
{
    /**
     * @var  string
     */
    protected $content;

    /**
     * Display module utput
     *
     * @return  void
     */
    public function display()
    {
        $this->css();

        if ($content = $this->getCacheContent()) {
            echo $content;
            return;
        }

        $this->run();
    }

    /**
     * Gnerate events calendar
     *
     * @return  void
     */
    public function run()
    {
        // Check the events component
        if (file_exists(\Hubzero\Facades\Component::path('com_events') . DS . 'helpers' . DS . 'html.php')) {
            // EventsDate class is autoloaded via ClassLoader
        } else {
            $this->setError(Lang::txt('MOD_EVENTS_LATEST_COMPONENT_REQUIRED'));
            return;
        }

        // Display last month?
        $displayLastMonth = $this->params->get('display_last_month');
        switch ($displayLastMonth) {
            case 'YES_stop':
                $disp_lastMonthDays = abs(intval($this->params->get('display_last_month_days')));
                $disp_lastMonth = 1;
                break;
            case 'YES_stop_events':
                $disp_lastMonthDays = abs(intval($this->params->get('display_last_month_days')));
                $disp_lastMonth = 2;
                break;
            case 'ALWAYS':
                $disp_lastMonthDays = 0;
                $disp_lastMonth = 1;
                break;
            case 'ALWAYS_events':
                $disp_lastMonthDays = 0;
                $disp_lastMonth = 2;
                break;
            case 'NO':
            default:
                $disp_lastMonthDays = 0;
                $disp_lastMonth = 0;
                break;
        }

        // Display next month?
        $displayNextMonth = $this->params->get('display_next_month');
        switch ($displayNextMonth) {
            case 'YES_stop':
                $disp_nextMonthDays = abs(intval($this->params->get('display_next_month_days')));
                $disp_nextMonth = 1;
                break;
            case 'YES_stop_events':
                $disp_nextMonthDays = abs(intval($this->params->get('display_next_month_days')));
                $disp_nextMonth = 2;
                break;
            case 'ALWAYS':
                $disp_nextMonthDays = 0;
                $disp_nextMonth = 1;
                break;
            case 'ALWAYS_events':
                $disp_nextMonthDays = 0;
                $disp_nextMonth = 2;
                break;
            case 'NO':
            default:
                $disp_nextMonthDays = 0;
                $disp_nextMonth = 0;
                break;
        }

        // Get the time in unix format
        $date = new \Hubzero\Utility\Date();
        $timeWithOffset = (int) $date->toLocal('U');

        // Get the start day
        $startday = $this->params->get('start_day');
        if (!defined('_CAL_CONF_STARDAY')) {
            define('_CAL_CONF_STARDAY', $startday);
        }
        //define('_CAL_CONF_DATEFORMAT',1);
        //define('_CAL_CONF_MAILVIEW','YES');
        if ((!$startday) || ($startday > 1)) {
            $startday = 0;
        }

        // An array of the names of the days of the week
        $day_name = [
            Lang::txt('EVENTS_CAL_LANG_SUNDAYSHORT'),
            Lang::txt('EVENTS_CAL_LANG_MONDAYSHORT'),
            Lang::txt('EVENTS_CAL_LANG_TUESDAYSHORT'),
            Lang::txt('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
            Lang::txt('EVENTS_CAL_LANG_THURSDAYSHORT'),
            Lang::txt('EVENTS_CAL_LANG_FRIDAYSHORT'),
            Lang::txt('EVENTS_CAL_LANG_SATURDAYSHORT'),
        ];

        $this->content = '';

        // Display a calendar. Want to show 1,2, or 3 calendars optionally
        // depending upon module parameters. (IE. Last Month, This Month, or Next Month)
        $thisDayOfMonth = date("j", $timeWithOffset);
        $daysLeftInMonth = date("t", $timeWithOffset) - date("j", $timeWithOffset) + 1;

        // Display last month?
        if ($disp_lastMonth && (!$disp_lastMonthDays || $thisDayOfMonth <= $disp_lastMonthDays)) {
            // Build last month calendar
            $lastMonthTime = mktime(0, 0, 0, date("n") - 1, 1, (int) date("Y"));
            $this->content .= $this->calendar(
                $timeWithOffset,
                $startday,
                $lastMonthTime,
                Lang::txt('_CAL_LANG_LAST_MONTH'),
                $day_name,
                $disp_lastMonth == 2,
            );
        }

        // Build this month
        $thisMonthTime = mktime(0, 0, 0, (int) date("n"), 1, (int) date("Y"));
        $this->content .= $this->calendar(
            $timeWithOffset,
            $startday,
            $thisMonthTime,
            Lang::txt('EVENTS_CAL_LANG_THIS_MONTH'),
            $day_name,
        );

        // Display next month?
        if ($disp_nextMonth && (!$disp_nextMonthDays || $daysLeftInMonth <= $disp_nextMonthDays)) {
            // Build next month calendar
            $nextMonthTime = mktime(0, 0, 0, date("n") + 1, 1, (int) date("Y"));
            $this->content .= $this->calendar(
                $timeWithOffset,
                $startday,
                $nextMonthTime,
                Lang::txt('_CAL_LANG_NEXT_MONTH'),
                $day_name,
                $disp_nextMonth == 2,
            );
        }

        require $this->getLayoutPath();
    }

    /**
     * Short description for '_calendar'
     *
     * Long description (if any) ...
     *
     * @param      mixed $timeWithOffset Parameter description (if any) ...
     * @param      int|float $startday Parameter description (if any) ...
     * @param      mixed $time Parameter description (if any) ...
     * @param      mixed $linkString Parameter description (if any) ...
     * @param      array<mixed> &$day_name Parameter description (if any) ...
     * @param      boolean|mixed $monthMustHaveEvent Parameter description (if any) ...
     * @return     string Return description (if any) ...
     */
    private function calendar($timeWithOffset, $startday, $time, $linkString, &$day_name, $monthMustHaveEvent = false)
    {
        $database = App::get('db');

        $cal_year  = date("Y", $time);
        $cal_month = date("m", $time);
        $calmonth  = date("n", $time);
        $to_day    = date("Y-m-d", $timeWithOffset);

        // Start building the table
        $content  = '<table class="mod_events_calendar">' . "\n";
        $content .= ' <caption>' . "\n";
        if ($this->params->get('show_nav_prev_month')) {
            $prevYear = ($cal_month == 1 ? $cal_year - 1 : $cal_year);
            $prevMonth = ($cal_month == 1 ? 12 : $cal_month - 1);
            $prevUrl = Route::url(
                'index.php?option=com_events&year=' . $prevYear . '&month=' . $prevMonth,
            );
            $content .= ' <a class="prev month" href="' . $prevUrl . '">'
                . Html::getMonthName($prevMonth) . '</a>' . "\n";
        }
        $currentUrl = Route::url(
            'index.php?option=com_events&year=' . $cal_year . '&month=' . $cal_month,
        );
        $content .= ' <a class="current month" href="' . $currentUrl . '">'
            . Html::getMonthName($cal_month) . '</a>' . "\n";
        if ($this->params->get('show_nav_next_month')) {
            $nextYear = ($cal_month == 12 ? $cal_year + 1 : $cal_year);
            $nextMonth = ($cal_month == 12 ? 1 : $cal_month + 1);
            $nextUrl = Route::url(
                'index.php?option=com_events&year=' . $nextYear . '&month=' . $nextMonth,
            );
            $content .= ' <a class="next month" href="' . $nextUrl . '">'
                . Html::getMonthName($nextMonth) . '</a>' . "\n";
        }
        $content .= ' </caption>' . "\n";
        $content .= ' <thead>' . "\n";
        $content .= '  <tr>' . "\n";
        // Days name rows
        for ($i = 0; $i < 7; $i++) {
            $content .= '   <th scope="col">' . $day_name[($i + $startday) % 7] . '</th>' . "\n";
        }
        $content .= '  </tr>' . "\n";
        $content .= ' </thead>' . "\n";
        $content .= ' <tbody>' . "\n";
        $content .= '  <tr>' . "\n";

        // Fix to fill in end days out of month correctly
        $dayOfWeek = $startday;
        $start = (date("w", (int) mktime(0, 0, 0, (int) $cal_month, 1, (int) $cal_year)) - $startday + 7) % 7;
        $d = date("t", (int) mktime(0, 0, 0, (int) $cal_month, 0, (int) $cal_year)) - $start + 1;
        $kownt = 0;
        for ($a = $start; $a > 0; $a--) {
            $content .= '   <td class="daylink">&nbsp;</td>' . "\n";
            $dayOfWeek++;
            $kownt++;
        }

        $monthHasEvent = false;
        $lastDayOfMonth = date("t", (int) mktime(0, 0, 0, (int) $cal_month, 1, (int) $cal_year));

        // The month's events in one query (it was one per day), then each day
        // picks the ones that touch it by the same three tests the query made
        $monthStart = "$cal_year-$cal_month-01 00:00:00";
        $monthEnd = "$cal_year-$cal_month-$lastDayOfMonth 23:59:59";
        $sql = "SELECT `#__events`.publish_up, `#__events`.publish_down FROM `#__events`, `#__categories` as b"
            . " WHERE #__events.catid = b.id "
            . " AND ((publish_up >= '$monthStart' AND publish_up <= '$monthEnd')"
            . " OR (publish_down >= '$monthStart' AND publish_down <= '$monthEnd')"
            . " OR (publish_up <= '$monthEnd' AND publish_down >= '$monthStart')) AND state='1'"
            . " AND (#__events.scope IS NULL OR #__events.scope="
            . $database->quote('event') . ")";
        $database->setQuery($sql);
        $monthEvents = (array) $database->loadObjectList();

        $rd = 0;
        for ($d = 1; $d <= $lastDayOfMonth; $d++) {
            $do = ($d < 10) ? "0$d" : "$d";
            $selected_date = "$cal_year-$cal_month-$do";
            $dayStart = "$selected_date 00:00:00";
            $dayEnd = "$selected_date 23:59:59";

            $hasEvents = false;
            foreach ($monthEvents as $event) {
                // a missing date (NULL) passed none of the tests that read it
                $up = $event->publish_up === null ? null : (string) $event->publish_up;
                $down = $event->publish_down === null ? null : (string) $event->publish_down;
                if (
                    ($up !== null && $up >= $dayStart && $up <= $dayEnd)
                    || ($down !== null && $down >= $dayStart && $down <= $dayEnd)
                    || ($up !== null && $down !== null && $up <= $dayStart && $down >= $dayEnd)
                ) {
                    $hasEvents = true;
                    break;
                }
            }
            $class = ($selected_date == $to_day) ? 'todaynoevents' : 'daynoevents';

            // do we have events
            if ($hasEvents) {
                $monthHasEvent = true;
                $class = 'daywithevents';
                if ($selected_date == $to_day) {
                    $class = 'todaywithevents';
                }
            }

            // Only adds link if event scheduled that day
            $content .= '   <td class="' . $class . '">';
            if ($class == 'todaywithevents' || $class == 'daywithevents') {
                $dayUrl = Route::url(
                    'index.php?option=com_events&year=' . $cal_year
                    . '&month=' . $cal_month . '&day=' . $do,
                );
                $content .= '<a class="mod_events_daylink" href="' . $dayUrl . '">' . $d . '</a>';
            } else {
                $content .= "$d";
            }
            $content .= '</td>' . "\n";
            $rd++;

            // Check if Next week row
            if ((1 + $dayOfWeek++) % 7 == $startday) {
                $content .= '  </tr>' . "\n" . '  <tr>' . "\n";
                $rd = ($rd >= 7) ? 0 : $rd;
            }
        }

        // Fill in any blank days for the rest of the row
        for ($d = $rd; $d <= 6; $d++) {
            $content .= '   <td>&nbsp;</td>' . "\n";
        }

        // Finish off the table
        $content .= '  </tr>' . "\n";
        $content .= ' </tbody>' . "\n";
        $content .= '</table>' . "\n";

        // Now check to see if this month needs to have at least 1 event in order to display
        if (!$monthMustHaveEvent || $monthHasEvent) {
            return $content;
        } else {
            return '';
        }
    }
}
