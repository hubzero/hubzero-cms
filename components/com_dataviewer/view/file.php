<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2013 by Purdue Research Foundation, West Lafayette, IN 47906
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

defined('_JEXEC') or die;

function view() {
	global $html_path, $com_name, $dv_conf;

	$base_path = $dv_conf['base_path'];
	$file = JRequest::getString('f', false);

	$pi = pathinfo($file);
	$file_name = $pi['basename'];

	$full_path = $base_path . $file;

	if (!$file || !file_exists($full_path)) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		exit;
	}

	if ($full_path !== realpath($full_path)) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
		exit;
	}

	if (is_file($full_path)) {
		if (!preg_match('/\.(gif|jpe?g|png)$/i', $file_name)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $file_name . '"');
			header('Content-Transfer-Encoding: binary');
		} else {
			$mime = '';
			switch (strtolower(pathinfo($full_path, PATHINFO_EXTENSION))) {
				case 'jpeg':
				case 'jpg':
					$mime = 'image/jpeg';
					break;
				case 'png':
					$mime =  'image/png';
					break;
				case 'gif':
					$mime =  'image/gif';
					break;
			}

			header('X-Content-Type-Options: nosniff');
			header('Content-Type: '. $mime);
			header('Content-Disposition: inline; filename="' . $file_name . '"');
		}

		header('Content-Length: ' . filesize($full_path));
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($full_path)));
	
		ob_clean();
		flush();
		readfile($full_path);
	}

	exit;
}
?>
