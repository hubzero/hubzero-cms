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


function filter($res, &$dd)
{
	$data = $res['data'];

	header('Content-Description: File Transfer');
	header('Content-Type: ' . 'application/vnd.google-earth.kmz');
	header('Content-Disposition: attachment; filename=' . preg_replace('/\W/', '_', $dd['title']) . '.kmz');

	$kml = '';
	$kml .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$kml .= '<kml xmlns="http://earth.google.com/kml/2.2">';
	$kml .= '<Document>';
	$kml .= '<name>' . $dd['title'] . '</name>';
	$kml .= '<description><![CDATA[' . $dd['title'] . ']]></description>';
	$kml .= '<Style id="style0">';
	$kml .= '<IconStyle><Icon><href>http://maps.gstatic.com/intl/en_us/mapfiles/ms/micons/blue-dot.png</href></Icon></IconStyle></Style>';

	if (!isset($dd['maps'])) {
		return;
	}

	while ($rec = mysql_fetch_assoc($data)) {
		if ($rec[$dd['maps'][0]['lat']] == NULL || $rec[$dd['maps'][0]['lng']] == NULL) {
			continue;
		}

		$lat = $rec[$dd['maps'][0]['lat']];
		$lng = $rec[$dd['maps'][0]['lng']];
		$cood = '';
		if (!isset($dd['maps'][0]['cood_type']) || $dd['maps'][0]['cood_type'] != 'dms') {
			$cood = "$lng,$lat,0.000000";
		} else {
			$cood = dms2dc($lng) . ',' . dms2dc($lat) . ',0.000000';
		}

		$pm = '<Placemark>';
		$pm .= '<name>' . $rec[$dd['maps'][0]['title']] . '</name>';
		$pm .= '<description><![CDATA[<div dir="ltr">' . $rec[$dd['maps'][0]['title']];
		if (isset($dd['maps'][0]['info'])) {
			$info_str = $dd['maps'][0]['info'];
			foreach($rec as $key=>$val) {
				$info_str = str_replace('{' . $key . '}', $rec[$key], $info_str);
				$info_str = str_replace('{' . $key . '|html}', $rec[$key], $info_str);
			}
			$pm .= "<br />$info_str";
		}
		$pm .= '</div>]]></description>';
		$pm .= '<styleUrl>#style0</styleUrl>';
		$pm .= '<Point>';
		$pm .= '<coordinates>' . $cood . '</coordinates>';
		$pm .= '</Point>';
		$pm .= '</Placemark>';

		$kml .= $pm;
	}

	$kml .= '</Document>';
	$kml .= '</kml>';

	$tmp = tempnam("/tmp", "kmz");
	$z = new ZipArchive();
	$z->open($tmp, ZIPARCHIVE::OVERWRITE);
	$z->addFromString('doc.kml', $kml);
	$z->close();
	ob_end_flush();
	readfile($tmp);
	unlink($tmp);
	exit();
}

function dms2dc($cood) {
	$cood = explode('° ', $cood);
	$d = $cood[0];
	$cood = explode('\' ', $cood[1]);
	$m = $cood[0];
	$cood = explode('" ', $cood[1]);
	$s = $cood[0];
	$dir = $cood[1];

	$dc = $d + ($m/60) + ($s/(60*60));

	if ($dir == "S" || $dir == "W") {
		$dc = $dc * -1;
	}
	return $dc;
}
?>
