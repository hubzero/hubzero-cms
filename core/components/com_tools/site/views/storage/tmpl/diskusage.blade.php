@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

@if(!$ajax)
<dl id="diskusage" data-base="{{ rtrim(Request::base(true), '/') }}">
@endif
    @if($writelink)
        @php $storageUrl = Route::url('index.php?option=' . $option . '&task=storage'); @endphp
        <dt>{{ Lang::txt('COM_TOOLS_STORAGE') }} (<a href="{{ $storageUrl }}">{{ Lang::txt('COM_TOOLS_STORAGE_MANAGE') }}</a>)</dt>
    @else
        <dt>{{ Lang::txt('COM_TOOLS_STORAGE') }}</dt>
    @endif

    <dd id="du-amount">
        <div class="du-amount-bar" style="width: {{ $amt }}%;" title="{{ $amt }}%">
            <strong>&nbsp;</strong>
            <span class="du-amount-text">{{ $amt }}% of {{ $total }}GB</span>
        </div>
    </dd>

    @if($msgs)
        @if(count($du) <= 1)
            <dd id="du-msg">
                <div role="alert" class="alert alert-error alert-sm">
                    <span>{{ Lang::txt('COM_TOOLS_STORAGE_ERROR_RETRIEVING') }}</span>
                </div>
            </dd>
        @endif
        @if($percent == 100)
            @php $resolveUrl = Route::url('index.php?option=' . $option . '&task=storageexceeded'); @endphp
            <dd id="du-msg">
                <div role="alert" class="alert alert-warning alert-sm">
                    <span>{{ Lang::txt('COM_TOOLS_STORAGE_WARNING_REACHED_LIMIT') }} <a href="{{ $resolveUrl }}">{{ Lang::txt('COM_TOOLS_STORAGE_HOW_TO_RESOLVE') }}</a>.</span>
                </div>
            </dd>
        @endif
        @if($percent > 100)
            @php $resolveUrl = Route::url('index.php?option=' . $option . '&task=storageexceeded'); @endphp
            <dd id="du-msg">
                <div role="alert" class="alert alert-warning alert-sm">
                    <span>{{ Lang::txt('COM_TOOLS_STORAGE_WARNING_EXCEEDING_LIMIT') }} <a href="{{ $resolveUrl }}">{{ Lang::txt('COM_TOOLS_STORAGE_HOW_TO_RESOLVE') }}</a>.</span>
                </div>
            </dd>
        @endif
    @endif
@if(!$ajax)
</dl>
@endif
