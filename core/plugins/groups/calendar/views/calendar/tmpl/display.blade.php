{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

$userLocalizer = new Plugins\Groups\Calendar\Helpers\UserLocalizer();
$timezone = $userLocalizer->getTimezone();
@endphp

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

@php
$isPublishedMember = $group->published == 1
    && in_array(User::get('id'), $members);
@endphp

@if ($isPublishedMember)
    @php
    $addTitle = Lang::txt('PLG_GROUPS_CALENDAR_ADD_NEW_LINK_TEXT');
    $addUrl = Route::url(
        'index.php?option=' . $option
        . '&cn=' . $group->cn
        . '&active=calendar&action=add'
    );
    @endphp
    <ul id="page_options">
        <li>
            <a class="btn btn-primary gap-2" href="{{ $addUrl }}" title="{{ $addTitle }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                {{ $addTitle }}
            </a>
            @if ($authorized == 'manager')
                @php
                $manageTitle = Lang::txt('Manage Calendars');
                $manageUrl = Route::url(
                    'index.php?option=' . $option
                    . '&cn=' . $group->cn
                    . '&active=calendar&action=calendars'
                );
                @endphp
                <a class="btn btn-outline gap-2" href="{{ $manageUrl }}" title="{{ $manageTitle }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    {{ $manageTitle }}
                </a>
            @endif
        </li>
    </ul>
@endif

@php
$canQuickCreate = $params->get('allow_quick_create', 1)
    && in_array(User::get('id'), $group->get('members'));
$quickCreate = $canQuickCreate ? true : 0;
$calendarBase = Route::url(
    'index.php?option=com_groups&cn='
    . $group->get('cn') . '&active=calendar'
);
@endphp

<div id="calendar"
    data-base="{{ $calendarBase }}"
    data-month="{{ $month }}"
    data-year="{{ $year }}"
    data-event-quickcreate="{{ $quickCreate }}"></div>

<select name="calendar" id="calendar-picker" class="select select-bordered w-full max-w-xs my-4">
    <option value="0">{{ Lang::txt('All Calendars') }}</option>
    @foreach ($calendars as $cal)
        @php
        $sel = ($cal->get('id') == $calendar) ? 'selected' : '';
        $color = $cal->get('color') ? strtolower($cal->get('color')) : 'gray';
        $imgBase = Request::base(true);
        $imgPath = $imgBase
            . '/core/plugins/groups/calendar/assets/img/swatch-'
            . $color . '.png';
        @endphp
        <option
            {{ $sel }}
            data-img="{{ $imgPath }}"
            value="{{ $cal->get('id') }}"
            class="calendar-picker-option"
        >{{ $cal->get('title') }}</option>
    @endforeach
</select>

<div class="subject group-calendar-subject event-list">
    <div class="container">
        <h3 class="text-lg font-semibold mb-4">{{ Lang::txt('Events List') }}</h3>
        @if ($eventsCount > 0)
            <ol class="calendar-entries list-none p-0 space-y-4">
                @foreach ($events as $evt)
                    @php
                    $evtParams = new \Hubzero\Config\Registry($evt->get('params'));
                    $evtIgnoreDst = $evtParams->get('ignore_dst') == 1;
                    @endphp
                    <li class="card card-compact bg-base-100 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title text-base">
                                <a href="{{ $evt->link() }}" class="link link-hover">
                                    {{ $evt->get('title') }}
                                </a>
                            </h4>
                            <dl class="entry-meta text-sm text-base-content/70">
                                @php
                                $calendarName = $evt->calendar()->get('id')
                                    ? $evt->calendar()->get('title')
                                    : 'Uncategorized';
                                $hasPublishDown = $evt->get('publish_down')
                                    && $evt->get('publish_down') != '0000-00-00 00:00:00';
                                @endphp
                                <dd class="inline">
                                    in {{ $calendarName }}
                                </dd>
                                @if ($hasPublishDown)
                                    <dd class="mt-1">
                                        {{ Date::of($evt->get('publish_up'))->toTimezone($timezone, 'l, F d, Y @ g:i a', $evtIgnoreDst) }}
                                        &mdash;
                                        {{ Date::of($evt->get('publish_down'))->toTimezone($timezone, 'l, F d, Y @ g:i a', $evtIgnoreDst) }}
                                    </dd>
                                @else
                                    <dd class="mt-1">
                                        {{ Date::of($evt->get('publish_up'))->toTimezone($timezone, 'l, F d, Y @ g:i a', $evtIgnoreDst) }}
                                    </dd>
                                @endif
                            </dl>
                            <div class="entry-content mt-2 text-sm">
                                @php
                                $content = strip_tags($evt->get('content'));
                                @endphp
                                <p>
                                    @if ($content)
                                        {{ Hubzero\Utility\Str::truncate($content, 500) }}
                                    @else
                                        <em>{{ Lang::txt('no content') }}</em>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>

            @php
            $pageNav = $__view->pagination(
                $eventsCount,
                $filters['start'],
                $filters['limit']
            );
            $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'calendar');
            echo $pageNav->render();
            @endphp
        @else
            <div class="alert alert-warning">
                <p>{{ Lang::txt('PLG_GROUPS_CALENDAR_NO_ENTRIES_FOUND') }}</p>
            </div>
        @endif
    </div>
</div>

@if ($params->get('allow_subscriptions', 1))
    @php
    $__view->view('subscribe')
        ->set('calendar', $calendar)
        ->set('calendars', $calendars)
        ->set('group', $group)
        ->display();
    @endphp
@endif
