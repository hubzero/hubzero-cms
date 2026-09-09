@php
/**
 * Spreadsheet toolbar partial (Blade / daisyUI).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$dlMenu = $dd['top_menu']['download']['show'] ?? null;
$showDl = !isset($dlMenu) || $dlMenu != false;

$wrapMenu = $dd['top_menu']['no_wrap']['show'] ?? null;
$showWrap = !isset($wrapMenu) || $wrapMenu != false;
@endphp

<div id="dv_title" class="mb-0">
    <h2 class="inline-block text-xl font-semibold">
        <i class="icon-table"></i>
        {{ $dd['title'] }}
    </h2>
    &nbsp;
    <h4 id="dv_return_link_container" class="inline-block m-0 text-sm">
        {!! $return !!}
    </h4>
</div>

<div id="dv-spreadsheet-toolbar" class="flex flex-wrap items-center gap-1 rounded-t-lg bg-base-200 p-2">
    @if ($showDl)
    <button class="btn btn-xs btn-ghost dv-btn-download" data-format="csv"
        title="{{ Lang::txt('COM_DATAVIEWER_DOWNLOAD_TITLE') }}">
        <i class="icon-download"></i>
        <span class="lbl">{{ Lang::txt('COM_DATAVIEWER_DOWNLOAD') }}</span>
    </button>
    @endif

    @if (!isset($dd['top_menu']['fullscreen']['show'])
        || $dd['top_menu']['fullscreen']['show'] != false)
    <button id="dv-btn-fullscreen" class="btn btn-xs btn-ghost" title="{{ Lang::txt('COM_DATAVIEWER_FULLSCREEN') }}" data-screen-mode="">
        <i class="icon-fullscreen"></i>
        <span class="lbl">{{ Lang::txt('COM_DATAVIEWER_FULLSCREEN') }}</span>
    </button>
    @endif

    @if (isset($dd['filters']) && count($dd['filters']) > 0)
    <button id="dv-btn-filters" class="btn btn-xs btn-ghost" title="{{ Lang::txt('COM_DATAVIEWER_FILTER_DIALOG') }}">
        <i class="icon-filter"></i>
        <span class="lbl">{{ Lang::txt('COM_DATAVIEWER_FILTER_DIALOG') }}</span>
    </button>
    @endif

    <button id="dv-btn-filter-clear-all" class="btn btn-xs btn-ghost"
        title="{{ Lang::txt('COM_DATAVIEWER_CLEAR_FILTERS_TITLE') }}">
        <i class="icon-remove-circle"></i>
        <span class="lbl">{{ Lang::txt('COM_DATAVIEWER_CLEAR_FILTERS') }}</span>
    </button>

    @if ($showWrap)
    <button id="dv-btn-no-wrap" class="btn btn-xs btn-ghost"
        title="{{ Lang::txt('COM_DATAVIEWER_NO_WRAP_TITLE') }}" data-current="normal">
        <i class="icon-text-width"></i>
        <span class="lbl">{{ Lang::txt('COM_DATAVIEWER_NO_WRAP') }}</span>
    </button>
    @endif

    @if ($showCharts)
    <button id="dv-spreadsheet-charts" class="btn btn-xs btn-ghost" title="{{ Lang::txt('COM_DATAVIEWER_CHARTS_TITLE') }}">
        <i class="icon-bar-chart"></i>
        <span class="lbl">{{ Lang::txt('COM_DATAVIEWER_CHARTS') }}</span>
    </button>
    @endif

    @if ($showMaps)
    <button id="dv-spreadsheet-maps" class="btn btn-xs btn-ghost" title="{{ Lang::txt('COM_DATAVIEWER_MAPS_TITLE') }}">
        <i class="icon-map-marker"></i>
        <span class="lbl">{{ Lang::txt('COM_DATAVIEWER_MAPS') }}</span>
    </button>
    @endif

    @if (isset($dd['customizer']))
    <button id="dv-customizer-btn" class="btn btn-xs btn-ghost"
        title="{{ Lang::txt('COM_DATAVIEWER_CUSTOMIZE_TITLE') }}">
        <i class="icon-edit"></i>
        <span class="lbl">{{ Lang::txt('COM_DATAVIEWER_CUSTOMIZE') }}</span>
    </button>
    @endif
</div>

{{-- Legacy hidden toolbar (kept for JS compatibility) --}}
<div class="hidden">
    <span id="dv_top_toolbar" class="rounded-t-lg p-1">
        @if ($help_file)
        <button id="dv_show_help">{{ Lang::txt('COM_DATAVIEWER_HELP') }}</button>
        @endif
        @if ($showMaps)
        <input type="checkbox" id="dv_maps" class="dv_panel_btn" /><label for="dv_maps">Map</label>
        @endif
        @if (isset($dd['customizer']) && $show_customizer)
        <input type="checkbox" id="" class="dv_panel_btn" /><label for="dv-customizer-btn"></label>
        @elseif (isset($dd['customizer']))
        <input type="checkbox" id="dv-customizer-btn" class="dv_panel_btn" />
        <label for="dv-customizer-btn">{{ Lang::txt('COM_DATAVIEWER_CUSTOMIZE_VIEW') }}</label>
        @endif
    </span>
</div>
