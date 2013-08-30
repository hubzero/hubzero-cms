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


function get_conf($db_id)
{
	global $dv_conf, $com_name;
	$db_dv_conf = array();
	$db_name = $db_id['name'];

	$db_conf_file = "/data/db/$db_name/database.json";
	$db_conf = json_decode(file_get_contents($db_conf_file), true);
	$dv_conf['db'] = array_merge($dv_conf['db'], $db_conf['database_ro']);

	$dv_conf_file = "/data/db/$db_name/applications/$com_name/config.json";

	if (file_exists($dv_conf_file)) {
		$db_dv_conf = json_decode(file_get_contents($dv_conf_file), true);
		if (!is_array($db_dv_conf)) {
			$db_dv_conf = array();
		} if(isset($db_dv_conf['settings'])) {
			$db_dv_conf['settings'] = array_merge($dv_conf['settings'], $db_dv_conf['settings']);
		}
	}

	$dv_conf = array_merge($dv_conf, $db_dv_conf);
	
	return $dv_conf;
}

function get_dd($db_id)
{
	global $dv_conf;
	$dd = false;
	$dv_id = JRequest::getVar('dv');
	$db_name = $db_id['name'];

	$dv_conf['dd_json'] = "/data/db/$db_name/applications/dataviewer/datadefinitions";

	$dd_json_file = (isset($dv_conf['dd_json']) && file_exists($dv_conf['dd_json'] . DS . $dv_id . '.json'))? $dv_conf['dd_json'] . DS . $dv_id . '.json': false;

	if ($db_id['extra'] && $db_id['extra'] == 'table') {
		$dd['title'] = 'Table : ' . $dv_id;
		$dd['table'] = $dv_id;
		$dd['serverside'] = true;

		$juser =& JFactory::getUser();
		if (!$juser->get('guest') && isset($dv_conf['_managers']) && $dv_conf['_managers'] !== false) {
			$dd['acl']['allowed_groups'] = $dv_conf['_managers'];
		} elseif (!$juser->get('guest') && $juser->authorize('login', 'administrator')) {
			// Remove access restrictions for managers
			$dd['acl']['allowed_users'] = false;
			$dd['acl']['allowed_groups'] = false;
		}
	} else {

		if ($dd_json_file) {
			$dd = json_decode(file_get_contents($dd_json_file), true);
		} elseif ($dd_php_file) {
			require_once ($dd_php_file);
			$dd_func = 'get_' . $dv_id;
			if (function_exists($dd_func)) {
				$dd = $dd_func();
			}
		} else {
			JError::raiseError('501', 'Invalid Dataview', 'Invalid Dataview');
			exit;
		}


		$dd['conf'] = (isset($dd['conf'])) ? $dd['conf'] : array();

		if(isset($dd['conf']['proc_mode_switch'])) {
			$dv_conf['proc_mode_switch'] = $dd['conf']['proc_mode_switch'];
		}

		if(isset($dd['conf']['proc_switch_threshold'])) {
			$dv_conf['proc_switch_threshold'] = $dd['conf']['proc_switch_threshold'];
		}


		$dd = _dd_post($dd);

		/* Dynamically set processing mode */
		if (isset($dv_conf['proc_mode_switch']) && $dv_conf['proc_mode_switch']) {
			$link = get_db();
			$total = mysql_query(query_gen_total($dd), $link);
			$total = mysql_fetch_assoc($total);
			$total = isset($total['total']) ? $total['total'] : 0;
			$dd['total_records'] = $total;

			$vis_col_count = 0;
			if(isset($dd['cols'])) {
				$vis_col_count = count(array_filter($dd['cols'], function ($col) { return !isset($col['hide']); }));
			}

			if ($dv_conf['proc_switch_threshold'] < ($total * $vis_col_count)) {
				$dd['serverside'] = true;
			}
		}
	}

	$dd['db_id'] = $db_id;
	$dd['dv_id'] = $dv_id;

	return $dd;
}

function _dd_post($dd)
{
	$id = JRequest::getVar('id', false);

	if ($id) {
		$dd['where'][] = array('field'=>$dd['pk'], 'value'=>$id);
		$dd['single'] = true;
	}

	$custom_field =  JRequest::getVar('custom_field', false);
	if ($custom_field) {
		$custom_field = explode('|', $custom_field);
		$dd['where'][] = array('field'=>$custom_field[0], 'value'=>$custom_field[1]);
		$dd['single'] = true;
	}

	// Data for Custom Views
	$custom_view = JRequest::getVar('custom_view', array());
	if (count($custom_view) > 0) {
		unset($dd['customizer']);

		// Custom Title
		$custom_title = JRequest::getString('custom_title', '');
		if ($custom_title !== '') {
			$dd['title'] = htmlspecialchars($custom_title);
		}

		// Custom Group by
		$group_by = JRequest::getString('group_by', '');
		if ($group_by !== '') {
			$dd['group_by'] = htmlspecialchars($group_by);
		}

		// Ordering
		$order_cols = $dd['cols'];
		$dd['cols'] = array();
		foreach($custom_view as $cv_col) {
			$dd['cols'][$cv_col] = $order_cols[$cv_col];
		}

		// Hiding
		foreach($order_cols as $id=>$prop) {
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


function pathway($dd)
{
	$document = &JFactory::getDocument();
	$document->setTitle($dd['title']);
	$mainframe = &JFactory::getApplication();
	$pathway =& $mainframe->getPathway();

	if(isset($_SERVER['HTTP_REFERER'])) {
		$ref_title = JRequest::getString('ref_title', $dd['title'] . " Resource");
		$ref_title = htmlentities($ref_title);
		$pathway->addItem($ref_title, $_SERVER['HTTP_REFERER']);
	}

	$pathway->addItem($dd['title'], $_SERVER['REQUEST_URI']);
}
?>
