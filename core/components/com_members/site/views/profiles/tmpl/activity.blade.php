{{--
 * Active users and guests activity page
 *
 * Variables:
 *   $title   - Page title
 *   $option  - Component option (e.g. 'com_members')
 *   $users   - Array of active users keyed by username
 *   $guests  - Array of guest sessions
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $__view->css()->css('usage.css');
@endphp

<x-page-container :title="$title">

    {{-- Active Users Table --}}
    <table class="table table-zebra w-full mb-12">
        <caption>{{ Lang::txt('COM_MEMBERS_ACTIVITY_TABLE1') }}</caption>
        <thead>
            <tr>
                <th>{{ Lang::txt('COM_MEMBERS_ACTIVITY_COL_NAME') }}</th>
                <th>{{ Lang::txt('COM_MEMBERS_ACTIVITY_COL_LOGIN') }}</th>
                <th>{{ Lang::txt('COM_MEMBERS_ACTIVITY_COL_ORG_TYPE') }}</th>
                <th>{{ Lang::txt('COM_MEMBERS_ACTIVITY_COL_ORGANIZATION') }}</th>
                <th>{{ Lang::txt('COM_MEMBERS_ACTIVITY_COL_RESIDENT') }}</th>
                <th>{{ Lang::txt('COM_MEMBERS_ACTIVITY_COL_IP') }}</th>
                <th>{{ Lang::txt('COM_MEMBERS_ACTIVITY_COL_IDLE') }}</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th colspan="6">{{ Lang::txt('COM_MEMBERS_ACTIVITY_TABLE1_TOTAL') }}</th>
                <td>{{ count($users) }}</td>
            </tr>
        </tfoot>
        <tbody>
            @if(count($users) > 0)
                @foreach(array_keys($users) as $userkey)
                    <tr>
                        <td>{{ e(stripslashes($users[$userkey]['name'])) }}</td>
                        <td>
                            <a href="{{ Route::url('index.php?option=' . $option . '&id=' . $users[$userkey]['uidNumber']) }}">
                                {{ e($userkey) }}
                            </a>
                        </td>
                        <td>
                            @switch($users[$userkey]['orgtype'])
                                @case('universitystudent')
                                    {{ Lang::txt('UNIVERSITY_STUDENT') }}
                                    @break
                                @case('university')
                                @case('universityfaculty')
                                    {{ Lang::txt('UNIVERSITY_FACULTY') }}
                                    @break
                                @case('universitystaff')
                                    {{ Lang::txt('UNIVERSITY_STAFF') }}
                                    @break
                                @case('precollege')
                                @case('precollegefacultystaff')
                                    {{ Lang::txt('PRECOLLEGE_STAFF') }}
                                    @break
                                @case('precollegestudent')
                                    {{ Lang::txt('PRECOLLEGE_STUDENT') }}
                                    @break
                                @case('educational')
                                    {{ Lang::txt('EDUCATIONAL') }}
                                    @break
                                @case('nationallab')
                                    {{ Lang::txt('NATIONALLAB') }}
                                    @break
                                @case('industry')
                                    {{ Lang::txt('INDUSTRY') }}
                                    @break
                                @case('government')
                                    {{ Lang::txt('GOVERNMENT') }}
                                    @break
                                @case('military')
                                    {{ Lang::txt('MILITARY') }}
                                    @break
                                @case('personal')
                                    {{ Lang::txt('PERSONAL') }}
                                    @break
                                @case('unemployed')
                                    {{ Lang::txt('UNEMPLOYED') }}
                                    @break
                                @default
                                    {{ $users[$userkey]['orgtype'] }}
                            @endswitch
                        </td>
                        <td>{{ e(stripslashes($users[$userkey]['org'])) }}</td>
                        <td>{{ e($users[$userkey]['countryresident']) }}</td>
                        <td>{{ e($users[$userkey][0]['ip']) }}</td>
                        <td>{!! \Components\Members\Helpers\Html::valformat($users[$userkey][0]['idle'], 3) !!}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7">{{ Lang::txt('COM_MEMBERS_ACTIVITY_NO_RESULTS') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Guests Table --}}
    <table class="table table-zebra w-full">
        <caption>{{ Lang::txt('COM_MEMBERS_ACTIVITY_TABLE2') }}</caption>
        <thead>
            <tr>
                <th>{{ Lang::txt('COM_MEMBERS_ACTIVITY_COL_NAME') }}</th>
                <th>{{ Lang::txt('COM_MEMBERS_ACTIVITY_COL_IP') }}</th>
                <th>{{ Lang::txt('COM_MEMBERS_ACTIVITY_COL_IDLE') }}</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th colspan="2">{{ Lang::txt('COM_MEMBERS_ACTIVITY_TABLE2_TOTAL') }}</th>
                <td>{{ count($guests) }}</td>
            </tr>
        </tfoot>
        <tbody>
            @if(count($guests) > 0)
                @foreach($guests as $guest)
                    <tr>
                        <td>{{ Lang::txt('COM_MEMBERS_ACTIVITY_GUEST') }}</td>
                        <td>{{ e($guest['ip'] ?: Lang::txt('COM_MEMBERS_ACTIVITY_UNKNOWN')) }}</td>
                        <td>{!! \Components\Members\Helpers\Html::valformat($guest['idle'], 3) !!}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3">{{ Lang::txt('COM_MEMBERS_ACTIVITY_NO_RESULTS') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

</x-page-container>
