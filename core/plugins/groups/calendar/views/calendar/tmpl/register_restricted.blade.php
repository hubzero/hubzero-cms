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

$groupCn   = $group->get('cn');
$eventId   = $event->get('id');

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
        <a class="btn btn-ghost gap-2" href="{{ $backUrl }}">
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

<form action="{{ $registerUrl }}"
    id="hubForm"
    method="post"
    class="full">
    <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
        <legend class="font-semibold px-2">{{ Lang::txt('Limited Registration') }}</legend>

        <div class="alert alert-info mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ Lang::txt('Registration is password protected. Please supply the password you were given with your invite to join the event.') }}</span>
        </div>

        <div class="form-control w-full">
            <label class="label">
                <span class="label-text">{{ Lang::txt('Password:') }}</span>
                <span class="label-text-alt badge badge-error badge-sm">{{ Lang::txt('Required') }}</span>
            </label>
            <input type="password" name="passwrd" class="input input-bordered w-full" />
        </div>
    </fieldset>

    <input type="hidden" name="option" value="com_groups" />
    <input type="hidden" name="cn" value="{{ $groupCn }}" />
    <input type="hidden" name="active" value="calendar" />
    <input type="hidden" name="action" value="register" />
    <input type="hidden" name="event_id" value="{{ $eventId }}" />

    <div class="mt-6">
        <button type="submit" name="event_submit" class="btn btn-primary">
            {{ Lang::txt('Submit') }}
        </button>
    </div>
</form>
