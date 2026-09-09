<?php
/**
 * Spreadsheet view template (legacy PHP).
 *
 * Renders the interactive DataTables spreadsheet with toolbar,
 * charts, maps, and customizer panels.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$dd             = $this->dd;
$settings       = $this->settings;
$f_data         = $this->f_data;
$d_arr          = $this->d_arr;
$show_customizer = $this->show_customizer;
$hide_str       = $this->hide_str;
$group_by       = $this->group_by;
$help_file      = $this->help_file;
$return         = $this->return_link;
$filtered_view  = $this->filtered_view;
$dv_show_filters = $this->dv_show_filters;
$showMaps       = $this->showMaps;
$showCharts     = $this->showCharts;
$full_list      = $this->full_list;
$selected_list  = $this->selected_list;
$htmlPath       = $this->htmlPath;

// Load assets via the document
$document = \Hubzero\Facades\App::get('document');

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
?>
<span id="dv_top"></span>
<div id="dv-spreadsheet">

<?php if (!\Hubzero\Facades\Request::getString('show_table_only', false)) : ?>
    <div id="dv_title" style="margin: 0;">
        <h2 class="ui-corner-all" style="display: inline-block;">
            <i class="icon-table"></i>
            <?php echo $dd['title']; ?>
        </h2>
        &nbsp;<h4 id="dv_return_link_container" style="display: inline-block; margin: 0;">
            <?php echo $return; ?>
        </h4>
    </div>
    <div id="dv-spreadsheet-toolbar" class="ui-corner-top">
        <?php
        $dlMenu = $dd['top_menu']['download']['show'] ?? null;
        $showDl = !isset($dlMenu) || $dlMenu != false;
        ?>
        <?php if ($showDl) : ?>
        <button class="btn btn-mini dv-btn-download" data-format="csv" title="Download Data as a spreadsheet">
            <i class="icon-download"> </i>
            <span class="lbl">Download</span>
        </button>
        <?php endif; ?>

        <?php if (!isset($dd['top_menu']['fullscreen']['show'])
            || $dd['top_menu']['fullscreen']['show'] != false) : ?>
        <button id="dv-btn-fullscreen" class="btn btn-mini" title="Fullscreen" data-screen-mode=''>
            <i class="icon-fullscreen"> </i>
            <span class="lbl">Fullscreen</span>
        </button>
        <?php endif; ?>

        <?php if (isset($dd['filters']) && count($dd['filters']) > 0) : ?>
        <button id="dv-btn-filters" class="btn btn-mini" title="Filter Dialog">
            <i class="icon-filter"> </i>
            <span class="lbl">Filter Dialog</span>
        </button>
        <?php endif; ?>

        <button id="dv-btn-filter-clear-all" class="btn btn-mini"
            title="Click this to clear all the column filters and the global search">
            <i class="icon-remove-circle"> </i>
            <span class="lbl">Clear Filters</span>
        </button>

        <?php
        $wrapMenu = $dd['top_menu']['no_wrap']['show'] ?? null;
        $showWrap = !isset($wrapMenu) || $wrapMenu != false;
        ?>
        <?php if ($showWrap) : ?>
        <button id="dv-btn-no-wrap" class="btn btn-mini"
            title="Disable text wrapping for all cells." data-current="normal">
            <i class="icon-text-width"> </i>
            <span class="lbl">No-Wrap</span>
        </button>
        <?php endif; ?>

        <?php if ($showCharts) : ?>
        <button id="dv-spreadsheet-charts" class="btn btn-mini" title="Display charts">
            <i class="icon-bar-chart"> </i>
            <span class="lbl">Charts</span>
        </button>
        <?php endif; ?>

        <?php if ($showMaps) : ?>
        <button id="dv-spreadsheet-maps" class="btn btn-mini" title="Display Maps">
            <i class="icon-map-marker"></i>
            <span class="lbl">Maps</span>
        </button>
        <?php endif; ?>

        <?php if (isset($dd['customizer'])) : ?>
        <button id="dv-customizer-btn" class="btn btn-mini"
            title="Enables users to select a sub-set of columns to view.">
            <i class="icon-edit"> </i>
            <span class="lbl">Customize DataView</span>
        </button>
        <?php endif; ?>
    </div>

    <div style="display: none;">
        <span id="dv_top_toolbar" class="ui-corner-top"
            style="padding: 3px 5px 3px 3px; margin: 0; border-style: inset; border-bottom-width: 0px;">
            <?php if ($help_file) : ?>
            <button id="dv_show_help">Help</button>
            <?php endif; ?>
            <?php if ($showMaps) : ?>
            <input type="checkbox" id="dv_maps" class="dv_panel_btn" /><label for="dv_maps">Map</label>
            <?php endif; ?>
            <?php if (isset($dd['customizer']) && $show_customizer) : ?>
            <input type="checkbox" id="" class="dv_panel_btn" /><label for="dv-customizer-btn"></label>
            <?php elseif (isset($dd['customizer'])) : ?>
            <input type="checkbox" id="dv-customizer-btn" class="dv_panel_btn" />
            <label for="dv-customizer-btn">Customize View</label>
            <?php endif; ?>
        </span>
    </div>

    <?php if ($showCharts) : ?>
    <div id="dv_charts_panel"
        style="display: none; clear: both; width: 860px; height: 380px; padding: 5px 10px 10px 5px;
            margin-top: 0; border: 1px solid #DDD; background: #EEE;"
        class="ui-corner-bottom dv_top_pannel">
        <button id="dv_pdcharts_download_btn" class="btn btn-mini btn-success"
            title="Download chart as an image" style="float: right; z-index: 1; margin: 3px;">
            <i class="icon-download"> </i>
            <span class="lbl">Download Chart</span>
        </button>
        <button id="dv_pdcharts_draw_btn" class="btn btn-mini btn-info"
            title="Reload charts" style="float: right; z-index: 1; margin: 3px;">
            <i class="icon-repeat"> </i>
            <span class="lbl">Reload</span>
        </button>

        <div style="float:left; height: 380px; width: 245px;">
            <div id="dv_charts_control_panel" style="padding: 0 5px;">
                <?php if (isset($dd['charts_list'])) : ?>
                <select id="dv_chart_name" style="width: 100%;">
                    <?php $pd_id = 0; ?>
                    <?php foreach ($dd['charts_list'] as $cl) : ?>
                    <option value="<?php echo $pd_id; ?>"><?php echo $cl['title']; ?></option>
                    <?php $pd_id++; ?>
                    <?php endforeach; ?>
                </select>
                <div id="dv_chart_desc" class="ui-widget-content ui-corner-all"
                    style="margin-top: 10px; font-size: 0.9em; border-style: inset; padding: 2px;
                        overflow: auto; height: 340px;">
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div id="dv_charts_preview_chart" style="height:100%; width:auto; margin-left: 248px;"
            class="ui-widget-content ui-corner-all">
        </div>
    </div>
    <?php endif; ?>

    <?php if ($showMaps) : ?>
    <div id="dv_maps_panel"
        style="position: relative; display: none; clear: both; width: 800px; height: 300px;
            padding: 3px 5px 3px 5px; margin-top: 0;"
        class="ui-widget ui-widget-header ui-corner-bottom dv_top_pannel">
        <button class="btn btn-inverse btn-mini" title="Click here to reload the map"
            id="dv_map_reload" style="float: left;">
            <i class="icon-refresh"></i> Reload Map
        </button>
        <div style="float: right;">
            <button data-format="kml" class="btn btn-inverse btn-mini dv-btn-download"
                style="background: #FAA732; background-image: linear-gradient(to bottom, #FBB450, #F89406);
                    background-repeat: repeat-x;"
                title="Export location data in KML format">
                <i class="icon-download"></i> KML
            </button>
            <button data-format="kmz" class="btn btn-inverse btn-mini dv-btn-download"
                style="background: #FAA732; background-image: linear-gradient(to bottom, #FBB450, #F89406);
                    background-repeat: repeat-x;"
                title="Export location data in KMZ format">
                <i class="icon-download"></i> KMZ
            </button>
            <button data-format="shp" class="btn btn-inverse btn-mini dv-btn-download"
                style="background: #FAA732; background-image: linear-gradient(to bottom, #F89406, #F89406);
                    background-repeat: repeat-x;"
                title="Export location data in SHP format">
                <i class="icon-download"></i> SHP
            </button>
        </div>
        <div id="dv_maps_canvas"
            style="position: absolute; top: 30px; bottom: 8px; left: 5px; right: 8px;"
            class="ui-widget-content ui-corner-all">
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($dd['customizer'])) : ?>
    <div id="dv_customizer_panel"
        style="display: none; clear: both; padding: 5px 10px 10px 5px; margin-top: 0;"
        class="ui-widget ui-widget-header ui-corner-bottom dv_top_pannel">
        <div id="dv_customizer_content" style="height:100%; width:auto;"
            class="ui-widget-content ui-corner-all">
            <div id="dv_customizer_group_by" title="Customizer Group Rows"
                style="display: none; width: 500px; height: 350px;">
                <p>You can use this option to group rows of the views by certain fields to reduce duplicate rows</p>
                <div id="dv_customizer_group_by_list"><?php echo $group_by; ?></div>
            </div>
            <p id="dv_customizer_title_container" style="padding-left: 10px;">
                New Title: <input id="dv_customizer_view_title"
                    value="<?php echo $this->escape($dd['title']); ?>" type="text" style="width: 550px;" />
                &nbsp;&nbsp;
                <input id="dv_customizer_group_by_btn"
                    data-view-url="<?php echo $this->escape($settings['view_url']); ?>"
                    value="Group By [Reduce Duplicates]" type="button"
                    style="display: none; padding: 2px;" />
                &nbsp;&nbsp;
                <input id="dv_customizer_launch_view_btn"
                    data-view-url="<?php echo $this->escape($settings['view_url']); ?>"
                    value="Launch Custom View" type="button"
                    style="display: none; padding: 2px;" />
            </p>
            <table border="0">
                <tr id="dv_customizer_lists_top">
                    <td style="width: 400px;">Full Columns List</td>
                    <td style="width: 400px;">Custom List</td>
                </tr>
                <tr>
                    <td>
                        <div class="dv_customizer_lists" style="overflow: auto;">
                        <ul id="dv_customizer_full_list" class="dv_customizer_col_lists">
                            <?php echo $full_list; ?>
                        </ul>
                        </div>
                    </td>
                    <td>
                        <div class="dv_customizer_lists" style="overflow: auto;">
                        <ul id="dv_customizer_selected" class="dv_customizer_col_lists">
                            <?php echo $selected_list; ?>
                        </ul>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div id="more_information" style="display: none; min-width: 300px;"></div>
    <?php if ($help_file) : ?>
    <div id="dv_help_dialog" style="display: none;">
        <iframe src="<?php echo $this->escape($help_file); ?>" id="modalIframeId"
            width="100%" height="100%" marginWidth="0" marginHeight="0"
            frameBorder="0" scrolling="auto" title="Help">
            IFRAMES not supported by the browser
        </iframe>
    </div>
    <?php endif; ?>

<?php endif; // Table Only ?>

    <div id="dv-spreadsheet-container" style="margin: 0px; padding: 0px; <?php echo $hide_str; ?>">
    <table id="dv-spreadsheet-tbl" style="margin-top: 0;">
        <thead></thead>
        <tfoot>
            <tr>
            <?php foreach ($dd['cols'] as $id => $conf) : ?>
                <?php if (!isset($conf['hide'])) : ?>
                    <?php
                    $label = isset($conf['label']) ? $conf['label'] : $id;
                    $label = str_replace('<br />', ' ', $label);
                    $label = html_entity_decode(strip_tags($label), ENT_QUOTES, 'UTF-8');

                    $filter_msg = '';
                    if ($settings['serverside']) {
                        $filter_msg = "  \n\nThe dropdown list only shows a limited number of available options."
                            . "  \n\nIf you don't see what you want on the list, please enter a filter text"
                            . " in the text box and then press Enter to bring up more results to match your text.";
                    } else {
                        $filter_msg = "\t\n\nClick on the search box to list all the entries in the column";
                    }

                    if (isset($conf['filter_hint'])) {
                        $filter_hint = $conf['filter_hint'];
                    } elseif (
                        $d_arr['field_types'][$id] == 'number'
                        || $d_arr['field_types'][$id] == 'numrange'
                    ) {
                        $filter_hint = "Enter a number to filter this column by."
                            . "  \n\nFollowing filter options are also supported,"
                            . "\t\nRange filtering - ( e.g. 15.7 to 25 )"
                            . "\t\nLess than, greater than ( e.g. <100 ), (e.g. >25)"
                            . "\t\nLess than or equal, greater than or equal"
                            . " ( e.g. <=-12.5 ), (e.g. >=0.3)"
                            . "\t\nEqual, not equal and ignore pattern"
                            . " ( e.g. =-2.55 ), ( e.g. !=-2.55 ), ( e.g. !55 )";
                    } elseif ($d_arr['field_types'][$id] == 'datetime') {
                        $filter_hint = "Enter a date to filter this column by."
                            . "\t\nRange filtering - ( e.g. 2011-01-25 to 2011-03-25 )"
                            . "\t\nLess than, greater than ( e.g. <2011-03-25 ), (e.g. >2011-01-25)"
                            . "\t\nLess than or equal, greater than or equal"
                            . " ( e.g. <=2011-03-25 ), (e.g. >=2010-03-25)"
                            . "\t\nEqual, not equal and ignore pattern"
                            . " ( e.g. =2009-01-17 ), ( e.g. !=2009-01-17 ), ( e.g. !2009-01 )";
                    } else {
                        $filter_hint = "Enter a word or a phrase to filter this column by."
                            . "  \n\nFollowing filter options are also supported,"
                            . "\t\nExact matches, use '=' ( e.g. =keyword)"
                            . "\t\nTo ignore a specific word, use '!=' ( e.g. !=keyword)"
                            . "\t\nTo ignore a pattern, use '!' ( e.g. !keyword )";
                    }

                    $title = $filter_hint . $filter_msg;
                    ?>
                    <?php if (isset($conf['type']) && $conf['type'] == 'image') : ?>
                <th><input title="<?php echo $this->escape($title); ?>" type="text"
                    placeholder="<?php echo $this->escape($label); ?>" disabled="disabled" /></th>
                    <?php elseif (isset($filtered_view[$id])) : ?>
                <th><input type="text"
                    placeholder="<?php echo $this->escape($filtered_view[$id]); ?>"
                    disabled="disabled" style="background: yellow;" /></th>
                    <?php else : ?>
                <th><input title="<?php echo $this->escape($title); ?>" type="text"
                    placeholder="<?php echo $this->escape($label); ?>"
                    class="search_init" style="background: #FFF;" /><span class="dv-col-clear-filter"
                    style="margin-left: -15px; color: #FFF; cursor: pointer;"><i class="icon-remove-sign"></i></span></th>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            </tr>
        </tfoot>
    </table>
    </div>

    <!-- Dialog boxes -->
    <div id="truncated_text_dialog" style="display: none; overflow: auto;" title="Full Text"></div>
    <div id="dv_filters_dialog" title="<?php echo $this->escape($dd['title']); ?> : Filters">
        <div id="dv_filters_tabs">
            <ul></ul>
        </div>
    </div>

    <!-- Configuration for JS (CSP-compliant data attributes) -->
    <div id="dv-config"
        data-dv-data="<?php echo $this->escape($f_data); ?>"
        data-dv-settings="<?php echo $this->escape(json_encode($settings)); ?>"
        data-dv-show-filters="<?php echo $dv_show_filters ? 'true' : 'false'; ?>"
        data-dv-show-chart="<?php echo $this->escape(\Hubzero\Facades\Request::getInt('show_chart', 0)); ?>"
        data-dv-show-customizer="<?php echo $show_customizer ? 'true' : 'false'; ?>"
        data-dv-show-map="<?php echo $this->escape(\Hubzero\Facades\Request::getString('show_map', '')); ?>"
        style="display: none;">
    </div>

    <form style="display: none;" id="dv-spreadsheet-dl" method="POST"
        action="<?php
        echo $this->escape($settings['data_url']);
        ?>&amp;nolimit=true<?php
        echo $this->escape($this->custom_view_url . $this->custom_field_url);
        ?>">
    </form>
</div>
