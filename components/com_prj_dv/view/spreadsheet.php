<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2010-2012 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2010-2012 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

function view($name, $dd = false) {
	global $com_name, $html_path, $dv_conf;
	$document = &JFactory::getDocument();

	$document->addScript($html_path . '/excanvas.min.js');
	$document->addScript($html_path . '/dataTables/jquery.dataTables.min.js');
	$document->addStyleSheet($html_path . '/dataTables/jquery.dataTables.css');
	$document->addScript($html_path . '/jquery.tipsy.js');
	$document->addStyleSheet($html_path . '/jquery.tipsy.css');
	$document->addScript($html_path . '/jqplot/jquery.jqplot.js');
	$document->addScript($html_path . '/jqplot/plugins/jqplot.canvasTextRenderer.min.js');
	$document->addScript($html_path . '/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js');
	$document->addScript($html_path . '/jqplot/plugins/jqplot.enhancedLegendRenderer.min.js');
	$document->addScript($html_path . '/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js');
	$document->addScript($html_path . '/jqplot/plugins/jqplot.categoryAxisRenderer.min.js');
	$document->addScript($html_path . '/jqplot/plugins/jqplot.barRenderer.min.js');
	$document->addScript($html_path . '/jqplot/plugins/jqplot.highlighter.min.js');
	$document->addScript($html_path . '/jqplot/plugins/jqplot.cursor.min.js');
	$document->addScript($html_path . '/jqplot/plugins/jqplot.pointLabels.min.js');
	$document->addScript($html_path . '/jqplot/plugins/jqplot.trendline.min.js');
	$document->addScript($html_path . '/jqplot/plugins/jqplot.dataviewfn.js');
	$document->addStyleSheet($html_path . '/jqplot/jquery.jqplot.css');
//	$document->addScript($html_path . '/jquery.highlight.js');
	$document->addScript($html_path . '/spreadsheet.js?v=2013-01-20');
	$document->addStyleSheet($html_path . '/spreadsheet.css');
	$document->addScript($html_path . '/dv_charts.js');
	$document->addScript($html_path . '/dv_maps.js');
	$document->addScript($html_path . '/dv_custom_views.js');
	$document->addStyleSheet($html_path . '/dv_custom_views.css');
	$document->addScript($html_path . '/dv_save_image.js');


	$version_str = isset($_GET['v']) ? '&v=' . $_GET['v'] : '';

	$dv_conf['settings']['url'] = "/?option=com_$com_name&task=data&obj=$name" . $version_str;

	if ($dd) {
		
		$dv_conf['settings']['show_filter_options'] = isset($dd['filter_options'])? $dd['filter_options']: true;
//		$link = get_db();
		$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

		if ($id) {
			$dd['where'][] = array('field'=>$dd['pk'], 'value'=>$id);
			$dd['single'] = true;
		}
		
		// Get the list of IDs if any
		$dv_ids = '';
		if (isset($_GET['id']) && $_GET['id'] != '') {
			$dv_ids = '&id=' . htmlentities($_GET['id']);
			$dataview_url = "/$com_name/spreadsheet/$name/?id=" . htmlentities($_GET['id'] . $version_str);
		} else {
			$dataview_url = "/$com_name/spreadsheet/$name/?dv_first=1" . $version_str;
		}

		// Custom views
		$custom_view = isset($_REQUEST['custom_view'])? explode(',', $_REQUEST['custom_view']): array();
		$custom_view_url = isset($_REQUEST['custom_view'])? "&custom_view=" . htmlentities($_REQUEST['custom_view']): '';
		if (count($custom_view) > 0) {
			unset($dd['customizer']);

			// Custom Title
			if (isset($_REQUEST['custom_title']) && trim($_REQUEST['custom_title']) != '') {
				$dd['title'] = htmlspecialchars(strip_tags($_REQUEST['custom_title']));
			}

			// Custom Group by
			if (isset($_REQUEST['group_by']) && trim($_REQUEST['group_by']) != '') {
				$dd['group_by'] = htmlspecialchars(strip_tags($_REQUEST['group_by']));
			}

			$dv_conf['settings']['url'] .= $custom_view_url;

			// Ordering
			$order_cols = $dd['cols'];
			$dd['cols'] = array();
			foreach($custom_view as $cv_col) {
				$dd['cols'][$cv_col] = $order_cols[$cv_col];
			}

			// Hiding
			foreach($order_cols as $id=>$prop) {
				if (!in_array($id, $custom_view)) {
					$dd['cols'][$id] = $prop;

					if (!isset($dd['cols'][$id]['hide'])) {
						$dd['cols'][$id]['hide'] = 'custom';
					}

				}
			}
		}

		// overrides
		$dv_conf['settings']['limit'] = (isset($dd['display_limit']))? $dd['display_limit']: $dv_conf['settings']['limit'];
		$dv_conf['settings']['hide_data'] = isset($dd['hide_data']);
		$dv_conf['settings']['serverside'] = (isset($dd['serverside']) && $dd['serverside'])? true: false;

		$sql = query_gen($dd);

		$res = get_results($sql, $dd);


		// Customizer View
		$hide_str = '';
		$group_by = '';
		$show_customizer = true;
		if (isset($dd['customizer'])) {
			if (isset($dd['customizer']['show_table']) && !$dd['customizer']['show_table']) {
				$hide_str = 'display: none;';
			}

			if (isset($dd['customizer']['show_customizer']) && !$dd['customizer']['show_customizer']) {
				$show_customizer = false;
			}

			if (isset($dd['group_by'])) {
				$arr = explode(',', $dd['group_by']);
				foreach($arr as $a) {
					$a = trim($a);
					$group_by .= '<div class="dv_customizer_group_by_item_div" style="padding: 3px; margin: 5px; border: 1px #EEE solid;"><input type="checkbox" checked="checked" class="dv_customizer_group_by_item" value="' . $a . '" /> &nbsp;<label style="cursor: pointer;">' . str_replace('<br />', ' ', $dd['cols'][$a]['label']) . '</label></div>';
				}
			}
		}


		$sg_arr = get_sg($dd);
		$dv_conf['settings']['sg'] = $sg_arr;

		$f_data = filter($res, $dd);
		$d_arr = json_decode($f_data, true);

		if(isset($dd['show_maps'])) {
			$document->addScript('//maps.google.com/maps/api/js?sensor=false');
		}

		$help_file = false;

		if (isset($dv_conf['help_file_base_path'])) {
			$help_file = $dv_conf['help_file_base_path'] . $name . '/' . $name . '-help.html';
			if (!file_exists(JPATH_BASE . $help_file)) {
				$help_file = false;
			}
		}

		$document->setTitle($dd['title']);
		$mainframe = &JFactory::getApplication();
		$pathway =& $mainframe->getPathway();

		if(isset($_SERVER['HTTP_REFERER'])) {
			$ref_title = isset($_GET['ref_title'])? htmlentities(strip_tags($_GET['ref_title']), ENT_QUOTES): $dd['title'] . " Resource";
			$pathway->addItem($ref_title, $_SERVER['HTTP_REFERER']);
		}

		$pathway->addItem($dd['title'], $_SERVER['REQUEST_URI']);

		$return = '';
		if(isset($dd['return'])) {
			$return = '<span id="dv_return_link" style="font-size: 1.1em; margin-left: 10px; padding-top: 12px; float: left;"><a href="' . $dd['return']['url'] . '"><strong>' . $dd['return']['label'] . '</strong></a></span>';
		}

		// Filtered Views
		$filted_view = array();
		$filted_view_str = '';
		if (isset($_GET['filter'])) {
			$filted_view_str = '&filter=' . $_GET['filter'];
			$dv_conf['settings']['url'] = $dv_conf['settings']['url'] . '&filter=' . $_GET['filter'];
			$ff = explode('||', $_GET['filter']);
			foreach($ff as $f) {
				$f = explode('|', $f);
				$filted_view[$f[0]] = $f[1];
			}
		}
	?>
	<br />
	<a name="dv_top"></a>
	<div id="spreadsheet_container" class="ss_wrapper">
		<div>
			<span id="dv_top_toolbar" class="ui-widget ui-widget-header ui-corner-top" style="padding: 3px 5px 3px 3px; margin: 0; float: left; border-bottom-width: 0px;">
				<input type="checkbox" id="dv_download"  data-format="csv" class="dv_download_button"><label for="dv_download">Download Data as a CSV file</label>
				<input type="checkbox" id="dv_fullscreen" /><label for="dv_fullscreen">Fullscreen [Ctrl+F11]</label>
				<?php if(isset($dd['filters']) && count($dd['filters'])>0): ?>
				<button id="dv_filter_dialog_btn">Filter Dialog</button>
				<?php endif; ?>
				<?php if($help_file): ?>
				<button id="dv_show_help">Help</button>
				<?php endif; ?>
				<?php if(isset($dd['custom_charts']) || isset($dd['charts_list'])): ?>
				<input type="checkbox" id="dv_charts" class="dv_panel_btn" /><label for="dv_charts">Charts</label>
				<?php endif; ?>
				<?php if(isset($dd['show_maps'])): ?>
				<input type="checkbox" id="dv_maps" class="dv_panel_btn" /><label for="dv_maps">Map</label>
				<?php endif; ?>
				<?php if(isset($dd['customizer']) && $show_customizer): ?>
				<input type="checkbox" checked="checked" id="dv_customizer_btn" class="dv_panel_btn" /><label for="dv_customizer_btn">Show/Hide Data view customizer</label>
				<?php elseif(isset($dd['customizer'])): ?>
				<input type="checkbox" id="dv_customizer_btn" class="dv_panel_btn" /><label for="dv_customizer_btn">Show/Hide Data view customizer</label>
				<?php endif; ?>
				<span id="dv_title" class="ui-widget ui-widget-content ui-corner-all" style="text-shadow: 1px 1px 1px #888; font-size: 1.2em; border-style: inset; padding: 2px 5px 6px 3px; margin-left: 5px;"><?=$dd['title']?></span>
			</span>
			&nbsp;<?=$return?>
		</div>

		<?php if(isset($dd['custom_charts']) || isset($dd['charts_list'])): ?>
		<div id="dv_charts_panel" style="display: none; clear: both; width: 860px; height: 380px; padding: 5px 10px 10px 5px; margin-top: 0;" class="ui-widget ui-widget-header ui-corner-bottom dv_top_pannel">
			<div style="float:left; height: 380px; width: 245px;">
				<div id="dv_charts_control_panel">
				<?php if(isset($dd['charts_list'])): ?>
				<h3><a href="#">Predefined Charts</a></h3>
				<div>
					<strong title='Please Select a chart from the list and click \"View Chart\"'>Select a Chart:</strong><br />
					<select id="dv_chart_name" style="width: 180px;">
						<option value="-1">Select Chart</option>
						<?php $pd_id = 0; foreach($dd['charts_list'] as $cl): ?>
						<option value="<?=$pd_id?>"><?=$cl['title']?></option>
						<?php $pd_id++; endforeach; ?>
					</select>
					<br />
					<input type="button" value="Reload" id="dv_pdcharts_draw_btn" style="margin-top: 10px; margin-bottom: 10px;" />
					<input type="button" value="Download" title="Download chart as an image" id="dv_pdcharts_download_btn" style="margin-top: 10px; margin-bottom: 10px;" />
					<br />
					<div id="dv_chart_desc" class="ui-widget ui-widget-content ui-corner-all" style="font-size: 0.9em; border-style: inset; padding: 2px; width: 180px; height: 185px; overflow: auto;"></div>
				</div>
				<?php endif; ?>
				</div>
			</div>
			<div id="dv_charts_preview_chart" style="height:100%; width:auto; margin-left: 248px;" class="ui-widget-content ui-corner-all"></div>
		</div>
		<?php endif; ?>

		<?php if(isset($dd['show_maps'])): ?>
		<div id="dv_maps_panel" style="position: relative; display: none; clear: both; width: 800px; height: 300px; padding: 3px 5px 3px 5px; margin-top: 0;" class="ui-widget ui-widget-header ui-corner-bottom dv_top_pannel">
			<a data-format="kml" class="dv_download_button" href="#" style="border: 0;" title="Export location data in KML format"><img src="<?=$html_path?>/kml.png" style="border: 0; height: 12px;" /></a>
			<a data-format="kmz" class="dv_download_button" href="#" style="border: 0;" title="Export location data in KMZ format"><img src="<?=$html_path?>/kmz.png" style="border: 0; height: 12px;" /></a>
			<a data-format="shp" class="dv_download_button" href="#" style="border: 0;" title="Export location data in ESRI Shape file format"><img src="<?=$html_path?>/shp.png" style="border: 0; height: 12px;" /></a>
			<a href="#" style="border: 0;" title="Click here to reload the map" alt="Reload button" id="dv_map_reload" style="font-size: 10px;"><img src="<?=$html_path?>/icon-refresh.png" style="border: 0; height: 14px; margin-right: 2px;" />Reload Map</a>
			<div id="dv_maps_canvas" style="position: absolute; top: 20px; bottom: 8px; left: 5px; right: 8px;" class="ui-widget-content ui-corner-all"></div>
		</div>
		<?php endif; ?>

		<?php if(isset($dd['customizer'])): ?>
		<?php 
			$full_list = '';
			$selected = '';
			foreach($dd['cols'] as $id=>$prop) {
				if (!isset($prop['hide']) || (isset($prop['hide']) && $prop['hide'] != 'hide')) {
					if (!isset($prop['label'])) {
						$prop['label'] = $id;
					}
					if (isset($dd['customizer']['selected']) && in_array($id, $dd['customizer']['selected'])) {
						$first_col = true;
						$selected .= '<li data-dv-id="' . $id . '">' . str_replace('<br />', ' ', $prop['label']) . '</li>';
					} else {
						$full_list .= '<li data-dv-id="' . $id . '">' . str_replace('<br />', ' ', $prop['label']) . '</li>';
					}
				}
			}
		?>
		<div id="dv_customizer_panel" style="display: none; clear: both;  padding: 5px 10px 10px 5px; margin-top: 0;" class="ui-widget ui-widget-header ui-corner-bottom dv_top_pannel">
			<div id="dv_customizer_content" style="height:100%; width:auto;" class="ui-widget-content ui-corner-all">
				<div id="dv_customizer_group_by" title="Customizer Group Rows" style="display: none; width: 500px; height: 350px;">
					<p>You can use this option to group rows of the views by certain fields to reduce duplicate rows</p>
					<div id="dv_customizer_group_by_list"><?=$group_by?></div>
				</div>
				<p id="dv_customizer_title_container" style="padding-left: 10px;">
					New Title: <input id="dv_customizer_view_title" value="<?=$dd['title']?>" type="text" style="width: 550px;" />
					&nbsp;&nbsp;
					<input id="dv_customizer_group_by_btn" data-view-url="<?=$dataview_url?>" value="Group By [Reduce Duplicates]" type="button" style="display: none; padding: 2px;" />
					&nbsp;&nbsp;
					<input id="dv_customizer_launch_view_btn" data-view-url="<?=$dataview_url?>" value="Launch Custom View" type="button" style="display: none; padding: 2px;" />
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
								<?=$full_list?>
							</ul>
							</div>
						</td>

						<td>
							<div class="dv_customizer_lists" style="overflow: auto;">
							<ul id="dv_customizer_selected" class="dv_customizer_col_lists">
								<?=$selected?>
							</ul>
							</div>
						</td>
					</tr>
				</table>

			</div>
		</div>
		<?php endif; ?>

		<div id="more_information" style="display: none;"></div>
		<?php if($help_file): ?>
		<div id="dv_help_dialog" style="display: none;">
			<iframe src="<?=$help_file?>" id="modalIframeId" width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" title="Help">IFRAMES not supported by the browser</iframe>
			</div>
		</div>
		<?php endif; ?>

		<div id="dv_table_container" style="margin: 0px; padding; 0px; <?=$hide_str?>">
		<table id="spreadsheet" style="margin-top: 0;">
	<?php
		print '<thead></thead><tfoot><tr>';
		$colid = 0;
		foreach ($dd['cols'] as $id => $conf) {
			if(!isset($conf['hide'])) {
				$label = isset($conf['label']) ? $conf['label'] : $id;
				$label = str_replace('<br />', ' ', $label);
				$label = html_entity_decode(strip_tags($label), ENT_QUOTES, 'UTF-8');
				$title = '';
				$filter_msg = '';
				if ($dv_conf['settings']['serverside']) {
					$filter_msg = "  \n\nThe dropdown list only shows a limited number of available options.  \n\nIf you don't see what you want on the list, please enter a filter text in the text box and then press Enter to bring up more results to match your text.";
				}
				
				if (isset($conf['filter_hint'])) {
					$filter_hint = $conf['filter_hint'];
				} else {
					$filter_hint = "[Column: $label]  \n\n" . "Enter a word or a phrase to filter this column by.";

					if ($d_arr['field_types'][$id] == 'number' || $d_arr['field_types'][$id] == 'numrange') {
						$filter_hint = "[Column: $label]  \n\n" . "Enter a number to filter this column by.";
						$filter_hint .= "  \n\nFollowing filter options are also supported,";
						$filter_hint .= "	\nRange filtering - ( e.g. 15.7 to 25 )";
						$filter_hint .= "	\nLess than, greater than ( e.g. <100 ), (e.g. >25)";
						$filter_hint .= "	\nLess than or equal, greater than or equal ( e.g. <=-12.5 ), (e.g. >=0.3)";
						$filter_hint .= "	\nEqual ( e.g. =-2.55 )";
					} elseif ($d_arr['field_types'][$id] == 'datetime') {
						$filter_hint = "[Column: $label]  \n\n" . "Enter a date to filter this column by.";
						$filter_hint .= "	\nRange filtering - ( e.g. 2011-01-25 to 2011-03-25 )";
						$filter_hint .= "	\nLess than, greater than ( e.g. <2011-03-25 ), (e.g. >2011-01-25)";
						$filter_hint .= "	\nLess than or equal, greater than or equal ( e.g. <=2011-03-25 ), (e.g. >=2010-03-25)";
						$filter_hint .= "	\nEqual ( e.g. =2009-01-17 )";
					}
				}


				$title = $filter_hint . $filter_msg;

				if (isset($conf['type']) && $conf['type'] == 'image') {
					print '<th><input title="' . $title . '" type="text" name="' . $label . '" value="' . $label . '" disabled=disabled /></th>';
				} elseif (isset($filted_view[$id])) {
					print '<th><input type="text" name="' . $label . '" value="' . $filted_view[$id] . '" disabled=disabled style="background: yellow;" /></th>';
				} else {
					print '<th><input title="' . $title . '" type="text" name="' . $label . '" value="' . $label . '" class="search_init" /></th>';
				}
			}

			$colid++ ;
		}
		print '</tr></tfoot>';
	?>
		</table>
		<br />
		<input type="button" value="Clear column filters" id="clear_column_filters">
		<input type="button" value="Clear all filters" id="clear_all_filters">
		</div>
	<?php
		if (isset($dd['search_groups'])) {
			print '<div id="ss_sg" title="Search Groups" style="">';
			$i = 0;
			foreach ($sg_arr as $group) {
	?>
		<div class="ui-state-highlight ui-corner-all param_tbl" style="margin-top: 20px; padding: 0 .7em;">
			<span title="Click to expand" class="collapsible-button" style="width: 400px; cursor: pointer;">
				<span class="ui-icon ui-icon-plus" style="float: left; margin-right: .3em;"></span>
				<strong><?=$group['name']?></strong>
			</span>
			&nbsp;&nbsp;<input name="<?=$i?>" class="ss_sg_input" type="text">
			<br />
			<div class="collapsible" style="display: none;">
			<br />
			<strong>Search applys to :
			<? foreach ($group['columns'] as $col): ?>
			[ <span name="<?=$col['idx']?>" class="ss_sg_columns" style="cursor: pointer;"><?=$col['label']?></span> ]&nbsp;
			<? endforeach; ?>
			</strong>
			<br /><br />
			</div>
		</div>
	<?php
				$i++;
			}
			print '</div>';
		}
		
		// Filter dialog show/hide parameter
		$dv_show_filters = 'false';
		$u =& JFactory::getURI();
		$path = explode('/', $u->_path);
		if (isset($path[4]) && $path[4] == 'filter_dialog') {
			$dv_show_filters = 'true';
		}
		//Legacy support
		if (isset($_GET['show_filters']) && $_GET['show_filters'] == 'true') {
			$dv_show_filters = 'true';
		}
	?>
		<script>
			jQuery = dv_jQuery; // Replacing jQuery if jQuery older version was loaded
			dv_data = <?=$f_data?>;
			dv_settings = <?=json_encode($dv_conf['settings'])?>;
			dv_show_filters = <?=$dv_show_filters?>;
			dv_show_chart = <?=(isset($_GET['show_chart']))? htmlentities(trim($_GET['show_chart'])): 'undefined';?>;
			dv_show_customizer = <?=($show_customizer)? 'true': 'false';?>;
			var dv_show_maps = <?=(isset($_GET['show_map']))? htmlentities(trim($_GET['show_map'])): 'undefined';?>;

			jQuery(function($) {
				$("#dv_charts_panel").resizable({
					minHeight: 380,
					minWidth: 800
				});
				$("#dv_maps_panel").resizable();

				$("#dv_download").button({
					text: false,
					icons: {
						primary: "ui-icon-disk"
					}
				});

				$("#dv_show_help").button({
					text: true,
					icons: {
						primary: "ui-icon-help"
					}
				});

				$("#dv_filter_dialog_btn").button({
					//text: false,
					//icons: {
					//	primary: "ui-icon-newwin"
					//}
				});

				$("#dv_fullscreen").button({
					text: false,
					icons: {
						primary: "ui-icon-newwin"
					}
				});

				$("#dv_customizer_btn").button({
					text: false,
					icons: {
						primary: "ui-icon-gear"
					}
				});

				$("#dv_charts").button({
					text: true,
					icons: {
						primary: "ui-icon-image"
					}
				});

				$("#dv_maps").button({
					text: true,
					icons: {
						primary: "ui-icon-flag"
					}
				});

				$('#dv_charts_control_panel').accordion({
					fillSpace: true
				});

			});

		</script>
		<div id="" style="display: none;">
			<form id="ss_download_form" method="POST" action="/?option=com_<?=$com_name?>&task=data&obj=<?=$name?>&nolimit=true<?=$version_str . $filted_view_str . $custom_view_url . $dv_ids?>">
			</form>
		</div>
		<div id="truncated_text_dialog" style="display: none; overflow: auto;" title="Full Text"></div>
		<div id="dv_filters_dialog" title="<?=$dd['title']?> : Filters">
			<div id="dv_filters_tabs">
				<ul></ul>
			</div>
		</div>
	</div>
	<?php
	} else {
		print "Error: Not implemented";
	}
}

function get_sg($dd)
{
	if (!isset($dd['search_groups'])) {
		return array();
	}

	$col_idx = array();

	$idx = 0;
	foreach($dd['cols'] as $k=>$v) {
		if (!isset($v['hide'])) {
			$label = isset($v['label'])? $v['label']: $k;
			$col_idx[$k] = array('label'=>$label, 'idx'=>$idx);
			$idx++;
		}
	}

	$sg_arr = array();
	foreach ($dd['search_groups'] as $sg) {
		$group = array();
		$group['name'] = $sg['name'];
		foreach($sg['columns'] as $sgc) {
			$group['columns'][] = array('idx'=>$col_idx[$sgc]['idx'], 'label'=>$col_idx[$sgc]['label']);
		}
		$sg_arr[] = $group;
	}

	return $sg_arr;
}
?>
