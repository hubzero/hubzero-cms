{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;
@endphp

@if ($disabled)
    <p id="primary-document">
        <span class="btn btn-disabled {{ $class }}">{{ $msg }}</span>
    </p>
@else
    @if (!empty($options))
        <div class="flex" id="primary-document">
            <a class="btn{{ $class ? ' ' . $class : '' }}"
                @if ($href) href="{{ $href }}" @endif
                @if ($title) title="{{ e($title) }}" @endif
                {!! $action ?? '' !!}
            >{{ $msg }}</a>
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </label>
                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box shadow-lg z-10 w-52 p-2">
                    @foreach ($options as $option)
                        <li>
                            <a
                                @if ($option->class) class="{{ $option->class }}" @endif
                                {!! $option->attrs ?? '' !!}
                                href="{{ $option->href }}"
                            >{!! $option->title !!}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        @if ($showDoc && !empty($ftpDoc))
            <p>
                <a href="{{ $ftpDoc }}" target="_blank">
                    {{ Lang::txt('Download Guide') }}
                </a>
            </p>
        @endif
    @else
        <p id="primary-document">
            <a class="btn btn-primary{{ $class ? ' ' . $class : '' }}"
                @if ($href) href="{{ $href }}" @endif
                @if ($title) title="{{ e($title) }}" @endif
                {!! $action ?? '' !!}
            >{{ $msg }}</a>
        </p>
    @endif
@endif

@if ($pop)
    <div id="primary-document_pop">
        <div>{!! $pop !!}</div>
    </div>
@endif
