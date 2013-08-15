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

function dv_config_update()
{
	check_rid();
	global $com_name, $conf;
	$base = $conf['dir_base'];

	$db_id = JRequest::getString('db', false);
	$dv_conf_text = JRequest::getString('conf_text', false);

	$dv_conf_file = $base . DS . $db_id . DS . 'applications/dataviewer/config.json';
	file_put_contents($dv_conf_file, $dv_conf_text);

	$_SESSION['dataviewer']['conf_file_updated'] = true;

	$url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']);
	$url .= "/administrator/index.php?option=com_" . $conf['com_name'] . "&task=config&db=$db_id";
	header("Location: $url");
	exit;
}
?>
