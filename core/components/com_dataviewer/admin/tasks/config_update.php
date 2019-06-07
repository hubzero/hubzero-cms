<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

function dv_config_update()
{
	check_rid();
	global $com_name, $conf;
	$base = $conf['dir_base'];

	$db_id = Request::getString('db', false);
	$dv_conf_text = Request::getString('conf_text', false);

	$dv_conf_file = $base . DS . $db_id . DS . 'applications/dataviewer/config.json';
	file_put_contents($dv_conf_file, $dv_conf_text);

	$_SESSION['dataviewer']['conf_file_updated'] = true;

	$url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']);
	$url .= "/administrator/index.php?option=com_" . $conf['com_name'] . "&task=config&db=$db_id";
	header("Location: $url");
	exit;
}
