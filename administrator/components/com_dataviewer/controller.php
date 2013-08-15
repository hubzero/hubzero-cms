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

function controller_exec()
{
	global $conf;

	if (!authorized()) {
		$params = &JComponentHelper::getParams('com_databases');
		$group = $params->get('access_limit_to_group');
		JToolBarHelper::title('Databases', 'databases');
		JToolBarHelper::preferences('com_databases', '200');
		print "<p class=\"error\">Not authorized, access is limited to \"<em>$group</em>\"</p>. <h3>Use the Databases component parameters to change this</h3>";
		return;
	}


	// Get the task
	$task = JRequest::getVar('task', 'list');

	$task_file = JPATH_COMPONENT . DS . 'tasks' . DS . $task . '.php';
	if(require_once($task_file)) {
		$task_func = 'dv_' . $task;
		if (function_exists($task_func)) {
			if (file_exists(JPATH_COMPONENT . DS . 'tasks' . DS . 'html' . DS . $task . '.js')) {
				$document = &JFactory::getDocument();
				$document->addScript(DB_PATH . DS . 'tasks' . DS . 'html' . DS . $task . '.js?v=2');
			}
			$task_func();
		}
	}
}

function authorized()
{
	global $conf;
	$juser =& JFactory::getUser();
	ximport('Hubzero_User_Helper');

	if ($conf['access_limit_to_group'] === false) {
		return true;
	}

	if ($conf['access_limit_to_group'] !== false && !$juser->get('guest')) {
		$groups = Hubzero_User_Helper::getGroups($juser->get('id'));
		if ($groups && count($groups)) {
			foreach ($groups as $g) {
				if ($g->cn == $conf['access_limit_to_group']) {
					return true;
				}
			}
		}
	}

	return false;
}
?>
