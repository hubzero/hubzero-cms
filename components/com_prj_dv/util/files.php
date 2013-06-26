<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2012 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2012 by Purdue Research Foundation, West Lafayette, IN 47906.
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

defined('_JEXEC') or die('Restricted access');

function stream_files($path)
{
	global $dv_conf;

	$fullpath = '';
	if (isset($_SESSION['dv']['allowed_files'][$path]) || isset($_GET['debug'])) {
		$fullpath = $dv_conf['file_store_base'] . $path;

		$last_modified = date("F d Y H:i:s", filemtime($fullpath));

		$etag = md5($fullpath);
		$ah = apache_request_headers();

		if (isset($ah['If-Modified-Since']) && isset($ah['If-None-Match'])) {
			if ($ah['If-Modified-Since'] == $last_modified && strpos($ah['If-None-Match'], $etag)) {
				header('HTTP/1.1 304 Not Modified');
				header("Cache-Control: private");
				header("Pragma: ");
				header("Expires: ");
				header('Etag: "' . $etag . '"');
				header("Content-Type: ");
				exit(0);
			}
		}

		if (strstr($fullpath, '.csv')) {
			$mimetype = 'text/csv';
		} elseif (strstr($fullpath, '.zip')) {
			$mimetype = 'application/zip';
		} else {
			$mimetype = mime_content_type($fullpath);
		}

		if (file_exists($fullpath)) {
			header("Cache-Control: private");
			header('Content-Type: ' . $mimetype);
			header('Content-Length: ' . filesize($fullpath));
			header('Last-Modified: ' . $last_modified);
			header('Etag: "' . $etag . '"');
			header('Content-Disposition: inline; filename=' . preg_replace('/\W/', '_',$fullpath));
			
			ob_end_flush();
			readfile($fullpath);
			exit(0);
		}
	}
	print "Invalid (or Missing) file : $fullpath";
}
?>
