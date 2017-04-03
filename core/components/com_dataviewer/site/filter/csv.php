<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();


function filter($res, &$dd, $ob_mode = false)
{
	$data = $res['data'];
	$w = '"';
	$s = ",";
	$nl = "\r\n";

	$csv = '';


	if (!$ob_mode) {
		$file_name = preg_replace('/\W/', '_', $dd['title']) . '.csv';

		header('Content-Description: File Transfer');
		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment;filename=' . $file_name);

		ob_end_flush();
	} else {
		ob_clean();
	}

	//Header
	$h_arr = mysqli_fetch_assoc($data);
	if (!$h_arr) {
		print 'No data available';
		return;
	}

	foreach ($h_arr as $key => $val) {
		if (!isset($dd['cols'][$key]['hide'])) {
			$label = isset($dd['cols'][$key]['label']) ? $dd['cols'][$key]['label'] : $key;
			if (isset($dd['cols'][$key]['unit']) && $dd['cols'][$key]['unit'] != '') {
				$label = $label . " [" . $dd['cols'][$key]['unit'] . "]";
			} elseif (isset($dd['cols'][$key]['units']) && $dd['cols'][$key]['units'] != '') {
				$label = $label . " [" . $dd['cols'][$key]['units'] . "]";
			}

			$label = str_replace('<br />', "  ", $label);
			$label = html_entity_decode(strip_tags($label), ENT_QUOTES, 'UTF-8');
			$label = str_replace('"', '""', $label);
			$csv .= $w . $label . $w . $s;
		}
	}
	$csv .= $nl;

	print $csv;

	mysqli_data_seek($data, 0);

	//Body
	while ($rec = mysqli_fetch_assoc($data)) {
		$row = '';
		foreach ($rec as $key => $val) {
			if (!isset($dd['cols'][$key]['hide'])) {

				if ($val != NULL) {
					$val = html_entity_decode(strip_tags($val), ENT_QUOTES, 'UTF-8');
				}

				if ($val == NULL) {
					$val = '';
				}

				$val = str_replace('"', '""', $val);
				$row .= $w . $val . $w . $s;
			}
		}
		print $row . $nl;
	}
	return '';
}
?>
