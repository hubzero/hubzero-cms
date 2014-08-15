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

/**
 * Manage resource authors
 */
class ResourcesControllerAuthors extends \Hubzero\Component\AdminController
{
	/**
	 * List resource authors
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Get filters
		$this->view->filters = array();
		$this->view->filters['search']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		));

		// Get sorting variables
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'name'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));

		// Get paging variables
		$this->view->filters['limit']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		$obj = new ResourcesContributor($this->database);

		// Get record count
		$this->view->total = $obj->getAuthorCount($this->view->filters);

		// Get records
		$this->view->rows = $obj->getAuthorRecords($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		$this->view->display();
	}

	/**
	 * Create a new entry
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit an entry
	 *
	 * @return     void
	 */
	public function editTask($rows=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'role.php');
		require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'role.type.php');

		$this->view->authorid = 0;
		if (is_array($rows))
		{
			$this->view->rows = $rows;
		}
		else
		{
			// Incoming
			$this->view->authorid = JRequest::getVar('id', array(0));

			if (is_array($this->view->authorid))
			{
				$this->view->authorid = (!empty($this->view->authorid) ? $this->view->authorid[0] : 0);
			}

			// Load category
			$obj = new ResourcesContributor($this->database);
			$this->view->rows = $obj->getRecordsForAuthor($this->view->authorid);
		}

		$model = new ResourcesContributorRole($this->database);
		$this->view->roles = $model->getRecords(array('sort' => 'title'));

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save an entry and come back to the edit form
	 *
	 * @return     void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save an entry
	 *
	 * @param      boolean $redirect Redirect after save?
	 * @return     void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');
		$authorid = JRequest::getVar('authorid', 0);
		$id = JRequest::getVar('id', 0);

		if (!$authorid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		$rows = array();
		if (is_array($fields))
		{
			foreach ($fields as $fieldset)
			{
				$rc = new ResourcesContributor($this->database);
				$rc->subtable     = 'resources';
				$rc->subid        = trim($fieldset['subid']);
				$rc->authorid     = $authorid;
				$rc->name         = trim($fieldset['name']);
				$rc->organization = trim($fieldset['organization']);
				$rc->role         = $fieldset['role'];
				$rc->ordering     = $fieldset['ordering'];
				if ($authorid != $id)
				{
					if (!$rc->createAssociation())
					{
						$this->addComponentMessage($rc->getError(), 'error');
					}
					if (!$rc->deleteAssociation($id, $rc->subid, $rc->subtable))
					{
						$this->addComponentMessage($rc->getError(), 'error');
					}
				}
				else
				{
					if (!$rc->updateAssociation())
					{
						$this->addComponentMessage($rc->getError(), 'error');
					}
				}

				$rows[] = $rc;
			}
		}

		// Instantiate a resource/contributor association object
		$rc = new ResourcesContributor($this->database);

		if ($redirect)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		$this->editTask($rows);
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
}
