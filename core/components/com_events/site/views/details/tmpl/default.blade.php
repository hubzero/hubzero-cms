{{--
  Event details page
  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$addUrl = \Hubzero\Facades\Route::url('index.php?option=' . $option . '&task=add');
$yearUrl = \Hubzero\Facades\Route::url('index.php?option=' . $option . '&year=' . $year);
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

<x-page-container :title="$title">
    @slot('actions')
        @if ($auth)
            <a class="btn btn-primary btn-sm" href="{{ $addUrl }}">
                {{ \Hubzero\Facades\Lang::txt('EVENTS_ADD_EVENT') }}
            </a>
        @endif
    @endslot

    {{-- Tab navigation (Year / Month / Week / Day) --}}
    <x-filter-tabs :options="$periodTabs" :active="$activePeriod" class="mb-4" />

    @slot('sidebar')
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-4">
                {!! $__view->view('calendar', 'browse')
                    ->set('option', $option)->set('task', $task)
                    ->set('year', $year)->set('month', $month)
                    ->set('day', $day)->set('offset', $offset)
                    ->set('shownav', 1)->loadTemplate() !!}
            </div>
        </div>
    @endslot

            @if ($row)
                @php
                $detailsUrl = \Hubzero\Facades\Route::url(
                    'index.php?option=' . $option . '&task=details&id=' . $row->id
                );
                $editUrl = \Hubzero\Facades\Route::url(
                    'index.php?option=' . $option . '&task=edit&id=' . $row->id
                );
                $deleteUrl = \Hubzero\Facades\Route::url(
                    'index.php?option=' . $option . '&task=delete&id=' . $row->id
                );
                @endphp

                {{-- Event title with edit/delete links --}}
                <div class="flex items-center gap-3 mb-4">
                    <h3 class="text-2xl font-bold">{{ e(stripslashes($row->title)) }}</h3>
                    @if ($auth && $row->created_by == \Hubzero\Facades\User::get('id'))
                        <a href="{{ $editUrl }}"
                            class="btn btn-ghost btn-xs"
                            title="{{ \Hubzero\Facades\Lang::txt('JACTION_EDIT') }}">
                            {{ strtolower(\Hubzero\Facades\Lang::txt('JACTION_EDIT')) }}
                        </a>
                        <a href="{{ $deleteUrl }}"
                            class="btn btn-ghost btn-xs text-error"
                            title="{{ \Hubzero\Facades\Lang::txt('JACTION_DELETE') }}">
                            {{ strtolower(\Hubzero\Facades\Lang::txt('JACTION_DELETE')) }}
                        </a>
                    @endif
                </div>

                {{-- Sub-tabs: Overview / custom pages / Register --}}
                @php
                $subTabs = [
                    $detailsUrl => \Hubzero\Facades\Lang::txt('EVENTS_OVERVIEW'),
                ];
                if ($pages) {
                    foreach ($pages as $p) {
                        $pUrl = \Hubzero\Facades\Route::url(
                            'index.php?option=' . $option . '&task=details&id=' . $row->id
                            . '&page=' . $p->alias
                        );
                        $subTabs[$pUrl] = trim(stripslashes($p->title));
                    }
                }
                if ($row->registerby && $row->registerby != '0000-00-00 00:00:00'
                    && strtotime($row->registerby) >= time()) {
                    $regTabUrl = \Hubzero\Facades\Route::url(
                        'index.php?option=' . $option . '&task=details&id=' . $row->id
                        . '&page=register'
                    );
                    $subTabs[$regTabUrl] = \Hubzero\Facades\Lang::txt('EVENTS_REGISTER');
                }
                $activeSubTab = $page->alias == '' ? $detailsUrl : \Hubzero\Facades\Route::url(
                    'index.php?option=' . $option . '&task=details&id=' . $row->id
                    . '&page=' . $page->alias
                );
                @endphp
                <x-filter-tabs :options="$subTabs" :active="$activeSubTab" class="mb-4" />

                @if ($page->alias != '')
                    {{-- Custom page content --}}
                    @if (trim($page->pagetext))
                        <div class="prose max-w-none">
                            {!! stripslashes($page->pagetext) !!}
                        </div>
                    @else
                        <div class="alert">
                            {{ \Hubzero\Facades\Lang::txt('EVENTS_NO_INFO_AVAILABLE') }}
                        </div>
                    @endif
                @else
                    {{-- Event details card --}}
                    @php
                    $user = \Hubzero\Facades\User::getInstance($row->created_by);
                    $authorName = is_object($user)
                        ? $user->get('name')
                        : \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_UNKNOWN');

                    $category = isset($categories[$row->catid])
                        ? $categories[$row->catid]
                        : 'N/A';

                    // Date/time formatting
                    $publish_up   = $row->publish_up;
                    $publish_down = $row->publish_down;
                    $upDate   = date('Y-m-d', strtotime($publish_up));
                    $downDate = date('Y-m-d', strtotime($publish_down));
                    @endphp

                    <div class="card bg-base-100 border border-base-300">
                        <div class="card-body p-0">
                            <table class="table">
                                <tbody>
                                    {{-- Category --}}
                                    <tr>
                                        <th class="w-40 font-semibold align-top">
                                            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_CATEGORY') }}:
                                        </th>
                                        <td>{{ stripslashes($category) }}</td>
                                    </tr>

                                    {{-- Description --}}
                                    <tr>
                                        <th class="font-semibold align-top">
                                            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION') }}:
                                        </th>
                                        <td class="prose max-w-none">
                                            {!! html_entity_decode($row->content) !!}
                                        </td>
                                    </tr>

                                    {{-- When --}}
                                    <tr>
                                        <th class="font-semibold align-top">
                                            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_WHEN') }}:
                                        </th>
                                        <td>
                                            @if ($upDate == $downDate)
                                                {{ \Hubzero\Facades\Date::of($publish_up)->format('l d F, Y') }},
                                                {{ \Hubzero\Facades\Date::of($publish_up)->format('g:i a') }}
                                                &ndash;
                                                {{ \Hubzero\Facades\Date::of($publish_down)->format('g:i a') }}
                                                {{ \Hubzero\Facades\Date::of($publish_down, $row->time_zone)->format('T', true) }}
                                            @else
                                                @php
                                                $tz = $row->time_zone;
                                                if (!isset($tz) || $tz == '') {
                                                    $event_timezone = \Hubzero\Facades\Config::get('offset');
                                                    $event_timezone_start = \Hubzero\Facades\Date::of(
                                                        $publish_up, $event_timezone
                                                    )->format('T', true);
                                                    $event_timezone_end = \Hubzero\Facades\Date::of(
                                                        $publish_down, $event_timezone
                                                    )->format('T', true);
                                                } else {
                                                    $event_timezone_start = \Hubzero\Facades\Date::of(
                                                        $publish_up, $tz
                                                    )->format('T', true);
                                                    $event_timezone_end = \Hubzero\Facades\Date::of(
                                                        $publish_down, $tz
                                                    )->format('T', true);
                                                }
                                                @endphp
                                                {{ \Hubzero\Facades\Date::of($publish_up, $row->time_zone)->toLocal('l d F, Y g:i a') }}
                                                {{ $event_timezone_start }}
                                                &ndash;
                                                {{ \Hubzero\Facades\Date::of($publish_down, $row->time_zone)->toLocal('l d F, Y g:i a') }}
                                                {{ $event_timezone_end }}
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Contact --}}
                                    @if (trim($row->contact_info))
                                        <tr>
                                            <th class="font-semibold align-top">
                                                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_CONTACT') }}:
                                            </th>
                                            <td>{{ $row->contact_info }}</td>
                                        </tr>
                                    @endif

                                    {{-- Address --}}
                                    @if (trim($row->adresse_info))
                                        <tr>
                                            <th class="font-semibold align-top">
                                                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_ADRESSE') }}:
                                            </th>
                                            <td>{{ $row->adresse_info }}</td>
                                        </tr>
                                    @endif

                                    {{-- Extra Info URL --}}
                                    @if (trim($row->extra_info))
                                        <tr>
                                            <th class="font-semibold align-top">
                                                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_EXTRA') }}:
                                            </th>
                                            <td>
                                                <a href="{{ e($row->extra_info) }}"
                                                    class="link link-primary">
                                                    {{ e($row->extra_info) }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endif

                                    {{-- Custom fields --}}
                                    @if ($fields)
                                        @foreach ($fields as $field)
                                            @if (end($field) != null)
                                                <tr>
                                                    <th class="font-semibold align-top">
                                                        {{ $field[1] }}:
                                                    </th>
                                                    <td>
                                                        @if (end($field) == '1')
                                                            {{ \Hubzero\Facades\Lang::txt('YES') }}
                                                        @else
                                                            {{ end($field) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif

                                    {{-- Author --}}
                                    @if ($config->getCfg('byview') == 'YES')
                                        <tr>
                                            <th class="font-semibold align-top">
                                                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_AUTHOR_ALIAS') }}:
                                            </th>
                                            <td>{{ $authorName }}</td>
                                        </tr>
                                    @endif

                                    {{-- Tags --}}
                                    @if ($tags)
                                        <tr>
                                            <th class="font-semibold align-top">
                                                {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_TAGS') }}:
                                            </th>
                                            <td>{!! $tags !!}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @else
                <div class="alert alert-warning">
                    {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REP_NOEVENTSELECTED') }}
                </div>
            @endif
</x-page-container>
