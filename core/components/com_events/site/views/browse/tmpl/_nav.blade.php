{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$yearUrl = \Hubzero\Facades\Route::url('index.php?option=' . $option . '&year=' . $year);
$monthUrl = \Hubzero\Facades\Route::url('index.php?option=' . $option . '&year=' . $year . '&month=' . $month);
$weekUrl = \Hubzero\Facades\Route::url('index.php?option=' . $option . '&year=' . $year . '&month=' . $month . '&day=' . $day . '&task=week');
$dayUrl = \Hubzero\Facades\Route::url('index.php?option=' . $option . '&year=' . $year . '&month=' . $month . '&day=' . $day);
$addUrl = \Hubzero\Facades\Route::url('index.php?option=' . $option . '&task=add');

$periodTabs = [
    $yearUrl  => \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REP_YEAR'),
    $monthUrl => \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REP_MONTH'),
    $weekUrl  => \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REP_WEEK'),
    $dayUrl   => \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REP_DAY'),
];
$activePeriod = match($task) {
    'year'  => $yearUrl,
    'week'  => $weekUrl,
    'day'   => $dayUrl,
    default => $monthUrl,
};
@endphp

<div class="flex items-center justify-between mb-4">
    <x-filter-tabs :options="$periodTabs" :active="$activePeriod" />
</div>
