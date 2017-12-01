<?php
/**
 * @package     hubzero.cms.admin
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

function dv_config_current()
{
	global $com_name, $conf;
	$base = $conf['dir_base'];
	$db_id = Request::getString('db', false);

	require_once(PATH_COMPONENT_SITE . DS . 'dv_config.php');

	$dv_conf_file = $base . DS . $db_id . DS . 'applications/dataviewer/config.json';

	$db_dv_conf = array();
	if (file_exists($dv_conf_file)) {
		$db_dv_conf = json_decode(file_get_contents($dv_conf_file), true);
		if (!is_array($db_dv_conf)) {
			$db_dv_conf = array();
		} if (isset($db_dv_conf['settings'])) {
			$db_dv_conf['settings'] = array_merge($dv_conf['settings'], $db_dv_conf['settings']);
		}
	}

	$dv_conf = array_merge($dv_conf, $db_dv_conf);

	print json_format(json_encode($dv_conf));
	exit;
}
?>
