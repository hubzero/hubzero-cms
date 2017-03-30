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

	$params = Plugin::params('projects', 'databases');

	$dv_conf['db']['host'] = $params->get('db_host');
	$dv_conf['db']['user'] = $params->get('db_ro_user');
	$dv_conf['db']['password'] = $params->get('db_ro_password');

	return $dv_conf;
}

function get_dd($db_id, $dv_id = false, $version = false)
{
	global $dv_conf;

	$dd = false;
	$db = App::get('db');

	if (!$dv_id)
	{
		$dv_id = Request::getVar('dv');
	}

	if (!$version)
	{
		$version = Request::getInt('v', false);
	}

	$name = $dv_id;


	// Curators
	$curator = '';
	$curator_groups = array();

	if (!$version)
	{
		$sql = 'SELECT data_definition FROM #__project_databases WHERE `database_name` = ' . $db->quote($name);
		$db->setQuery($sql);
		$database = $db->loadAssoc();
		$dd = json_decode($database['data_definition'], true);
	}
	else
	{
		$sql = 'SELECT data_definition FROM #__project_database_versions WHERE database_name=' . $db->quote($name) .
			' AND version=' . $db->quote($version);
		$db->setQuery($sql);
		$ver = $db->loadAssoc();
		$dd = json_decode($ver['data_definition'], true);

		// Check publication state
		$sql = 'SELECT state, curator FROM #__publication_versions ' .
			'LEFT JOIN #__publication_attachments ON ' .
				'(#__publication_versions.publication_id=#__publication_attachments.publication_id '.
				'AND #__publication_versions.id=#__publication_attachments.publication_version_id) '.
			'WHERE object_name=' . $db->quote($name) . 'AND object_revision=' . $db->quote($version);

		$db->setQuery($sql);
		$pub_version = $db->loadAssoc();

		$state = $pub_version['state'];

		$dd['version'] = $version;
		$dd['publication_state'] = $state;

		if ($state != 1)
		{
			// curator groups
			$curation_enabled = Component::params('com_publications')->get('curation');

			$curator_group = trim(Component::params('com_publications')->get('curatorgroup'));

			if ($curation_enabled && $curator_group != '')
			{
				$curator_groups[] = $curator_group;
			}

			$sql = "SELECT cn FROM #__xgroups g LEFT JOIN #__publication_master_types t ON (g.gidNumber = t.curatorgroup) WHERE t.type = 'Databases'";
			$db = App::get('db');
			$db->setQuery($sql);
			$dsl_curators = $db->loadResult();

			if ($curation_enabled && $dsl_curators != '')
			{
				$curator_groups[] = $dsl_curators;
			}

			if ($curation_enabled && $curator != '')
			{
				$curator = $pub_version['curator'];
				$curator = User::getInstance($curator)->get('username');
			}
		}
	}

	// Access control
	if (!isset($dd['publication_state']) || $dd['publication_state'] != 1)
	{
		// Project owners
		$sql = "SELECT username FROM #__project_owners po JOIN #__users u ON (u.id = po.userid) WHERE projectid = {$dd['project']}";
		$db = App::get('db');
		$db->setQuery($sql);
		$dd['acl']['allowed_users'] = $db->loadColumn();

		// Curators
		if (isset($dd['publication_state']))
		{
			$dd['acl']['allowed_groups'] = $curator_groups;

			if (isset($dd['acl']['allowed_users']) && is_array($dd['acl']['allowed_users']))
			{
				$dd['acl']['allowed_users'][] = $curator;
			}
		}
	}
	elseif (isset($dd['publication_state']) && $dd['publication_state'] == 1)
	{
		$dd['acl']['allowed_users'] = false;
		$dd['acl']['allowed_groups'] = false;
		$dd['acl']['public'] = true;
	}


	$dv_conf['db']['database'] = $dd['database'];

	$dd['db_id'] = $db_id;
	$dd['dv_id'] = $dv_id;

	_dd_post($dd);

	/* Dynamically set processing mode */
	$link = get_db($dv_conf['db']);
	$cell_count_threshold = (isset($dv_conf['proc_switch_threshold']) && $dv_conf['proc_switch_threshold']) != 0 ? $dv_conf['proc_switch_threshold'] : 20000;
	mysqli_query($link, query_gen_total($dd));
        $total = mysqli_query($link, 'SELECT FOUND_ROWS() AS total');
        $total = mysqli_fetch_assoc($total);
	$total = isset($total['total']) ? $total['total'] : 0;
	$dd['total_records'] = $total;

	$vis_col_count = count(array_filter($dd['cols'], function ($col) { return !isset($col['hide']); }));

	if ($cell_count_threshold < ($total * $vis_col_count))
	{
		$dd['serverside'] = true;
	}

	return $dd;
}

function _dd_post($dd)
{
	$id = Request::getVar('id', false);

	if ($id)
	{
		$dd['where'][] = array('field'=>$dd['pk'], 'value'=>$id);
		$dd['single'] = true;
	}

	$custom_field =  Request::getVar('custom_field', false);
	if ($custom_field)
	{
		$custom_field = explode('|', $custom_field);
		$dd['where'][] = array('field'=>$custom_field[0], 'value'=>$custom_field[1]);
		$dd['single'] = true;
	}

	// Data for Custom Views
	$custom_view = Request::getVar('custom_view', array());
	if (count($custom_view) > 0)
	{
		unset($dd['customizer']);

		// Custom Title
		$custom_title = Request::getString('custom_title', '');
		if ($custom_title !== '')
		{
			$dd['title'] = htmlspecialchars($custom_title);
		}

		// Custom Group by
		$group_by = Request::getString('group_by', '');
		if ($group_by !== '')
		{
			$dd['group_by'] = htmlspecialchars($group_by);
		}

		// Ordering
		$order_cols = $dd['cols'];
		$dd['cols'] = array();
		foreach ($custom_view as $cv_col)
		{
			$dd['cols'][$cv_col] = $order_cols[$cv_col];
		}

		// Hiding
		foreach ($order_cols as $id=>$prop)
		{
			if (!in_array($id, $custom_view))
			{
				$dd['cols'][$id] = $prop;

				if (!isset($dd['cols'][$id]['hide']))
				{
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

	Document::setTitle($dd['title']);

	if (isset($db_id['extra']) && $db_id['extra'] == 'table')
	{
		$ref_title = "Datastore";
		Pathway::append($ref_title, '/datastores/' . $db_id['name'] . '#tables');
	}
	elseif (isset($_SERVER['HTTP_REFERER']))
	{
		$ref_title = Request::getString('ref_title', $dd['title'] . " Resource");
		$ref_title = htmlentities($ref_title);
		Pathway::append($ref_title, $_SERVER['HTTP_REFERER']);
	}

	Pathway::append($dd['title'], $_SERVER['REQUEST_URI']);
}
