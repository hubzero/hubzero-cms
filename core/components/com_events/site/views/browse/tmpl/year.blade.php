{{--
  Year view - shows all events for the current year in a list
  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$yearUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&year=' . $year
);

// Year navigation: determine if prev/next years have events
$thisDate = new \Components\Events\Helpers\EventsDate();
$thisDate->setDate($year, 0, 0);

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
    $prevUrl = \Hubzero\Facades\Route::url(
        'index.php?option=' . $option . '&' . $prevYearDate->toDateURL($task)
    );
    $prevText = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_PREVIOUSYEAR');
    $prevDisabled = false;
} else {
    $prevUrl = 'javascript:void(0);';
    $prevText = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR')
        . ' ' . \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_PREVIOUSYEAR');
    $prevDisabled = true;
}

$thisDatetime->add(new \DateInterval('P1Y'));
if ($thisDatetime <= $lastEventTime) {
    $nextUrl = \Hubzero\Facades\Route::url(
        'index.php?option=' . $option . '&' . $nextYearDate->toDateURL($task)
    );
    $nextText = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NEXTYEAR');
    $nextDisabled = false;
} else {
    $nextUrl = 'javascript:void(0);';
    $nextText = \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR')
        . ' ' . \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NEXTYEAR');
    $nextDisabled = true;
}
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
        {{-- Year navigation --}}
        <div class="card bg-base-100 border border-base-300 mt-10">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <a href="{{ $prevUrl }}"
                        title="{{ $prevText }}"
                        @class(['btn btn-ghost btn-sm', 'btn-disabled opacity-40' => $prevDisabled])>
                        &lsaquo;
                    </a>
                    <span class="font-semibold text-lg">{{ $year }}</span>
                    <a href="{{ $nextUrl }}"
                        title="{{ $nextText }}"
                        @class(['btn btn-ghost btn-sm', 'btn-disabled opacity-40' => $nextDisabled])>
                        &rsaquo;
                    </a>
                </div>
            </div>
        </div>

        {{-- Category filter --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-4">
                <form action="{{ $yearUrl }}" method="get">
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
    @endslot

    {{-- Tab navigation --}}
    {!! $__view->view('_nav')
        ->set('option', $option)->set('task', $task)
        ->set('year', $year)->set('month', $month)->set('day', $day)
        ->set('authorized', $authorized)
        ->loadTemplate() !!}

    @if (count($rows) > 0)
        <ul class="list-none">
            @foreach ($rows as $row)
                {!! $__view->view('item')
                    ->set('option', $option)->set('task', $task)
                    ->set('row', $row)->set('fields', $fields)
                    ->set('categories', $categories)->set('showdate', 1)
                    ->loadTemplate() !!}
            @endforeach
        </ul>
    @else
        <div class="alert">
            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR') }}
            <strong>{{ $year }}</strong>
        </div>
    @endif
</x-page-container>
