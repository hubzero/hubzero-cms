{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$database = \Hubzero\Facades\App::get('db');

$startday = ((!defined('_CAL_CONF_STARDAY') || !_CAL_CONF_STARDAY) || (_CAL_CONF_STARDAY > 1)) ? 0 : _CAL_CONF_STARDAY;

$date = new \Hubzero\Utility\Date('now', \Hubzero\Facades\Config::get('offset'));
$timeWithOffset = $date->toLocal('U');
$to_day = date("Y-m-d", $timeWithOffset);

$day_name = [
    \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_SUNDAYSHORT'),
    \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_MONDAYSHORT'),
    \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_TUESDAYSHORT'),
    \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
    \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_THURSDAYSHORT'),
    \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_FRIDAYSHORT'),
    \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_SATURDAYSHORT'),
];

$time  = mktime(0, 0, 0, intval($month), 1, intval($year));
$ptime = mktime(0, 0, 0, ($month - 1), 1, intval($year));
$ntime = mktime(0, 0, 0, ($month + 1), 1, intval($year));

// This month
$cal_year  = date("Y", $time);
$cal_month = date("m", $time);
$calmonth  = date("n", $time);

$this_date = new \Components\Events\Helpers\EventsDate();
$this_date->setDate($year, $month, $day);

$prev_month = clone($this_date);
$prev_month->addMonths(-1);
$next_month = clone($this_date);
$next_month->addMonths(+1);

// Query for event date range
$sql = "SELECT MIN(publish_up) min, MAX(publish_down) max FROM `#__events` as e
    WHERE `scope`='event'
    AND `state`=1
    AND `approved`=1";
$database->setQuery($sql);
$rows = $database->loadObjectList();
$first_event_time = new DateTime($rows[0]->min ? $rows[0]->min : '');
$last_event_time = new DateTime($rows[0]->max ? $rows[0]->max : '');
$this_datetime = new DateTime($year . '-' . $month . '-01');

// Check for events before this month
if ($this_datetime > $first_event_time) {
    $prev = \Hubzero\Facades\Route::url(
        'index.php?option=' . $option . '&' . $prev_month->toDateURL($task)
    );
    $prev_text = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_PREVIOUSMONTH');
} else {
    $prev = "javascript:void(0);";
    $prev_text = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR')
        . ' ' . \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_PREVIOUSMONTH');
}

// Check for events after this month
$this_datetime->add(new DateInterval("P1M"));
if ($this_datetime <= $last_event_time) {
    $next = \Hubzero\Facades\Route::url(
        'index.php?option=' . $option . '&' . $next_month->toDateURL($task)
    );
    $next_text = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NEXTMONTH');
} else {
    $next = "javascript:void(0);";
    $next_text = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR')
        . ' ' . \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NEXTMONTH');
}

$monthName = \Components\Events\Helpers\Html::getMonthName($cal_month);

// Calculate grid layout
$dayOfWeek = $startday;
$start = (date("w", mktime(0, 0, 0, $cal_month, 1, $cal_year)) - $startday + 7) % 7;
$lastDayOfMonth = date("t", mktime(0, 0, 0, $cal_month, 1, $cal_year));

// Pre-query which days have events (one query instead of per-day)
$monthStart = "$cal_year-$cal_month-01 00:00:00";
$monthEnd = "$cal_year-$cal_month-$lastDayOfMonth 23:59:59";
$sql = "SELECT DISTINCT DAY(publish_up) as eday FROM `#__events`
    WHERE `scope`='event' AND `state`=1 AND `approved`=1
    AND `publish_up` <= '$monthEnd'
    AND `publish_down` >= '$monthStart'
    UNION
    SELECT DISTINCT DAY(publish_down) as eday FROM `#__events`
    WHERE `scope`='event' AND `state`=1 AND `approved`=1
    AND `publish_up` <= '$monthEnd'
    AND `publish_down` >= '$monthStart'";

// Per-day query approach (faithful to original logic)
$daysWithEvents = [];
for ($d = 1; $d <= $lastDayOfMonth; $d++) {
    $do = ($d < 10) ? "0$d" : "$d";
    $selected_date = "$cal_year-$cal_month-$do";

    $sql = "SELECT COUNT(*) FROM `#__events` as e
        WHERE `scope`='event'
        AND `state`=1
        AND `approved`=1
        AND ((`publish_up` >= '$selected_date 00:00:00' AND `publish_up` <= '$selected_date 23:59:59')
        OR (`publish_down` >= '$selected_date 00:00:00' AND `publish_down` <= '$selected_date 23:59:59')
        OR (`publish_up` <= '$selected_date 00:00:00' AND `publish_down` >= '$selected_date 23:59:59'))";

    $database->setQuery($sql);
    if ($database->loadResult() > 0) {
        $daysWithEvents[$d] = true;
    }
}
@endphp

<table class="text-center w-full text-sm border-collapse">
	<caption class="pb-2">
		<span class="flex items-center justify-between">
			@if ($shownav)
				<a class="px-1 hover:text-primary"
					href="{{ $prev }}"
					title="{{ $prev_text }}">&lsaquo;</a>
			@else
				<span></span>
			@endif
			<span class="font-semibold">{{ $monthName }}</span>
			@if ($shownav)
				<a class="px-1 hover:text-primary"
					href="{{ $next }}"
					title="{{ $next_text }}">&rsaquo;</a>
			@else
				<span></span>
			@endif
		</span>
	</caption>
	<thead>
		<tr>
			@for ($i = 0; $i < 7; $i++)
				<th scope="col" class="text-xs font-medium p-1">
					{{ $day_name[($i + $startday) % 7] }}
				</th>
			@endfor
		</tr>
	</thead>
	<tbody>
		@php
		$dayOfWeek = $startday;
		$kownt = 0;
		$rd = 0;
		@endphp

		<tr>
			{{-- Leading empty cells --}}
			@for ($a = $start; $a > 0; $a--)
				<td class="p-1 @if($a == $start) text-base-content/50 @endif">&nbsp;</td>
				@php $dayOfWeek++; $kownt++; @endphp
			@endfor

			{{-- Day cells --}}
			@for ($d = 1; $d <= $lastDayOfMonth; $d++)
				@php
				$do = ($d < 10) ? "0$d" : "$d";
				$selected_date = "$cal_year-$cal_month-$do";
				$hasevents = isset($daysWithEvents[$d]);

				$classes = [];
				if ($selected_date == $to_day) {
				    $classes[] = 'bg-primary/10 font-bold';
				}
				if ((($dayOfWeek) % 7 == $startday) || ((1 + $dayOfWeek) % 7 == $startday)) {
				    $classes[] = 'text-base-content/50';
				}
				$cellClass = implode(' ', $classes);
				@endphp

				<td class="p-1 {{ $cellClass }}">
					@if ($hasevents)
						@php
						$dayUrl = \Hubzero\Facades\Route::url(
						    'index.php?option=' . $option
						    . '&year=' . $cal_year
						    . '&month=' . $cal_month
						    . '&day=' . $do
						);
						@endphp
						<a class="text-primary font-semibold hover:underline" href="{{ $dayUrl }}">{{ $d }}</a>
					@else
						{{ $d }}
					@endif
				</td>

				@php
				$rd++;
				// Check if next week row
				if ((1 + $dayOfWeek++) % 7 == $startday) {
				    $rd = ($rd >= 7) ? 0 : $rd;
				@endphp
				</tr><tr>
				@php
				}
				@endphp
			@endfor

			{{-- Trailing empty cells --}}
			@for ($d = $rd; $d <= 6; $d++)
				<td class="p-1 @if($d == 6) text-base-content/50 @endif">&nbsp;</td>
			@endfor
		</tr>
	</tbody>
</table>
