<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

function controller_exec()
{
	global $conf;

	if (!authorized()) {
		$err_str = 'Access restricted.';

		if ($conf['modes']['db']['enabled']) {
			$group = $conf['access_limit_to_group'];
			Toolbar::title('Databases', 'databases');
			Toolbar::preferences('com_databases', '200');
			$err_str =  "<p class=\"error\">Not authorized, access is limited to \"<em>$group</em>\"</p>. <h3>Use the Databases component parameters to change this</h3>";
		}

		print $err_str;
		return;
	}


	// Get the task
	$task = Request::getCmd('task', 'list');

	$task_file = __DIR__ . DS . 'tasks' . DS . $task . '.php';
	if (require_once($task_file)) {
		$task_func = 'dv_' . $task;
		if (function_exists($task_func)) {
			if (file_exists(__DIR__ . DS . 'tasks' . DS . 'html' . DS . $task . '.js')) {
				$document = App::get('document');
				$document->addScript(DB_PATH . DS . 'tasks' . DS . 'html' . DS . $task . '.js?v=2');
			}
			$task_func();
		}
	}
}

function authorized()
{
	global $conf;

	if ($conf['access_limit_to_group'] === false) {
		return true;
	}

	if ($conf['access_limit_to_group'] !== false && !User::isGuest()) {
		$groups = \Hubzero\User\Helper::getGroups(User::get('id'));
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
