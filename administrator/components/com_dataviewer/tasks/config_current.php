<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2005-2011,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

function dv_config_current()
{
	global $com_name, $conf;
	$base = $conf['dir_base'];
	$db_id = JRequest::getString('db', false);

	require_once(JPATH_COMPONENT_SITE . DS . 'dv_config.php');

	$dv_conf_file = $base . DS . $db_id . DS . 'applications/dataviewer/config.json';

	$db_dv_conf = array();
	if (file_exists($dv_conf_file)) {
		$db_dv_conf = json_decode(file_get_contents($dv_conf_file), true);
		if (!is_array($db_dv_conf)) {
			$db_dv_conf = array();
		} if(isset($db_dv_conf['settings'])) {
			$db_dv_conf['settings'] = array_merge($dv_conf['settings'], $db_dv_conf['settings']);
		}
	}

	$dv_conf = array_merge($dv_conf, $db_dv_conf);

	print json_format(json_encode($dv_conf));
	exit;
}
?>
