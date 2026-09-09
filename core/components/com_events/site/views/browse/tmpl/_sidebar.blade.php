{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

{{-- Category filter --}}
<div class="card bg-base-100 border border-base-300 mt-10">
    <div class="card-body p-4">
        <form action="{{ $formAction }}" method="get">
            <label class="sr-only" for="event-category">
                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_CATEGORY') }}
            </label>
            <div class="flex gap-2 items-center">
                <select name="category"
                    id="event-category"
                    class="select select-bordered select-sm flex-1">
                    <option value="">
                        {{ \Hubzero\Facades\Lang::txt('EVENTS_ALL_CATEGORIES') }}
                    </option>
                    @if ($categories)
                        @foreach ($categories as $id => $title)
                            <option value="{{ $id }}"
                                @selected($category == $id)>
                                {{ stripslashes($title) }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <button type="submit"
                    class="btn btn-sm btn-primary px-3"
                    style="min-height:2rem;height:2rem">
                    {{ \Hubzero\Facades\Lang::txt('EVENTS_GO') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Year navigation --}}
@php
$_database = \Hubzero\Facades\App::get('db');
$_database->setQuery(
    "SELECT MIN(publish_up) min, MAX(publish_down) max FROM `#__events`"
    . " WHERE `scope`='event' AND `state`=1 AND `approved`=1"
);
$_eventRange = $_database->loadObjectList();

$_firstEventTime = new \DateTime($_eventRange[0]->min ?? '');
$_lastEventTime = new \DateTime($_eventRange[0]->max ?? '');
$_thisDatetime = new \DateTime($year . '-01-01');

$_thisDate = new \Components\Events\Helpers\EventsDate();
$_thisDate->setDate($year, $month ?? 1, $day ?? 1);
$_prevYearDate = clone $_thisDate;
$_prevYearDate->addMonths(-12);
$_nextYearDate = clone $_thisDate;
$_nextYearDate->addMonths(+12);

if ($_thisDatetime > $_firstEventTime) {
    $_prevYearUrl = \Hubzero\Facades\Route::url(
        'index.php?option=' . $option . '&' . $_prevYearDate->toDateURL($task)
    );
    $_prevYearDisabled = false;
} else {
    $_prevYearUrl = 'javascript:void(0);';
    $_prevYearDisabled = true;
}

$_thisDatetime->add(new \DateInterval('P1Y'));
if ($_thisDatetime <= $_lastEventTime) {
    $_nextYearUrl = \Hubzero\Facades\Route::url(
        'index.php?option=' . $option . '&' . $_nextYearDate->toDateURL($task)
    );
    $_nextYearDisabled = false;
} else {
    $_nextYearUrl = 'javascript:void(0);';
    $_nextYearDisabled = true;
}
@endphp
<div class="card bg-base-100 border border-base-300">
    <div class="card-body p-4">
        <div class="flex items-center justify-between">
            <a href="{{ $_prevYearUrl }}"
                @class(['btn btn-ghost btn-sm', 'btn-disabled opacity-40' => $_prevYearDisabled])>
                &lsaquo;
            </a>
            <span class="font-semibold text-lg">{{ $year }}</span>
            <a href="{{ $_nextYearUrl }}"
                @class(['btn btn-ghost btn-sm', 'btn-disabled opacity-40' => $_nextYearDisabled])>
                &rsaquo;
            </a>
        </div>
    </div>
</div>

{{-- Mini calendar --}}
<div class="card bg-base-100 border border-base-300">
    <div class="card-body p-4">
        {!! $__view->view('calendar')
            ->set('option', $option)
            ->set('task', $task)
            ->set('year', $year)
            ->set('month', $month)
            ->set('day', $day)
            ->set('offset', $offset)
            ->set('shownav', 1)
            ->loadTemplate() !!}
    </div>
</div>
