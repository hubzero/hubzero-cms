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

function dv_data_definition_remove()
{
	check_rid();
	global $com_name, $conf;
	$base = $conf['dir_base'];

	$db_id = JRequest::getString('db', false);
	$dd_name = JRequest::getString('dd_name', false);

	$juser =& JFactory::getUser();
	$author = $juser->get('name') . ' <' . $juser->get('email') . '>';


	$dd_file_php = "$base/$db_id/applications/$com_name/datadefinitions-php/$dd_name.php";
	system("rm $dd_file_php");
	$cmd = "cd $base/$db_id/applications/$com_name/datadefinitions-php/; git commit $dd_name.php --author=\"$author\" -m\"[DELETE] $dd_name.php.\"  > /dev/null";
	system($cmd);

	$dd_file_json = "$base/$db_id/applications/$com_name/datadefinitions/$dd_name.json";
	system("rm $dd_file_json");
	$cmd = "cd $base/$db_id/applications/$com_name/datadefinitions/; git commit $dd_name.json --author=\"$author\" -m\"[DELETE] $dd_name.json.\"  > /dev/null";
	system($cmd);

	db_msg('Dataview successfully removed', 'message');
	$url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']);
	$url .= "/administrator/index.php?option=com_$com_name&task=dataview_list&db=$db_id";
	header("Location: $url");
}
?>
