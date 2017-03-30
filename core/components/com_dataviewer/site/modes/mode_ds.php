<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();


function get_conf($db_id)
{
	global $dv_conf, $com_name;

	$params = Component::params('com_datastores');
	$dv_conf['db']['host'] = $params->get('db_host');
	$dv_conf['db']['user'] = $params->get('db_ro_user');
	$dv_conf['db']['password'] = $params->get('db_ro_pass');

	$dv_conf['db']['database'] = 'ds_' . $db_id['name'];

	// DataStores base directory
	$ds_base_dir = $params->get('base_dir');
	if ($ds_base_dir == '') {
		$ds_base_dir = '/data/datastores'; // Switch the default to /db when it's created
	}

	$dv_conf['base_path'] = $ds_base_dir . '/' . $db_id['name'];

	return $dv_conf;
}

function get_dd($db_id)
{
	global $dv_conf;
	$dd = false;
	$db = App::get('db');

	$dv_id = Request::getVar('dv');

	if ($db_id['extra']) {
		$sql = "SELECT * FROM #__datastore_tables WHERE datastore_id = " . $db_id['name'] . " AND id = " . $db->quote($dv_id);
		$db->setQuery($sql);
		$r = $db->loadAssoc();

		$td = json_decode($r['table_definition'], true);

		$dd['db'] = $dv_conf['db'];
		$dd['db']['name'] = 'ds_' . $r['datastore_id'];
		$dd['table'] = $td['name'];
		$dd['title'] = $r['name'];

		if (isset($db_id['extra']) && ($db_id['extra'] == 'table' || $db_id['extra'] == 'update')) {

			if ($db_id['extra'] == 'update') {
				$update_link = '/datastores/' . $db_id['name'] . '/table/data_record_update/?table=' . $dv_id . '&__ds_rec_id=';

				$dd['cols'][$td['name'] . '.__ds_rec_id'] = array(
					'label'=>'Select <br />Record',
					'raw'=>"CONCAT('$update_link', __ds_rec_id)",
					'type'=>'link',
					'relative'=>'true',
					'link_label'=>'Edit',
					'link_title'=>'Click here to update or remove this record',
					'popup'=>array('window'=>'Edit_Record', 'features'=>'width=1175px,resizable,scrollbars,status')
				);
			}

			foreach ($td['columns'] as $col) {
				if ($col['name'] != '__ds_rec_id') {
					if ($col['type'] == 'file') {
						$dd['cols'][$td['name'] . '.' . $col['name']]['type'] = 'file';
						$dd['cols'][$td['name'] . '.' . $col['name']]['type_extra'] = $col['type_extra'];
						$dd['cols'][$td['name'] . '.' . $col['name']]['ds-repo-path'] = "/file_repo/{$td['name']}/{$col['name']}";
						$dd['cols'][$td['name'] . '.' . $col['name']]['file-verify'] = true;
					}

					if ($col['type'] == 'url') {
						$dd['cols'][$td['name'] . '.' . $col['name']]['type'] = 'url';
						$dd['cols'][$td['name'] . '.' . $col['name']]['url-display'] = 'full_link';
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
		$path = "{$dv_conf['base_path']}/datadefinitions";
		$dd_file = "$dv_id.json";
		if (file_exists("$path/$dd_file")) {
			$dd = json_decode(file_get_contents("$path/$dd_file"), true);
		} else {
			return false;
		}
	}



	$dd['db_id'] = $db_id;
	$dd['dv_id'] = $dv_id;

	$dd = _dd_post($dd);

	$dd['conf'] = (isset($dd['conf'])) ? $dd['conf'] : array();

	if (isset($dd['conf']['proc_mode_switch'])) {
		$dv_conf['proc_mode_switch'] = $dd['conf']['proc_mode_switch'];
	}

	if (isset($dd['conf']['proc_switch_threshold'])) {
		$dv_conf['proc_switch_threshold'] = $dd['conf']['proc_switch_threshold'];
	}

	/* Dynamically set processing mode */
	if (isset($dv_conf['proc_mode_switch']) && $dv_conf['proc_mode_switch']) {
		$link = get_db();
		mysqli_query($link, query_gen_total($dd));
                $total = mysqli_query($link, 'SELECT FOUND_ROWS() AS total');
		if ($total) {
			$total = mysqli_fetch_assoc($total);
			$total = isset($total['total']) ? $total['total'] : 0;
			$dd['total_records'] = $total;

			$vis_col_count = 0;
			if (isset($dd['cols'])) {
				$vis_col_count = count(array_filter($dd['cols'], function ($col) {
						return !isset($col['hide']);
					})
				);
			}

			if ($dv_conf['proc_switch_threshold'] < ($total * $vis_col_count)) {
				$dd['serverside'] = true;
			}
		}
	}


	// Record Filters
	if (isset($dd['record_filters']) && is_array($dd['record_filters'])) {
		foreach ($dd['record_filters'] as $f) {
			switch ($f['type']) {
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

	// Dataviews attached to resources & publised
	$sql = "SELECT r.id, r.published, r.access, r.group_owner, r.group_access, dv.path
		FROM jos_datastore_resources AS dr
			LEFT JOIN (jos_resources AS r, jos_resource_assoc ra, jos_resources AS dv) ON (r.id = dr.resource_id AND ra.parent_id = r.id AND ra.child_id = dv.id)
		WHERE r.id IS NOT NULL
			AND r.published = 1
			AND dr.datastore_id = {$db_id['name']}
			AND dv.path = '/dataviewer/view/{$db_id['name']}:ds/$dv_id/'";
	$db->setQuery($sql);
	$res = $db->loadAssoc();

	if (isset($res['id'])) {
		$dd['acl'] = array();

		// Public
		if ($res['access'] == 0) {
			$dd['acl']['public'] = true;
		}
	}

	//$sql = 'SELECT username FROM #__datastores ds LEFT JOIN #__users u ON (u.id = ds.created_by)';
	$sql = "SELECT username FROM #__datastore_users ds LEFT JOIN #__users u ON (u.id = ds.value AND ds.type='user') WHERE ds.id = " . $db_id['name'];
	$db->setQuery($sql);
	$managers = $db->loadColumn();

	if (!isset($dd['acl'])) {
		$dd['acl']['allowed_users'] = $managers;
	} elseif (!isset($dd['acl']['registered']) || !isset($dd['acl']['public'])) {
		$dd['acl']['allowed_users'] = isset($dd['acl']['allowed_users']) ? $dd['acl']['allowed_users'] : array();
		$dd['acl']['allowed_users'] = array_merge($dd['acl']['allowed_users'], $managers);
	}

	// Giving Hub admins full access to the DataStore dataviews
	if (JAccess::check(User::get('id'), 'core.admin')) {
		$dd['acl']['allowed_users'] = isset($dd['acl']['allowed_users']) ? $dd['acl']['allowed_users'] : array();
		$dd['acl']['allowed_users'][] = User::get('username');
	}

	return $dd;
}

function _dd_post($dd)
{
	$id = Request::getVar('id', false);

	if ($id) {
		$dd['where'][] = array('field'=>$dd['pk'], 'value'=>$id);
		$dd['single'] = true;
	}

	$custom_field =  Request::getVar('custom_field', false);
	if ($custom_field) {
		$custom_field = explode('|', $custom_field);
		$dd['where'][] = array('field'=>$custom_field[0], 'value'=>$custom_field[1]);
		$dd['single'] = true;
	}

	// Data for Custom Views
	$custom_view = Request::getVar('custom_view', '');

	if ($custom_view != '') {
		$custom_view = explode(',', $custom_view);
		unset($dd['customizer']);

		// Custom Title
		$custom_title = Request::getString('custom_title', '');
		if ($custom_title !== '') {
			$dd['title'] = htmlspecialchars($custom_title);
		}

		// Custom Group by
		$group_by = Request::getString('group_by', '');
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
		foreach ($order_cols as $id=>$prop) {
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

	$document = App::get('document');
	$document->setTitle($dd['title']);

	if (isset($db_id['extra']) && $db_id['extra'] == 'table') {
		$ref_title = "Datastore";
		Pathway::append($ref_title, '/datastores/' . $db_id['name'] . '#tables');
	} elseif (isset($_SERVER['HTTP_REFERER'])) {
		$ref_title = Request::getString('ref_title', $dd['title'] . " Resource");
		$ref_title = htmlentities($ref_title);
		Pathway::append($ref_title, $_SERVER['HTTP_REFERER']);
	}

	Pathway::append($dd['title'], $_SERVER['REQUEST_URI']);
}
?>
