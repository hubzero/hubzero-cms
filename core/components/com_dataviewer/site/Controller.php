<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site;

class Controller
{
	public static function dispatch()
	{
		$db_id = array();

		$db_id['id'] = \Request::getString('db', '');
		$db_info = explode(':', $db_id['id']);
		// Sanitize the request-derived database identifier and mode (name feeds
		// SQL, file paths and a database name; mode selects a require'd mode_*.php).
		$db_id['name'] = str_replace('..', '', preg_replace('/[^A-Za-z0-9_.-]/', '', (string) $db_info[0]));
		$db_id['mode'] = isset($db_info[1]) ? preg_replace('/[^A-Za-z0-9]/', '', $db_info[1]) : 'db';
		$db_id['extra'] = isset($db_info[2]) ? preg_replace('/[^A-Za-z0-9_.-]/', '', $db_info[2]) : false;

		// Rebuild the composite id from the sanitized parts (keeping its shape): the
		// views and filter output echo it into URLs and HTML attributes
		$parts = array($db_id['name']);
		if (isset($db_info[1])) {
			$parts[] = $db_id['mode'];
		}
		if (isset($db_info[2])) {
			$parts[] = $db_id['extra'];
		}
		$db_id['id'] = implode(':', $parts);

		DvConfig::$dv_conf['settings']['db_id'] = $db_id;

		// Only the shipped modes exist; anything else names a class that does not
		if ($db_id['name'] === '' || !in_array($db_id['mode'], array('db', 'ds', 'dsl'), true)) {
			\App::abort(404, 'Invalid or Missing Dataview', 'Invalid or Missing Dataview');
		}

		/* Include database mode specific functionality */
		$modeClass = __NAMESPACE__ . '\\Modes\\Mode' . ucfirst($db_id['mode']);

		/* Update config with DB specific values */
		$modeClass::get_conf($db_id);

		/* Store mode class for use in task methods */
		DvConfig::$dv_conf['settings']['mode_class'] = $modeClass;

		$task = strtolower(\Request::getCmd('task'));
		$task_method = 'task_' . $task;

		if (method_exists(self::class, $task_method)) {
			self::$task_method($db_id);
		} else {
			\App::abort(404, 'Invalid or Missing Dataview', 'Invalid or Missing Dataview');
		}
	}

	public static function task_view($db_id)
	{
		$view = 'spreadsheet';

		$modeClass = DvConfig::$dv_conf['settings']['mode_class'];
		$dd = $modeClass::get_dd($db_id);

		if (!$dd) {
			throw new \Exception('Invalid DataView', 404);
			return;
		}

		if (!self::authorize($dd)) {
			print ('<br /><p class="warning">Sorry, you are not authorized to view this page.</p>');
			return;
		}

		// This name selects the filter class and comes straight from the request,
		// so reduce it to the charset every shipped filter is written in.
		$filter = preg_replace('/[^a-z0-9_]/', '', strtolower(\Request::getString('format', 'json')));
		$filterClass = __NAMESPACE__ . '\\Filter\\' . ucfirst($filter);

		$modeClass::pathway($dd);

		$viewClass = __NAMESPACE__ . '\\View\\' . ucfirst($view);
		$viewClass::render($dd);
	}

	public static function task_data($db_id)
	{
		$modeClass = DvConfig::$dv_conf['settings']['mode_class'];
		$dd = $modeClass::get_dd($db_id);


		if (!self::authorize($dd)) {
			print ('<br /><p class="error">Sorry, you are not authorized to view this page.</p>');
			return;
		}

		// As in task_view above: request-controlled part of a class name.
		$filter = preg_replace('/[^a-z0-9_]/', '', strtolower(\Request::getString('type', 'csv')));
		$filterClass = __NAMESPACE__ . '\\Filter\\' . ucfirst($filter);

		if ($dd) {
			$link = Lib\Db::get_db();

			$sql = Lib\Db::query_gen($dd);

			$res = Lib\Db::get_results($sql, $dd);

			$filteredResults = $filterClass::filter($res, $dd);

			print $filteredResults;
			exit(0);
		} else {
			print "<p class=\"error\">Invalid Request</p>";
			exit(1);
		}
	}

	public static function authorize($dd)
	{
		$allowedUsers = isset($dd['acl']['allowed_users']) ? $dd['acl']['allowed_users'] : null;
		if (isset($allowedUsers) && (is_array($allowedUsers) || $allowedUsers === false || $allowedUsers == 'registered')) {
			DvConfig::$dv_conf['acl']['allowed_users'] = $dd['acl']['allowed_users'];
		}

		$allowedGroups = isset($dd['acl']['allowed_groups']) ? $dd['acl']['allowed_groups'] : null;
		if (isset($allowedGroups) && (is_array($allowedGroups) || $allowedGroups === false)) {
			DvConfig::$dv_conf['acl']['allowed_groups'] = $dd['acl']['allowed_groups'];
		}

		$usersAllowed = DvConfig::$dv_conf['acl']['allowed_users'];
		$groupsAllowed = DvConfig::$dv_conf['acl']['allowed_groups'];
		if ($usersAllowed === false && $groupsAllowed === false || isset($dd['acl']['public'])) {
			return true;
		} elseif (\User::isGuest()) {
			$redir_url = '?return=' . base64_encode($_SERVER['REQUEST_URI']);
			$login_url = '/login';
			$url = $login_url . $redir_url;
			header('Location: ' . $url);
			return;
		}

		if (!\User::isGuest() && isset($dd['acl']['registered'])) {
			return true;
		}

		if ($usersAllowed !== false && $usersAllowed == 'registered' && !\User::isGuest()) {
			return true;
		} elseif (isset($usersAllowed) && is_array($usersAllowed) && !\User::isGuest()) {
			if (in_array(\User::get('username'), DvConfig::$dv_conf['acl']['allowed_users'])) {
				return true;
			}
		}

		if ($groupsAllowed !== false && is_array($groupsAllowed) && !\User::isGuest()) {
			$groups = \Hubzero\User\Helper::getGroups(\User::get('id'));
			if ($groups && count($groups)) {
				foreach ($groups as $g) {
					if (in_array($g->cn, DvConfig::$dv_conf['acl']['allowed_groups'])) {
						return true;
					}
				}
			}
		}

		return false;
	}
}
