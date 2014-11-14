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
 * Blog controller class for entries
 */
class BlogControllerEntries extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of blog entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get configuration
		$jconfig = JFactory::getConfig();
		$app = JFactory::getApplication();

		$this->view->filters = array(
			'scope' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.scope',
				'scope',
				'site'
			),
			'search' => urldecode($app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'state' => urldecode($app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				''
			)),
			// Get sorting variables
			'sort' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			// Get paging variables
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				$jconfig->getValue('config.list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		if ($this->view->filters['scope'] == 'group')
		{
			$this->view->filters['scope_id'] = $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.scope_id',
				'scope_id',
				0,
				'int'
			);
		}
		$this->view->filters['order'] = $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'];

		// Instantiate our HelloEntry object
		$obj = new BlogModelArchive();

		// Get record count
		$this->view->total = $obj->entries('count', $this->view->filters);

		// Get records
		$this->view->rows  = $obj->entries('list', $this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new category
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   object  $row  BlogEntry
	 * @return  void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming
			$id = JRequest::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the article
			$this->view->row = new BlogModelEntry($id);
		}

		if (!$this->view->row->exists())
		{
			$this->view->row->set('created_by', $this->juser->get('id'));
			$this->view->row->set('created', JFactory::getDate()->toSql());
			$this->view->row->set('publish_up', JFactory::getDate()->toSql());
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry and show the edit form
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save an entry
	 *
	 * @param   boolean  $redirect  Redirect after save?
	 * @return  void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post', 'none', 2);

		if (isset($fields['publish_up']) && $fields['publish_up'] != '')
		{
			$fields['publish_up']   = JFactory::getDate($fields['publish_up'], JFactory::getConfig()->get('offset'))->toSql();
		}
		if (isset($fields['publish_down']) && $fields['publish_down'] != '')
		{
			$fields['publish_down'] = JFactory::getDate($fields['publish_down'], JFactory::getConfig()->get('offset'))->toSql();
		}

		// Initiate extended database class
		$row = new BlogModelEntry($fields['id']);
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store(true))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Process tags
		$row->tag(trim(JRequest::getVar('tags', '')));

		if ($redirect)
		{
			// Set the redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_BLOG_ENTRY_SAVED')
			);
			return;
		}

		$this->editTask($row);
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		if (count($ids) > 0)
		{
			// Loop through all the IDs
			foreach ($ids as $id)
			{
				$entry = new BlogModelEntry(intval($id));
				// Delete the entry
				if (!$entry->delete())
				{
					$this->addComponentMessage($entry->getError(), 'error');
				}
			}
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_BLOG_ENTRIES_DELETED')
		);
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return  void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @param   integer  $state  The state to set entries to
	 * @return  void
	 */
	public function stateTask($state=0)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for a resource
		if (count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::sprintf('COM_BLOG_SELECT_ENTRY_TO', $this->_task),
				'error'
			);
			return;
		}

		// Loop through all the IDs
		$success = 0;
		foreach ($ids as $id)
		{
			// Load the article
			$row = new BlogModelEntry(intval($id));
			$row->set('state', $state);

			// Store new content
			if (!$row->store())
			{
				$this->addComponentMessage($row->getError(), 'error');
				continue;
			}
			$success++;
		}

		switch ($this->_task)
		{
			case 'publish':
				$message = JText::sprintf('COM_BLOG_ITEMS_PUBLISHED', $success);
			break;
			case 'unpublish':
				$message = JText::sprintf('COM_BLOG_ITEMS_UNPUBLISHED', $success);
			break;
			case 'archive':
				$message = JText::sprintf('COM_BLOG_ITEMS_ARCHIVED', $success);
			break;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message
		);
	}

	/**
	 * Turn comments on/off
	 *
	 * @return  void
	 */
	public function setcommentsTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);
		$state = JRequest::getInt('state', 0);

		// Check for a resource
		if (count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::sprintf('COM_BLOG_SELECT_ENTRY_TO_COMMENTS', $this->_task),
				'error'
			);
			return;
		}

		// Loop through all the IDs
		foreach ($ids as $id)
		{
			// Load the article
			$row = new BlogModelEntry(intval($id));
			$row->set('allow_comments', $state);

			// Store new content
			if (!$row->store())
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					$row->getError(),
					'error'
				);
				return;
			}
		}

		switch ($state)
		{
			case 1:
				$message = JText::sprintf('COM_BLOG_ITEMS_COMMENTS_ENABLED', count($ids));
			break;
			case 0:
			default:
				$message = JText::sprintf('COM_BLOG_ITEMS_COMMENTS_DISABLED', count($ids));
			break;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message
		);
	}

	/**
	 * Cancels a task and redirects to listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}

