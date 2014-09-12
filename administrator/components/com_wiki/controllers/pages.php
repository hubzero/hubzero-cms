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
 * Controller class for wiki pages
 */
class WikiControllerPages extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		define('WIKI_SUBPAGE_SEPARATOR', $this->config->get('subpage_separator', '/'));
		define('WIKI_MAX_PAGENAME_LENGTH', $this->config->get('max_pagename_length', 100));

		parent::execute();
	}

	/**
	 * Display all pages in the wiki(s)
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		$this->view->filters = array(
			'authorized' => true
		);
		// Paging
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart', 
			'limitstart', 
			0, 
			'int'
		);
		// Sorting
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort', 
			'filter_order', 
			'id'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir', 
			'filter_order_Dir', 
			'ASC'
		));
		$this->view->filters['sortby'] = $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'];

		// Filters
		$this->view->filters['search'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search', 
			'search', 
			''
		));
		$this->view->filters['group'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.group', 
			'group', 
			''
		));

		$p = new WikiPage($this->database);

		// Get record count
		$this->view->total = $p->getPagesCount($this->view->filters);

		// Get records
		$this->view->rows = $p->getPages($this->view->filters);

		$this->view->groups = $p->getGroups();

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getError() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new entry
	 *
	 * @return	void
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
	public function editTask($row = null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');
		
		// Incoming
		$ids = JRequest::getVar('id', array(0));
		if (is_array($ids) && !empty($ids)) 
		{
			$id = $ids[0];
		}

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else 
		{
			// Load the article
			$this->view->row = new WikiPage($this->database);
			$this->view->row->loadById($id);
		}
		
		if (!$id) 
		{
			// Creating new
			$this->view->row->created_by = $this->juser->get('id');
		}

		$wpa = new WikiPageAuthor($this->database);
		$auths = $wpa->getAuthors($this->view->row->id);
		$this->view->row->authors = '';
		if (count($auths) > 0) 
		{
			$autharray = array();
			foreach ($auths as $auth)
			{
				$autharray[] = $auth->username;
			}
			$this->view->row->authors = implode(', ', $autharray);
		}

		$this->view->creator = JUser::getInstance($this->view->row->created_by);

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getError() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save changes to an entry
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$page = JRequest::getVar('page', array(), 'post');
		$page = array_map('trim', $page);

		// Initiate extended database class
		$row = new WikiPage($this->database);
		if (!$row->bind($page)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		/*if (!$row->id) 
		{
			$row->created_by = $row->created_by ? $row->created_by : $this->juser->get('id');
		}

		if (!$row->pagename && $row->title) {
			$row->pagename = preg_replace("/[^\:a-zA-Z0-9]/", "", $row->title);
		}
		$row->pagename = preg_replace("/[^\:a-zA-Z0-9]/", "", $row->pagename);
		if (!$row->title && $row->pagename) {
			$row->title = $row->pagename;
		}*/
		$row->access = JRequest::getInt('access', 0, 'post');

		// Get parameters
		$params = JRequest::getVar('params', array(), 'post');
		if (is_array($params)) 
		{
			$paramsClass = 'JRegistry';
			if (version_compare(JVERSION, '1.6', 'lt'))
			{
				$paramsClass = 'JParameter';
			}

			$pparams = new $paramsClass($row->params);
			$pparams->loadArray($params);

			$row->params = $pparams->toString();
		}

		if (!$row->updateAuthors($page['authors'])) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
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

		// Log the change
		$log = new WikiLog($this->database);
		$log->pid = $page->id;
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action = ($page['id']) ? 'page_edited' : 'page_created';
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Page successfully saved')
		);
	}

	/**
	 * Remove one or more pages
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Incoming
		$ids = JRequest::getVar('id', array(0));
		if (count($ids) <= 0) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('No ID'),
				'warning'
			);
			return;
		}

		$step = JRequest::getInt('step', 1);
		$step = (!$step) ? 1 : $step;

		// What step are we on?
		switch ($step)
		{
			case 1:
				// Instantiate a new view
				$this->view->ids = $ids;

				// Set any errors
				if ($this->getError()) 
				{
					foreach ($this->getError() as $error)
					{
						$this->view->setError($error);
					}
				}

				// Output the HTML
				$this->view->display();
			break;

			case 2:
				// Check for request forgeries
				JRequest::checkToken() or jexit('Invalid Token');

				// Check if they confirmed
				$confirmed = JRequest::getInt('confirm', 0);
				if (!$confirmed) 
				{
					// Instantiate a new view
					$this->view->ids = $ids;

					$this->addComponentMessage(JText::_('Please confirm removal'), 'error');

					// Output the HTML
					$this->view->display();
					return;
				}

				if (!empty($ids)) 
				{
					// Create a category object
					$page = new WikiPage($this->database);

					foreach ($ids as $id)
					{
						// Delete the page's history, tags, comments, etc.
						$page->deleteBits($id);

						// Finally, delete the page itself
						$page->delete($id);

						// Delete the page's files
						jimport('joomla.filesystem.folder');
						$path = JPATH_ROOT . DS . trim($this->config->get('filepath', '/site/wiki'), DS);
						if (!JFolder::delete($path . DS . $id)) 
						{
							$this->setError(JText::_('COM_WIKI_UNABLE_TO_DELETE_FOLDER'));
						}

						// Log the action
						$log = new WikiLog($this->database);
						$log->pid = $id;
						$log->uid = $this->juser->get('id');
						$log->timestamp = date('Y-m-d H:i:s', time());
						$log->action = 'page_removed';
						$log->actorid = $this->juser->get('id');
						if (!$log->store()) 
						{
							$this->setError($log->getError());
						}
					}
				}

				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_(count($ids).' page(s) successfully removed')
				);
			break;
		}
	}

	/**
	 * Set the access level to public
	 * 
	 * @return     void
	 */
	public function accesspublicTask()
	{
		$this->accessTask(0);
	}

	/**
	 * Set the access level to registered users
	 * 
	 * @return     void
	 */
	public function accessregisteredTask()
	{
		$this->accessTask(1);
	}

	/**
	 * Set the access level to special
	 * 
	 * @return     void
	 */
	public function accessspecialTask()
	{
		$this->accessTask(2);
	}

	/**
	 * Set the access level
	 * 
	 * @return     void
	 */
	public function accessTask($access = 0)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('No ID'),
				'warning'
			);
			return;
		}

		// Load the article
		$row = new WikiPage($this->database);
		$row->loadById($id);
		$row->access = $access;

		// Check and store the changes
		if (!$row->check()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$row->getError(),
				'error'
			);
			return;
		}
		if (!$row->store()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$row->getError(),
				'error'
			);
			return;
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Reset the page hits
	 * 
	 * @return     void
	 */
	public function resethitsTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('No ID'),
				'warning'
			);
			return;
		}

		// Load and reset the article's hits
		$page = new WikiPage($this->database);
		$page->loadById($id);
		$page->hits = 0;

		if (!$page->check()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$page->getError(),
				'error'
			);
			return;
		}
		if (!$page->store()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$page->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Set the state for a page
	 * 
	 * @return     void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('No ID'),
				'warning'
			);
			return;
		}

		// Load and reset the article's hits
		$page = new WikiPage($this->database);
		$page->loadById($id);
		$page->state = JRequest::getInt('state', 0);

		if (!$page->check()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$page->getError(),
				'error'
			);
			return;
		}
		if (!$page->store()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$page->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Cancels a task and redirects to listing
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}

