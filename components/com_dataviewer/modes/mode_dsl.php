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

	$params = new JParameter(JPluginHelper::getPlugin('projects', 'databases')->params);

	$dv_conf['db']['host'] = $params->get('db_host');
	$dv_conf['db']['user'] = $params->get('db_ro_user');
	$dv_conf['db']['password'] = $params->get('db_ro_password');

	return $dv_conf;
}

function get_dd($db_id, $dv_id = false, $version = false)
{
	global $dv_conf;
	$dd = false;
	$db = &JFactory::getDBO();

	if (!$dv_id) {
		$dv_id = JRequest::getVar('dv');
	}

	if (!$version) {
		$version = JRequest::getInt('v', false);
	}

	$name = $dv_id;


	if (!$version) {
		$sql = 'SELECT data_definition FROM #__project_databases WHERE `database_name` = ' . $db->quote($name);
		$db->setQuery($sql);
		$database = $db->loadAssoc();
		$dd = json_decode($database['data_definition'], true);
	} else {
		$sql = 'SELECT data_definition FROM #__project_database_versions WHERE database_name=' . $db->quote($name) .
			' AND version=' . $db->quote($version);
		$db->setQuery($sql);
		$ver = $db->loadAssoc();
		$dd = json_decode($ver['data_definition'], true);

		// Check publication state
		$sql = 'SELECT state FROM #__publication_versions ' .
			'LEFT JOIN #__publication_attachments ON ' .
				'(#__publication_versions.publication_id=#__publication_attachments.publication_id '.
				'AND #__publication_versions.id=#__publication_attachments.publication_version_id) '.
			'WHERE object_name=' . $db->quote($name);

		$db->setQuery($sql);
		$state = $db->loadResult();

		$dd['publication_state'] = $state;

	}

	$dv_conf['db']['database'] = $dd['database'];

	$dd['db_id'] = $db_id;
	$dd['dv_id'] = $dv_id;

	_dd_post($dd);

	/* Dynamically set processing mode */
	$link = get_db($dv_conf['db']);
	$cell_count_threshold = 20000;
	$total = mysql_query(query_gen_total($dd), $link);
	$total = mysql_fetch_assoc($total);
	$total = isset($total['total']) ? $total['total'] : 0;
	$dd['total_records'] = $total;

	$vis_col_count = count(array_filter($dd['cols'], function ($col) { return !isset($col['hide']); }));

	if ($cell_count_threshold < ($total * $vis_col_count)) {
		$dd['serverside'] = true;
	}

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
	$db_id = $dd['db_id'];

	$document = &JFactory::getDocument();
	$document->setTitle($dd['title']);

	$mainframe = &JFactory::getApplication();
	$pathway = &$mainframe->getPathway();

	if (isset($db_id['extra']) && $db_id['extra'] == 'table') {
		$ref_title = "Datastore";
		$pathway->addItem($ref_title, '/datastores/' . $db_id['name'] . '#tables');
	} elseif(isset($_SERVER['HTTP_REFERER'])) {
		$ref_title = JRequest::getString('ref_title', $dd['title'] . " Resource");
		$ref_title = htmlentities($ref_title);
		$pathway->addItem($ref_title, $_SERVER['HTTP_REFERER']);
	}

	$pathway->addItem($dd['title'], $_SERVER['REQUEST_URI']);
}
?>
