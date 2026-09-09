{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $contribUrl = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=contributions&$area=$resources');
@endphp

<div class="flex gap-2 mb-3">
    <a class="btn btn-sm btn-outline" href="{{ $contribUrl }}">
        {{ Lang::txt('MOD_MYRESOURCES_ALL_PUBLICATIONS') }}
    </a>
</div>

<div id="myresources-$content">
    @if (!$contributions)
        <p class="text-base-content/60">{{ Lang::txt('MOD_MYRESOURCES_NONE_FOUND') }}</p>
    @else
        <ul class="list bg-base-100 rounded-box">
            @for ($i = 0; $i < count($contributions); $i++)
                @php
                    $class = match($contributions[$i]->published) {
                        1 => 'published',
                        2 => 'draft',
                        3 => 'pending',
                        0 => 'deleted',
                        default => '',
                    };
                    $thedate = Date::of($contributions[$i]->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                    $resUrl = Route::url('index.php?option=com_resources&id=' . $contributions[$i]->id);
                @endphp
                <li class="list-row">
                    <div class="list-col grow">
                        <a href="{{ $resUrl }}">
                            {{ \Hubzero\Utility\Str::truncate(stripslashes($contributions[$i]->title), 40) }}
                        </a>
                        <span class="text-xs text-base-content/60">
                            {{ $thedate }} &nbsp; {{ stripslashes($contributions[$i]->typetitle) }}
                        </span>
                    </div>
                </li>
            @endfor
        </ul>
    @endif
</div>
