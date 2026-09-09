{{--
  Week view - shows events grouped by day of the week
  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$weekUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&year=' . $year
    . '&month=' . $month . '&day=' . $day . '&task=week'
);

// Year navigation
$thisDate = new \Components\Events\Helpers\EventsDate();
$thisDate->setDate($year, $month, 0);

$prevYearDate = clone $thisDate;
$prevYearDate->addMonths(-12);
$nextYearDate = clone $thisDate;
$nextYearDate->addMonths(+12);

$database = \Hubzero\Facades\App::get('db');
$database->setQuery(
    "SELECT MIN(publish_up) min, MAX(publish_down) max FROM `#__events`"
    . " WHERE `scope`='event' AND `state`=1 AND `approved`=1"
);
$eventRange = $database->loadObjectList();

$firstEventTime = new \DateTime($eventRange[0]->min ?? '');
$lastEventTime = new \DateTime($eventRange[0]->max ?? '');
$thisDatetime = new \DateTime($year . '-01-01');

if ($thisDatetime > $firstEventTime) {
    $prevYearUrl = \Hubzero\Facades\Route::url(
        'index.php?option=' . $option . '&' . $prevYearDate->toDateURL($task)
    );
    $prevYearText = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_PREVIOUSYEAR');
    $prevYearDisabled = false;
} else {
    $prevYearUrl = 'javascript:void(0);';
    $prevYearText = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR')
        . ' ' . \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_PREVIOUSYEAR');
    $prevYearDisabled = true;
}

$thisDatetime->add(new \DateInterval('P1Y'));
if ($thisDatetime <= $lastEventTime) {
    $nextYearUrl = \Hubzero\Facades\Route::url(
        'index.php?option=' . $option . '&' . $nextYearDate->toDateURL($task)
    );
    $nextYearText = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NEXTYEAR');
    $nextYearDisabled = false;
} else {
    $nextYearUrl = 'javascript:void(0);';
    $nextYearText = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR')
        . ' ' . \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NEXTYEAR');
    $nextYearDisabled = true;
}

// Week navigation
$weekDate = new \Components\Events\Helpers\EventsDate();
$weekDate->setDate($year, $month, $day);

$prevWeek = clone $weekDate;
$prevWeek->addDays(-7);
$nextWeek = clone $weekDate;
$nextWeek->addDays(+7);

$prevWeekUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&' . $prevWeek->toDateURL($task)
);
$nextWeekUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&' . $nextWeek->toDateURL($task)
);
$prevWeekTxt = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_PREVIOUSWEEK');
$nextWeekTxt = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NEXTWEEK');

$dateFmt = \Hubzero\Facades\Lang::txt('DATE_FORMAT_HZ1');
@endphp

<x-page-container :title="$title">
    @slot('actions')
        @if ($authorized)
            <a class="btn btn-primary btn-sm"
                href="{{ \Hubzero\Facades\Route::url('index.php?option=' . $option . '&task=add') }}">
                {{ \Hubzero\Facades\Lang::txt('EVENTS_ADD_EVENT') }}
            </a>
        @endif
    @endslot

    @slot('sidebar')
        {{-- Week navigation --}}
        <div class="card bg-base-100 border border-base-300 mt-10">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <a href="{{ $prevWeekUrl }}"
                        title="{{ $prevWeekTxt }}"
                        class="btn btn-ghost btn-sm">
                        &lsaquo;
                    </a>
                    <span class="font-semibold text-sm text-center">
                        {{ $startdate }} &ndash; {{ $enddate }}
                    </span>
                    <a href="{{ $nextWeekUrl }}"
                        title="{{ $nextWeekTxt }}"
                        class="btn btn-ghost btn-sm">
                        &rsaquo;
                    </a>
                </div>
            </div>
        </div>

        {{-- Year navigation --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <a href="{{ $prevYearUrl }}"
                        title="{{ $prevYearText }}"
                        @class(['btn btn-ghost btn-sm', 'btn-disabled opacity-40' => $prevYearDisabled])>
                        &lsaquo;
                    </a>
                    <span class="font-semibold text-lg">{{ $year }}</span>
                    <a href="{{ $nextYearUrl }}"
                        title="{{ $nextYearText }}"
                        @class(['btn btn-ghost btn-sm', 'btn-disabled opacity-40' => $nextYearDisabled])>
                        &rsaquo;
                    </a>
                </div>
            </div>
        </div>

        {{-- Category filter --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-4">
                <form action="{{ $weekUrl }}" method="get">
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
                                @foreach ($categories as $id => $catTitle)
                                    <option value="{{ $id }}"
                                        @selected($category == $id)>
                                        {{ stripslashes($catTitle) }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <button type="submit" class="btn btn-xs btn-primary">
                            {{ \Hubzero\Facades\Lang::txt('EVENTS_GO') }}
                        </button>
                    </div>
                </form>
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
    @endslot

    {{-- Tab navigation --}}
    {!! $__view->view('_nav')
        ->set('option', $option)->set('task', $task)
        ->set('year', $year)->set('month', $month)->set('day', $day)
        ->set('authorized', $authorized)
        ->loadTemplate() !!}

    @if (count($rows) > 0)
        @foreach ($rows as $dayData)
            @php
            $isToday = (
                $dayData['week']['month'] == date('m', time() + ($offset * 60 * 60))
                && $dayData['week']['year'] == date('Y', time() + ($offset * 60 * 60))
                && $dayData['week']['day'] == date('d', time() + ($offset * 60 * 60))
            );
            $dateStr = $dayData['week']['year'] . '-'
                . $dayData['week']['month'] . '-'
                . $dayData['week']['day'] . ' 00:00:00';
            @endphp
            <div @class(['card bg-base-100 border border-base-300 mb-3', 'ring-2 ring-primary' => $isToday])>
                <div class="card-body p-4">
                    <h3 class="font-semibold text-sm text-base-content/70 mb-2">
                        {{ \Hubzero\Facades\Date::of($dateStr, date('T'))->toLocal($dateFmt) }}
                        @if ($isToday)
                            <span class="badge badge-primary badge-sm ml-2">
                                Today
                            </span>
                        @endif
                    </h3>
                    @if (count($dayData['events']) > 0)
                        <ul class="list-none">
                            @foreach ($dayData['events'] as $row)
                                {!! $__view->view('item')
                                    ->set('option', $option)->set('task', $task)
                                    ->set('row', $row)->set('fields', $fields)
                                    ->set('categories', $categories)->set('showdate', 0)
                                    ->loadTemplate() !!}
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-base-content/50">
                            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NO_EVENTS') }}
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="alert">
            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR') }}
            <strong>{{ \Components\Events\Helpers\Html::getDateFormat($year, $month, '', 3) }}</strong>
        </div>
    @endif
</x-page-container>
