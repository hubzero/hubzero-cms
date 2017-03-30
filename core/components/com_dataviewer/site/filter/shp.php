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


function filter($res, &$dd)
{
	$data = $res['data'];

	if (!isset($dd['maps'])) {
		return;
	}

	$uid = uniqid('dv_shp_' . session_id());
	$path = '/tmp/' . $uid;
	mkdir($path);

	$file_name = $path . '/data';

	$vrt = '';
	$vrt .= '<OGRVRTDataSource>' . "\n\t";
	$vrt .= '<OGRVRTLayer name="data">' . "\n\t\t";
	$vrt .= '<SrcDataSource relativeToVRT="1">data.csv</SrcDataSource>' . "\n\t\t";
	$vrt .= '<GeometryType>wkbPoint</GeometryType>' . "\n\t\t";
	$vrt .= '<GeometryField encoding="PointFromColumns" x="lng" y="lat"/>' . "\n\t";
	$vrt .= '</OGRVRTLayer>' . "\n";
	$vrt .= '</OGRVRTDataSource>';

	$w = '"';
	$s = ",";
	$nl = "\r\n";

	$csv = $w . 'lng' . $w . $s;
	$csv .= $w . 'lat' . $w . $s;
	$csv .= $w . 'name' . $w;
	$csv .= $nl;

	while ($rec = mysqli_fetch_assoc($data)) {
		if ($rec[$dd['maps'][0]['lat']] == NULL || $rec[$dd['maps'][0]['lng']] == NULL) {
			continue;
		}

		$lat = $rec[$dd['maps'][0]['lat']];
		$lng = $rec[$dd['maps'][0]['lng']];
		$cood = '';
		if (!isset($dd['maps'][0]['cood_type']) || $dd['maps'][0]['cood_type'] != 'dms') {
			$cood = $w . $lng . $w . $s . $w . $lat . $w;
		} else {
			$cood = $w . dms2dc($lng) . $w . $s . $w . dms2dc($lat) . $w;
		}

		$csv .= $cood . $s;
		$csv .= $w . $rec[$dd['maps'][0]['title']] . $w . $nl;
	}

	file_put_contents ("$file_name.vrt", $vrt);
	file_put_contents ("$file_name.csv", $csv);
	system("ogr2ogr $path $file_name.vrt");

	header('Content-Description: File Transfer');
	header('Content-Type: ' . 'application/zip');
	header('Content-Disposition: attachment; filename=' . preg_replace('/\W/', '_', $dd['title']) . '.zip');

	$tmp = tempnam("/tmp", "shp");
	$z = new ZipArchive();
	$z->open($tmp, ZIPARCHIVE::OVERWRITE);
	$z->addFile("$file_name.csv", 'data.csv');
	$z->addFile("$file_name.vrt", 'data.vrt');
	$z->addFile("$file_name.shp", 'data.shp');
	$z->addFile("$file_name.shx", 'data.shx');
	$z->addFile("$file_name.dbf", 'data.dbf');
	$z->close();

	unlink("$file_name.csv");
	unlink("$file_name.vrt");
	unlink("$file_name.shp");
	unlink("$file_name.shx");
	unlink("$file_name.dbf");
	rmdir($path);

	ob_end_flush();
	readfile($tmp);
	unlink($tmp);
	exit();
}

function dms2dc($cood)
{
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
