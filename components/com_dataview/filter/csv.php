<?php
/**
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

function filter($res)
{
	$data = $res['data'];
	$dd = $res['dd'];
	$w = '"';
	$s = ",";
	$nl = "\r\n";

	$csv = '';

	$file_name = str_replace(' ', '_', $dd['title']) . '.csv';
	header('Content-Description: File Transfer');
	header('Content-Type: text/csv; charset=UTF-8');
	header('Content-Disposition: attachment; filename=' . $file_name);

	//Header
	$h_arr = mysql_fetch_assoc($data);
	foreach ($h_arr as $key => $val) {
		if (!isset($dd['cols'][$key]['hide'])) {
			$label = isset($dd['cols'][$key]['label']) ? $dd['cols'][$key]['label'] : $key;
			$label = str_replace('<br />', "  ", $label);
			$label = html_entity_decode(strip_tags($label), ENT_QUOTES, 'UTF-8');
			$label = str_replace('"', '""', $label);
			$csv .= $w . $label . $w . $s;
		}
	}
	$csv .= $nl;

	print $csv;

	mysql_data_seek($data, 0);

	//Body
	while ($rec = mysql_fetch_assoc($data)) {
		$row = '';
		foreach($rec as $key => $val) {
			if (!isset($dd['cols'][$key]['hide'])) {

				if ($val != NULL) {
					$val = html_entity_decode(strip_tags($val), ENT_QUOTES, 'UTF-8');
				}

				if($val == NULL) {
					$val = "-";
				} elseif(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'date') {
					$val = strtotime($val);
					$val = date("m/d/Y", $val);
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
