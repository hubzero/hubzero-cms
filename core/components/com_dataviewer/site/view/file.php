<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

function view()
{
	global $html_path, $com_name, $dv_conf;

	$hash = Request::getString('hash');
	$hash_list = Request::getString('hash_list');

	if ($hash != '')
	{
		$file = $_SESSION['dv']['file_download']['list'][$hash];
		$file_name = basename($file);
		$full_path = $file;
	}
	elseif ($hash_list != '')
	{
		zip_files($hash_list);
	}
	else
	{
		$base_path = $dv_conf['base_path'];
		$file = Request::getString('f', false);
		$pi = pathinfo($file);
		$file_name = $pi['basename'];
		$full_path = $base_path . $file;
	}

	if (!$file || !file_exists($full_path)) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		exit;
	}

	if ($full_path !== realpath($full_path)) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
		exit;
	}

	if (is_file($full_path)) {
		if (!preg_match('/\.(gif|jpe?g|png|pdf)$/i', $file_name)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $file_name . '"');
			header('Content-Transfer-Encoding: binary');
		} else {
			$mime = 'application/octet-stream';
			switch (strtolower(pathinfo($full_path, PATHINFO_EXTENSION))) {
				case 'jpeg':
				case 'jpg':
					$mime = 'image/jpeg';
					break;
				case 'png':
					$mime = 'image/png';
					break;
				case 'gif':
					$mime = 'image/gif';
					break;
				case 'pdf':
					$mime = 'application/pdf';
					break;
			}

			header('X-Content-Type-Options: nosniff');
			header('Content-Type: '. $mime);
			header('Content-Disposition: inline; filename="' . $file_name . '"');
		}

		header('Content-Length: ' . filesize($full_path));
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($full_path)));

		ob_clean();
		ob_end_flush();
		readfile($full_path);
	}

	exit;
}
