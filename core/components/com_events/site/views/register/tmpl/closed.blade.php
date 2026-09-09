{{--
  Registration closed message for event
  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$yearUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&year=' . $year
);
$monthUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&year=' . $year . '&month=' . $month
);
$weekUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&year=' . $year . '&month=' . $month
    . '&day=' . $day . '&task=week'
);
$dayUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&year=' . $year . '&month=' . $month
    . '&day=' . $day
);
$detailsUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&task=details&id=' . $event->id
);
$registerUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&task=details&id=' . $event->id . '&page=register'
);

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

$subTabs = [
    $detailsUrl => \Hubzero\Facades\Lang::txt('EVENTS_OVERVIEW'),
];
if ($pages) {
    foreach ($pages as $p) {
        $pUrl = \Hubzero\Facades\Route::url(
            'index.php?option=' . $option . '&task=details&id=' . $event->id
            . '&page=' . $p->alias
        );
        $subTabs[$pUrl] = trim(stripslashes($p->title));
    }
}
$subTabs[$registerUrl] = \Hubzero\Facades\Lang::txt('EVENTS_REGISTER');
$activeSubTab = $page->alias == '' ? $detailsUrl : ($page->alias == 'register'
    ? $registerUrl
    : \Hubzero\Facades\Route::url(
        'index.php?option=' . $option . '&task=details&id=' . $event->id
        . '&page=' . $page->alias
    ));
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

    {{-- Period navigation tabs --}}
    <x-filter-tabs :options="$periodTabs" :active="$activePeriod" class="mb-6" />

    <h3 class="text-xl font-semibold mb-4">{{ stripslashes($event->title) }}</h3>

    {{-- Sub-tabs: Overview / Pages / Register --}}
    <x-filter-tabs :options="$subTabs" :active="$activeSubTab" class="mb-6" />

    @if ($__view->getError())
        <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
    @endif

    <div class="alert alert-warning">
        {{ \Hubzero\Facades\Lang::txt('EVENTS_CLOSED_REGISTRATION') }}
    </div>
</x-page-container>
