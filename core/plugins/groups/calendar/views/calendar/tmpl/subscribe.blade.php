{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

$thisCalendar = new stdClass();
$thisCalendar->id        = 0;
$thisCalendar->published = 1;
$thisCalendar->title     = "All Calendars";
foreach ($calendars as $cal) {
    if ($cal->get('id') == $calendar) {
        $thisCalendar = $cal;
    }
}
@endphp

<div class="subject group-calendar-subject subscribe">
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <h3 class="card-title">{{ Lang::txt('Subscribe') }}</h3>
                @php
                $helpUrl = Route::url(
                    'index.php?option=com_help'
                    . '&component=groups'
                    . '&extension=calendar'
                    . '&page=subscriptions'
                );
                @endphp
                <a class="btn btn-ghost btn-sm" href="{{ $helpUrl }}">
                    {{ Lang::txt('Need Help?') }}
                </a>
            </div>

            <div class="subscribe-content mt-4">
                <div class="alert alert-info mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ Lang::txt('If you are prompted to enter a username & password when subscribing to a calendar, enter your HUB credentials.') }}</span>
                </div>

                <p class="font-semibold mb-2">
                    {{ Lang::txt('Select the calendars you wish to subscribe to:') }}
                </p>

                @php
                $swatchBase = Request::base(true)
                    . '/core/plugins/groups/calendar'
                    . '/assets/img/swatch-';
                @endphp

                <div class="flex flex-col gap-2 mb-4">
                    <label class="label cursor-pointer justify-start gap-2">
                        <input type="checkbox" value="0" checked class="checkbox checkbox-sm" />
                        <img src="{{ $swatchBase }}gray.png" alt="gray" class="w-4 h-4" />
                        <span class="label-text">{{ Lang::txt('Uncategorized Events') }}</span>
                    </label>

                    @php $cals = [0]; @endphp
                    @foreach ($calendars as $cal)
                        @php
                        $enabled = false;
                        if ($cal->get('published') == 1) {
                            $enabled = true;
                            $cals[] = $cal->get('id');
                        }
                        @endphp
                        <label class="label cursor-pointer justify-start gap-2 {{ !$enabled ? 'opacity-50' : '' }}">
                            <input
                                {{ !$enabled ? 'disabled' : 'checked' }}
                                name="subscribe[]"
                                type="checkbox"
                                value="{{ $cal->get('id') }}"
                                class="checkbox checkbox-sm" />
                            @if ($cal->get('color'))
                                <img src="{{ $swatchBase }}{{ $cal->get('color') }}.png"
                                    alt="{{ $cal->get('color') }}"
                                    class="w-4 h-4" />
                            @else
                                <img src="{{ $swatchBase }}gray.png"
                                    alt="gray"
                                    class="w-4 h-4" />
                            @endif
                            <span class="label-text">
                                {{ $cal->get('title') }}
                                @if (!$enabled)
                                    {{ Lang::txt('(Calendar is not publishing events.)') }}
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>

                @php
                $link = $_SERVER['HTTP_HOST']
                    . '/'
                    . 'groups'
                    . '/'
                    . $group->get('cn')
                    . '/'
                    . 'calendar'
                    . '/'
                    . 'subscribe'
                    . '/'
                    . implode(',', $cals)
                    . '.ics';
                $httpsLink = 'https://' . $link;
                $webcalLink = 'webcal://' . $link;
                @endphp

                <div class="form-control w-full mt-4" id="subscribe-link">
                    <label class="label">
                        <span class="label-text font-semibold">{{ Lang::txt('Click the subscribe button to the right or add the link below to add as a calendar subscription:') }}</span>
                    </label>
                    <input type="text" value="{{ $httpsLink }}" class="input input-bordered w-full mb-2" readonly />
                    <div class="flex gap-2">
                        <a class="btn btn-outline btn-sm" href="{{ $httpsLink }}">
                            {{ Lang::txt('Download') }}
                        </a>
                        <a class="btn btn-primary btn-sm" href="{{ $webcalLink }}">
                            {{ Lang::txt('Subscribe') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
