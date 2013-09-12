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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Short description for 'SupportControllerTaggroups'
 * 
 * Long description (if any) ...
 */
class SupportControllerTaggroups extends Hubzero_Controller
{
	/**
	 * Displays a list of tickets
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.taggroups.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.taggroups.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['sortby'] = JRequest::getVar('sortby', 'priority ASC');

		$model = new TagsTableGroup($this->database);

		// Get record count
		$this->view->total = $model->getCount();

		// Get records
		$this->view->rows  = $model->getRecords();

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) {
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new record
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Display a form for adding/editing a record
	 *
	 * @return	void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);
		
		$this->view->setLayout('edit');
		
		ximport('Hubzero_Group');

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming
			$id = JRequest::getInt('id', 0);

			// Initiate database class and load info
			$this->view->row = new TagsTableGroup($this->database);
			$this->view->row->load($id);
		}

		$this->view->tag = new TagsTableTag($this->database);
		$this->view->tag->load($this->view->row->tagid);

		$this->view->group = Hubzero_Group::getInstance($this->view->row->groupid);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save changes to a record
	 *
	 * @return	void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		ximport('Hubzero_Group');

		$taggroup = JRequest::getVar('taggroup', array(), 'post');

		// Initiate class and bind posted items to database fields
		$row = new TagsTableGroup($this->database);
		if (!$row->bind($taggroup))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Incoming tag
		$tag = trim(JRequest::getVar('tag', '', 'post'));

		// Attempt to load the tag
		$ttag = new TagsTableTag($this->database);
		$ttag->loadTag($tag);

		// Set the group ID
		if ($ttag->id)
		{
			$row->tagid = $ttag->id;
		}

		// Incoming group
		$group = trim(JRequest::getVar('group', '', 'post'));

		// Attempt to load the group
		$hzg = Hubzero_Group::getInstance($group);

		// Set the group ID
		if ($hzg->get('gidNumber'))
		{
			$row->groupid = $hzg->get('gidNumber');
		}

		// Check content
		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('ENTRY_SUCCESSFULLY_SAVED')
		);
	}

	/**
	 * Delete one or more records
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('SUPPORT_ERROR_SELECT_ENTRY_TO_DELETE'),
				'error'
			);
			return;
		}

		$tg = new TagsTableGroup($this->database);
		foreach ($ids as $id)
		{
			// Delete entry
			$tg->delete(intval($id));
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::sprintf('ENTRY_SUCCESSFULLY_DELETED', count($ids))
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Reorder entries in the database
	 *
	 * @return	void
	 */
	public function reorderTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getVar('id', array());
		$id = intval($id[0]);

		// Ensure we have an ID to work with
		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('No entry ID found.'),
				'error'
			);
			return;
		}

		// Get the element moving down - item 1
		$tg1 = new TagsTableGroup($this->database);
		$tg1->load($id);

		// Get the element directly after it in ordering - item 2
		$tg2 = clone($tg1);
		$tg2->getNeighbor($this->_task);

		switch ($this->_task)
		{
			case 'orderup':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $tg2->priority;
				$orderdn = $tg1->priority;

				$tg1->priority = $orderup;
				$tg2->priority = $orderdn;
			break;

			case 'orderdown':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $tg1->priority;
				$orderdn = $tg2->priority;

				$tg1->priority = $orderdn;
				$tg2->priority = $orderup;
			break;
		}

		// Save changes
		$tg1->store();
		$tg2->store();

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
