{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$year  = date("Y", strtotime($event->get('publish_up')));
$month = date("m", strtotime($event->get('publish_up')));

$cn = $group->get('cn');
$eventId = $event->get('id');
$baseUrl = 'index.php?option=' . $option
    . '&cn=' . $cn . '&active=calendar';

$calendarUrl = Route::url(
    'index.php?option=' . $option
    . '&cn=' . $group->cn
    . '&active=calendar&year=' . $year
    . '&month=' . $month
);
$deleteUrl = Route::url(
    $baseUrl . '&action=delete&event_id=' . $eventId
);
$editUrl = Route::url(
    $baseUrl . '&action=edit&event_id=' . $eventId
);
$detailsUrl = Route::url(
    $baseUrl . '&action=details&event_id=' . $eventId
);
$registerUrl = Route::url(
    $baseUrl . '&action=register&event_id=' . $eventId
);
$registrantsUrl = Route::url(
    $baseUrl . '&action=registrants&event_id=' . $eventId
);

$isOwnerOrManager = $user->get('id') == $event->get('created_by')
    || $authorized == 'manager';
$hasRegistration = $event->get('registerby')
    && $event->get('registerby') != '0000-00-00 00:00:00';
@endphp

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

<ul id="page_options">
    <li>
        <a class="btn btn-ghost gap-2" href="{{ $calendarUrl }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            {{ Lang::txt('Back to Calendar') }}
        </a>
    </li>
</ul>

<div class="flex items-center justify-between mb-4">
    <h3 class="text-xl font-semibold">
        {{ $event->get('title') }}
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
        <a role="tab" class="tab tab-active" href="{{ $registerUrl }}">
            {{ Lang::txt('Register') }}
        </a>
        @if ($isOwnerOrManager)
            <a role="tab" class="tab" href="{{ $registrantsUrl }}">
                {{ Lang::txt('Registrants') }} ({{ $registrants }})
            </a>
        @endif
    @endif
</div>

<div class="alert alert-warning">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
    <span>{{ Lang::txt('Registration is closed for this event.') }}</span>
</div>
