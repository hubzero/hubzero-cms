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

$year  = date("Y", strtotime($event->get('publish_up')));
$month = date("m", strtotime($event->get('publish_up')));
$params = new \Hubzero\Config\Registry($event->get('params'));
$ignoreDst = $params->get('ignore_dst', 0) == 1;

$calBase = 'index.php?option=' . $option
    . '&cn=' . $group->get('cn')
    . '&active=calendar';
$eventId = $event->get('id');

$backUrl = Route::url(
    'index.php?option=' . $option
    . '&cn=' . $group->cn
    . '&active=calendar&year=' . $year
    . '&month=' . $month
);
$deleteUrl = Route::url(
    $calBase . '&action=delete&event_id=' . $eventId
);
$editUrl = Route::url(
    $calBase . '&action=edit&event_id=' . $eventId
);
$detailsUrl = Route::url(
    $calBase . '&action=details&event_id=' . $eventId
);
$registerUrl = Route::url(
    $calBase . '&action=register&event_id=' . $eventId
);
$registrantsUrl = Route::url(
    $calBase . '&action=registrants&event_id=' . $eventId
);
$exportUrl = Route::url(
    $calBase . '&action=export&event_id=' . $eventId
);

$isPublished = $group->published == 1;
$isCreator = $user->get('id') == $event->get('created_by');
$isManager = $authorized == 'manager';
$canManage = $isPublished && ($isCreator || $isManager);
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
            {{ Lang::txt('Back to Events Calendar') }}
        </a>
    </li>
</ul>

<div class="flex items-center justify-between mb-4">
    <h3 class="text-xl font-semibold">
        {{ $event->get('title') }}
        @if (isset($calendar))
            <span class="text-base-content/60">&ndash; {{ $calendar->get('title') }}</span>
        @endif
    </h3>
    @if ($canManage)
        @if (!isset($calendar) || !$calendar->get('readonly'))
            <div class="flex gap-2">
                <a class="btn btn-sm btn-outline" href="{{ $editUrl }}">
                    {{ Lang::txt('Edit') }}
                </a>
                <a class="btn btn-sm btn-error btn-outline" href="{{ $deleteUrl }}">
                    {{ Lang::txt('Delete') }}
                </a>
            </div>
        @endif
    @endif
</div>

<div role="tablist" class="tabs tabs-border mb-6">
    <a role="tab" class="tab tab-active" href="{{ $detailsUrl }}">
        {{ Lang::txt('Details') }}
    </a>
    @if ($event->get('registerby') && $event->get('registerby') != '0000-00-00 00:00:00')
        <a role="tab" class="tab" href="{{ $registerUrl }}">
            {{ Lang::txt('Register') }}
        </a>
        @if ($isCreator || $isManager)
            <a role="tab" class="tab" href="{{ $registrantsUrl }}">
                {{ Lang::txt('Registrants') }} ({{ $registrants }})
            </a>
        @endif
    @endif
</div>

@php
$timezone     = timezone_name_from_abbr('', $event->get('time_zone') * 3600);
$publish_up   = $event->get('publish_up');
$publish_down = $event->get('publish_down');
$allday_event = $event->get('allday');

$start = Request::getInt('start', null, 'get');
$end   = Request::getInt('end', null, 'get');

if ($start || ($start && $end)) {
    $publish_up   = Date::of($start)->toSql();
    $publish_down = Date::of($end)->toSql();
}
@endphp

<div class="overflow-x-auto">
    <table class="table">
        <tbody>
            @if ($allday_event)
                <tr>
                    <th class="font-semibold w-32">{{ Lang::txt('Date') }}</th>
                    <td>
                        @php
                        $d1 = Date::of($publish_up);
                        $d2 = Date::of($publish_down)->modify('-24 hours');
                        if ($d1 == $d2 || !$publish_down || $publish_down == '0000-00-00 00:00:00') {
                            echo $d1->format('l, F d, Y', true);
                        } else {
                            echo $d1->format('l, F d, Y', true) . ' - ' . $d2->format('l, F d, Y', true);
                        }
                        @endphp
                    </td>
                    <th class="font-semibold w-32">{{ Lang::txt('Time') }}</th>
                    <td>{{ Lang::txt('All Day Event') }}</td>
                </tr>
            @elseif ($publish_down && $publish_down != '0000-00-00 00:00:00')
                <tr>
                    <th class="font-semibold w-32">{{ Lang::txt('Date') }}</th>
                    <td colspan="3">
                        @php
                        $dateFmt = 'l, F d, Y @ h:i a T';
                        $tz = $event->get('time_zone');
                        if ($tz) {
                            $startFormatted = Date::of($publish_up)
                                ->toTimezone($tz, $dateFmt, $ignoreDst);
                            $endFormatted = Date::of($publish_down)
                                ->toTimezone($tz, $dateFmt, $ignoreDst);
                        } else {
                            $startFormatted = Date::of($publish_up)
                                ->toLocal($dateFmt);
                            $endFormatted = Date::of($publish_down)
                                ->toLocal($dateFmt);
                        }
                        @endphp
                        {{ $startFormatted }} &mdash; {{ $endFormatted }}
                    </td>
                </tr>
            @else
                <tr>
                    <th class="font-semibold w-32">{{ Lang::txt('Date') }}</th>
                    <td>
                        {{ Date::of($publish_up, $event->get('time_zone'))->format('l, F d, Y', true) }}
                    </td>
                    <th class="font-semibold w-32">{{ Lang::txt('Time') }}</th>
                    <td>
                        {{ Date::of($publish_up, $event->get('time_zone'))->format('g:i a T', true) }}
                    </td>
                </tr>
            @endif

            @if ($event->get('repeating_rule') != '')
                <tr>
                    <th class="font-semibold">{{ Lang::txt('Repeating') }}</th>
                    <td colspan="3">{{ $event->humanReadableRepeatingRule() }}</td>
                </tr>
            @endif

            @if ($event->get('adresse_info') != '')
                <tr>
                    <th class="font-semibold">{{ Lang::txt('Location') }}</th>
                    <td colspan="3">{{ $event->get('adresse_info') }}</td>
                </tr>
            @endif

            @if ($event->get('contact_info') != '')
                <tr>
                    <th class="font-semibold">{{ Lang::txt('Contact') }}</th>
                    <td colspan="3">
                        {!! \Plugins\Groups\Calendar\Helper::autoLinkText(
                            $event->get('contact_info')
                        ) !!}
                    </td>
                </tr>
            @endif

            @if ($event->get('extra_info') != '')
                <tr>
                    <th class="font-semibold">{{ Lang::txt('Website') }}</th>
                    <td colspan="3">
                        <a href="{{ $event->get('extra_info') }}" rel="external" class="link link-primary">
                            {{ $event->get('extra_info') }}
                        </a>
                    </td>
                </tr>
            @endif

            @if ($event->get('content') != '')
                <tr>
                    <th class="font-semibold">{{ Lang::txt('Details') }}</th>
                    <td colspan="3">
                        {!! \Plugins\Groups\Calendar\Helper::autoLinkText(
                            nl2br(e($event->get('content')))
                        ) !!}
                    </td>
                </tr>
            @endif

            <tr>
                <td colspan="4" class="pt-4">
                    <a class="btn btn-outline btn-sm gap-2" href="{{ $exportUrl }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        {{ Lang::txt('Export to My Calendar (ics)') }}
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
