@php
/**
 * Spreadsheet view template (Blade / daisyUI).
 *
 * Renders the interactive DataTables spreadsheet with toolbar,
 * charts, maps, and customizer panels.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$return         = $return_link;

// Load assets
// Component-specific vendor libraries
\Components\Dataviewer\Site\Lib\Html::dvAddScript('js/vendor/datatables/jquery.dataTables.min.js');
\Components\Dataviewer\Site\Lib\Html::dvAddCss('js/vendor/datatables/css/jquery.dataTables_themeroller.css');
\Components\Dataviewer\Site\Lib\Html::dvAddCss('js/vendor/datatables/css/jquery.dataTables_dv.css');
\Components\Dataviewer\Site\Lib\Html::dvAddScript('js/vendor/jqplot/jquery.jqplot.min.js');
\Components\Dataviewer\Site\Lib\Html::dvAddScript('js/vendor/jqplot/plugins.dev');
\Components\Dataviewer\Site\Lib\Html::dvAddCss('js/vendor/jqplot/jquery.jqplot.css');

// Component JS
\Components\Dataviewer\Site\Lib\Html::dvAddScript('js/util.js');
\Components\Dataviewer\Site\Lib\Html::dvAddScript('js/dv-config-init.js');
\Components\Dataviewer\Site\Lib\Html::dvAddScript('js/datatables-plugins.js');
\Components\Dataviewer\Site\Lib\Html::dvAddScript('js/spreadsheet.js');
\Components\Dataviewer\Site\Lib\Html::dvAddScript('js/dv-spreadsheet-charts.js');
\Components\Dataviewer\Site\Lib\Html::dvAddScript('js/dv-spreadsheet-charts-dl.js');
\Components\Dataviewer\Site\Lib\Html::dvAddScript('js/custom-views.js');
\Components\Dataviewer\Site\Lib\Html::dvAddScript('js/jquery.lazyload.min.js');

// Component CSS
\Components\Dataviewer\Site\Lib\Html::dvAddCss('css/spreadsheet.css');
\Components\Dataviewer\Site\Lib\Html::dvAddCss('css/custom-views.css');

if ($showMaps) {
    \Components\Dataviewer\Site\Lib\Html::dvAddScript('js/vendor/leaflet/leaflet.js');
    \Components\Dataviewer\Site\Lib\Html::dvAddCss('js/vendor/leaflet/leaflet.css');
    \Components\Dataviewer\Site\Lib\Html::dvAddScript('js/maps.js');
}

\Components\Dataviewer\Site\Lib\Html::dvAddScript('js/dv-core.js');
@endphp

<span id="dv_top"></span>
<div id="dv-spreadsheet">

@if (!Hubzero\Facades\Request::getString('show_table_only', false))
    @include('com_dataviewer::site.views.spreadsheet.tmpl._toolbar')

    @if ($showCharts)
        @include('com_dataviewer::site.views.spreadsheet.tmpl._charts')
    @endif

    @if ($showMaps)
        @include('com_dataviewer::site.views.spreadsheet.tmpl._maps')
    @endif

    @if (isset($dd['customizer']))
        @include('com_dataviewer::site.views.spreadsheet.tmpl._customizer')
    @endif

    <div id="more_information" class="hidden min-w-[300px]"></div>

    @if ($help_file)
    <div id="dv_help_dialog" class="hidden">
        <iframe src="{{ $help_file }}" id="modalIframeId"
            width="100%" height="100%" marginWidth="0" marginHeight="0"
            frameBorder="0" scrolling="auto" title="{{ Lang::txt('COM_DATAVIEWER_HELP') }}">
            {{ Lang::txt('COM_DATAVIEWER_IFRAMES_NOT_SUPPORTED') }}
        </iframe>
    </div>
    @endif
@endif

    @include('com_dataviewer::site.views.spreadsheet.tmpl._table')

    {{-- Dialog boxes --}}
    <div id="truncated_text_dialog" class="hidden overflow-auto" title="{{ Lang::txt('COM_DATAVIEWER_FULL_TEXT') }}"></div>
    <div id="dv_filters_dialog" title="{{ $dd['title'] }} : {{ Lang::txt('COM_DATAVIEWER_FILTERS') }}">
        <div id="dv_filters_tabs">
            <ul></ul>
        </div>
    </div>

    {{-- Configuration for JS (CSP-compliant data attributes) --}}
    <div id="dv-config"
        data-dv-data="{{ $f_data }}"
        data-dv-settings="{{ json_encode($settings) }}"
        data-dv-show-filters="{{ $dv_show_filters ? 'true' : 'false' }}"
        data-dv-show-chart="{{ Hubzero\Facades\Request::getInt('show_chart', 0) }}"
        data-dv-show-customizer="{{ $show_customizer ? 'true' : 'false' }}"
        data-dv-show-map="{{ Hubzero\Facades\Request::getString('show_map', '') }}"
        class="hidden">
    </div>

    <form class="hidden" id="dv-spreadsheet-dl" method="POST"
        action="{{ $settings['data_url'] }}&amp;nolimit=true{{ $custom_view_url . $custom_field_url }}">
    </form>
</div>
