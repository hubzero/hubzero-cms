{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@if ($error)
    <div role="alert" class="alert alert-error">{{ Lang::txt('MOD_MYPOINTS_MISSING_TABLE') }}</div>
@else
    @php
        $pointsUrl = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=points');
    @endphp
    <div{!! ($moduleclass) ? ' class="' . $moduleclass . '"' : '' !!}>
        <div class="flex gap-2 mb-3">
            <a class="btn btn-sm btn-outline" href="{{ $pointsUrl }}">
                {{ Lang::txt('MOD_MYPOINTS_ALL_TRANSACTIONS') }}
            </a>
        </div>

        <p class="text-lg font-bold">
            <span>{{ Lang::txt('MOD_MYPOINTS_YOU_HAVE') }} </span>
            {{ $summary }}
            <small>{{ strtolower(Lang::txt('MOD_MYPOINTS_POINTS')) }}</small>
        </p>

        @if (count($history) > 0)
            <ul class="list bg-base-100 rounded-box">
                <li class="list-row font-semibold text-sm">
                    <div class="list-col">{{ Lang::txt('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_DATE') }}</div>
                    <div class="list-col">{{ Lang::txt('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_TYPE') }}</div>
                    <div class="list-col text-right">{{ Lang::txt('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_AMOUNT') }}</div>
                    <div class="list-col text-right">{{ Lang::txt('MOD_MYPOINTS_TRANSACTIONS_TBL_TH_BALANCE') }}</div>
                </li>
                @foreach ($history as $item)
                    <li class="list-row">
                        <div class="list-col">
                            <time datetime="{{ $item->created }}">{{ Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</time>
                        </div>
                        <div class="list-col">
                            {{ $item->type }}
                        </div>
                        <div class="list-col text-right">
                            @if ($item->type == 'withdraw')
                                <span class="text-error">-{{ $item->amount }}</span>
                            @elseif ($item->type == 'hold')
                                <span class="text-warning">({{ $item->amount }})</span>
                            @else
                                <span class="text-success">+{{ $item->amount }}</span>
                            @endif
                        </div>
                        <div class="list-col text-right">
                            {{ $item->balance }}
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
