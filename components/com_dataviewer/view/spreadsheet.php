<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2012,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


function view($dd = false) {
	global $com_name, $html_path, $dv_conf;
	$name = $dd['dv_id'];

	$document = &JFactory::getDocument();

//	dv_add_script('modernizr.js');

	dv_add_script('util.js');

	/* jQuery */
	dv_add_script('jquery.js');
//	dv_add_script('jquery-migrate.js');

	/* Bootstrap */
	dv_add_script('bootstrap/bootstrap.js');
	dv_add_css('bootstrap/css/bootstrap.css');

	/* jQuery-UI */
	dv_add_script('jquery-ui/jquery-ui.js');
	dv_add_css('jquery-ui/smoothness/jquery-ui.min.css');


	dv_add_css('font-awesome/css/font-awesome.css');

	dv_add_script('excanvas.js');

	dv_add_script('jquery-datatables/jquery.dataTables.js');
	dv_add_css('jquery-datatables/css/jquery.dataTables_themeroller.css');
	dv_add_css('jquery-datatables/css/jquery.dataTables_dv.css');

	dv_add_script('datatables.plugins.js');

//	$document->addScript($html_path . '/jquery.tipsy.js' . $ver);
//	$document->addStyleSheet($html_path . '/jquery.tipsy.css' . $ver);

	dv_add_script('jqplot/jquery.jqplot.js');
	dv_add_script('jqplot/plugins.dev');
	dv_add_css('jqplot/jquery.jqplot.css');

	dv_add_script('spreadsheet.js');
	dv_add_css('spreadsheet.css');

	dv_add_script('dv-spreadsheet-charts.js');
	dv_add_script('dv-spreadsheet-charts-dl.js');
//	$document->addScript($html_path . '/dv_maps.js' . $ver);
//	$document->addScript($html_path . '/dv_custom_views.js' . $ver);
//	$document->addStyleSheet($html_path . '/dv_custom_views.css' . $ver);

	dv_add_script('jquery.dv.js');


	$dv_conf['settings']['view']['id'] = $dd['dv_id'];
	$dv_conf['settings']['view']['type'] = 'spreadsheet';
	$dv_conf['settings']['data_url'] = "/?option=com_$com_name&task=data&db=" . $dd['db_id']['id'] . '&dv=' . $dd['dv_id'];


	// Get the list of IDs if any
	$rec_ids = JRequest::getVar('id', '');
	if ($rec_ids != '') {
		$dv_conf['settings']['data_url'] .= '&id=' . htmlentities($rec_ids);
		$dataview_url = "/$com_name/spreadsheet/$name/?id=" . htmlentities($rec_ids);
	} else {
		$dataview_url = "/$com_name/spreadsheet/$name/?dv_first=1";
	}

	if ($dd) {
		
		$dv_conf['settings']['show_filter_options'] = isset($dd['filter_options'])? $dd['filter_options']: true;

		$custom_field_url = '';
		$custom_field = JRequest::getVar('custom_field', false);
		if ($custom_field) {
			$custom_field_url = '&custom_field=' . $custom_field;
		}

		// Custom views
		$custom_view = JRequest::getVar('custom_view', false);
		$custom_view_url = '';
		if ($custom_field) {
			$custom_view_url = '&custom_view=' . $custom_field;
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
					$lbl = isset($dd['cols'][$a]['label'])? $dd['cols'][$a]['label']: $a;
					$group_by .= '<div class="dv_customizer_group_by_item_div" style="padding: 3px; margin: 5px; border: 1px #EEE solid;"><input type="checkbox" checked="checked" class="dv_customizer_group_by_item" value="' . $a . '" /> &nbsp;<label style="cursor: pointer;">' . str_replace('<br />', ' ', $lbl) . '</label></div>';
				}
			}
		}


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


		$return = '';
		if(isset($dd['return']) && isset($dd['return']['raw'])) {
			$return = $dd['return']['raw'];
		} elseif (isset($dd['return'])) {
			$return = '<span id="dv_return_link" style="font-size: 1.1em; margin-left: 10px; padding-top: 12px;"><a href="' . $dd['return']['url'] . '"><strong>' . $dd['return']['label'] . '</strong></a></span>';
		}

		// Filtered Views
		$filter = JRequest::getVar('filter', false);
		$filted_view = array();
		$filted_view_str = '';
		if ($filter !== false) {
			$dv_conf['settings']['data_url'] .= '&filter=' . $filter;
			$dv_conf['settings']['filters']['fv_vals'] = $filter;
			$ff = explode('||', $filter);
			foreach($ff as $f) {
				$f = explode('|', $f);
				$filted_view[$f[0]] = $f[1];
			}
		}
	?>
	<a name="dv_top"></a>
	<div id="dv-spreadsheet" class="ss_wrapper" style="padding: 20px 10px;">
	
	<?php if(!JRequest::getVar('show_table_only', false)): ?>

		<div id="dv_title" style="margin: 0;">
			<h2 class="ui-corner-all" style="display: inline;">
				<i class="icon-table"></i>
				<?=$dd['title']?>
			</h2>
			&nbsp;<h4 id="dv_return_link_container" style="display: inline;"><?=$return?></h4>
		</div>
		<div id="dv-spreadsheet-toolbar" class="ui-corner-top">
			<button class="btn btn-mini dv-btn-download" data-format="csv" title="Download Data as a spreadsheet">
				<i class="icon-download"> </i >
				<span class="lbl">Download</span>
			</button>
			<button id="dv-btn-fullscreen" class="btn btn-mini" title="Fullscreen" data-screen-mode=''>
				<i class="icon-fullscreen"> </i >
				<span class="lbl">Fullscreen</span>
			</button>
			<?php if(isset($dd['filters']) && count($dd['filters'])>0): ?>
			<button id="dv-btn-filters" class="btn btn-mini" title="Filter Dialog">
				<i class="icon-filter"> </i >
				<span class="lbl">Filter Dialog</span>
			</button>
			<?php endif; ?>
			<button id="dv-btn-filter-clear-all" class="btn btn-mini" title="Click this to clear all the column filters and the global search">
				<i class="icon-remove-circle"> </i >
				<span class="lbl">Clear Filters</span>
			</button>
			
			<button id="dv-btn-no-wrap" class="btn btn-mini" title="Disable text wrapping for all cells." data-current="normal">
				<i class="icon-text-width"> </i >
				<span class="lbl">No-Wrap</span>
			</button>

			<?php if(isset($dd['custom_charts']) || isset($dd['charts_list'])): ?>
			<button id="dv-spreadsheet-charts" class="btn btn-mini" title="Display charts">
				<i class="icon-bar-chart"> </i >
				<span class="lbl">Charts</span>
			</button>
			<?php endif; ?>
		</div>

		<div style="display: none;">
			<span id="dv_top_toolbar" class="ui-corner-top" style="padding: 3px 5px 3px 3px; margin: 0; border-style: inset; border-bottom-width: 0px;">
				<?php if($help_file): ?>
				<button id="dv_show_help">Help</button>
				<?php endif; ?>
				<?php if(isset($dd['show_maps'])): ?>
				<input type="checkbox" id="dv_maps" class="dv_panel_btn" /><label for="dv_maps">Map</label>
				<?php endif; ?>
				<?php if(isset($dd['customizer']) && $show_customizer): ?>
				<input type="checkbox" checked="checked" id="dv_customizer_btn" class="dv_panel_btn" /><label for="dv_customizer_btn">Customize View</label>
				<?php elseif(isset($dd['customizer'])): ?>
				<input type="checkbox" id="dv_customizer_btn" class="dv_panel_btn" /><label for="dv_customizer_btn">Customize View</label>
				<?php endif; ?>
			</span>
		</div>

		<?php if(isset($dd['custom_charts']) || isset($dd['charts_list'])): ?>
		<div id="dv_charts_panel" style="display: none; clear: both; width: 860px; height: 380px; padding: 5px 10px 10px 5px; margin-top: 0; border: 1px solid #DDD; background: #EEE;" class="ui-corner-bottom dv_top_pannel">
			<button id="dv_pdcharts_download_btn" class="btn btn-mini btn-success" title="Download chart as an image"  style="float: right; z-index: 1; margin: 3px;">
				<i class="icon-download"> </i >
				<span class="lbl">Download Chart</span>
			</button>
			<button id="dv_pdcharts_draw_btn" class="btn btn-mini btn-info" title="Reload charts" style="float: right; z-index: 1; margin: 3px;">
				<i class="icon-repeat"> </i >
				<span class="lbl">Reload</span>
			</button>

			<div style="float:left; height: 380px; width: 245px;">
				<div id="dv_charts_control_panel" style="padding: 0 5px; ">
				<?php if(isset($dd['charts_list'])): ?>
				<select id="dv_chart_name" style="width: 100%;">
					<?php $pd_id = 0; foreach($dd['charts_list'] as $cl): ?>
					<option value="<?=$pd_id?>"><?=$cl['title']?></option>
					<?php $pd_id++; endforeach; ?>
				</select>

				<div id="dv_chart_desc" class=" ui-widget-content ui-corner-all" style="margin-top: 10px; font-size: 0.9em; border-style: inset; padding: 2px; overflow: auto; height: 340px;"></div>
				<?php endif; ?>
				</div>
			</div>
			<div id="dv_charts_preview_chart" style="height:100%; width:auto; margin-left: 248px;" class="ui-widget-content ui-corner-all">
			</div>
		</div>
		<?php endif; ?>

		<?php if(isset($dd['show_maps'])): ?>
		<div id="dv_maps_panel" style="position: relative; display: none; clear: both; width: 800px; height: 300px; padding: 3px 5px 3px 5px; margin-top: 0;" class="ui-widget ui-widget-header ui-corner-bottom dv_top_pannel">
			<button class="btn btn-inverse btn-mini dv-btn-download" data-format="kml" class="dv-btn-download" style="font-weight: bold; background-color: #FAA732; background-image: linear-gradient(to bottom, #FBB450, #F89406); background-repeat: repeat-x;" title="Export location data in KML format">
				<i class="icon-download"></i > KML
			</button>
			<button class="btn btn-inverse btn-mini dv-btn-download" data-format="kmz" class="dv-btn-download" style="font-weight: bold; background-color: #FAA732; background-image: linear-gradient(to bottom, #FBB450, #F89406); background-repeat: repeat-x;" title="Export location data in KMZ format">
				<i class="icon-download"></i > KMZ
			</button>
			<button class="btn btn-inverse btn-mini dv-btn-download" data-format="shp" class="dv-btn-download" style="font-weight: bold; background-color: #FAA732; background-image: linear-gradient(to bottom, #FBB450, #F89406); background-repeat: repeat-x;" title="Export location data in SHP format">
				<i class="icon-download"></i > SHP
			</button>
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
					$col_label = str_replace('<br />', ' ', $prop['label']);
					$col_label = str_replace('<hr />', '&nbsp/&nbsp', $col_label);

					if (isset($prop['units']) && $prop['units'] != '') {
						$col_label = $col_label . ' <small>[' . $prop['units'] . ']</small>';
					} elseif (isset($prop['unit']) && $prop['unit'] != '') {
						$col_label = $col_label . ' <small>[' . $prop['unit'] . ']</small>';
					}

					if (isset($dd['customizer']['selected']) && in_array($id, $dd['customizer']['selected'])) {
						$first_col = true;
						$selected .= '<li data-dv-id="' . $id . '">' . $col_label . '</li>';
					} else {
						$full_list .= '<li data-dv-id="' . $id . '">' . $col_label . '</li>';
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

		<div id="more_information" style="display: none; min-width: 300px;"></div>
		<?php if($help_file): ?>
		<div id="dv_help_dialog" style="display: none;">
			<iframe src="<?=$help_file?>" id="modalIframeId" width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" title="Help">IFRAMES not supported by the browser</iframe>
			</div>
		</div>
		<?php endif; ?>


	<?php endif; //Table Only ?>
		

		<div id="dv-spreadsheet-container" style="margin: 0px; padding; 0px; <?=$hide_str?>">
		<table id="dv-spreadsheet-tbl" style="margin-top: 0;">
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
				} else {
					$filter_msg = "	\n\nClick on the search box to list all the entries in the column";
				}
				
				if (isset($conf['filter_hint'])) {
					$filter_hint = $conf['filter_hint'];
				} else {
					if ($d_arr['field_types'][$id] == 'number' || $d_arr['field_types'][$id] == 'numrange') {
						$filter_hint = "Enter a number to filter this column by.";
						$filter_hint .= "  \n\nFollowing filter options are also supported,";
						$filter_hint .= "	\nRange filtering - ( e.g. 15.7 to 25 )";
						$filter_hint .= "	\nLess than, greater than ( e.g. <100 ), (e.g. >25)";
						$filter_hint .= "	\nLess than or equal, greater than or equal ( e.g. <=-12.5 ), (e.g. >=0.3)";
						$filter_hint .= "	\nEqual, not equal and ignore pattern ( e.g. =-2.55 ), ( e.g. !=-2.55 ), ( e.g. !55 )";
					} elseif ($d_arr['field_types'][$id] == 'datetime') {
						$filter_hint = "Enter a date to filter this column by.";
						$filter_hint .= "	\nRange filtering - ( e.g. 2011-01-25 to 2011-03-25 )";
						$filter_hint .= "	\nLess than, greater than ( e.g. <2011-03-25 ), (e.g. >2011-01-25)";
						$filter_hint .= "	\nLess than or equal, greater than or equal ( e.g. <=2011-03-25 ), (e.g. >=2010-03-25)";
						$filter_hint .= "	\nEqual, not equal and ignore pattern ( e.g. =2009-01-17 ), ( e.g. !=2009-01-17 ), ( e.g. !2009-01 )";
					} else {
						$filter_hint = "Enter a word or a phrase to filter this column by.";
						$filter_hint .= "  \n\nFollowing filter options are also supported,";
						$filter_hint .= "	\nExact matches, use '=' ( e.g. =keyword)";
						$filter_hint .= "	\nTo ignore a specific word, use '!=' ( e.g. !=keyword)";
						$filter_hint .= "	\nTo ignore a pattern, use '!' ( e.g. !keyword )";
					}
				}


				$title = $filter_hint . $filter_msg;

				if (isset($conf['type']) && $conf['type'] == 'image') {
					print '<th><input title="' . $title . '" type="text" placeholder="' . $label . '" disabled=disabled /></th>';
				} elseif (isset($filted_view[$id])) {
					print '<th><input type="text" placeholder="' . $filted_view[$id] . '" disabled=disabled style="background: yellow;" /></th>';
				} else {
					print '<th><input title="' . $title . '" type="text" placeholder="' . $label . '" class="search_init" /></th>';
				}
			}

			$colid++ ;
		}
		print '</tr></tfoot>';
	?>
		</table>
		</div>
	<?php		
		// Filter dialog show/hide parameter
		$dv_show_filters = 'false';
		$u =& JFactory::getURI();
		$path = explode('/', $u->_path);
		if (isset($path[4]) && $path[4] == 'filter_dialog') {
			$dv_show_filters = 'true';
		}
		//Legacy support
		if (JRequest::getVar('show_filters', 'false') === 'true') {
			$dv_show_filters = 'true';
		}
	?>
		<!-- Start: Dialog boxes -->
		<div id="truncated_text_dialog" style="display: none; overflow: auto;" title="Full Text"></div>

		<div id="dv_filters_dialog" title="<?=$dd['title']?> : Filters">
			<div id="dv_filters_tabs">
				<ul></ul>
			</div>
		</div>
		<!-- End: Dialog boxes -->

		<script>
			dv_data = <?=$f_data?>;
			dv_settings = <?=json_encode($dv_conf['settings'])?>;
			dv_show_filters = <?=$dv_show_filters?>;
			dv_settings.show_charts = <?=JRequest::getInt('show_chart', 'undefined');?>;
			dv_show_customizer = <?=($show_customizer)? 'true': 'false';?>;
			var dv_show_maps = <?=JRequest::getInt('show_map', 'undefined');?>;
		</script>

		<form style="display: none;" id="dv-spreadsheet-dl" method="POST"
			action="<?=$dv_conf['settings']['data_url']?>&nolimit=true<?=$custom_view_url . $custom_field_url?>">
		</form>
	</div>
	<?php
	}
}
?>
