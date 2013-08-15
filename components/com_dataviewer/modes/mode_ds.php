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

	$params = &JComponentHelper::getParams('com_datastores');
	$dv_conf['db']['host'] = $params->get('db_host');
	$dv_conf['db']['user'] = $params->get('db_ro_user');
	$dv_conf['db']['password'] = $params->get('db_ro_pass');

	$dv_conf['db']['database'] = 'ds_' . $db_id['name'];

	$dv_conf['base_path'] = '/data/datastores/' . $db_id['name'];

	return $dv_conf;
}

function get_dd($db_id)
{
	global $dv_conf;
	$dd = false;
	$db = &JFactory::getDBO();

	$dv_id = JRequest::getVar('dv');

	if ($db_id['extra']) {
		$sql = "SELECT * FROM #__datastore_tables WHERE datastore_id = " . $db_id['name'] . " AND id = " . $db->quote($dv_id);
		$db->setQuery($sql);
		$r = $db->loadAssoc();

		$td = json_decode($r['table_definition'], true);
		
		$dd['db'] = $dv_conf['db'];
		$dd['db']['name'] = 'ds_' . $r['datastore_id'];
		$dd['table'] = $td['name'];
		$dd['title'] = $r['name'];

		if ($db_id['extra'] == 'table') {
			foreach($td['columns'] as $col) {
				if ($col['name'] != '__ds_rec_id') {
					if ($col['type'] == 'file') {
						$dd['cols'][$td['name'] . '.' . $col['name']]['type'] = 'file';
						$dd['cols'][$td['name'] . '.' . $col['name']]['type_extra'] = $col['type_extra'];
						$dd['cols'][$td['name'] . '.' . $col['name']]['ds-repo-path'] = "/file_repo/{$td['name']}/{$col['name']}";
						$dd['cols'][$td['name'] . '.' . $col['name']]['file-verify'] = true;
					}

					if ($col['type'] == 'txt' && ($col['type_extra'] == 'medium' || $col['type_extra'] == 'large')) {
						$dd['cols'][$td['name'] . '.' . $col['name']]['width'] = '150';
						$dd['cols'][$td['name'] . '.' . $col['name']]['truncate'] = 'truncate';
					}

					$dd['cols'][$td['name'] . '.' . $col['name']]['label'] = $col['label'];
				}
			}
		} elseif ($db_id['extra'] == 'update') {
			$update_link = '/datastores/' . $db_id['name'] . '/table/data_record_update/?table=' . $dv_id . '&__ds_rec_id=';

			$dd['cols'][$td['name'] . '.__ds_rec_id'] = array(
				'label'=>'Select <br />Record',
				'raw'=>"CONCAT('$update_link', __ds_rec_id)",
				'type'=>'link',
				'relative'=>'true',
				'link_label'=>'Edit',
				'link_title'=>'Click here to update or remove this record'
			);

			foreach($td['columns'] as $col) {
				if ($col['name'] != '__ds_rec_id') {
					if ($col['type'] == 'file') {
						$dd['cols'][$td['name'] . '.' . $col['name']]['type'] = 'file';
						$dd['cols'][$td['name'] . '.' . $col['name']]['type_extra'] = $col['type_extra'];
						$dd['cols'][$td['name'] . '.' . $col['name']]['ds-repo-path'] = "/file_repo/{$td['name']}/{$col['name']}";
						$dd['cols'][$td['name'] . '.' . $col['name']]['file-verify'] = true;
					}

					if ($col['type'] == 'txt' && ($col['type_extra'] == 'medium' || $col['type_extra'] == 'large')) {
						$dd['cols'][$td['name'] . '.' . $col['name']]['width'] = '150';
						$dd['cols'][$td['name'] . '.' . $col['name']]['truncate'] = 'truncate';
					}

					$dd['cols'][$td['name'] . '.' . $col['name']]['label'] = $col['label'];
				}
			}
		}
	} else {
		$dsid = $db_id['name'];
		$path = "/data/datastores/$dsid/datadefinitions";
		$dd_file = "$dv_id.json";
		$dd = json_decode(file_get_contents("$path/$dd_file"), true);
	}
	
	
	
	$dd['db_id'] = $db_id;
	$dd['dv_id'] = $dv_id;

	$dd = _dd_post($dd);


	/* Dynamically set processing mode */
	$link = get_db($dd['db']);
	$cell_count_threshold = 20000;
	$total = mysql_query(query_gen_total($dd), $link);
	$total = mysql_fetch_assoc($total);
	$total = isset($total['total']) ? $total['total'] : 0;
	$dd['total_records'] = $total;

	$vis_col_count = count(array_filter($dd['cols'], function ($col) { return !isset($col['hide']); }));

	if ($cell_count_threshold < ($total * $vis_col_count)) {
		$dd['serverside'] = true;
	}


	// Record Filters
	if (isset($dd['record_filters']) && is_array($dd['record_filters'])) {
		foreach($dd['record_filters'] as $f) {
			switch($f['type']) {
				case 'E':
					$dd['where'][] = array('raw'=>$f['col'] . " = '" . $f['val'] . "'");
					break;
				case 'NE':
					$dd['where'][] = array('raw'=>$f['col'] . " <> '" . $f['val'] . "'");
					break;
				case 'LT':
					$dd['where'][] = array('raw'=>$f['col'] . " < '" . $f['val'] . "'");
					break;
				case 'GT':
					$dd['where'][] = array('raw'=>$f['col'] . " > '" . $f['val'] . "'");
					break;
				case 'LK':
					$dd['where'][] = array('raw'=>$f['col'] . " LIKE '%" . $f['val'] . "%'");
					break;
				case 'NLK':
					$dd['where'][] = array('raw'=>$f['col'] . " NOT LIKE '%" . $f['val'] . "%'");
					break;
				case 'NULL':
					$dd['where'][] = array('raw'=>$f['col'] . " IS NULL");
					break;
				case 'NNULL':
					$dd['where'][] = array('raw'=>$f['col'] . " IS NOT NULL");
					break;
			}
		}
	}


	/* ACL */
	$sql = 'SELECT username FROM #__datastores ds LEFT JOIN #__users u ON (u.id = ds.created_by)';
	$db->setQuery($sql);
	$managers = $db->loadResultArray();

	if (!isset($dd['acl'])) {
		$dd['acl']['allowed_users'] = $managers;
	} elseif(!isset($dd['acl']['registered']) || !isset($dd['acl']['public'])) {
		$dd['acl']['allowed_users'] = isset($dd['acl']['allowed_users']) ? $dd['acl']['allowed_users'] : array();
		$dd['acl']['allowed_users'] = array_merge($dd['acl']['allowed_users'], $managers);
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
