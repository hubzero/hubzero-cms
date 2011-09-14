<?php
/**
 * HUBzero CMS
 *
 * Copyright 2008-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2008-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for '_importtrac'
 * 
 * Long description (if any) ...
 * 
 * @return void
 */
function _importtrac()
{
	$xhub = &Hubzero_Factory::getHub();
    $conn = &Hubzero_Factory::getPLDC();
	$db   = &JFactory::getDBO();

if (0) {
    $infrastructure_projects = array( 'ncn_students','pharmengine','rkspack' );

	foreach ($infrastructure_projects as $group)
	{
		echo "group $group <br>";
		$query = "SELECT id FROM jos_trac_project WHERE name=" . $db->Quote('infrastructure:' . $group) .";";
   		$db->setQuery($query);
		$trac_project_id = $db->loadResult();

		$query = "SELECT gidNumber FROM jos_xgroups WHERE cn=" . $db->Quote($group) .";";
   		$db->setQuery($query);
		$gidNumber = $db->loadResult();
		$group_perms = array('WIKI_ADMIN','MILESTONE_ADMIN','BROWSER_VIEW','LOG_VIEW','FILE_VIEW','CHANGESET_VIEW','ROADMAP_VIEW','TIMELINE_VIEW','SEARCH_VIEW');
		foreach ($group_perms as $action)
		{
			$query = "INSERT IGNORE INTO jos_trac_group_permission (group_id,action,trac_project_id) VALUE (" .
				$db->Quote($gidNumber) . "," . $db->Quote($action) . "," . $db->Quote($trac_project_id) . ");";
   			$db->setQuery($query);
        	$db->query();
			echo "$query <br>";
		}
		$anon_perms = array('MILESTONE_VIEW','ROADMAP_VIEW','WIKI_VIEW','SEARCH_VIEW');
		foreach ($anon_perms as $action)
		{
			$query = "INSERT IGNORE INTO jos_trac_user_permission (group_id,action,trac_project_id) VALUE (" .
				$db->Quote(0) . "," . $db->Quote($action) . "," . $db->Quote($trac_project_id) . ");";
   			$db->setQuery($query);
        	$db->query();
			echo "$query <br>";
		}
	}

    $infrastructure_projects = array( 'nanowhim','rappture','rappture-bat','rappture-runtime' );

	foreach ($infrastructure_projects as $group)
	{
		echo "group $group <br>";
		$query = "SELECT id FROM jos_trac_project WHERE name=" . $db->Quote('infrastructure:' . $group) .";";
   		$db->setQuery($query);
		$trac_project_id = $db->loadResult();

		$query = "SELECT gidNumber FROM jos_xgroups WHERE cn=" . $db->Quote($group) .";";
   		$db->setQuery($query);
		$gidNumber = $db->loadResult();
		$group_perms = array('WIKI_ADMIN','MILESTONE_ADMIN','BROWSER_VIEW','LOG_VIEW','FILE_VIEW','CHANGESET_VIEW','ROADMAP_VIEW','TIMELINE_VIEW','SEARCH_VIEW');
		foreach ($group_perms as $action)
		{
			$query = "INSERT IGNORE INTO jos_trac_group_permission (group_id,action,trac_project_id) VALUE (" .
				$db->Quote($gidNumber) . "," . $db->Quote($action) . "," . $db->Quote($trac_project_id) . ");";
   			$db->setQuery($query);
        	$db->query();
			echo "$query <br>";
		}
		$anon_perms = array('BROWSER_VIEW','CHANGESET_VIEW','FILE_VIEW','LOG_VIEW','MILESTONE_VIEW','ROADMAP_VIEW','WIKI_VIEW','SEARCH_VIEW');
		foreach ($anon_perms as $action)
		{
			$query = "INSERT IGNORE INTO jos_trac_user_permission (group_id,action,trac_project_id) VALUE (" .
				$db->Quote(0) . "," . $db->Quote($action) . "," . $db->Quote($trac_project_id) . ");";
   			$db->setQuery($query);
        	$db->query();
			echo "$query <br>";
		}
	}

	die();

    $infrastructure_projects = array( 'breeze-admin','hubzero','lib-gangli','mw','nanohub','nanohub-for-kids','nanohub-support','ncn','sysman','xhub','nmi' );

	foreach ($infrastructure_projects as $group)
	{
		echo "group $group <br>";
		$query = "SELECT id FROM jos_trac_project WHERE name=" . $db->Quote('infrastructure:' . $group) .";";
   		$db->setQuery($query);
		$trac_project_id = $db->loadResult();

		$query = "SELECT gidNumber FROM jos_xgroups WHERE cn=" . $db->Quote($group) .";";
   		$db->setQuery($query);
		$gidNumber = $db->loadResult();
		$group_perms = array('WIKI_ADMIN','MILESTONE_ADMIN','BROWSER_VIEW','LOG_VIEW','FILE_VIEW','CHANGESET_VIEW','ROADMAP_VIEW','TIMELINE_VIEW','SEARCH_VIEW');
		foreach ($group_perms as $action)
		{
			$query = "INSERT IGNORE INTO jos_trac_group_permission (group_id,action,trac_project_id) VALUE (" .
				$db->Quote($gidNumber) . "," . $db->Quote($action) . "," . $db->Quote($trac_project_id) . ");";
   			$db->setQuery($query);
        	$db->query();
			echo "$query <br>";
   			$db->setQuery($query);
			$query = "INSERT IGNORE INTO jos_trac_group_permission (group_id,action,trac_project_id) VALUE (" .
				$db->Quote('1001') . "," . $db->Quote($action) . "," . $db->Quote($trac_project_id) . ");";
   			$db->setQuery($query);
        	if ($group == 'lib-gangli')
			{
				$db->query();
				echo "$query <br>";
			}
		}
	}

    die();

    $group_projects = array( 'alam_group','group_p_in_si','klimeck','koslowski','mse597g','piezo_frg','strachangroup' );

	foreach ($group_projects as $group)
	{
		echo "group $group <br>";
		$query = "SELECT id FROM jos_trac_project WHERE name=" . $db->Quote('group:' . $group) .";";
   		$db->setQuery($query);
		$trac_project_id = $db->loadResult();

		$query = "SELECT gidNumber FROM jos_xgroups WHERE cn=" . $db->Quote($group) .";";
   		$db->setQuery($query);
		$gidNumber = $db->loadResult();
		$group_perms = array('WIKI_ADMIN','MILESTONE_ADMIN','BROWSER_VIEW','LOG_VIEW','FILE_VIEW','CHANGESET_VIEW','ROADMAP_VIEW','TIMELINE_VIEW','SEARCH_VIEW');
		foreach ($group_perms as $action)
		{
			$query = "INSERT IGNORE INTO jos_trac_group_permission (group_id,action,trac_project_id) VALUE (" .
				$db->Quote($gidNumber) . "," . $db->Quote($action) . "," . $db->Quote($trac_project_id) . ");";
   			$db->setQuery($query);
        	$db->query();
			echo "$query <br>";
   			$db->setQuery($query);
			$query = "INSERT IGNORE INTO jos_trac_group_permission (group_id,action,trac_project_id) VALUE (" .
				$db->Quote('1001') . "," . $db->Quote($action) . "," . $db->Quote($trac_project_id) . ");";
   			$db->setQuery($query);
        	$db->query();
			echo "$query <br>";
		}
	}

    die();
}
	$query = "SELECT jt.toolname,jtv.toolid,p.tool_version_id,p.tracperm FROM jos_tool_version_tracperm AS p, jos_tool_version AS jtv, jos_tool AS jt WHERE p.tool_version_id=jtv.id AND jtv.state=3 AND jt.id=jtv.toolid";
    $db->setQuery($query);
	$result = $db->loadAssocList();

	foreach ($result as $entry)
	{
		if (empty($projects[$entry['toolid']]))
		{
			$query = "SELECT id FROM jos_trac_project WHERE name=" . $db->Quote('app:' . $entry['toolname']) .";";
    		$db->setQuery($query);
			$trac_project_id = $db->loadResult();

			if (empty($trac_project_id))
			{
				$query = "INSERT INTO jos_trac_project (name) VALUE (" . $db->Quote('app:' . $entry['toolname']) . ");";
				$db->setQuery($query);
				$db->query();
				$trac_project_id = $db->insertid();
			}

			if (empty($trac_project_id))
				continue;

			$projects[$entry['toolid']] = $trac_project_id;
		}
		else
			$trac_project_id = $projects[$entry['toolid']];

		$query = "INSERT IGNORE INTO jos_trac_user_permission (user_id,action,trac_project_id) VALUE ('0'," . $db->Quote($entry['tracperm'])  . "," . $db->Quote($trac_project_id) . ");";
		$db->setQuery($query);
		$db->query();
			echo "$query <br>";
    }

	$query = "SELECT j.cn,toolid,toolname,gidNumber FROM jos_tool_groups j, jos_tool t,jos_xgroups x WHERE x.cn=j.cn AND j.toolid = t.id AND (role='1' OR role='2') and j.cn='apps';";
	$db->setQuery($query);
	$result = $db->loadAssocList();

	foreach ($result as $entry)
	{
		echo $entry['cn'] . ' ' . $entry['toolid'] . ' ' . $entry['toolname'] . ' ' . $entry['gidNumber'] . '<br>';

		if (empty($projects[$entry['toolid']]))
		{
			echo "project id not in cache<br>";
			$query = "SELECT id FROM jos_trac_project WHERE name=" . $db->Quote('app:' . $entry['toolname']) .";";
    		$db->setQuery($query);
			$trac_project_id = $db->loadResult();

			if (empty($trac_project_id))
			{
				$query = "INSERT INTO jos_trac_project (name) VALUE (" . $db->Quote('app:' . $entry['toolname']) . ");";
				$db->setQuery($query);
				$db->query();
				$trac_project_id = $db->insertid();
			}

			if (empty($trac_project_id))
			{
					var_dump($db);
					die();
			}

			$projects[$entry['toolid']] = $trac_project_id;
		}
		else
			$trac_project_id = $projects[$entry['toolid']];

		$group_perms = array('WIKI_ADMIN','MILESTONE_ADMIN','BROWSER_VIEW','LOG_VIEW','FILE_VIEW','CHANGESET_VIEW','ROADMAP_VIEW','TIMELINE_VIEW','SEARCH_VIEW');

		foreach ($group_perms as $perm)
		{
			$query = "INSERT IGNORE INTO jos_trac_group_permission (group_id,action,trac_project_id) VALUE (" . $db->Quote($entry['gidNumber']) . "," . $db->Quote($perm) . "," . $db->Quote($trac_project_id) . ");";
			$db->setQuery($query);
			$db->query();
			echo "$query <br>";
		}
	}

	echo "Hi there";
}

?>