{{--
  Plain text email confirmation for event registration
  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
{{ \Hubzero\Facades\Lang::txt('EVENTS_REGISTRATION_CONFIRMATION') }}

----------------------------------------
{{ $eventTitle }}
----------------------------------------

{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_STARTTIME') }}: {{ $eventStart }}
{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_ENDTIME') }}: {{ $eventEnd }}

{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_NAME') }}: {{ $register['firstname'] }} {{ $register['lastname'] }}
@if ($params->get('show_title') && !empty($register['title']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_TITLE') }}: {{ $register['title'] }}
@endif
@if ($params->get('show_affiliation') && !empty($register['affiliation']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_AFFILIATION') }}: {{ $register['affiliation'] }}
@endif
@if ($params->get('show_email'))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_EMAIL') }}: {{ $register['email'] }}
@endif
@if ($params->get('show_website') && !empty($register['website']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_WEBSITE') }}: {{ $register['website'] }}
@endif
@if ($params->get('show_telephone') && !empty($register['telephone']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_PHONE') }}: {{ $register['telephone'] }}
@endif
@if ($params->get('show_fax') && !empty($register['fax']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_FAX') }}: {{ $register['fax'] }}
@endif
@if ($params->get('show_address'))
@if (!empty($register['city']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_CITY') }}: {{ $register['city'] }}
@endif
@if (!empty($register['state']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_STATE') }}: {{ $register['state'] }}
@endif
@if (!empty($register['postalcode']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_ZIP') }}: {{ $register['postalcode'] }}
@endif
@if (!empty($register['country']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_COUNTRY') }}: {{ $register['country'] }}
@endif
@endif
@if ($params->get('show_position') && (!empty($register['position']) || !empty($register['position_other'])))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_POSITION') }}: {{ $register['position'] ? ucfirst($register['position']) : $register['position_other'] }}
@endif
@if ($params->get('show_degree') && !empty($register['degree']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_DEGREE') }}: {{ ucfirst($register['degree']) }}
@endif
@if ($params->get('show_gender') && !empty($register['sex']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_GENDER') }}: {{ ucfirst($register['sex']) }}
@endif
@if ($params->get('show_race') && $race)
@php
$raceList = '';
foreach ($race as $r => $t) {
    if ($r != 'nativetribe') {
        $raceList .= ucfirst($r) . ', ';
    }
}
if (!empty($race['nativetribe'])) {
    $raceList .= $race['nativetribe'];
}
$raceList = rtrim($raceList, ', ');
@endphp
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_RACE') }}: {{ $raceList }}
@endif
@if ($params->get('show_disability'))
@if ($disability)
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_HAS_DISABILITY') }}
@else
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_NO_DISABILITY') }}
@endif
@endif
@if ($params->get('show_dietary'))
@if (!empty($dietary['needs']) || !empty($dietary['specific']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_HAS_DIETARY', $dietary['specific']) }}
@else
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_NO_DIETARY') }}
@endif
@endif
@if ($params->get('show_arrival') && $arrival && (!empty($arrival['day']) || !empty($arrival['time'])))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_ARRIVAL') }}
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_ARRIVAL_DAY', $arrival['day']) }}
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_ARRIVAL_TIME', $arrival['time']) }}
@endif
@if ($params->get('show_departure') && $departure && (!empty($departure['day']) || !empty($departure['time'])))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_DEPARTURE') }}
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_DEPARTURE_DAY', $departure['day']) }}
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_DEPARTURE_TIME', $departure['time']) }}
@endif
@if ($params->get('show_dinner'))
@if ($dinner)
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_ATTENDING_DINNER') }}
@else
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_NOT_ATTENDING_DINNER') }}
@endif
@endif
@if ($params->get('show_abstract') && !empty($register['additional']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_ADDITIONAL', $register['additional']) }}
@endif
@if ($params->get('show_comments') && !empty($register['comments']))
{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_COMMENTS', $register['comments']) }}
@endif
