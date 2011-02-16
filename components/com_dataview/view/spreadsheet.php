<?php
/**
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

function view($name) {

	global $com_name, $dv_conf;

	$settings = $_SESSION['dv']['settings'];
	$settings['url'] = "/?option=com_$com_name&task=data&obj=$name";

	$file = (JPATH_COMPONENT.DS."data".DS."$name.php");

	if (file_exists($file)) {
		require_once ($file);
	} 

	$res_func = 'get_' . $name;

	if (function_exists($res_func)) {
		$res = $res_func();

		$sg_arr = get_sg($res['dd']);
		$settings['sg'] = $sg_arr;

		$f_data = filter($res);
		$d_arr = json_decode($f_data, true);

		$settings['serverside'] = (isset($res['dd']['serverside']) && $res['dd']['serverside'])? true: false;

		$document = &JFactory::getDocument();

		if(isset($res['dd']['show_maps'])) {
			$document->addScript('http://maps.google.com/maps/api/js?sensor=false');
		}

		$help_file = false;

		if (isset($dv_conf['help_file_base_path'])) {
			$help_file = $dv_conf['help_file_base_path'] . $name . '/' . $name . '-help.html';
			if (!file_exists($help_file)) {
				$help_file = false;
			} else {
				$help_file = str_replace(JPATH_BASE, '', $help_file);
			}
		}
		

		$document->setTitle($res['dd']['title']);
	?>
	<a name="dv_top"></a>
	<div id="spreadsheet_container" class="ss_wrapper">
		<div>
			<span id="dv_top_toolbar" class="ui-widget ui-widget-header ui-corner-top" style="padding: 3px 5px 3px 3px; margin: 0; float: left; border-bottom-width: 0px;">
				<button id="dv_download">Download Data as a CSV file</button>
				<input type="checkbox" id="dv_fullscreen" /><label for="dv_fullscreen">Fullscreen [Ctrl+F11]</label>
				<?php if($help_file): ?>
				<button id="dv_show_help">Help</button>
				<?php endif; ?>
				<?php if(isset($res['dd']['show_charts'])): ?>
				<input type="checkbox" id="dv_charts" class="dv_panel_btn" /><label for="dv_charts">Charts</label>
				<?php endif; ?>
				<?php if(isset($res['dd']['show_maps'])): ?>
				<input type="checkbox" id="dv_maps" class="dv_panel_btn" /><label for="dv_maps">Map</label>
				<?php endif; ?>
				<span id="dv_title" class="ui-widget ui-widget-content ui-corner-all" style="text-shadow: 1px 1px 1px #888; font-size: 1.2em; border-style: inset; padding: 2px 5px 6px 3px; margin-left: 5px;"><?=$res['dd']['title']?></span>
			</span>
		</div>

		<?php if(isset($res['dd']['show_charts'])): ?>
		<div id="dv_charts_panel" style="display: none; clear: both; width: 800px; height: 300px; padding: 5px 10px 10px 5px; margin-top: 0;" class="ui-widget ui-widget-header ui-corner-bottom dv_top_pannel">
			<div style="float:left;">
				Select X:<br />
				<select id="dv_charts_x" style="width: 210px;">
				<?php
				$colid = 0;
				foreach ($res['dd']['cols'] as $id => $conf) {
					if (!isset($conf['hide'])) {
						$label = isset($conf['label']) ? $conf['label'] : $id;
						$label = str_replace('<br />', ' ', $label);
						print '<option value="' . $colid . '" />' . $label . '</option>';
						$colid++;
					}
				}
				?>
				</select>
				<br />
				<br />
				Select Y:<br />
				<select id="dv_charts_y" multiple="multiple" size="10"  style="width: 210px;">
				<?php
				$colid = 0;
				foreach ($res['dd']['cols'] as $id => $conf) {
					if (!isset($conf['hide'])) {
						if ($d_arr['aoColumns'][$colid]["sType"] == 'int' || $d_arr['aoColumns'][$colid]["sType"] == 'float' || $d_arr['aoColumns'][$colid]["sType"] == 'real' || $d_arr['aoColumns'][$colid]["sType"] == 'number') {
							$label = isset($conf['label']) ? $conf['label'] : $id;
							$label = str_replace('<br />', ' ', $label);
							print '<option value="' . $colid . '" />' . $label . '</option>';
						}
						$colid++;
					}
				}
				?>
				</select>
				<br /><br />
				<select id="dv_charts_type">
					<option value="2">Bar</option>
					<option value="0">Line</option>
					<option value="1">Line2</option>
				</select>
				&nbsp;&nbsp;
				<input type="button" value="Draw" id="dv_charts_draw_btn" />
			</div>
			<div id="dv_charts_preview_chart" style="height:100%; width:auto; margin-left: 225px;" class="ui-widget-content ui-corner-all"></div>
		</div>
		<?php endif; ?>

		<?php if(isset($res['dd']['show_maps'])): ?>
		<div id="dv_maps_panel" style="display: none; clear: both; width: 800px; height: 300px; padding: 5px 10px 10px 5px; margin-top: 0;" class="ui-widget ui-widget-header ui-corner-bottom dv_top_pannel">
			<div id="dv_maps_canvas" style="height:100%; width:auto;" class="ui-widget-content ui-corner-all"></div>
		</div>
		<?php endif; ?>

		<div id="more_information" style="display: none;"></div>
		<?php if($help_file): ?>
		<div id="dv_help_dialog" style="display: none;">
			<iframe src="<?=$help_file?>" id="modalIframeId" width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" title="Help">IFRAMES not supported by the browser</iframe>
			</div>
		</div>
		<?php endif; ?>
		<div id="dv_ss_charts_container">
			<div class="dv_ss_charts">
				<div class="dv_ss_charts_tabs">
					<ul></ul>
				</div>
			</div>
		</div>
		<table id="spreadsheet" style="margin-top: 0;">
	<?php
		$filted_view = array();
		$filted_view_str = '';
		if (isset($_GET['filter'])) {
			$filted_view_str = '&filter=' . $_GET['filter'];
			$settings['url'] = $settings['url'] . '&filter=' . $_GET['filter'];
			$ff = explode('||', $_GET['filter']);
			foreach($ff as $f) {
				$f = explode('|', $f);
				$filted_view[$f[0]] = $f[1];
			}
		}
		print '<tfoot><tr>';
		$colid = 0;
		foreach ($res['dd']['cols'] as $id => $conf) {
			if (!isset($conf['hide'])) {
				$label = isset($conf['label']) ? $conf['label'] : $id;
				$label = str_replace('<br />', ' ', $label);
				$label = html_entity_decode(strip_tags($label), ENT_QUOTES, 'UTF-8');
				$title = '';
				$filter_msg = '';
				if ($settings['serverside']) {
					$filter_msg = "\n\nPress Enter after entering the filter text.";
				}
				
				$filter_hint = "[Column: $label]  \n\n" . "Enter a word or a phrase to filter this column by.";

				if ($d_arr['field_types'][$id] == 'int' || $d_arr['field_types'][$id] == 'float' || $d_arr['field_types'][$id] == 'real' || $d_arr['field_types'][$id] == 'number') {
					$filter_hint = "[Column: $label]  \n\n" . "Enter a number to filter this column by.";
					$filter_hint .= "  \n\nFollowing filter options are also supported,";
					$filter_hint .= "    \nRange filtering - ( e.g. 15.7 to 25 )";
					$filter_hint .= "    \nLess than, greater than ( e.g. <100 ), (e.g. >25)";
					$filter_hint .= "    \nLess than or equal, greater than or equal ( e.g. <=100 ), (e.g. >=25)";
					$filter_hint .= "    \nLess than or equal, greater than or equal ( e.g. <=-12.5 ), (e.g. >=0.3)";
					$filter_hint .= "    \nEqual ( e.g. =-2.55 )\n\n";
				}
				
				$title = $filter_hint . $filter_msg;

				if (isset($filted_view[$id])) {
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
	<?php
		if (isset($res['dd']['search_groups'])) {
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
	?>
		<script>
			jQuery = dv_jQuery; // Replacing jQuery if jQuery older version was loaded
			dv_data = <?=$f_data?>;
			dv_settings = <?=json_encode($settings)?>;

			jQuery(function($) {
				$("#dv_charts_panel").resizable();
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

				$("#dv_fullscreen").button({
					text: false,
					icons: {
						primary: "ui-icon-newwin"
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

			});

		</script>
		<div id="" style="display: none;">
			<form id="ss_download_form" method="POST" action="/?option=com_<?=$com_name?>&task=data&obj=<?=$name?>&format=csv&nolimit=true<?=$filted_view_str?>">
			</form>
		</div>
		<div id="truncated_text_dialog" style="display: none;" title="Full Text"></div>
	</div>
	<?php
	} else {
		print "Error: Not impelemted";
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
