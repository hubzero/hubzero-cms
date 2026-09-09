@php
/**
 * Charts panel partial (Blade / daisyUI).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

<div id="dv_charts_panel"
    class="hidden clear-both w-[860px] h-[380px] p-2 mt-0 border border-base-300 bg-base-200 rounded-b-lg dv_top_pannel">
    <div class="float-right flex gap-1 z-10 m-1">
        <button id="dv_pdcharts_draw_btn" class="btn btn-xs btn-info" title="{{ Lang::txt('COM_DATAVIEWER_RELOAD_CHARTS_TITLE') }}">
            <i class="icon-repeat"></i>
            <span class="lbl">{{ Lang::txt('COM_DATAVIEWER_RELOAD') }}</span>
        </button>
        <button id="dv_pdcharts_download_btn" class="btn btn-xs btn-success" title="{{ Lang::txt('COM_DATAVIEWER_DOWNLOAD_CHART_TITLE') }}">
            <i class="icon-download"></i>
            <span class="lbl">{{ Lang::txt('COM_DATAVIEWER_DOWNLOAD_CHART') }}</span>
        </button>
    </div>

    <div class="float-left h-[380px] w-[245px]">
        <div id="dv_charts_control_panel" class="px-1">
            @if (isset($dd['charts_list']))
            <select id="dv_chart_name" class="select select-bordered select-sm w-full">
                @foreach ($dd['charts_list'] as $pdId => $cl)
                <option value="{{ $pdId }}">{{ $cl['title'] }}</option>
                @endforeach
            </select>
            <div id="dv_chart_desc"
                class="mt-2 text-sm border border-base-300 rounded-lg p-1 overflow-auto h-[340px]">
            </div>
            @endif
        </div>
    </div>
    <div id="dv_charts_preview_chart"
        class="h-full w-auto ml-[248px] border border-base-300 rounded-lg">
    </div>
</div>
