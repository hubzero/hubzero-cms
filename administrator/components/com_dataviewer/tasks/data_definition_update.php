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

function dv_data_definition_update()
{
	check_rid();
	global $com_name, $conf;
	$base = $conf['dir_base'];

	$db_id = JRequest::getString('db', false);
	$dd_name = JRequest::getString('dd', false);
	$dd_text = stripslashes($_POST['dd_text']);


	$db_conf_file = $base . DS . $db_id . DS . 'database.json';
	$db_conf = json_decode(file_get_contents($db_conf_file), true);

	$juser =& JFactory::getUser();
	$author = $juser->get('name') . ' <' . $juser->get('email') . '>';


	$dd_file_php = "$base/$db_id/applications/$com_name/datadefinitions-php/$dd_name.php";
	file_put_contents($dd_file_php, $dd_text);

	$cmd = "cd $base/$db_id/applications/$com_name/datadefinitions-php/; git commit $dd_name.php --author=\"$author\" -m\"[UPDATE] $dd_name.php.\"  > /dev/null";
	system($cmd);

	$dd_file_json = "$base/$db_id/applications/$com_name/datadefinitions/$dd_name.json";
	
	$cmd = "cd " . JPATH_COMPONENT . "; php ./ddconvert.php -i$dd_file_php -o$dd_file_json";
	system($cmd);

	$cmd = "cd $base/$db_id/applications/$com_name/datadefinitions/; git commit $dd_name.json --author=\"$author\" -m\"[UPDATE] $dd_name.json.\"  > /dev/null";
	system($cmd);

	$url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']);
	$url .= "/administrator/index.php?option=com_$com_name&task=data_definition&db=$db_id&dd=$dd_name";
	header("Location: $url");
	exit;
}
?>
