<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Filter;

use Components\Dataviewer\Site\DvConfig;

class Json
{
	public static function filter($res, $dd)
	{
		/* URLs */
		$db_id = $dd['db_id']['id'];
		$dv_id = $dd['dv_id'];
		$dv_link = "/" . DvConfig::$com_name . "/view/$db_id/$dv_id/";

		$data = $res['data'];
		$total = $res['total'];
		$found = $res['found'];

		$single_record = isset($dd['single']) ? $dd['single'] : false;

		$table = array();
		$table['title'] = $dd['title'];
		$table['__sql'] = $res['sql'];
		$table['sColumns'] = array();
		$table['aaData'] = array();
		$table['iTotalRecords'] = $total;
		$table['iTotalDisplayRecords'] = $found;
		$table['filters'] = isset($dd['filters']) ? $dd['filters'] : array();
		$table['cols'] = array();
		$vis_keys = array();
		$field_types = array();

		$header = isset($dd['cols']) ? $dd['cols'] : $data[0];
		$field_offset = 0;
		$first_col = true;

		foreach ($header as $key => $val) {
			$field_type = 'string';

			$table['cols']['all'][] = $key;
			$field_offset++;

			if (!isset($dd['cols'][$key]['hide'])) {
				$table['cols']['visible'][] = $key;
				$vis_keys[] = $key;
				$align = (isset($dd['cols'][$key]['align'])) ? $dd['cols'][$key]['align'] : false;
				$data_type = (isset($dd['cols'][$key]['data_type'])) ? $dd['cols'][$key]['data_type'] : false;
				$data_type = (!$data_type) ? $field_type : $data_type;

				switch ($data_type) {
					case 'int':
					case 'float':
					case 'number':
					case 'numeric':
					case 'real':
						$data_type = 'number';
						$align = (!$align) ? 'right' : $align;
						break;
					case 'numrange':
						$data_type = 'numrange';
						$align = (!$align) ? 'right' : $align;
						break;
					case 'time':
						$align = (!$align) ? 'right' : $align;
						break;
					case 'datetime':
					case 'date':
					case 'timestamp':
					case 'year':
						$data_type = 'datetime';
						$align = (!$align) ? 'right' : $align;
						break;
					case 'cint':
						$align = (!$align) ? 'right' : $align;
						break;
					default:
						$data_type = 'html';
						$align = (!$align) ? 'left' : $align;
						break;
				}

				$hasWidth = isset($dd['cols'][$key]['width']) && $dd['cols'][$key]['width'] != '';
				$width = $hasWidth ? $dd['cols'][$key]['width'] : 'auto';
				if (!strstr($width, '%') && $width != 'auto') {
					$width .= 'px';
				}

				// Column specific CSS
				$table['col_styles'][] = (isset($dd['cols'][$key]['styles'])) ? $dd['cols'][$key]['styles'] : '';
				$table['col_h_styles'][] = (isset($dd['cols'][$key]['h_styles'])) ? $dd['cols'][$key]['h_styles'] : '';

				$tool_tip = '';
				if (isset($dd['cols'][$key]['desc'])) {
					$tool_tip = htmlentities($dd['cols'][$key]['desc'], ENT_QUOTES, 'UTF-8');
				}

				$label = '<div class="colum-label-text" title="' . htmlentities($tool_tip, ENT_QUOTES, 'UTF-8') . '">';
				$label .= (isset($dd['cols'][$key]['label'])) ? $dd['cols'][$key]['label'] : $key;
				$label .= '</div>';

				if (isset($dd['cols'][$key]['unit'])) {
					$dd['cols'][$key]['units'] = $dd['cols'][$key]['unit'];
				}

				if (isset($dd['cols'][$key]['units'])) {
					$units = $dd['cols'][$key]['units'];
					$name = $dd['cols'][$key]['units'];
					$desc = $dd['cols'][$key]['units'];

					if (is_array($dd['cols'][$key]['units'])) {
						$units = $dd['cols'][$key]['units']['label'];
						$name = $dd['cols'][$key]['units']['name'];
						$desc = $dd['cols'][$key]['units']['desc'];
					}

					$descEncoded = htmlentities($desc, ENT_QUOTES, 'UTF-8');
					$label .= '<div data-name="' . $name . '" data-desc="' . $desc . '" data-label="'
						. $units . '" title="' . $descEncoded . '">[' . $units . ']</div>';
				}

				$table['col_labels'][] = (isset($dd['cols'][$key]['label'])) ? $dd['cols'][$key]['label'] : $key;

				$tool_bar = '';

				$colType = isset($dd['cols'][$key]['type']) ? $dd['cols'][$key]['type'] : '';
				if ($colType == 'custom_field_link' && !$single_record) {
					$cfl = $dd['cols'][$key]['custom_field_link'];
					$link_base = '/' . DvConfig::$com_name . '/view/' . $dd['db_id']['id'] . '/' . $cfl['data']
						. '/?custom_field=' . $cfl['field'] . '|';
					$keyId = str_replace('.', '_', $key);
					$imgSrc = DvConfig::$html_path . '/download-m.png';
					$tool_bar .= '<a style="text-decoration: none; display: none; margin-left: 5px;" '
						. 'class="dv-multi-link" id="dv-' . $keyId . '" data-link-base="' . $link_base
						. '" title="Load filtered view" target="_blank" href="">'
						. '<img src="' . $imgSrc . '" />&nbsp;</a>';
				} elseif ($colType == 'tool' && !$single_record) {
					$name = isset($dd['cols'][$key]['name']) ? $dd['cols'][$key]['name'] : 'Tool';
					$link_format = explode('{p}', $dd['cols'][$key]['link_format']);
					$param = explode(',', $dd['cols'][$key]['param']);
					$link_zip = DvConfig::$com_name . '/file/' . $db_id . '/?hash_list=';// . '/?task=zip&hash_list=';

					$noMultiLaunch = !isset($dd['cols'][$key]['multi_launch']);
					if ($noMultiLaunch || $dd['cols'][$key]['multi_launch'] != false) {
						$runImg = DvConfig::$html_path . '/run-m.png';
						$tool_bar .= '<a style="text-decoration: none; margin-left: 2px;" '
							. 'class="dv_tools_launch_multi" title="Launch ' . $name . ' with selected files" '
							. 'target="_blank" href="' . $link_format[0] . '">'
							. '<img src="' . $runImg . '" />&nbsp;</a>';
					}
					$downImg = DvConfig::$html_path . '/download-m.png';
					$tool_bar .= '<a style="text-decoration: none;" class="dv_tools_down_multi" '
						. 'title="Download selected files" target="_blank" href="/' . $link_zip . '">'
						. '<img src="' . $downImg . '" />&nbsp;</a>';
					$keyId = str_replace('.', '_', $key);
					$tool_bar .= '<input type="checkbox" class="dv_header_select_all" value="'
						. $keyId . '" title="Select all" />';
				} elseif (
					(isset($dd['cols'][$key]['more_info']) || isset($dd['cols'][$key]['more_info_multi']))
					&& isset($dd['cols'][$key]['compare']) && !$single_record
				) {
					$mode = isset($dd['cols'][$key]['more_info']) ? 'more_info' : 'more_info_multi';
					$align = 'left';
					$mi = explode('|', $dd['cols'][$key][$mode]);
					$mi_dv_id = $mi[0];
					$link = "/" . DvConfig::$com_name . "/data/$db_id/$mi_dv_id/json/?id=";

					$tool_bar .= '<div style="position: relative;">';
					$compareTitle = $dd['cols'][$key]['compare'] . ': Select one or more items';
					$tool_bar .= '<button disabled data-link="' . $link . '" data-col-id="' . $key
						. '" class="btn btn-mini icon-exchange dv-compare" title="' . $compareTitle
						. '"></button>';
					$tool_bar .= '<input style="position: absolute; right: 0px;" type="checkbox" '
						. 'class="dv-select-all" data-col-id="' . $key . '" title="Select all" />';
					$tool_bar .= '</div>';
				}

				if ($first_col) {
					$align = (isset($dd['cols'][$key]['align'])) ? $dd['cols'][$key]['align'] : 'left';
					$first_col = false;
				}
				$label = "<div class=\"dv_label css_left\" data-fieldtype=\"$data_type\" >"
					. "$label $tool_bar</div>";
				$table['aoColumns'][] = array(
					'sTitle' => $label . '&nbsp;&nbsp;&nbsp;&nbsp;',
					'sClass' => $align,
					'sType' => $data_type,
					'sWidth' => $width
				);
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

		if (!$data) {
			return json_encode($table);
		}

		// Charts
		if (isset($dd['charts_list'])) {
			$table['charts_list'] = $dd['charts_list'];
		}

		// Maps
		if (isset($dd['maps']) && count($dd['maps']) > 0) {
			$table['maps'] = $dd['maps'];
		}

		// Custom
		if (isset($dd['customizer'])) {
			$table['customizer'] = $dd['customizer'];
		}

		$res_count = 0;
		foreach ($data as $rec) {
			$row = array();
			foreach ($rec as $key => $val) {
				$null_val = false;
				if (!isset($dd['cols'][$key]['hide'])) {
					if ($val != null && isset($dd['cols'][$key]['fmt'])) {
						$val = sprintf($dd['cols'][$key]['fmt'], $val);
					}

					if ($val == null) {
						$val = '-';

						if (isset($dd['replace_null'])) {
							$val = $dd['replace_null'];
						} elseif (isset($dd['cols'][$key]['replace_null'])) {
							$val = $dd['cols'][$key]['replace_null'];
						}

						$title = DvConfig::$dv_conf['null_desc'];

						if (isset($dd['null_desc'])) {
							$title = $dd['null_desc'];
						} elseif (isset($dd['cols'][$key]['null_desc'])) {
							$title = $dd['cols'][$key]['null_desc'];
						}

						$titleAttr = htmlentities($title == null ? '' : $title, ENT_QUOTES, 'UTF-8');
						$val = '<span title="' . $titleAttr . '" class="dv-null-dash">' . $val . '</span>';

						$null_val = true;
					} elseif (isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'file') {
						if (!isset($dd['cols'][$key]['ds-repo-path'])) {
							$dd['cols'][$key]['ds-repo-path'] = "/file_repo/{$dd['table']}/$key";
						}

						if (isset($dd['cols'][$key]['type_extra']) && $dd['cols'][$key]['type_extra'] == 'multi') {
							$list = explode('|#|', $val);
							$val = array();
							foreach ($list as $l) {
								$link_label = $l;
								$link = '/dataviewer/file/' . $db_id . '/?f=' . $dd['cols'][$key]['ds-repo-path'] . DS . $l;

								$title = $l;
								$missing = '';
								if (isset($dd['cols'][$key]['file-verify'])) {
									$full_path = DvConfig::$dv_conf['base_path'] . $dd['cols'][$key]['ds-repo-path'] . DS . $l;
									if (!file_exists($full_path)) {
										$missing = 'dv-file-missing';
										$title = 'Missing File: ' . $dd['cols'][$key]['ds-repo-path'] . DS . $l;
										$link_label = $link_label . '&nbsp;[missing]';
									}
								}

								$isThumb = isset($dd['cols'][$key]['file-display'])
									&& $dd['cols'][$key]['file-display'] == 'thumb';
								if ($isThumb) {
									$repoPath = $dd['cols'][$key]['ds-repo-path'];
									$small_img = '/dataviewer/file/' . $db_id . '/?f='
										. $repoPath . DS . '__thumb' . DS . $l;
									$medium_img = '/dataviewer/file/' . $db_id . '/?f='
										. $repoPath . DS . '__medium' . DS . $l;
									$link_label = '<img class="dv_image lazy-load dv_img_preview '
										. $missing . '" src="' . $small_img
										. '" data-preview-img="' . $medium_img . '" />';
								}

								$val[] = '<a title="' . $title . '" target="_blank" class="'
									. $missing . '" href="' . $link . '">' . $link_label . '</a>';
							}
							$val = implode('&nbsp;', $val);
						} else {
							$link_label = $val;
							$link = '/dataviewer/file/' . $db_id . '/?f=' . $dd['cols'][$key]['ds-repo-path'] . DS . $val;


							$title = $val;
							$missing = '';
							if (isset($dd['cols'][$key]['file-verify'])) {
								$full_path = DvConfig::$dv_conf['base_path'] . $dd['cols'][$key]['ds-repo-path'] . DS . $val;
								if (!file_exists($full_path)) {
									$missing = 'dv-file-missing';
									$title = 'Missing File: ' . $dd['cols'][$key]['ds-repo-path'] . DS . $val;
									$link_label = $link_label . '&nbsp;[missing]';
								}
							}

							$isThumb = isset($dd['cols'][$key]['file-display'])
								&& $dd['cols'][$key]['file-display'] == 'thumb';
							if ($isThumb) {
								$repoPath = $dd['cols'][$key]['ds-repo-path'];
								$small_img = '/dataviewer/file/' . $db_id . '/?f='
									. $repoPath . DS . '__thumb' . DS . $val;
								$medium_img = '/dataviewer/file/' . $db_id . '/?f='
									. $repoPath . DS . '__medium' . DS . $val;
								$link_label = '<img class="dv_image lazy-load dv_img_preview '
									. $missing . '" src="' . $small_img
									. '" data-preview-img="' . $medium_img . '" />';
							}
							$val = '<a title="' . $title . '" target="_blank" class="'
								. $missing . '" href="' . $link . '">' . $link_label . '</a>';
						}
					} elseif (isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'date') {
						$val = strtotime($val);
						$val = date("m/d/Y", $val);
					} elseif (
						(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'image')
						|| (isset($dd['cols'][$key]['url-display'])
						&& $dd['cols'][$key]['url-display'] == 'image')
					) {
						$small_img = $val;
						$medium_img = $val;

						if (isset($dd['cols'][$key]['linktype']) && $dd['cols'][$key]['linktype'] == 'repofiles') {
							$link = $dd['repo_base'];

							$linkpath = isset($dd['cols'][$key]['linkpath']) ? trim($dd['cols'][$key]['linkpath']) : '';

							if (isset($dd['publication_state'])) {
								// Publication id
								$repo_base = parse_url($dd['repo_base']);
								$repo_base_path = explode('/', $repo_base['path']);
								$pub_id = $repo_base_path[2];
								if (strlen($pub_id) < 5) {
									$pub_id = str_pad($pub_id, 5, '0', STR_PAD_LEFT);
								}

								// Publication version
								$arr = array();
								parse_str($repo_base['query'], $arr);

								$pub_vid = $arr['vid'];
								if (strlen($pub_vid) < 5) {
									$pub_vid = str_pad($pub_vid, 5, '0', STR_PAD_LEFT);
								}

								$link = "/app/site/publications/$pub_id/$pub_vid/data/";

								$pi = pathinfo($val);

								$linkpath = ($linkpath != '') ? $linkpath . '/' : '';
								$val = $link . $linkpath . $val;

								$small_img = $link . $linkpath  . $pi['filename'] . '_tn.gif';
								$medium_img =  $link . $linkpath . $pi['filename'] . '_medium.gif';
							} else {
								$linkpath = ($linkpath != '') ? '/' . $linkpath : '';
								$val = $link . $linkpath . '&file=' . $val;

								$small_img = $val . '&render=thumb';
								$medium_img = $val . '&render=medium';
							}
						} else {
							$small_img = $val;
							$medium_img = $val;
						}

						$original_img = $val;

						if (isset($dd['cols'][$key]['resized'])) {
							$bn = basename($val);
							$small_img = str_replace($bn, "small/$bn", $val);
							$medium_img = str_replace($bn, "medium/$bn", $val);
						}

						if (isset($dd['cols'][$key]['thumb'])) {
							$small_img = $rec[$dd['cols'][$key]['thumb']] . '"';
						}

						if (isset($dd['cols'][$key]['medium'])) {
							$medium_img = $rec[$dd['cols'][$key]['medium']] . '"';
						}

						if (isset($dd['cols'][$key]['gallery'])) {
							$path = $rec[$dd['cols'][$key]['gallery']];
							$hash = \Components\Dataviewer\Site\Lib\Dl::get_dl_hash($path, 'gallery');
							$gal_url = "/" . DvConfig::$com_name . '/gallery/' . $hash;
							$imgTag = '<img class="dv_image lazy-load dv_img_preview" '
								. 'src="' . DvConfig::$html_path . '/1x1.png" data-original="' . $small_img
								. '" data-preview-img="' . $medium_img . '" />';
							$val = '<a target="_blank" class="dv_gallery_link" href="'
								. $gal_url . '">' . $imgTag . '</a>';
						} else {
							$imgTag = '<img class="dv_image lazy-load dv_img_preview" '
								. 'src="' . DvConfig::$html_path . '/1x1.png" data-original="' . $small_img
								. '" data-preview-img="' . $medium_img . '" />';
							$val = '<a target="_blank" href="' . $original_img . '">'
								. $imgTag . '</a>';
						}
					} elseif (isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'email') {
						$val = '<a href="mailto:' . $val . '">' . $val . '</a>';
					} elseif (
						(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'link')
						|| (isset($dd['cols'][$key]['url-display'])
						&& $dd['cols'][$key]['url-display'] != 'image')
					) {
						if (isset($dd['cols'][$key]['url-display']) && $dd['cols'][$key]['url-display'] == 'full_link') {
							$dd['cols'][$key]['full_url'] = true;
						}

						if (!isset($dd['cols'][$key]['multi'])) {
							$preview = '';
							if (isset($dd['cols'][$key]['preview'])) {
								$preview = 'data-preview-img="' . $rec[$dd['cols'][$key]['preview']] . '"';
							}

							if (isset($dd['cols'][$key]['linktype']) && $dd['cols'][$key]['linktype'] == 'repofiles') {
								$link = $dd['repo_base'];

								$linkpath = isset($dd['cols'][$key]['linkpath']) ? trim($dd['cols'][$key]['linkpath']) : '';

								if (isset($dd['publication_state'])) {
									$linkpath = ($linkpath != '') ? $linkpath . '/' : '';
									$val = $link . '&file=' . $linkpath . $val;
								} else {
									$linkpath = ($linkpath != '') ? '/' . $linkpath : '';
									$val = $link . $linkpath . '&file=' . $val;
								}


								$dd['cols'][$key]['link_label'] = $key;
								$dd['cols'][$key]['relative'] = 'relative';
							}

							$val = self::dv_to_link($rec, $key, $dd, $val, $preview);
						} else {
							$sep = isset($dd['cols'][$key]['sep']) ? $dd['cols'][$key]['sep'] : ',';
							$multi_val = explode($sep, $val);


							if (isset($dd['cols'][$key]['preview'])) {
								$prv_list = explode($sep, $rec[$dd['cols'][$key]['preview']]);
							}

							$links = array();

							for ($i = 0; $i < count($multi_val); $i++) {
								$preview = '';
								if (isset($prv_list[$i])) {
									$preview = 'data-preview-img="' . trim($prv_list[$i]) . '"';
								}

								$links[] = self::dv_to_link($rec, $key, $dd, trim($multi_val[$i]), $preview);
							}
							$val = implode('<br />', $links);
						}
					} elseif (isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'tool') {
						$colConf = $dd['cols'][$key];
						$hasLabel = isset($colConf['link_label']);
						$label = $hasLabel
							? $rec[$colConf['link_label']]
							: basename($rec[$colConf['dl']], '.csv');
						$name = isset($colConf['name']) ? $colConf['name'] : 'Tool';

						$tool_link = '';
						$dl = '';
						$check = '';

						if (isset($colConf['dl']) && file_exists($rec[$colConf['dl']])) {
							$link_format = explode('{p}', $colConf['link_format']);
							$param = explode(',', $colConf['param']);
							$tl = '';
							for ($i = 0; $i < count($param); $i++) {
								$tl .= $link_format[$i] . $rec[trim($param[$i])];
							}
							$runImg = DvConfig::$html_path . '/run.png';
							$tool_link = '<a class="dv_tools_launch_link" title="Launch ' . $name
								. '" target="_blank" href="' . $tl . '">'
								. '<img src="' . $runImg . '" /></a>';

							if (isset($colConf['dl'])) {
								$path = $rec[$colConf['dl']];
								$link = '';
								if (strpos($path, PATH_ROOT) === 0) {
									$link = substr($path, strlen(PATH_ROOT) + 1);
								} else {
									$hash = \Components\Dataviewer\Site\Lib\Dl::get_dl_hash($path);
									$link = DvConfig::$com_name . '/file/' . $db_id . '/?hash=' . $hash;
								}
								$downImg = DvConfig::$html_path . '/download.png';
								$dl .= '<a title="Download File" data-data-file="' . $path
									. '" class="dv_tools_dl_link" target="_blank" href="/' . $link
									. '"><img src="' . $downImg . '" /></a>';
							}

							if (isset($colConf['tool_name']) && $rec[$colConf['tool_name']] == null) {
								$tool_link = '';
							}

							$keyClass = str_replace('.', '_', $key);
							$check = '<input type="checkbox" class="' . $keyClass
								. '" value="' . $path . '" style="float: right;" />';
						} else {
							$missingTitle = 'File is missing or not uploaded yet.';
							$runImg = DvConfig::$html_path . '/run.png';
							$downImg = DvConfig::$html_path . '/download.png';
							$tool_link = '<span class="hand" title="' . $missingTitle . '">'
								. '<img src="' . $runImg . '" /></span>';
							$dl = '<span class="hand" title="' . $missingTitle . '">'
								. '<img src="' . $downImg . '" /></span>';
							$check = '<input title="' . $missingTitle . '" disabled="disabled" '
								. 'type="checkbox" value="" style="float: right;" />';
						}

						$val = $label . '&nbsp;&nbsp;&nbsp;' . $tool_link . '&nbsp;&nbsp;' . $dl . '&nbsp;&nbsp;' . $check;
					}

					if (isset($dd['cols'][$key]['numrange'])) {
						$min = $rec[$dd['cols'][$key]['numrange']['min']];
						$max = $rec[$dd['cols'][$key]['numrange']['max']];
						$range = ($min == $max) ? $min : "$min to $max";
						$val = "<span data-min='$min' data-max='$max' class='range-data'>$range</span>";
					}

					$hasMoreInfo = isset($dd['cols'][$key]['more_info'])
						|| isset($dd['cols'][$key]['more_info_multi']);
					if ($hasMoreInfo && !$null_val) {
						$mode = isset($dd['cols'][$key]['more_info']) ? 'more_info' : 'more_info_multi';
						$mi = explode('|', $dd['cols'][$key][$mode]);
						$mi_dv_id = $mi[0];
						$id = (isset($mi[1])) ? $rec[$mi[1]] : '';

						$link = "/" . DvConfig::$com_name . "/data/$db_id/$mi_dv_id/json/?id=$id";
						$linkTitle = 'Click to view more information about this item';

						if (isset($dd['cols'][$key]['compare']) && !$single_record) {
							$new_val = '<div style="position: relative;">';
							$new_val .= '<a title="' . $linkTitle . '" class="' . $mode
								. '" href="' . $link . '">' . $val . '</a>';
							$new_val .= '<input style="position: absolute; top: 0px; right: 0px;" '
								. 'data-col-id="' . $key . '" type="checkbox" class="select-cell" '
								. 'value="' . $rec[$mi[1]] . '" />';
							$new_val .= '</div>';
						} else {
							$new_val = '<a title="' . $linkTitle . '" class="' . $mode
								. '" href="' . $link . '">' . $val . '</a>';
						}

						$val = $new_val;
					}

					if (isset($dd['cols'][$key]['filtered_view']) && !$null_val) {
						$fv = $dd['cols'][$key]['filtered_view'];
						$fv['task'] = isset($fv['task']) ? $fv['task'] : 'view';
						$fv['db'] = isset($fv['db']) ? $fv['db'] : $dd['db_id']['id'];

						$filter = array();
						if (isset($fv['filter'])) {
							foreach ($fv['filter'] as $c => $k) {
								$filter[] = "$c|" . urlencode((isset($rec[$k]) ? $rec[$k] : $k));
							}
							$filter = '?filter=' . implode('||', $filter);
						} else {
							$filter = '';
						}

						$append_to_url = isset($fv['append_to_url']) ? $fv['append_to_url'] : '';
						$fv_data = array_key_exists($fv['data'], $rec) ? $rec[$fv['data']] : $fv['data'];

						$url = '/' . DvConfig::$com_name . '/' . $fv['task'] . '/' . $fv['db'] . '/' . $fv_data
							. '/' . $filter . $append_to_url . '#dv_top';
						if ($fv_data != null) {
							$val = '<a class="filtered_view" href="' . $url
								. '" title="Pre-filtered view">' . $val . '</a>';
						}
						$val .= '<a class="filtered_view dv-link" href="' . $url
							. '" title="Open in a new window" target="_blank">'
							. '<i class="icon-external-link"></i></a>';
					}

					if (isset($dd['cols'][$key]['custom_field_link']) && !$null_val) {
						$cfl = $dd['cols'][$key]['custom_field_link'];
						$dv = $cfl['data'];
						$field = $cfl['field'];
						$cflVal = $cfl['value'];
						$v = array_key_exists($cflVal, $rec) ? $rec[$cflVal] : $cflVal;

						$ll = $val;
						if (isset($dd['cols'][$key]['link_label'])) {
							$ll = array_key_exists($ll, $rec) ? $rec[$ll] : $ll;
						}

						$append_to_url = isset($cfl['append_to_url']) ? $cfl['append_to_url'] : '';
						$keyId = str_replace('.', '_', $key);

						$check = '<input type="checkbox" class="dv-custom-field-link" '
							. 'data-url-append=' . $append_to_url . ' data-col-id="dv-' . $keyId
							. '" value="' . $v . '" style="float: right;" />';
						$linkUrl = '/' . DvConfig::$com_name . '/view/' . $dd['db_id']['id'] . '/' . $dv
							. '/?custom_field=' . $field . '|' . $v . $append_to_url . '#dv_top';
						$val = $check . '<a class="dv-custom-field-link" '
							. 'title="View filtered spreadsheet" target="_blank" href="'
							. $linkUrl . '">' . $ll . '</a>';
					}

					if (isset($dd['cols'][$key]['launch_view']) && !$null_val) {
						$fv = $dd['cols'][$key]['filtered_view'];
						$filter = array();
						foreach ($fv['filter'] as $c => $k) {
							$filter[] = "$c|" . (isset($rec[$k]) ? $rec[$k] : $k);
						}
						$filter = '?filter=' . implode('||', $filter);
						$append_to_url = isset($fv['append_to_url']) ? $fv['append_to_url'] : '';

						$linkUrl = '/' . DvConfig::$com_name . '/view/' . $dd['db_id']['id'] . '/'
							. $fv['data'] . '/' . $fv['view'] . '/' . $filter
							. $append_to_url . '#dv_top';
						$val = '<a class="filtered_view" title="View filtered spreadsheet" '
							. 'target="_blank" href="' . $linkUrl . '">' . $val . '</a>';
					}

					if (isset($dd['cols'][$key]['abbr'])) {
						$title = 'title="' . htmlentities($rec[$dd['cols'][$key]['abbr']], ENT_QUOTES, 'UTF-8') . '"';

						$val = '<span class="quick_tip hand" ' . $title . '>' . $val . '</span>';
					}

					$opmod_style = '';
					$opmod_title = '';

					if (isset($dd['cols'][$key]['opmod'])) {    // Only doing text style now, More to come....
						$switch = $rec[$dd['cols'][$key]['opmod']['switch']];
						foreach ($dd['cols'][$key]['opmod']['case'] as $is_val => $func) {
							if ($switch == $is_val) {
								$func = explode('|', $func);
								$param = $func[1];
								$func = 'dv_opmod_' . $func[0];
								$opmod_style = self::$func($param);
								$opmod_title = htmlspecialchars($is_val);
								break;
							}
						}
					}

					if (isset($dd['cols'][$key]['width'])) {
						$nowrap = '';
						if (isset($dd['cols'][$key]['nowrap'])) {
							$nowrap = 'white-space: nowrap;';
						}
						$extra_style = '';
						$title = $opmod_title;
						$class = '';
						$label = isset($dd['cols'][$key]['label']) ? $dd['cols'][$key]['label'] : $key;
						if (!$null_val && isset($dd['cols'][$key]['truncate'])) {
							$title = $val;
							$escaped = strip_tags(str_replace('"', '&#34;', $val));
							$title = ' title="' . $escaped . '" ';
							$class = 'class="truncate hand"';
							$colWidth = $dd['cols'][$key]['width'];

							$val = '<span style="white-space: nowrap;"><span ' . $title . $class
								. ' style="' . $opmod_style . ' width: ' . $colWidth . 'px;">'
								. $val . '</span><span class="truncateafter">&nbsp;</span></span>';
						} elseif (!$null_val && isset($dd['cols'][$key]['height'])) {
							$title = $val;
							$escaped = strip_tags(str_replace('"', '&#34;', $val));
							$title = ' title="' . $escaped . '" ';
							$class = 'class="truncate hand scrollcell"';
							$colWidth = $dd['cols'][$key]['width'];
							$colHeight = $dd['cols'][$key]['height'];

							$val = '<div ' . $title . $class . ' style="' . $nowrap . $opmod_style
								. ' width: ' . $colWidth . 'px; max-height: ' . $colHeight
								. 'px; overflow: clip;">' . $val . '</div>';
						} else {
							if (isset($dd['cols'][$key]['forcetruncate'])) {
								$class = 'class="forcetruncate"';
							}
							$colWidth = $dd['cols'][$key]['width'];
							$val = '<p ' . $title . ' ' . $class . ' style="' . $nowrap
								. $opmod_style . ' width: ' . $colWidth . 'px;">' . $val . '</p>';
						}
					} elseif ($opmod_style != '') {
						$val = '<span title="' . $opmod_title . '" class="hand" style="'
							. $opmod_style . '">' . $val . '</span>';
					}

					$row[] = $val;
				}
			}
			$res_count++;
			$table['aaData'][] = $row;
		}

		return json_encode($table);
	}

	public static function dv_to_link($rec, $key, $dd, $val, $preview)
	{
		$nowarp = isset($dd['cols'][$key]['nowrap']) ? 'style="white-space: nowrap;"' : '';
		$path = $val;
		$link = '';

		$label = false;
		$title = false;

		if (isset($dd['cols'][$key]['link_label']) && $dd['cols'][$key]['link_label'] != '') {
			if (isset($rec[$dd['cols'][$key]['link_label']])) {
				$label = $rec[$dd['cols'][$key]['link_label']];
			} else {
				$label = $dd['cols'][$key]['link_label'];
			}
		}

		if (isset($dd['cols'][$key]['link_title']) && $dd['cols'][$key]['link_title'] != '') {
			if (isset($rec[$dd['cols'][$key]['link_title']])) {
				$title = $rec[$dd['cols'][$key]['link_title']];
			} else {
				$title = $dd['cols'][$key]['link_title'];
			}
		}

		if (strpos($path, 'http') === 0) {
			$link = $path;
			$url = parse_url($link);
			if (!$label) {
				$pi = pathinfo($path);
				$label = isset($pi['filename']) ? $pi['filename'] : (isset($url['host']) ? $url['host'] : false);
				if (isset($pi['extension'])) {
					$label .= isset($dd['cols'][$key]['ext']) ? '.' . $pi['extension'] : '';
				}
			}
		} elseif (strpos($path, PATH_ROOT) === 0) {
			$link = substr($path, strlen(PATH_ROOT));
			if (!$label) {
				$pi = pathinfo($path);
				$label = isset($pi['filename']) ? $pi['filename'] : false;
				if (isset($pi['extension'])) {
					$label .= isset($dd['cols'][$key]['ext']) ? '.' . $pi['extension'] : '';
				}
			}
		} elseif (strpos($path, '/site/') === 0 || strpos($path, '/app/site/') === 0) {
			$link = $path;
			if (!$label) {
				$pi = pathinfo($path);
				$label = isset($pi['filename']) ? $pi['filename'] : false;
				if (isset($pi['extension'])) {
					$label .= isset($dd['cols'][$key]['ext']) ? '.' . $pi['extension'] : '';
				}
			}
		} elseif (isset($dd['cols'][$key]['relative'])) {
			$link = $path;
			if (!$label) {
				$pi = pathinfo($path);
				$label = isset($pi['filename']) ? $pi['filename'] : false;
				if (isset($pi['extension'])) {
					$label .= isset($dd['cols'][$key]['ext']) ? '.' . $pi['extension'] : '';
				}
			}
		} elseif ($dd['cols'][$key]['type'] == 'url') {
			$link = $path;
			$label = $path;
		} else {
			$hash = \Components\Dataviewer\Site\Lib\Dl::get_dl_hash($path);
			$link = '/' . DvConfig::$com_name . '/stream_file/' . $dd['db_id']['id'] . '/?hash=' . $hash;
			if (!$label) {
				$pi = pathinfo($path);
				$label = isset($pi['filename']) ? $pi['filename'] : false;
				if (isset($pi['extension'])) {
					$label .= isset($dd['cols'][$key]['ext']) ? '.' . $pi['extension'] : '';
				}
			}
		}

		if (!$label || isset($dd['cols'][$key]['full_url'])) {
			$label = $link;
		}

		if (!$title) {
			$title = $link;
		}

		$popup_class = '';
		$popup_data = '';
		if (isset($dd['cols'][$key]['popup'])) {
			$win_name = 'dataviewer_popup';
			$popupConf = $dd['cols'][$key]['popup'];
			if (isset($popupConf['name'])) {
				$popupName = $popupConf['name'];
				$win_name = isset($rec[$popupName]) ? $rec[$popupName] : $popupName;
				$win_name = str_replace(' ', '_', $win_name);
			}

			$win_features = isset($popupConf['features']) ? $popupConf['features'] : '';

			$popup_data = "data-popup-name='$win_name' data-popup-features='$win_features'";
			$popup_class = 'dv-popup';
		}

		$label = htmlentities($label);

		if ($preview != '') {
			$val = "<a $popup_data $nowarp title=\"$title\" href=\"" . $link
				. "\" $preview class=\"dv_img_preview dv-link $popup_class\">$label</a>";
		} else {
			$val = "<a $popup_data $nowarp title=\"$title\" href=\"" . $link
				. "\" class=\"dv-link $popup_class\">$label</a>";
		}

		if (!isset($dd['cols'][$key]['popup'])) {
			$val .= "<a title=\"Open in a new window\" target=\"_blank\" href=\"" . $link
				. "\" class=\"dv-link\" ><i class=\"icon-external-link\"></i></a>";
		}

		return $val;
	}


	// HTML data output modifires
	public static function dv_opmod_set_color($param)
	{
		return "color: $param;";
	}
}
