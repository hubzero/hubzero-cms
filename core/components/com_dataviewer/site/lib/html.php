<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();


function dv_add_script($script, $local = true)
{
	$document = App::get('document');

	if (!$local) {
		$document->addScript($script);
		return;
	}

	if (file_exists(DV_PATH_HTML . DS . $script)) {

		if (is_dir(DV_PATH_HTML . DS . $script)) {
			$path = $script;
			chdir(DV_PATH_HTML . DS . $path);
			foreach (glob("*.js") as $file) {
				$document->addScript(
					DV_COM_HTML . '/' . $path . '/' . $file . '?mt='
					. filemtime(DV_PATH_HTML . DS . $path . DS . $file)
				);
			}
		} else {
			$document->addScript(
				DV_COM_HTML . '/' . $script . '?mt='
				. filemtime(DV_PATH_HTML . DS . $script)
			);
		}

	}

}

function dv_add_css($css, $local = true)
{
	$document = App::get('document');

	if (!$local) {
		$document->addStyleSheet($css);
		return;
	}

	if (file_exists(DV_PATH_HTML . DS . $css)) {

		if (is_dir(DV_PATH_HTML . DS . $css)) {
			$path = $css;
			chdir(DV_PATH_HTML . DS . $path);
			foreach (glob("*.css") as $file) {
				$document->addStyleSheet(
					DV_COM_HTML . '/' . $path . '/' . $file . '?mt='
					. filemtime(DV_PATH_HTML . DS . $path . DS . $file)
				);
			}
		} else {
			$document->addStyleSheet(
				DV_COM_HTML . '/' . $css . '?mt='
				. filemtime(DV_PATH_HTML . DS . $css)
			);
		}

	}

}
