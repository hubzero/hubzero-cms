{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$activityUrl = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=activity');
@endphp

<div{!! ($moduleclass) ? ' class="' . $moduleclass . '"' : '' !!}>
    @if ($params->get('button_show_all', 1))
        <div class="flex gap-2 mb-3">
            <a class="btn btn-sm btn-outline" href="{{ $activityUrl }}">
                {{ Lang::txt('MOD_MYACTIVITY_ALL_ACTIVITY') }}
            </a>
        </div>
    @endif

    @if ($rows->count())
        <ul class="list bg-base-100 rounded-box">
            @foreach ($rows as $row)
                @include('modules.mod_myactivity.tmpl.default_item', ['row' => $row])
            @endforeach
        </ul>
    @else
        <p class="text-base-content/60">{{ Lang::txt('MOD_MYACTIVITY_NO_RESULTS') }}</p>
    @endif
</div>
