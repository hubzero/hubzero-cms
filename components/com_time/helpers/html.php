<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HTML helper class for time component
 */
class TimeHtml
{
	/**
	 * Build a select list of hubs
	 *
	 * @param  $tab          - currently active tab (default: none)
	 * @param  $hub_id       - id of currently selected collection (default: 0 - no active hub)
	 * @param  $active       - whether to pull all hubs, or only active hubs (default: 1 - only include active hubs)
	 * @param  $empty_select - whether to make the first select option an empty one - i.e. "No hub selected" (default: 1 - include empty select)
	 * @param  $limit        - number of records to limit the query to (default: 1000)
	 * @param  $start        - record number to begin query at (default: 0)
	 * @return $hlist        - select list of hubs
	 */
	public static function buildHubsList($tab, $hub_id=0, $active=1, $empty_select=1, $limit=1000, $start=0)
	{
		$db      = JFactory::getDbo();
		$hlist   = array();
		$filters = array('limit'=>$limit, 'start'=>$start, 'active'=>$active);

		$hub      = new TimeHubs($db);
		$hubs     = $hub->getRecords($filters);
		$selected = '';

		// Add an empty select option first in the list
		if ($empty_select == 1)
		{
			$options[] = JHTML::_('select.option', '', JText::_('COM_TIME_'.strtoupper($tab).'_NO_HUB_SELECTED'), 'value', 'text');
		}
		elseif ($empty_select == 2)
		{
			$options[] = JHTML::_('select.option', '0', JText::_('COM_TIME_'.strtoupper($tab).'_ALL_HUBS'), 'value', 'text');
		}

		// Go through all the hubs and add a select option for each
		foreach ($hubs as $hub)
		{
			$options[] = JHTML::_('select.option', $hub->id, JText::_($hub->name), 'value', 'text');
			if ($hub->id == $hub_id)
			{
				$selected = $hub->id;
			}
		}
		$hlist = JHTML::_('select.genericlist', $options, $tab . '[hub_id]', '', 'value', 'text', $selected, 'hub_id', false, false);

		return $hlist;
	}

	/**
	 * Build a select list of tasks
	 *
	 * @param  $task_id      - id of currently selected task (default: none)
	 * @param  $tab          - currently selected tab (default: none)
	 * @param  $hub_id       - id of hub to limit tasks list to (default: null)
	 * @param  $active       - whether or not to limit tasks list should be limited to active tasks (default: 1 - limit to active tasks)
	 * @param  $empty_select - whether to make the first select option an empty one - i.e. "No hub selected" (default: 1 - include empty select)
	 * @param  $limit        - number of records to limit the query to (default: 1000)
	 * @param  $start        - record number to begin query at (default: 0)
	 * @return $tlist        - select list of tasks
	 */
	public static function buildTasksList($task_id, $tab, $hub_id=null, $active=1, $empty_select=1, $limit=1000, $start=0)
	{
		$db    = JFactory::getDbo();
		$tlist = array();
		$filters = array('limit'=>$limit, 'start'=>$start, 'hub'=>$hub_id, 'active'=>$active);

		$task = new TimeTasks($db);
		$tasks = $task->getTasks($filters);
		$selected = '';

		// Add an empty select option first in the list
		if ($empty_select == 1)
		{
			$options[] = JHTML::_('select.option', '', JText::_('COM_TIME_'.strtoupper($tab).'_NO_HUB_SELECTED'), 'value', 'text');
		}
		elseif ($empty_select == 2)
		{
			$options[] = JHTML::_('select.option', '0', JText::_('COM_TIME_'.strtoupper($tab).'_ALL_TASKS'), 'value', 'text');
		}

		// Make sure this hub has tasks associated with it
		if (count($tasks) > 0)
		{
			// Go through all the tasks and add a select option for each
			foreach ($tasks as $task)
			{
				$options[] = JHTML::_('select.option', $task->id, JText::_($task->name), 'value', 'text');
				if ($task->id == $task_id)
				{
					$selected = $task->id;
				}
			}
		}
		else
		{
			// No tasks area available for this hub, just add an entry to that effect
			$options[] = JHTML::_('select.option', '', JText::_('COM_TIME_'.strtoupper($tab).'_NO_TASKS_AVAILABLE'), 'value', 'text');
		}

		$tlist = JHTML::_('select.genericlist', $options, $tab . '[task_id]', '', 'value', 'text', $selected, 'task', false, false);

		return $tlist;
	}

	/**
	 * Build a select list of support levels (currently only used in the hub edit view)
	 *
	 * @param  $sl of currently selected support level (default: Classic Support)
	 * @return $slist
	 */
	public static function buildSupportLevelList($sl="Classic Support")
	{
		$options[] = JHTML::_('select.option', "Classic Support", "Classic Support", 'value', 'text');
		$options[] = JHTML::_('select.option', "Standard Support", "Standard Support", 'value', 'text');
		$options[] = JHTML::_('select.option', "Bronze Support", "Bronze Support", 'value', 'text');
		$options[] = JHTML::_('select.option', "Silver Support", "Silver Support", 'value', 'text');
		$options[] = JHTML::_('select.option', "Gold Support", "Gold Support", 'value', 'text');
		$options[] = JHTML::_('select.option', "Platinum Support", "Platinum Support", 'value', 'text');

		// @FIXME: generalize by removing "hub[support_level]" and replacing with variable for active tab
		$slist = JHTML::_('select.genericlist', $options, 'hubs[support_level]', '', 'value', 'text', $sl, 'support_level', false, false);

		return $slist;
	}

	/**
	 * Build a select list of time hours (currently only used in the records edit view)
	 *
	 * @param  $time of currently selected collection
	 * @return $tlist
	 */
	public static function buildTimeListHours($time=1)
	{
		$options[] = JHTML::_('select.option', 0, "0", 'value', 'text');
		$options[] = JHTML::_('select.option', 1, "1", 'value', 'text');
		$options[] = JHTML::_('select.option', 2, "2", 'value', 'text');
		$options[] = JHTML::_('select.option', 3, "3", 'value', 'text');
		$options[] = JHTML::_('select.option', 4, "4", 'value', 'text');
		$options[] = JHTML::_('select.option', 5, "5", 'value', 'text');
		$options[] = JHTML::_('select.option', 6, "6", 'value', 'text');
		$options[] = JHTML::_('select.option', 7, "7", 'value', 'text');
		$options[] = JHTML::_('select.option', 8, "8", 'value', 'text');

		// @FIXME: generalize by removing "record[time]" and replacing with variable for active tab
		$tlist = JHTML::_('select.genericlist', $options, 'records[htime]', '', 'value', 'text', $time, 'htime', false, false);

		return $tlist;
	}

	/**
	 * Build a select list of time minutes (currently only used in the records edit view)
	 *
	 * @param  $time of currently selected collection
	 * @return $tlist
	 */
	public static function buildTimeListMins($time=1)
	{
		$options[] = JHTML::_('select.option', 0,  ":00", 'value', 'text');
		$options[] = JHTML::_('select.option', 25, ":15", 'value', 'text');
		$options[] = JHTML::_('select.option', 5,  ":30", 'value', 'text');
		$options[] = JHTML::_('select.option', 75, ":45", 'value', 'text');

		// @FIXME: generalize by removing "record[time]" and replacing with variable for active tab
		$tlist = JHTML::_('select.genericlist', $options, 'records[mtime]', '', 'value', 'text', $time, 'mtime', false, false);

		return $tlist;
	}

	/**
	 * Build a select list users
	 *
	 * @param  $id    - currently selected user (default: 0)
	 * @param  $tab   - currently selected tab (default: none)
	 * @param  $empty - what type of item to use for first entry (ex: none, all, etc...)
	 * @return $ulist
	 */
	public static function buildUserList($id=0, $tab, $empty=1)
	{
		// Get group members
		$query  = "SELECT u.id, u.name";
		$query .= " FROM #__xgroups_members AS m";
		$query .= " LEFT JOIN #__xgroups AS g ON m.gidNumber = g.gidNumber";
		$query .= " LEFT JOIN #__users AS u ON u.id = m.uidNumber";
		$query .= " WHERE g.cn = 'time'";
		$query .= " ORDER BY u.name ASC";

		$db = JFactory::getDbo();
		$db->setQuery($query);
		$result = $db->loadAssocList();

		// Add 'all' option first
		if ($empty == 1)
		{
			$options[] = JHTML::_('select.option', 0, JText::_('COM_TIME_'.strtoupper($tab).'_ALL_USERS'), 'value', 'text');
		}
		elseif ($empty == 2)
		{
			$options[] = JHTML::_('select.option', 0, JText::_('COM_TIME_'.strtoupper($tab).'_NO_ASSIGNEE'), 'value', 'text');
		}
		elseif ($empty == 3)
		{
			$options[] = JHTML::_('select.option', 0, JText::_('COM_TIME_'.strtoupper($tab).'_ALL_ASSIGNEES'), 'value', 'text');
		}

		// Iterate through members and add them to the list
		foreach ($result as $member)
		{
			$options[] = JHTML::_('select.option', $member['id'], $member['name'], 'value', 'text');
		}

		if ($tab == 'tasks')
		{
			$ulist = JHTML::_('select.genericlist', $options, 'tasks[assignee]', '', 'value', 'text', $id, 'assignee', false, false);
		}
		else
		{
			$ulist = JHTML::_('select.genericlist', $options, 'user', '', 'value', 'text', $id, 'user', false, false);
		}

		return $ulist;
	}

	/**
	 * Build a select list of liaisons
	 *
	 * @param  $id    - currently selected user (default: 0)
	 * @param  $tab   - currently selected tab (default: none)
	 * @param  $empty - what type of item to use for first entry (ex: none, all, etc...)
	 * @return $llist
	 */
	public static function buildLiaisonList($id=0, $tab, $empty=1)
	{
		// Get liaisons
		$query  = "SELECT tu.*, u.name";
		$query .= " FROM #__time_users tu";
		$query .= " LEFT JOIN #__users AS u ON u.id = tu.user_id";
		$query .= " WHERE liaison = 1";
		$query .= " ORDER BY u.name ASC";

		$db = JFactory::getDbo();
		$db->setQuery($query);
		$result = $db->loadAssocList();

		// Add 'all' option first
		if ($empty == 1)
		{
			$options[] = JHTML::_('select.option', 0, JText::_('COM_TIME_'.strtoupper($tab).'_NO_LIAISON'), 'value', 'text');
		}
		elseif ($empty == 2)
		{
			$options[] = JHTML::_('select.option', 0, JText::_('COM_TIME_'.strtoupper($tab).'_ALL_LIAISONS'), 'value', 'text');
		}

		// Iterate through liaisons and add them to the list
		foreach ($result as $liaison)
		{
			$options[] = JHTML::_('select.option', $liaison['user_id'], $liaison['name'], 'value', 'text');
		}

		$llist = JHTML::_('select.genericlist', $options, $tab . '[liaison]', '', 'value', 'text', $id, 'liaison', false, false);

		return $llist;
	}

	/**
	 * Build a select list of priorities
	 *
	 * @param  $priority - priority of currently selected task
	 * @param  $active   - currently active tab
	 * @param  $empty    - whether to include empty select "all" option
	 * @return $plist
	 */
	public static function buildPriorityList($priority=0, $active='task', $empty=0)
	{
		// Add 'all' option first
		if ($empty == 1)
		{
			$options[] = JHTML::_('select.option', NULL, JText::_('COM_TIME_'.strtoupper($active).'_ALL_PRIORITIES'), 'value', 'text');
		}

		$options[] = JHTML::_('select.option', 0, "(0) Unknown", 'value', 'text');
		$options[] = JHTML::_('select.option', 1, "(1) Trivial", 'value', 'text');
		$options[] = JHTML::_('select.option', 2, "(2) Minor", 'value', 'text');
		$options[] = JHTML::_('select.option', 3, "(3) Normal", 'value', 'text');
		$options[] = JHTML::_('select.option', 4, "(4) Major", 'value', 'text');
		$options[] = JHTML::_('select.option', 5, "(5) Critical", 'value', 'text');

		$plist = JHTML::_('select.genericlist', $options, $active . '[priority]', '', 'value', 'text', $priority, 'priority', false, false);

		return $plist;
	}

	/**
	 * Build a select list of subordinates
	 *
	 * @param  $id    - currently selected user (default: 0)
	 * @param  $subs  - array of subordinates
	 * @return $ulist
	 */
	public static function buildSubordinatesList($id=0, $subs=array())
	{
		// First, add current user
		$options[] = JHTML::_('select.option', JFactory::getUser()->get('id'), JFactory::getUser()->get('name'), 'value', 'text');

		// Iterate through subs and add them to the list
		if (!empty($subs))
		{
			foreach ($subs as $sub)
			{
				$options[] = JHTML::_('select.option', $sub, JFactory::getUser($sub)->get('name'), 'value', 'text');
			}
		}

		$ulist = JHTML::_('select.genericlist', $options, 'records[user_id]', '', 'value', 'text', $id, 'user_id', false, false);

		return $ulist;
	}

	/**
	 * Check if user is a manager
	 *
	 * @param  $id of active user
	 * @return void
	 */
	public static function getSubordinates($id)
	{
		// Get users that current user is a manager of
		$query  = "SELECT tu.user_id";
		$query .= " FROM #__time_users as tu";
		$query .= " WHERE tu.manager_id = " . $id;

		$db = JFactory::getDbo();
		$db->setQuery($query);
		return $result = $db->loadResultArray();
	}
}