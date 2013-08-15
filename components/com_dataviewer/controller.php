<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2012,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

function controller()
{
	global $dv_conf;
	$db_id = array();

	$db_id['id'] = JRequest::getVar('db');
	$db_info = explode(':', $db_id['id']);
	$db_id['name'] = $db_info[0];
	$db_id['mode'] = isset($db_info[1]) ? $db_info[1] : 'db';
	$db_id['extra'] = isset($db_info[2]) ? $db_info[2] : false;

	$dv_conf['settings']['db_id'] = $db_id;

	/* Include database mode specific functionality */
	require_once(JPATH_COMPONENT . DS . 'modes' . DS . 'mode_' . $db_id['mode'] . '.php');

	/* Update config with DB specific values */
	get_conf($db_id);



	$task = strtolower(JRequest::getVar('task'));
	$task_func = 'task_' . $task;
	
	if (function_exists($task_func)) {
		$task_func($db_id);
	} else {
		JError::raiseError('501', 'Invalid Task', 'Invalid Task');
	}
}

function task_file($db_id)
{
	$view = 'file';
	$file = (JPATH_COMPONENT.DS."view".DS."$view.php");
	
	if (file_exists($file)) {
		require_once ($file);
		view();
	}
}

function task_view($db_id)
{
	global $dv_conf;
	$view = 'spreadsheet';

	$dd = get_dd($db_id);

	if (!authorize($dd)) {
		print ('<br /><p class="warning">Sorry, you are not authorized to view this page.</p>');
		return;
	}

	$filter = strtolower(JRequest::getVar( 'format', 'json' ));
	$file = (JPATH_COMPONENT.DS."filter/$filter.php");
	if (file_exists($file)) {
		require_once ($file);
	}


	pathway($dd);

	$file = (JPATH_COMPONENT.DS."view".DS."$view.php");
	if (file_exists($file)) {
		require_once ($file);
		view($dd);
	}
}

function task_data($db_id)
{
	global $dv_conf;
	$dd = get_dd($db_id);


	if (!authorize($dd)) {
		print ('<br /><p class="error">Sorry, you are not authorized to view this page.</p>');
		return;
	}

	$filter = strtolower(JRequest::getVar('type', 'json'));
	$file = (JPATH_COMPONENT.DS."filter/$filter.php");
	if (file_exists($file)) {
		require_once ($file);
	}

	if ($dd) {
		$link = get_db();

		$sql = query_gen($dd);

		$res = get_results($sql, $dd);
		print filter($res, $dd);
		exit(0);
	} else {
		print print "<p class=\"error\">Invalid Request</p>";
		exit(1);
	}
}

function authorize($dd)
{
	global $dv_conf;
	ximport('Hubzero_Group');
	ximport('Hubzero_User_Helper');
	$juser =& JFactory::getUser();

	if (isset($dd['acl']['allowed_users']) && (is_array($dd['acl']['allowed_users']) || $dd['acl']['allowed_users'] === false || $dd['acl']['allowed_users'] == 'registered')) {
		$dv_conf['acl']['allowed_users'] = $dd['acl']['allowed_users'];
	}

	if (isset($dd['acl']['allowed_groups']) && (is_array($dd['acl']['allowed_groups']) || $dd['acl']['allowed_groups'] === false)) {
		$dv_conf['acl']['allowed_groups'] = $dd['acl']['allowed_groups'];
	}

	if ($dv_conf['acl']['allowed_users'] === false && $dv_conf['acl']['allowed_groups'] === false || isset($dd['acl']['public'])) {
		return true;
	} elseif ($juser->get('guest')) {
		$redir_url = '?return=' . base64_encode($_SERVER['REQUEST_URI']);
		$login_url = '/login';
		$url = $login_url . $redir_url;
		header('Location: ' . $url);
		return;
	}

	if (!$juser->get('guest') && isset($dd['acl']['registered'])) {
		return true;
	}

	if ($dv_conf['acl']['allowed_users'] !== false && $dv_conf['acl']['allowed_users'] == 'registered' && !$juser->get('guest')) {
		return true;
	} elseif (isset($dv_conf['acl']['allowed_users']) && is_array($dv_conf['acl']['allowed_users']) && !$juser->get('guest')) {
		if(in_array($juser->get('username'), $dv_conf['acl']['allowed_users'])) {
			return true;
		}
	}
	
	if ($dv_conf['acl']['allowed_groups'] !== false && is_array($dv_conf['acl']['allowed_groups']) && !$juser->get('guest')) {
		$groups = Hubzero_User_Helper::getGroups($juser->get('id'));
		if ($groups && count($groups)) {
			foreach ($groups as $g) {
				if (in_array($g->cn, $dv_conf['acl']['allowed_groups'])) {
					return true;
				}
			}
		}
	}

	return false;
}


?>
