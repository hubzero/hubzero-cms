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


function get_dl_hash($path, $type = 'file_download')
{
	$salt = "Ju   st( in c4s3.........:-0()";
	$hash = md5($salt . $path);

	if(!isset($_SESSION['dv'])) {
		$_SESSION['dv'] = array();
	}

	$_SESSION['dv'][$type]['list'][$hash] = $path;

	return $hash;
}

function stream_file($hash)
{
	$fullpath = '';
	if (isset($_SESSION['dv']['file_download']['list'][$hash])) {
		$fullpath = $_SESSION['dv']['file_download']['list'][$hash];

		//TODO: Better mimetype detection
		if (strstr($fullpath, '.csv')) {
			$mimetype = 'text/csv';
		} elseif (strstr($fullpath, '.zip')) {
			$mimetype = 'application/zip';
		} else {
			$mimetype = 'application/octet-stream';
		}

		if (file_exists($fullpath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $mimetype);
			header('Content-Length: ' . filesize($fullpath));
			header('Content-Disposition: attachment; filename=' . str_replace(' ', '_', basename($fullpath)));
			ob_end_flush();
			readfile($fullpath);
			exit(0);
		}
	}
	print "Invalid (or Missing) file : $fullpath";
}

function zip_files($hash_list)
{
	$hash_list = explode(',', $hash_list);

	$fl = '';

	foreach($hash_list as $hash) {
		if (isset($_SESSION['dv']['file_download']['list'][$hash])) {
			$fullpath = '"' . $_SESSION['dv']['file_download']['list'][$hash] . '"';
			$fl .= $fullpath . ' ';
		}
	}

	header('Content-Description: File Transfer');
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename=selected_files.zip');

	ob_end_flush();

	print `zip -qj - $fl`;

	exit(0);
}
?>
