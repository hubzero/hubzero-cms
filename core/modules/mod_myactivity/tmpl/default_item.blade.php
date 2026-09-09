{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$status = '';
if (!$row->wasViewed()) {
    $status = 'new';
    $row->markAsViewed();
}
$name = stripslashes($row->log->creator->get('name'));
$scopeClass = $row->get('scope') . '.' . $row->get('scope_id') . ' ' . $row->log->get('action');
@endphp

<li class="list-row"
    data-time="{{ $row->get('created') }}"
    data-$id="{{ $row->get('id') }}"
    data-log_id="{{ $row->get('log_id') }}"
>
    <div class="list-col grow">
        <div>
            <span class="font-semibold">
                @if (in_array($row->log->creator->get('access'), User::getAuthorisedViewLevels()))
                    <a href="{{ Route::url($row->log->creator->link()) }}">
                        {{ $name }}
                    </a>
                @else
                    {{ $name }}
                @endif
            </span>
            <span class="text-xs text-base-content/60">
                <time datetime="{{ $row->get('created') }}">{{ Date::of($row->get('created'))->relative() }}</time>
            </span>
        </div>
        <div class="text-sm">
            {!! $row->log->get('description') !!}
        </div>
    </div>
</li>
