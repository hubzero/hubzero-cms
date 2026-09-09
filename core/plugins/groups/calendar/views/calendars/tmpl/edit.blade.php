{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
@endphp

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

<ul id="page_options">
    <li>
        @php
        $backUrl = Route::url(
            'index.php?option=' . $option
            . '&cn=' . $group->cn
            . '&active=calendar&action=calendars'
        );
        @endphp
        <a class="btn btn-ghost gap-2" href="{{ $backUrl }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            {{ Lang::txt('Back to Manage Calendars') }}
        </a>
    </li>
</ul>

@php
$formAction = Route::url(
    'index.php?option=' . $option
    . '&cn=' . $group->cn
    . '&active=calendar&action=savecalendar'
);
@endphp
<form action="{{ $formAction }}"
    id="hubForm" method="post" class="full">

    <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
        <legend class="font-semibold px-2">{{ Lang::txt('Group Calendar') }}</legend>

        <div class="form-control w-full mb-4">
            <label class="label" for="field-title">
                <span class="label-text">{{ Lang::txt('Title:') }}</span>
                <span class="label-text-alt badge badge-error badge-sm">{{ Lang::txt('Required') }}</span>
            </label>
            <input type="text"
                name="calendar[title]"
                id="field-title"
                class="input input-bordered w-full"
                value="{{ e($calendar->get('title')) }}" />
        </div>

        <div class="form-control w-full mb-4">
            <label class="label" for="field-url">
                <span class="label-text">{{ Lang::txt('URL:') }}</span>
                <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
            </label>
            <input type="text"
                name="calendar[url]"
                id="field-url"
                class="input input-bordered w-full"
                value="{{ e($calendar->get('url')) }}" />
            <label class="label">
                <span class="label-text-alt">{{ Lang::txt('This is used to fetch remote calendar events from other services such as a Google Calendar.') }}</span>
            </label>
        </div>

        <div class="form-control w-full mb-4">
            <label class="label" for="field-color">
                <span class="label-text">{{ Lang::txt('Color:') }}</span>
                <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
            </label>
            @php $colors = ['red','orange','yellow','green','blue','purple','brown']; @endphp
            <select name="calendar[color]" id="field-color" class="select select-bordered w-full">
                <option value="">{{ Lang::txt('— Select Color —') }}</option>
                @foreach ($colors as $color)
                    <option {{ $calendar->get('color') == $color ? 'selected' : '' }} value="{{ $color }}">
                        {{ ucfirst($color) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-control w-full mb-4">
            <label class="label" for="field-published">
                <span class="label-text">{{ Lang::txt('Publish Events to Subscribers?:') }}</span>
            </label>
            <select name="calendar[published]" id="field-published" class="select select-bordered w-full">
                <option {{ $calendar->get('published') == 1 ? 'selected' : '' }} value="1">
                    {{ Lang::txt('Yes') }}
                </option>
                <option {{ $calendar->get('published') != 1 ? 'selected' : '' }} value="0">
                    {{ Lang::txt('No') }}
                </option>
            </select>
        </div>
    </fieldset>

    <div class="mt-6">
        <button type="submit" class="btn btn-primary">
            {{ Lang::txt('Submit') }}
        </button>
    </div>

    <input type="hidden" name="option" value="com_groups" />
    <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="active" value="calendar" />
    <input type="hidden" name="action" value="savecalendar" />
    <input type="hidden" name="calendar[id]" value="{{ $calendar->get('id') }}" />
    {!! Html::input('token') !!}
</form>
