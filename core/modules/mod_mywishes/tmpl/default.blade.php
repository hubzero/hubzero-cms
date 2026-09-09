{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<div{!! ($params->get('moduleclass')) ? ' class="' . $params->get('moduleclass') . '"' : '' !!}>
    @if ($params->get('button_show_add', 1))
        <div class="flex gap-2 mb-3">
            <a class="btn btn-sm btn-outline"
                href="{{ Route::url('index.php?option=com_wishlist&task=add&category=general&rid=1') }}"
            >{{ Lang::txt('MOD_MYWISHES_NEW_WISH') }}</a>
        </div>
    @endif

    <h4>{{ Lang::txt('MOD_MYWISHES_SUBMITTED') }}</h4>
    @if (count($rows1) <= 0)
        <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYWISHES_NO_WISHES') }}</em></p>
    @else
        <ul class="list bg-base-100 rounded-box">
            @foreach ($rows1 as $row)
                @php
                    $title = strip_tags($row->about)
                        ? stripslashes($row->subject) . ' :: ' . \Hubzero\Utility\Str::truncate(strip_tags($row->about), 160)
                        : null;
                    $wishUrl = Route::url('index.php?option=com_wishlist&task=wish&id=' . $row->wishlist . '&wishid=' . $row->id);
                    $statusClass = '';
                    $statusText = '';
                    if ($row->status == 3) {
                        $statusClass = 'badge-error';
                        $statusText = Lang::txt('MOD_MYWISHES_REJECTED');
                    } elseif ($row->status == 0) {
                        if ($row->accepted == 1) {
                            $statusClass = 'badge-success';
                            $statusText = Lang::txt('MOD_MYWISHES_ACCEPTED');
                        } else {
                            $statusClass = 'badge-warning';
                            $statusText = Lang::txt('MOD_MYWISHES_PENDING');
                        }
                    }
                @endphp
                <li class="list-row">
                    <div class="list-col grow">
                        <a href="{{ $wishUrl }}"
                            $title="{{ $title }}"
                        >#{{ $row->id }}: {{ \Hubzero\Utility\Str::truncate(stripslashes($row->subject), 35) }}</a>
                        <span class="text-xs text-base-content/60">
                            {{ Lang::txt('MOD_MYWISHES_WISHLIST') }}: {{ stripslashes($row->listtitle) }}
                        </span>
                    </div>
                    @if ($statusText)
                        <div class="list-col">
                            <span class="badge badge-sm {{ $statusClass }}">{{ $statusText }}</span>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <h4>{{ Lang::txt('MOD_MYWISHES_ASSIGNED') }}</h4>
    @if (count($rows2) <= 0)
        <p class="text-base-content/60">{{ Lang::txt('MOD_MYWISHES_NO_WISHES') }}</p>
    @else
        <ul class="list bg-base-100 rounded-box">
            @foreach ($rows2 as $row)
                @php
                    $title = strip_tags($row->about)
                        ? stripslashes($row->subject) . ' :: ' . \Hubzero\Utility\Str::truncate(strip_tags($row->about), 160)
                        : null;
                    $wishUrl = Route::url('index.php?option=com_wishlist&task=wish&id=' . $row->wishlist . '&wishid=' . $row->id);
                    $statusClass = '';
                    $statusText = '';
                    if ($row->status == 3) {
                        $statusClass = 'badge-error';
                        $statusText = Lang::txt('MOD_MYWISHES_REJECTED');
                    } elseif ($row->status == 0) {
                        if ($row->accepted == 1) {
                            $statusClass = 'badge-success';
                            $statusText = Lang::txt('MOD_MYWISHES_ACCEPTED');
                        } else {
                            $statusClass = 'badge-warning';
                            $statusText = Lang::txt('MOD_MYWISHES_PENDING');
                        }
                    }
                @endphp
                <li class="list-row">
                    <div class="list-col grow">
                        <a href="{{ $wishUrl }}"
                            $title="{{ $title }}"
                        >#{{ $row->id }}: {{ \Hubzero\Utility\Str::truncate(stripslashes($row->subject), 35) }}</a>
                        <span class="text-xs text-base-content/60">
                            {{ Lang::txt('MOD_MYWISHES_WISHLIST') }}: {{ stripslashes($row->listtitle) }}
                        </span>
                    </div>
                    @if ($statusText)
                        <div class="list-col">
                            <span class="badge badge-sm {{ $statusClass }}">{{ $statusText }}</span>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
