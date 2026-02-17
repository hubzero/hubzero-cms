<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Modes;

use Components\Dataviewer\Site\DvConfig;

class ModeDb
{
	public static function get_conf($db_id)
	{
		$db_dv_conf = array();

		$db_name = isset($db_id['name']) ? $db_id['name'] : '';

		if (empty($db_name)) {
			return array();
		}

		// Base directory
		DvConfig::$dv_conf['db_base_dir'] = \Component::params('com_databases')->get('base_dir');
		if (!DvConfig::$dv_conf['db_base_dir'] || DvConfig::$dv_conf['db_base_dir'] == '') {
			DvConfig::$dv_conf['db_base_dir'] = '/db/databases';
		}

		$db_conf_file = DvConfig::$dv_conf['db_base_dir'] . "/$db_name/database.json";
		$db_conf = json_decode(file_get_contents($db_conf_file), true);
		DvConfig::$dv_conf['db'] = array_merge(DvConfig::$dv_conf['db'], $db_conf['database_ro']);

		$dv_conf_file = DvConfig::$dv_conf['db_base_dir'] . "/$db_name/applications/" . DvConfig::$com_name . "/config.json";

		if (file_exists($dv_conf_file)) {
			$db_dv_conf = json_decode(file_get_contents($dv_conf_file), true);
			if (!is_array($db_dv_conf)) {
				$db_dv_conf = array();
			} if (isset($db_dv_conf['settings'])) {
				$db_dv_conf['settings'] = array_merge(DvConfig::$dv_conf['settings'], $db_dv_conf['settings']);
			}
		}

		if (!isset(DvConfig::$dv_conf['base_path'])) {
			DvConfig::$dv_conf['base_path'] = '';
		}

		DvConfig::$dv_conf = array_merge(DvConfig::$dv_conf, $db_dv_conf);

		return DvConfig::$dv_conf;
	}

	public static function get_dd($db_id)
	{
		$dd = false;
		$dv_id = \Request::getString('dv');
		$db_name = $db_id['name'];

		DvConfig::$dv_conf['dd_json'] = DvConfig::$dv_conf['db_base_dir'] . "/$db_name/applications/dataviewer/datadefinitions";

		$dd_json_file = false;
		$jsonPath = DvConfig::$dv_conf['dd_json'] . DS . $dv_id . '.json';
		if (isset(DvConfig::$dv_conf['dd_json']) && file_exists($jsonPath)) {
			$dd_json_file = $jsonPath;
		}

		if (isset($db_id['extra']) && $db_id['extra'] == 'table') {
			$dd['title'] = 'Table : ' . $dv_id;
			$dd['table'] = $dv_id;

			if (!\User::isGuest() && isset(DvConfig::$dv_conf['_managers']) && DvConfig::$dv_conf['_managers'] !== false) {
				$dd['acl']['allowed_groups'] = DvConfig::$dv_conf['_managers'];
			} elseif (!\User::isGuest() && \User::authorise('login', 'administrator')) {
				// Remove access restrictions for managers
				$dd['acl']['allowed_users'] = false;
				$dd['acl']['allowed_groups'] = false;
			}
		} else {
			if ($dd_json_file) {
				$dd = json_decode(file_get_contents($dd_json_file), true);
			} elseif ($dd_php_file) {
				require_once($dd_php_file);
				$dd_func = 'get_' . $dv_id;
				if (function_exists($dd_func)) {
					$dd = $dd_func();
				}
			} else {
				\App::abort(404, 'Invalid or Missing Dataview', 'Invalid or Missing Dataview');
				exit;
			}


			$dd['conf'] = (isset($dd['conf'])) ? $dd['conf'] : array();

			if (isset($dd['conf']['proc_mode_switch'])) {
				DvConfig::$dv_conf['proc_mode_switch'] = $dd['conf']['proc_mode_switch'];
			}

			if (isset($dd['conf']['proc_switch_threshold'])) {
				DvConfig::$dv_conf['proc_switch_threshold'] = $dd['conf']['proc_switch_threshold'];
			}

			// Database override form dd
			if (isset($dd['db']) && is_array($dd['db'])) {
				DvConfig::$dv_conf['db'] = array_merge(DvConfig::$dv_conf['db'], $dd['db']);
			}

			$dd = self::_dd_post($dd);
		}

		/* Dynamically set processing mode */
		if (isset(DvConfig::$dv_conf['proc_mode_switch']) && DvConfig::$dv_conf['proc_mode_switch']) {
			$link = \Components\Dataviewer\Site\Lib\Db::get_db();
			$link->setQuery(\Components\Dataviewer\Site\Lib\Db::query_gen_total($dd));
			$link->loadAssoc();
			$link->setQuery('SELECT FOUND_ROWS() AS total');
			$total = $link->loadAssoc();
			$total = isset($total['total']) ? $total['total'] : 0;
			$dd['total_records'] = $total;

			$vis_col_count = 0;
			if (isset($dd['cols'])) {
				$vis_col_count = count(array_filter($dd['cols'], function ($col) {
					return !isset($col['hide']);
				}));
			} elseif (isset($db_id['extra']) && $db_id['extra'] == 'table') {
				$link->setQuery("SELECT COUNT(*) AS cols FROM information_schema.columns WHERE table_name = " . $link->quote($dd['table']));
				$cols = $link->loadAssoc();
				$vis_col_count = $cols['cols'];
			}

			if (DvConfig::$dv_conf['proc_switch_threshold'] < ($total * $vis_col_count)) {
				$dd['serverside'] = true;
			}
		}

		$dd['db_id'] = $db_id;
		$dd['dv_id'] = $dv_id;

		return $dd;
	}

	public static function _dd_post($dd)
	{
		$id = \Request::getString('id', false);

		if ($id) {
			$dd['where'][] = array('field' => $dd['pk'], 'value' => $id);
			$dd['single'] = true;
		}

		$custom_field =  \Request::getString('custom_field', false);
		if ($custom_field) {
			$custom_field = explode('|', $custom_field);
			$dd['where'][] = array('field' => $custom_field[0], 'value' => $custom_field[1]);
			$dd['single'] = true;
		}

		// Data for Custom Views
		$custom_view = \Request::getString('custom_view', '');

		if ($custom_view != '') {
			$custom_view = explode(',', $custom_view);
			unset($dd['customizer']);

			// Custom Title
			$custom_title = \Request::getString('custom_title', '');
			if ($custom_title !== '') {
				$dd['title'] = htmlspecialchars($custom_title);
			}

			// Custom Group by
			$group_by = \Request::getString('group_by', '');
			if ($group_by !== '') {
				$dd['group_by'] = htmlspecialchars($group_by);
			}

			// Ordering
			$order_cols = $dd['cols'];
			$dd['cols'] = array();
			foreach ($custom_view as $cv_col) {
				$dd['cols'][$cv_col] = $order_cols[$cv_col];
			}

			// Hiding
			foreach ($order_cols as $id => $prop) {
				if (!in_array($id, $custom_view)) {
					$dd['cols'][$id] = $prop;

					if (!isset($dd['cols'][$id]['hide'])) {
						$dd['cols'][$id]['hide'] = 'custom';
					}
				}
			}
		}

		return $dd;
	}

	public static function pathway($dd)
	{
		$document = \App::get('document');
		$document->setTitle($dd['title']);

		if (isset($_SERVER['HTTP_REFERER'])) {
			$ref_title = \Request::getString('ref_title', $dd['title'] . " Resource");
			$ref_title = htmlentities($ref_title);
			\Pathway::append($ref_title, $_SERVER['HTTP_REFERER']);
		}

		\Pathway::append($dd['title'], $_SERVER['REQUEST_URI']);
	}
}
