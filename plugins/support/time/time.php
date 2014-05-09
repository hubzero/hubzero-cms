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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Plugin for abuse reports on KB comments
 */
class plgSupportTime extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Called on the ticket comment form
	 * 
	 * @param      object $ticket
	 * @return     array
	 */
	public function onTicketComment($ticket)
	{
		$juser = JFactory::getUser();
		$db = JFactory::getDBO();

		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'tasks.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'hubs.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'records.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'helpers'.DS.'html.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'helpers'.DS.'filters.php');

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'create'
			)
		);
		$view->ticket = $ticket;

		$record = new TimeRecords($db);
		$view->row = $record->getRecord(0);

		if (strstr($view->row->time, '.') !== false)
		{
			list($hrs, $mins) = explode('.', $view->row->time);
		}
		else
		{
			$hrs = $view->row->time;
			$mins = 0;
		}

		if (empty($view->row->user_id))
		{
			// Set some defaults
			$view->row->user_id = $juser->get('id');
			$view->row->uname   = $juser->get('name');
			$view->row->date    = date('Y-m-d');
		}

		$view->htimelist = TimeHTML::buildTimeListHours($hrs);
		$view->mtimelist = TimeHTML::buildTimeListMins($mins);
		$view->hubslist  = TimeHTML::buildHubsList('records', $view->row->hid);
		$view->tasklist  = TimeHTML::buildTasksList($view->row->task_id, 'records', $view->row->hid, $view->row->pactive);

		return $view->loadTemplate();
	}

	/**
	 * Called after updating a ticket
	 * 
	 * @param      object $ticket
	 * @param      object $comment
	 * @return     void
	 */
	public function onTicketUpdate($ticket, $comment)
	{
		$juser = JFactory::getUser();
		$db = JFactory::getDBO();

		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'tasks.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'hubs.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'records.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'helpers'.DS.'html.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'helpers'.DS.'filters.php');

		// Incoming posted data
		$record = JRequest::getVar('record', array(), 'post');
		$record = array_map('trim', $record);

		// Combine the time entry
		$record['user_id'] = $juser->get('id');
		$record['time'] = $record['htime'] . '.' . $record['mtime'];
		$record['description'] = $comment->comment;
		// Don't attempt to save a record if no time or task was chosen
		if (!$record['time'] || !$record['task_id'])
		{
			return;
		}

		// Create object and store new content
		$records = new TimeRecords($db);

		if (!$records->save($record))
		{
			// Something went wrong...return errors (probably from 'check')
			$this->addPluginMessage($records->getError(), 'error');
		}
	}
}
