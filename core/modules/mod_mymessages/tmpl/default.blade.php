{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@if ($__module->getError())
    <div role="alert" class="alert alert-error">{{ $__module->getError() }}</div>
@else
    @php
        $messagesUrl = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages');
        $settingsUrl = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages&task=settings');
    @endphp
    <div{!! ($moduleclass) ? ' class="' . $moduleclass . '"' : '' !!}>
        <div class="flex gap-2 mb-3">
            <a class="btn btn-sm btn-outline" href="{{ $messagesUrl }}">
                {{ Lang::txt('MOD_MYMESSAGES_ALL_MESSAGES') }}
            </a>
            <a class="btn btn-sm btn-outline" href="{{ $settingsUrl }}">
                {{ Lang::txt('MOD_MYMESSAGES_MESSAGE_SETTINGS') }}
            </a>
        </div>

        @if (count($rows) <= 0)
            <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYMESSAGES_NO_MESSAGES') }}</em></p>
        @else
            <ul class="list bg-base-100 rounded-box">
                @foreach ($rows as $row)
                    @php
                        if ($row->component == 'support' || $row->component == 'com_support') {
                            $fg = explode(' ', $row->subject);
                            array_pop($fg);
                            $row->subject = implode(' ', $fg);
                        }
                        $msgUrl = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages&msg=' . $row->id);
                    @endphp
                    <li class="list-row">
                        <div class="list-col grow">
                            <a href="{{ $msgUrl }}">
                                {{ stripslashes($row->subject) }}
                            </a>
                            <span class="text-xs text-base-content/60">
                                <time datetime="{{ $row->created }}">{{ Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</time>
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($total > $limit)
            @php
                $moreUrl = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages');
            @endphp
            <p class="text-sm mt-2">{!! Lang::txt('MOD_MYMESSAGES_YOU_HAVE_MORE', $limit, $total, $moreUrl) !!}</p>
        @endif
    </div>
@endif
