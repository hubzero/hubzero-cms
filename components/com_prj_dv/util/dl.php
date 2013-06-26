<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
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
			$fullpath = $_SESSION['dv']['file_download']['list'][$hash];
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
