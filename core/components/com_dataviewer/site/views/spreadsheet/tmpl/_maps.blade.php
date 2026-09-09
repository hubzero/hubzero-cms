@php
/**
 * Maps panel partial (Blade / daisyUI).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

<div id="dv_maps_panel"
    class="relative hidden clear-both w-[800px] h-[300px] p-1 mt-0 bg-base-200 rounded-b-lg dv_top_pannel">
    <button class="btn btn-xs btn-neutral float-left" title="{{ Lang::txt('COM_DATAVIEWER_RELOAD_MAP_TITLE') }}" id="dv_map_reload">
        <i class="icon-refresh"></i> {{ Lang::txt('COM_DATAVIEWER_RELOAD_MAP') }}
    </button>
    <div class="float-right flex gap-1">
        <button data-format="kml" class="btn btn-xs btn-warning dv-btn-download"
            title="{{ Lang::txt('COM_DATAVIEWER_KML_TITLE') }}">
            <i class="icon-download"></i> {{ Lang::txt('COM_DATAVIEWER_KML') }}
        </button>
        <button data-format="kmz" class="btn btn-xs btn-warning dv-btn-download"
            title="{{ Lang::txt('COM_DATAVIEWER_KMZ_TITLE') }}">
            <i class="icon-download"></i> {{ Lang::txt('COM_DATAVIEWER_KMZ') }}
        </button>
        <button data-format="shp" class="btn btn-xs btn-warning dv-btn-download"
            title="{{ Lang::txt('COM_DATAVIEWER_SHP_TITLE') }}">
            <i class="icon-download"></i> {{ Lang::txt('COM_DATAVIEWER_SHP') }}
        </button>
    </div>
    <div id="dv_maps_canvas"
        class="absolute top-[30px] bottom-2 left-1 right-2 border border-base-300 rounded-lg">
    </div>
</div>
