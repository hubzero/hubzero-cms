{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<div{!! ($moduleclass) ? ' class="' . $moduleclass . '"' : '' !!}>
    <div class="flex gap-2 mb-3">
        <a class="btn btn-sm btn-outline"
            href="{{ Route::url('index.php?option=com_support&task=tickets') }}"
        >{{ Lang::txt('MOD_MYTICKETS_ALL_TICKETS') }}</a>
        <a class="btn btn-sm btn-outline"
            href="{{ Route::url('index.php?option=com_support&task=new') }}"
        >{{ Lang::txt('MOD_MYTICKETS_NEW_TICKET') }}</a>
    </div>

    <h4>{{ Lang::txt('MOD_MYTICKETS_SUBMITTED') }}</h4>
    @if (count($rows1) <= 0)
        <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYTICKETS_NO_TICKETS') }}</em></p>
    @else
        <ul class="list bg-base-100 rounded-box">
            @foreach ($rows1 as $row)
                @php
                    $ticketUrl = Route::url('index.php?option=com_support&task=ticket&id=' . $row->id);
                @endphp
                <li class="list-row">
                    <div class="list-col grow">
                        <a href="{{ $ticketUrl }}"
                            $title="#{{ $row->id }} :: {{ stripslashes($row->summary) }}"
                        >#{{ $row->id }}: {{ \Hubzero\Utility\Str::truncate(stripslashes($row->summary), 35) }}</a>
                        <span class="text-xs text-base-content/60">
                            {{ Date::of($row->created)->relative() }},
                            {{ Lang::txt('MOD_MYTICKETS_COMMENTS', $row->comments) }}
                        </span>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <h4>{{ Lang::txt('MOD_MYTICKETS_ASSIGNED') }}</h4>
    @if (count($rows2) <= 0)
        <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYTICKETS_NO_TICKETS') }}</em></p>
    @else
        <ul class="list bg-base-100 rounded-box">
            @foreach ($rows2 as $row)
                @php
                    $ticketUrl = Route::url('index.php?option=com_support&task=ticket&id=' . $row->id);
                @endphp
                <li class="list-row">
                    <div class="list-col grow">
                        <a href="{{ $ticketUrl }}"
                            $title="#{{ $row->id }} :: {{ stripslashes($row->summary) }}"
                        >#{{ $row->id }}: {{ \Hubzero\Utility\Str::truncate(stripslashes($row->summary), 35) }}</a>
                        <span class="text-xs text-base-content/60">
                            {{ Date::of($row->created)->relative() }},
                            {{ Lang::txt('MOD_MYTICKETS_COMMENTS', $row->comments) }}
                        </span>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <h4>{{ Lang::txt('MOD_MYTICKETS_CONTRIBUTIONS') }}</h4>
    @if (empty($rows3))
        <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYTICKETS_NO_TICKETS') }}</em></p>
    @else
        <ul class="list bg-base-100 rounded-box">
            @foreach ($rows3 as $row)
                @php
                    $ticketUrl = Route::url('index.php?option=com_support&task=ticket&id=' . $row->id);
                @endphp
                <li class="list-row">
                    <div class="list-col grow">
                        <a href="{{ $ticketUrl }}"
                            $title="#{{ $row->id }} :: {{ stripslashes($row->summary) }}"
                        >#{{ $row->id }}: {{ \Hubzero\Utility\Str::truncate(stripslashes($row->summary), 35) }}</a>
                        <span class="text-xs text-base-content/60">
                            {{ Date::of($row->created)->relative() }},
                            {{ Lang::txt('MOD_MYTICKETS_COMMENTS', $row->comments) }}
                        </span>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
