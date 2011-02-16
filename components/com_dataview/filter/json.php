<?php
/**
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

function filter($res)
{
	global $com_name, $html_path;

	$data = $res['data'];
	$dd = $res['dd'];
	$total = $res['total'];
	$found = $res['found'];

	$single_record = isset($dd['single'])? $dd['single']: false;

	$table = array();
	$table['__sql'] = $res['sql'];
	$table['sColumns'] = array();
	$table['aaData'] = array();
	$table['iTotalRecords'] = $total;
	$table['iTotalDisplayRecords'] = $found;
	$vis_keys = array();
	$field_types = array();

	$header = isset($dd['cols'])? $dd['cols']: $data[0];
	$field_offset = 0;
	$first_col = true;

	foreach ($header as $key => $val) {
		if (count($data)>0) {
			$field_type = mysql_field_type($data, $field_offset);
		} else {
			$field_type = 'string';
		}
		
		$field_offset++;
		if (!isset($dd['cols'][$key]['hide'])) {
			$vis_keys[] = $key;
			$align = (isset($dd['cols'][$key]['align']))? $dd['cols'][$key]['align']: false;
			$data_type = (isset($dd['cols'][$key]['data_type']))? $dd['cols'][$key]['data_type']: false;
			$data_type = (!$data_type)? $field_type: $data_type;

			switch($data_type) {
				case 'int':
				case 'float':
				case 'real':
					$data_type = 'int';
					$align = (!$align)? 'right': $align;
					break;
				case 'cint':
					//$data_type = 'cnum';
					$align = (!$align)? 'right': $align;
					break;
				default:
					$data_type = 'html';
					$align = (!$align)? 'left': $align;
					break;
			}

			$width = (isset($dd['cols'][$key]['width']))? $dd['cols'][$key]['width']: 'auto';
			if (!strstr($width, '%') && $width != 'auto') {
				$width .= 'px';
			}

			$tool_tip = '';

			if (isset($dd['cols'][$key]['desc'])) {
				$tool_tip = htmlentities($dd['cols'][$key]['desc'], ENT_QUOTES, 'UTF-8');
			}

			$label = '<strong class="dv_label_text" title="' . $tool_tip . '">';
			$label .= (isset($dd['cols'][$key]['label']))? $dd['cols'][$key]['label'] : $key;
			$label .= '</strong>';

			$table['col_labels'][] = (isset($dd['cols'][$key]['label']))? $dd['cols'][$key]['label'] : $key;

			$tool_bar = '';

			if (isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'tool' && !$single_record) {
				$name = isset($dd['cols'][$key]['name'])? $dd['cols'][$key]['name']: 'Tool';
				$link_format = explode('{p}', $dd['cols'][$key]['link_format']);
				$param = explode(',', $dd['cols'][$key]['param']);
				$link_zip = $com_name . '/?task=zip&hash_list=';

				$tool_bar .= '<a style="text-decoration: none; margin-left: 2px;" class="dv_tools_launch_multi" title="Launch ' . $name . ' with selected files" target="_blank" href="' . $link_format[0] . '"><img src="' . $html_path . '/run-m.png' . '" />&nbsp;</a>';
				$tool_bar .= '<a style="text-decoration: none;" class="dv_tools_down_multi" title="Download selected files" target="_blank" href="/' . $link_zip . '"><img src="' . $html_path . '/download-m.png' . '" />&nbsp;</a>';
				$tool_bar .= '<input type="checkbox" class="dv_header_select_all" value="' . str_replace('.', '_', $key) . '" title="Select all" />';
			} elseif (isset($dd['cols'][$key]['more_info']) && isset($dd['cols'][$key]['compare']) && !$single_record) {
				$align = 'left';
				$mi = explode('|', $dd['cols'][$key]['more_info']);

				$tool_bar .= '<a style="text-decoration: none;" class="dv_compare_multi" title="' . $dd['cols'][$key]['compare'] . '" target="_blank"  href="/' . $com_name . '/?task=data&format=json&nolimit=true&obj=' . $mi[0] . '&id=">&nbsp;<img src="' . $html_path . '/compare-l.png' . '" />&nbsp;</a>';
				$tool_bar .= '<input type="checkbox" class="dv_header_select_all" value="' . str_replace('.', '_', $key) . '" title="Select all" />';
			}

			if ($first_col) {
				$align = 'left';
				$first_col = false;
			}
			$label = "<div class=\"dv_label css_left\" data-fieldtype=\"$data_type\" >$label $tool_bar</div>";
			$table['aoColumns'][] = array('sTitle' => $label . '&nbsp;&nbsp;&nbsp;&nbsp;', 'sClass'=>$align, 'sType'=>$data_type, 'sWidth'=>$width);
			$table['sColumns'][] = $label;
			$field_types[$key] = $data_type;
		}
	}

	if (isset($dd['order_by'])) {
		$table['aaSorting'] = array();			
	}

	$table['sColumns'] = implode(',', $table['sColumns']);
	$table['vis_cols'] = $vis_keys;
	$table['field_types'] = $field_types;

	// Chart info
	if (isset($dd['charts']) && count($dd['charts']) > 0) {
		$table['charts'] = array();
		foreach($dd['charts'] as $chart) {
			$lines = array();

			foreach($chart['lines'] as $line) {
				if (is_array($line) && count($line) == 2) {
					$lines[] = array(array_search($line[0], $vis_keys), array_search($line[1], $vis_keys));
				} elseif (is_array($line) && count($line) == 3) {
					$lines[] = array(array_search($line[0], $vis_keys), array_search($line[1], $vis_keys), array_search($line[2], $vis_keys));
				} else {
					$lines[] = array_search($line, $vis_keys);
				}
			}

			$type = isset($chart['type'])? $chart['type']: 'bar';
			$x_min = isset($chart['x_min'])? $chart['x_min']: 0;
			$y_min = isset($chart['y_min'])? $chart['y_min']: 0;
			$interval = isset($chart['interval'])? $chart['interval']: 10;
			$desc = isset($chart['desc'])? $chart['desc']: false;
			$table['charts'][] = array('name'=>$chart['name'], 'type'=>$type, 'x_label'=>$chart['x_label'], 'y_label'=>$chart['y_label'], 'x_min'=>$x_min, 'y_min'=>$y_min, 'lines'=>$lines, 'interval'=>$interval, 'desc'=>$desc);
		}
	}

	// Maps
	if (isset($dd['maps']) && count($dd['maps']) > 0) {
		$table['maps'] = $dd['maps'];
	}

	if (!$data) {
		return json_encode($table);
	}

	$res_count = 0;
	while ($rec = mysql_fetch_assoc($data)) {
		$row = array();
		foreach($rec as $key => $val) {
			if (!isset($dd['cols'][$key]['hide'])) {
				if($val == NULL) {
					if (isset($dd['cols'][$key]['link_label'])) {
						$val = 	$rec[$dd['cols'][$key]['link_label']];
					}
					
					if ($val == '' && isset($dd['cols'][$key]['replace_null'])) {
						$val = $dd['cols'][$key]['replace_null'];
					}
					
					if ($val == '') {
						$val = '-';
					}

				} elseif(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'date') {
					$val = strtotime($val);
					$val = date("m/d/Y", $val);
				} elseif(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'image') {
					$original_img = $val;
					$small_img = $val;
					$medium_img = $val;

					if (isset($dd['cols'][$key]['resized'])) {
						$bn = basename($val);
						$small_img = str_replace($bn, "small/$bn", $val);
						$medium_img = str_replace($bn, "medium/$bn", $val);
					}

					if (isset($dd['cols'][$key]['gallery'])) {

						$path = $rec[$dd['cols'][$key]['gallery']];
						$hash = get_dl_hash($path, 'gallery');
						$gal_url = "/" . $com_name . '/gallery/' . $hash;
						$val = '<a target="_blank" class="dv_gallery_link" href="' . $gal_url . '"><img class="dv_image dv_img_preview" src="' . $small_img . '" data-preview-img="' . $medium_img . '" /></a>';
					} else {
						$val = '<a target="_blank" href="' . $original_img . '"><img class="dv_image dv_img_preview" src="' . $small_img . '" data-preview-img="' . $medium_img . '" /></a>';
					}
				} elseif(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'email') {
					$val = '<a href="mailto:' . $val . '">' . $val . '</a>';
				} elseif(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'link') {
					if (!isset($dd['cols'][$key]['multi'])) {
						$preview = '';
						if (isset($dd['cols'][$key]['preview'])) {
							$preview = 'data-preview-img="' . $rec[$dd['cols'][$key]['preview']] . '"';
						}
						$val = dv_to_link($rec, $key, $dd, $val, $preview);
					} else {
						$sep = isset($dd['cols'][$key]['sep'])? $dd['cols'][$key]['sep']: ',';
						$multi_val = explode($sep, $val);


						if (isset($dd['cols'][$key]['preview'])) {
							$prv_list = explode($sep, $rec[$dd['cols'][$key]['preview']]);
						}

						$links = array();

						for($i=0; $i<count($multi_val); $i++) {
							$preview = '';
							if(isset($prv_list[$i])) {
								$preview = 'data-preview-img="' . trim($prv_list[$i]) . '"';
							}

							$links[] = dv_to_link($rec, $key, $dd, trim($multi_val[$i]), $preview);
						}
						$val = implode('<br />', $links);
					}
				} elseif(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'tool') {
					$label = isset($dd['cols'][$key]['link_label'])? $rec[$dd['cols'][$key]['link_label']]: basename($rec[$dd['cols'][$key]['dl']], '.csv');
					$name = isset($dd['cols'][$key]['name'])? $dd['cols'][$key]['name']: 'Tool';

					$tool_link = '';
					$dl = '';
					$check = '';

					if (isset($dd['cols'][$key]['dl']) && file_exists($rec[$dd['cols'][$key]['dl']])) {
						$link_format = explode('{p}', $dd['cols'][$key]['link_format']);
						$param = explode(',', $dd['cols'][$key]['param']);
						$tl = '';
						for ($i = 0; $i < count($param); $i++) {
							$tl .= $link_format[$i] . $rec[$param[$i]];
						}
						$tool_link = '<a title="Launch ' . $name . '" target="_blank" href="' . $tl . '"><img src="' . $html_path . '/run.png' . '" /></a>';

						if (isset($dd['cols'][$key]['dl'])) {
							$path = $rec[$dd['cols'][$key]['dl']];
							$link = '';
							if (strpos($path, JPATH_BASE) === 0) {
								$link = substr($path, strlen(JPATH_BASE)+1);
							} else {
								$hash = get_dl_hash($path);
								$link = $com_name . '/?task=file&hash=' . $hash;
							}
							$dl .= '<a title="Download File" class="dv_tools_dl_link" target="_blank" href="/' . $link . '"><img src="' . $html_path . '/download.png' . '" /></a>';
						}

						$check = '<input type="checkbox" class="' . str_replace('.', '_', $key) . '" value="' . $path . '" style="float: right;" />';
					} else {
						$tool_link = '<span class="hand" title="File is missing or not uploaded yet."><img src="' . $html_path . '/run.png' . '" /></span>';
						$dl = '<span class="hand" title="File is missing or not uploaded yet."><img src="' . $html_path . '/download.png' . '" /></span>';
						$check = '<input title="File is missing or not uploaded yet." disabled="disabled" type="checkbox" value="" style="float: right;" />';
					}

					$val = $label . '&nbsp;&nbsp;&nbsp;' . $tool_link . '&nbsp;&nbsp;' . $dl . '&nbsp;&nbsp;' . $check;
				}

				if (isset($dd['cols'][$key]['more_info'])) {
					$mi = explode('|', $dd['cols'][$key]['more_info']);
					$obj = '&obj=' . $mi[0];
					$id = (isset($mi[1]))? '&id=' . $rec[$mi[1]]: '';

					$check = '';
					if (isset($dd['cols'][$key]['compare']) && !$single_record) {
						$check = '<input data-colname= "' . str_replace('.', '_', $key) . '" type="checkbox" class="' . str_replace('.', '_', $key) . ' dv_compare_chk" value="' . $rec[$mi[1]] . '" style="float: right;" />';
					}

					$val = '<a title="Click to view more information about this item" class="more_info" href="/' . $com_name . '/?task=data' . $obj . $id . '&format=json" style="color: blue;">' . $val . '</a>' . '&nbsp;&nbsp;' . $check;
				} elseif (isset($dd['cols'][$key]['more_info_multi'])) {
					$mi = explode('|', $dd['cols'][$key]['more_info_multi']);
					$obj = '&obj=' . $mi[0];
					$id = (isset($mi[1]))? '&id=' . $rec[$mi[1]]: '';

					$check = '';
					if (isset($dd['cols'][$key]['compare']) && !$single_record) {
						$check = '<input data-colname= "' . str_replace('.', '_', $key) . '" type="checkbox" class="' . str_replace('.', '_', $key) . ' dv_compare_chk" value="' . $rec[$mi[1]] . '" style="float: right;" />';
					}

					$val = '<a title="Click to view more information about this item" class="more_info_multi" href="/' . $com_name . '/?task=data' . $obj . $id . '&format=json" style="color: blue;">' . $val . '</a>' . '&nbsp;&nbsp;' . $check;
				}

				if (isset($dd['cols'][$key]['filtered_view'])) {
					$fv = $dd['cols'][$key]['filtered_view'];
					$filter = array();
					foreach ($fv['filter'] as $c => $k) {
						$filter[] = "$c|" . (isset($rec[$k])? $rec[$k]: $k);
					}
					$filter = '?filter=' . implode('||', $filter);

					$val = '<a class="filtered_view" title="View filtered spreadsheet" target="_blank" href="/' .$com_name . '/' . $fv['view'] . '/' . $fv['data'] . '/' . $filter . '#dv_top">' . $val . '</a>';
				}

				if (isset($dd['cols'][$key]['abbr'])) {
					$title = 'title="' . str_replace('"', '&#34;', $rec[$dd['cols'][$key]['abbr']]) . '"';

					$val = '<span class="quick_tip hand" ' . $title . '>' . $val . '</span>';
				}
				
				$opmod_style = '';
				$opmod_title = '';

				if (isset($dd['cols'][$key]['opmod'])) {	// Only doing text style now, More to come....
					$switch = $rec[$dd['cols'][$key]['opmod']['switch']];
					foreach($dd['cols'][$key]['opmod']['case'] as $is_val=>$func) {
						if ($switch == $is_val) {
							$func = explode('|', $func);
							$param = $func[1];
							$func = 'dv_opmod_' . $func[0];
							$opmod_style = $func($param);
							$opmod_title = htmlspecialchars($is_val);
							break;
						}
					}
				}
				
				if(isset($dd['cols'][$key]['width'])) {
					$extra_style = '';
					$title = $opmod_title;
					$class = '';
					$label = isset($dd['cols'][$key]['label'])? $dd['cols'][$key]['label']: $key;
					if ($val != "-" && isset($dd['cols'][$key]['truncate'])) {
						$len = $dd['cols'][$key]['width'];
						$len = intval($len/8);
						$val_t = substr($val, 0, $len);
						if ($val_t != $val) {
							$title = $val;
							preg_match('/(http:\/\/[^\s]+)/', $title, $text);
							if (isset($text[0])) {
								//$url = parse_url($text[0]);
								$html = "<a target=\"_blank\" href=\"". $text[0] . "\" style=\"color: blue; word-wrap: break-word;\">" . $text[0] . "</a>";
								$newString = preg_replace('/(http:\/\/[^\s]+)/', $html, $title);
								$title = $newString;
							}

							$title = "title=" . '"' . str_replace('"', '&#34;', $title) . '"';
							$class = 'class="truncate hand"';
							$val = $val_t . '...';
						}
					}

					$val = '<p ' . $title . ' ' . $class . ' style="' . $opmod_style . ' min-width: ' . $dd['cols'][$key]['width'] . 'px;">' . $val . '</p>';
				} elseif ($opmod_style != '') {
					$val = '<p title="' . $opmod_title . '" class="hand" style="' . $opmod_style . '">' . $val . '</p>';
				}

				$row[] = $val;
			}
		}
		$res_count++;
		$table['aaData'][] = $row;
	}

	return json_encode($table);
}

function dv_to_link($rec, $key, $dd, $val, $preview)
{
	global $com_name, $html_path;

	$path = $val;
	$link = '';
	$label = isset($dd['cols'][$key]['link_label'])? $rec[$dd['cols'][$key]['link_label']]: false;

	if (strpos($path, 'http') === 0) {
		$link = $path;
		$url = parse_url($link);
		if (!$label) {
			$label = pathinfo($path);
			$label = isset($label['filename'])? $label['filename']: (isset($url['host'])? $url['host']: false);
		}
	} elseif (strpos($path, JPATH_BASE) === 0) {
		$link = substr($path, strlen(JPATH_BASE)+1);
	} elseif (strpos($path, '/site/') === 0) {
		$link = $path;
		if (!$label) {
			$label = pathinfo($path);
			$label = isset($label['filename'])? $label['filename']: false;
		}
	} else {
		$hash = get_dl_hash($path);
		$link = "/" . $com_name . '/?task=file&hash=' . $hash;
		if (!$label) {
			$label = pathinfo($path);
			$label = isset($label['filename'])? $label['filename']: false;
		}
	}

	if (!$label) {
		$label = $link;
	}

	if ($preview != '') {
		$val = "<a title=\"$link\" target=\"_blank\" href=\"$link\" $preview class=\"dv_img_preview\">$label</a>";
	} else {
		$val = "<a title=\"$link\" target=\"_blank\" href=\"$link\" >$label</a>";
	}

	return $val;
}


// HTML data output modifires
function dv_opmod_set_color($param)
{
	return "color: $param;";
}

?>
