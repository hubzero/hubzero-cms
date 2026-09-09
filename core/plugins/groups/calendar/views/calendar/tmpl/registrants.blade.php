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
use Hubzero\Facades\Route;

$year  = date("Y", strtotime($event->publish_up));
$month = date("m", strtotime($event->publish_up));

$groupCn   = $group->get('cn');
$eventId   = $event->id;

$calendarBase = 'index.php?option=' . $option
    . '&cn=' . $groupCn . '&active=calendar';

$backUrl = Route::url(
    $calendarBase . '&year=' . $year . '&month=' . $month
);
$deleteUrl = Route::url(
    $calendarBase . '&action=delete&event_id=' . $eventId
);
$editUrl = Route::url(
    $calendarBase . '&action=edit&event_id=' . $eventId
);
$detailsUrl = Route::url(
    $calendarBase . '&action=details&event_id=' . $eventId
);
$registerUrl = Route::url(
    $calendarBase . '&action=register&event_id=' . $eventId
);
$registrantsUrl = Route::url(
    $calendarBase . '&action=registrants&event_id=' . $eventId
);
$downloadUrl = Route::url(
    $calendarBase . '&action=download&event_id=' . $eventId
);

$isOwnerOrManager = $user->get('id') == $event->created_by
    || $authorized == 'manager';

$hasRegistration = isset($event->registerby)
    && $event->registerby
    && $event->registerby != '0000-00-00 00:00:00';

$registrantCount = count($registrants);
@endphp

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

<ul id="page_options">
    <li>
        <a class="btn btn-ghost gap-2" href="{{ $backUrl }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            {{ Lang::txt('Back to Calendar') }}
        </a>
    </li>
</ul>

<div class="flex items-center justify-between mb-4">
    <h3 class="text-xl font-semibold">
        {{ $event->title }}
    </h3>
    @if ($isOwnerOrManager)
        <div class="flex gap-2">
            <a class="btn btn-sm btn-outline" href="{{ $editUrl }}">{{ Lang::txt('Edit') }}</a>
            <a class="btn btn-sm btn-error btn-outline" href="{{ $deleteUrl }}">{{ Lang::txt('Delete') }}</a>
        </div>
    @endif
</div>

<div role="tablist" class="tabs tabs-border mb-6">
    <a role="tab" class="tab" href="{{ $detailsUrl }}">
        {{ Lang::txt('Details') }}
    </a>
    @if ($hasRegistration)
        <a role="tab" class="tab" href="{{ $registerUrl }}">
            {{ Lang::txt('Register') }}
        </a>
    @endif
    @if ($isOwnerOrManager)
        <a role="tab" class="tab tab-active" href="{{ $registrantsUrl }}">
            {{ Lang::txt('Registrants') }} ({{ $registrantCount }})
        </a>
    @endif
</div>

<div class="flex justify-end mb-4">
    <a href="{{ $downloadUrl }}" class="btn btn-outline btn-sm gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
        {{ Lang::txt('Download Registrants (.csv)') }}
    </a>
</div>

<div class="overflow-x-auto">
    <table class="table table-zebra">
        <thead>
            <tr>
                <th>{{ Lang::txt('Name') }}</th>
                <th>{{ Lang::txt('Email') }}</th>
                <th>{{ Lang::txt('Register Date') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (count($registrants) > 0)
                @foreach ($registrants as $registrant)
                    <tr>
                        <td>{{ $registrant->last_name }}, {{ $registrant->first_name }}</td>
                        <td>{{ $registrant->email }}</td>
                        <td>{{ Date::of($registrant->registered)->toLocal('l, F d, Y @ g:i a') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center text-base-content/60">
                        {{ Lang::txt('Currently there are no event registrants.') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
