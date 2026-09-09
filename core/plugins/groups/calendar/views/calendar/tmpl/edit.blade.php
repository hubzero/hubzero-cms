{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

$includeRegistration = Request::getInt('includeRegistration', 0);

$formTitle = Lang::txt('Add Group Event');
$submitBtn = Lang::txt('Submit New Event');
if ($event->get('id')) {
    $formTitle = Lang::txt('Edit Group Event');
    $submitBtn = Lang::txt('Update Event');
}

$showImport = false;
if ($params->get('allow_import', 1) && !$event->get('id')) {
    $showImport = true;
}
$eventParams = new \Hubzero\Config\Registry($event->get('params'));
$ignoreDst = $eventParams->get('ignore_dst') == 1;
@endphp

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

<ul id="page_options">
    <li>
        @if (Request::getString('action') == 'edit')
            @php
            $detailsUrl = Route::url(
                'index.php?option=' . $option
                . '&cn=' . $group->cn
                . '&active=calendar&action=details'
                . '&event_id=' . $event->get('id')
            );
            @endphp
            <a class="btn btn-ghost gap-2" href="{{ $detailsUrl }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                {{ Lang::txt('Back to Event') }}
            </a>
        @else
            @php
            $calendarUrl = Route::url(
                'index.php?option=' . $option
                . '&cn=' . $group->cn
                . '&active=calendar'
                . '&year=' . $year
                . '&month=' . $month
            );
            @endphp
            <a class="btn btn-ghost gap-2" href="{{ $calendarUrl }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                {{ Lang::txt('Back to Events Calendar') }}
            </a>
        @endif
    </li>
</ul>

<div class="grid grid-cols-1 {{ $showImport ? 'lg:grid-cols-12' : '' }} gap-6">
    <div class="{{ $showImport ? 'lg:col-span-9' : '' }}">
        @php
        $formAction = Route::url(
            'index.php?option=' . $option
            . '&cn=' . $group->cn
            . '&active=calendar'
        );
        @endphp
        <form name="editevent"
            action="{{ $formAction }}"
            method="post"
            id="hubForm"
            class="full">
            <fieldset>
                <legend class="text-lg font-semibold">{{ $formTitle }}</legend>

                <div class="form-control w-full mb-4">
                    <label class="label" for="event_title">
                        <span class="label-text">{{ Lang::txt('Title:') }}</span>
                        <span class="label-text-alt badge badge-error badge-sm">{{ Lang::txt('Required') }}</span>
                    </label>
                    <input type="text"
                        name="event[title]"
                        id="event_title"
                        class="input input-bordered w-full"
                        value="{{ e($event->get('title')) }}" />
                </div>

                @if (count($calendars) > 0 || $authorized == 'manager')
                    <div class="form-control w-full mb-4">
                        <label class="label" for="event-calendar-picker">
                            <span class="label-text">{{ Lang::txt('Calendar:') }}</span>
                            <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                        </label>
                        <select name="event[calendar_id]" id="event-calendar-picker" class="select select-bordered w-full">
                            <option value="">{{ Lang::txt('— Select Calendar for Event —') }}</option>
                            @php $colors = ['red','orange','yellow','green','blue','purple','brown']; @endphp
                            @foreach ($calendars as $cal)
                                @php
                                if (!in_array($cal->get('color'), $colors)) {
                                    $cal->set('color', '');
                                }
                                $sel = ($cal->get('id') == $event->get('calendar_id'))
                                    ? 'selected' : '';
                                $color = $cal->get('color') ? $cal->get('color') : 'gray';
                                $swatchImg = Request::base(true)
                                    . '/core/plugins/groups/calendar'
                                    . '/assets/img/swatch-' . $color . '.png';
                                @endphp
                                <option {{ $sel }}
                                    data-img="{{ $swatchImg }}"
                                    value="{{ $cal->get('id') }}">
                                    {{ $cal->get('title') }}
                                </option>
                            @endforeach
                        </select>

                        @if ($authorized == 'manager')
                            @php
                            $addCalUrl = Route::url(
                                'index.php?option=' . $option
                                . '&cn=' . $group->cn
                                . '&active=calendar'
                                . '&action=addcalendar'
                            );
                            @endphp
                            <label class="label">
                                <span class="label-text-alt">
                                    {{ Lang::txt('Need a new calendar?') }}
                                    <a href="{{ $addCalUrl }}" class="link link-primary">{{ Lang::txt('Click here!') }}</a>
                                </span>
                            </label>
                        @endif
                    </div>
                @endif

                <div class="form-control w-full mb-4">
                    <label class="label" for="event_content">
                        <span class="label-text">{{ Lang::txt('Details:') }}</span>
                        <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </label>
                    <textarea name="content"
                        id="event_content"
                        rows="10"
                        class="textarea textarea-bordered w-full">{{ e($event->get('content')) }}</textarea>
                    <label class="label">
                        <span class="label-text-alt">{{ Lang::txt('Limited HTML allowed (a, iframe, strong, em, u)') }}</span>
                    </label>
                </div>

                <div class="form-control w-full mb-4">
                    <label class="label" for="event_location">
                        <span class="label-text">{{ Lang::txt('Location:') }}</span>
                        <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </label>
                    <input type="text"
                        name="event[adresse_info]"
                        id="event_location"
                        class="input input-bordered w-full"
                        value="{{ e($event->get('adresse_info')) }}" />
                </div>

                <div class="form-control w-full mb-4">
                    <label class="label" for="event-contact_info">
                        <span class="label-text">{{ Lang::txt('Contact:') }}</span>
                        <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </label>
                    <input type="text"
                        name="event[contact_info]"
                        id="event-contact_info"
                        class="input input-bordered w-full"
                        value="{{ e($event->get('contact_info')) }}" />
                    <label class="label">
                        <span class="label-text-alt">{{ Lang::txt('Accepts names and email addresses. (ex. John Doe john_doe@domain.com)') }}</span>
                    </label>
                </div>

                <div class="form-control w-full mb-4">
                    <label class="label" for="event_website">
                        <span class="label-text">{{ Lang::txt('Website:') }}</span>
                        <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </label>
                    <input type="text"
                        name="event[extra_info]"
                        id="event_website"
                        class="input input-bordered w-full"
                        value="{{ e($event->get('extra_info')) }}" />
                </div>

                <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
                    <legend class="font-semibold px-2">{{ Lang::txt('Date & Time Settings') }}</legend>

                    <div class="form-control w-full mb-4">
                        <label class="label" for="event_start_date">
                            <span class="label-text">{{ Lang::txt('Start:') }}</span>
                            <span class="label-text-alt badge badge-error badge-sm">{{ Lang::txt('Required') }}</span>
                        </label>
                        @php
                        $start           = Request::getString('start', '', 'get');
                        $publish_up      = ($event->get('publish_up'))
                            ? $event->get('publish_up')
                            : $start;
                        $publish_up_date = '';
                        $publish_up_time = '';
                        if ($publish_up && $publish_up != '0000-00-00 00:00:00') {
                            $publish_up_date = Date::of($publish_up)->toTimezone($timezone, 'm/d/Y', $ignoreDst);
                            $publish_up_time = Date::of($publish_up)->toTimezone($timezone, 'g:i a', $ignoreDst);
                        }
                        @endphp
                        <div class="join w-full">
                            <input type="text"
                                name="event[publish_up]"
                                id="event_start_date"
                                value="{{ e($publish_up_date) }}"
                                placeholder="mm/dd/yyyy"
                                class="input input-bordered join-item flex-1" />
                            <input type="text"
                                name="event[publish_up_time]"
                                id="event_start_time"
                                value="{{ e($publish_up_time) }}"
                                placeholder="h:mm am/pm"
                                class="input input-bordered join-item flex-1" />
                        </div>
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label" for="event_end_date">
                            <span class="label-text">{{ Lang::txt('End:') }}</span>
                            <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                        </label>
                        @php
                        $end               = Request::getString('end', '', 'get');
                        $publish_down      = ($event->get('publish_down')) ? $event->get('publish_down') : $end;
                        $publish_down_date = '';
                        $publish_down_time = '';
                        if ($publish_down && $publish_down != '0000-00-00 00:00:00') {
                            $publish_down_date = Date::of($publish_down)->toTimezone(
                                $timezone, 'm/d/Y', $ignoreDst
                            );
                            $publish_down_time = Date::of($publish_down)->toTimezone(
                                $timezone, 'g:i a', $ignoreDst
                            );
                        }
                        @endphp
                        <div class="join w-full">
                            <input type="text"
                                name="event[publish_down]"
                                id="event_end_date"
                                value="{{ e($publish_down_date) }}"
                                placeholder="mm/dd/yyyy"
                                class="input input-bordered join-item flex-1" />
                            <input type="text"
                                name="event[publish_down_time]"
                                id="event_end_time"
                                value="{{ e($publish_down_time) }}"
                                placeholder="h:mm am/pm"
                                class="input input-bordered join-item flex-1" />
                        </div>
                    </div>

                    <label class="label cursor-pointer justify-start gap-2">
                        <input type="hidden" name="event[allday]" value="0" />
                        <input type="checkbox"
                            id="event_allday"
                            name="event[allday]"
                            value="1"
                            class="checkbox checkbox-sm"
                            {{ $event->get('allday') ? 'checked' : '' }} />
                        <span class="label-text">{{ Lang::txt('All day event') }}</span>
                        <span class="label-text-alt text-base-content/60">{{ Lang::txt(' - can span multiple days') }}</span>
                    </label>
                </fieldset>

                <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
                    <legend class="font-semibold px-2">{{ Lang::txt('Timezone Settings') }}</legend>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">{{ Lang::txt('Timezone:') }}</span>
                            <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                        </label>
                        {!! \Components\Events\Helpers\Html::buildTimeZoneSelect($timezone, '') !!}
                    </div>

                    <label class="label cursor-pointer justify-start gap-2">
                        <input type="checkbox"
                            id="ignore_dst"
                            name="params[ignore_dst]"
                            value="1"
                            class="checkbox checkbox-sm"
                            {{ $ignoreDst ? 'checked' : '' }} />
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CALENDAR_IGNORE_DST') }}</span>
                    </label>
                </fieldset>

                @php
                $repeating = $event->parseRepeatingRule();
                $freqs = [
                    ''        => '— None —',
                    'daily'   => 'Daily',
                    'weekly'  => 'Weekly',
                    'monthly' => 'Monthly',
                    'yearly'  => 'Yearly'
                ];
                @endphp
                <fieldset class="reccurance border border-base-300 rounded-lg p-4 mb-4">
                    <legend class="font-semibold px-2">{{ Lang::txt('Repeating Settings') }}</legend>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">{{ Lang::txt('Recurrence:') }}</span>
                            <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                        </label>
                        <select name="reccurance[freq]" class="select select-bordered w-full event_recurrence_freq">
                            @foreach ($freqs as $k => $v)
                                <option {{ $repeating['freq'] == $k ? 'selected' : '' }} value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="reccurance-options options-daily">
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">{{ Lang::txt('Repeat Every:') }}</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <select name="reccurance[interval][daily]" class="select select-bordered select-sm daily-days event_recurrence_interval">
                                    @for ($i = 1; $i < 31; $i++)
                                        <option {{ ($repeating['freq'] == 'daily' && $repeating['interval'] == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <span>{{ Lang::txt('days') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="reccurance-options options-weekly">
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">{{ Lang::txt('Repeat Every:') }}</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <select name="reccurance[interval][weekly]" class="select select-bordered select-sm weekly-weeks event_recurrence_interval">
                                    @for ($i = 1; $i < 31; $i++)
                                        <option {{ ($repeating['freq'] == 'weekly' && $repeating['interval'] == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <span>{{ Lang::txt('weeks') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="reccurance-options options-monthly">
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">{{ Lang::txt('Repeat Every:') }}</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <select name="reccurance[interval][monthly]" class="select select-bordered select-sm monthly-months event_recurrence_interval">
                                    @for ($i = 1; $i < 31; $i++)
                                        <option {{ ($repeating['freq'] == 'monthly' && $repeating['interval'] == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <span>{{ Lang::txt('months') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="reccurance-options options-yearly">
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">{{ Lang::txt('Repeat Every:') }}</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <select name="reccurance[interval][yearly]" class="select select-bordered select-sm yearly-years event_recurrence_interval">
                                    @for ($i = 1; $i < 31; $i++)
                                        <option {{ ($repeating['freq'] == 'yearly' && $repeating['interval'] == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <span>{{ Lang::txt('years') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">{{ Lang::txt('Ends:') }}</span>
                            <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                        </label>
                        <div class="flex flex-col gap-2">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input id="never"
                                    type="radio"
                                    name="reccurance[ends][when]"
                                    value="never"
                                    class="radio radio-sm"
                                    {{ $repeating['end'] == 'never' ? 'checked' : '' }} />
                                <span class="label-text">{{ Lang::txt('Never') }}</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input id="after"
                                    type="radio"
                                    name="reccurance[ends][when]"
                                    value="count"
                                    class="radio radio-sm"
                                    {{ $repeating['end'] == 'count' ? 'checked' : '' }} />
                                <span class="label-text">{{ Lang::txt('After') }}</span>
                                <input type="text"
                                    name="reccurance[ends][count]"
                                    placeholder="x"
                                    class="input input-bordered input-sm w-20 after-input event_recurrence_end_count"
                                    value="{{ $repeating['count'] }}" />
                                <span class="label-text">{{ Lang::txt('occurrences') }}</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input id="on"
                                    type="radio"
                                    name="reccurance[ends][when]"
                                    value="until"
                                    class="radio radio-sm"
                                    {{ $repeating['end'] == 'until' ? 'checked' : '' }} />
                                <span class="label-text">{{ Lang::txt('On') }}</span>
                                <input type="text"
                                    name="reccurance[ends][until]"
                                    placeholder="mm/dd/yyyy"
                                    class="input input-bordered input-sm w-40 on-input event_recurrence_end_date"
                                    value="{{ $repeating['until'] }}" />
                            </label>
                        </div>
                    </div>
                </fieldset>
            </fieldset>

            @if ($params->get('allow_registrations', 1) && $authorized == 'manager')
                <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
                    <legend class="font-semibold px-2">{{ Lang::txt('Registration Settings') }}</legend>

                    @php
                    $hasRegisterBy = $event->get('registerby')
                        && $event->get('registerby') != '0000-00-00 00:00:00';
                    $regChecked = ($hasRegisterBy || $includeRegistration);
                    @endphp
                    <label class="label cursor-pointer justify-start gap-2 mb-4" id="include-registration-toggle">
                        <input type="checkbox"
                            id="include-registration"
                            name="include-registration"
                            value="1"
                            class="checkbox checkbox-sm"
                            {{ $regChecked ? 'checked' : '' }} />
                        <span class="label-text">{{ Lang::txt('Include registration for this event.') }}</span>
                    </label>

                    <div id="registration-fields" class="{{ !$regChecked ? 'hidden' : '' }}">
                        <div class="form-control w-full mb-4">
                            <label class="label" for="event_registerby">
                                <span class="label-text">{{ Lang::txt('Deadline:') }}</span>
                                <span class="label-text-alt badge badge-error badge-sm">{{ Lang::txt('Required for Registration Tab to Appear') }}</span>
                            </label>
                            @php
                            $register_by = '';
                            if ($event->get('registerby') && $event->get('registerby') != '0000-00-00 00:00:00') {
                                $register_by = Date::of($event->get('registerby'))->toLocal('m/d/Y @ g:i a');
                            }
                            @endphp
                            <input type="text"
                                name="event[registerby]"
                                id="event_registerby"
                                class="input input-bordered w-full"
                                value="{{ e($register_by) }}"
                                placeholder="mm/dd/yyyy @ h:mm am/pm" />
                            <label class="label">
                                <span class="label-text-alt">{{ Lang::txt('Deadlines are on Eastern Standard Time (EST).') }}</span>
                            </label>
                        </div>

                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text">{{ Lang::txt('Event Admin Email:') }}</span>
                                <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                            </label>
                            <input type="text"
                                name="event[email]"
                                class="input input-bordered w-full"
                                value="{{ e($event->get('email')) }}" />
                            <label class="label">
                                <span class="label-text-alt">{{ Lang::txt("A copy of event registrations will get sent to this event's admin email address.") }}</span>
                            </label>
                        </div>

                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text">{{ Lang::txt('Password:') }}</span>
                                <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                            </label>
                            <input type="text"
                                name="event[restricted]"
                                class="input input-bordered w-full"
                                value="{{ e($event->get('restricted')) }}" />
                            <label class="label">
                                <span class="label-text-alt">{{ Lang::txt('If you want registration to be restricted (invite only), enter the password users must enter to gain access to the registration form.') }}</span>
                            </label>
                        </div>

                        <fieldset class="border border-base-300 rounded-lg p-4">
                            <legend class="font-semibold px-2">{{ Lang::txt('Registration Fields') }}</legend>
                            {!! $registrationFields->render() !!}
                        </fieldset>
                    </div>
                </fieldset>
            @endif

            <input type="hidden" name="option" value="com_groups" />
            <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
            <input type="hidden" name="active" value="calendar" />
            <input type="hidden" name="action" value="save" />
            <input type="hidden" name="event[id]" value="{{ $event->get('id') }}" />
            {!! Html::input('token') !!}

            <div class="mt-6">
                <button type="submit" name="event_submit" class="btn btn-primary">
                    {{ $submitBtn }}
                </button>
            </div>
        </form>
    </div>

    @if ($showImport)
        <div class="lg:col-span-3">
            @php
            $importAction = Route::url(
                'index.php?option=' . $option
                . '&cn=' . $group->cn
                . '&active=calendar'
            );
            @endphp
            <form name="importevent"
                action="{{ $importAction }}"
                method="post"
                enctype="multipart/form-data">
                <fieldset class="border border-base-300 rounded-lg p-4">
                    <legend class="font-semibold px-2">{{ Lang::txt('Import Event') }}</legend>

                    <div class="text-center p-4 border-2 border-dashed border-base-300 rounded-lg">
                        <p class="font-semibold">{{ Lang::txt('Upload Event') }}</p>
                        <p class="text-sm text-base-content/60 mb-4">{{ Lang::txt('Drag & Drop an Event File Here to Upload') }}</p>
                        <label class="btn btn-outline btn-sm">
                            {{ Lang::txt('or, Select Event') }}
                            @php
                            $importUrl = Route::url(
                                'index.php?option=' . $option
                                . '&cn=' . $group->get('cn')
                                . '&active=calendar&action=import'
                            );
                            @endphp
                            <input type="file"
                                name="import"
                                id="import"
                                class="hidden"
                                data-url="{{ $importUrl }}" />
                        </label>
                    </div>
                </fieldset>
            </form>
        </div>
    @endif
</div>
