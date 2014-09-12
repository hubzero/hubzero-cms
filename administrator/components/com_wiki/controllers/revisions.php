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
 * Controller class for wiki page revisions
 */
class WikiControllerRevisions extends Hubzero_Controller
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
	 * Display all revisions for a page in the wiki(s)
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		$this->view->filters = array();
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
			'version'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir', 
			'filter_order_Dir', 
			'DESC'
		));
		$this->view->filters['sortby'] = $this->view->filters['sort']  . ' ' . $this->view->filters['sort_Dir'];
		// Filters
		$this->view->filters['search'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search', 
			'search', 
			''
		));
		$this->view->filters['pageid']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.pageid', 
			'pageid', 
			0, 
			'int'
		);

		$this->view->page = new WikiPage($this->database);
		if ($this->view->filters['pageid']) 
		{
			$this->view->page->loadById($this->view->filters['pageid']);
		}

		$r = new WikiPageRevision($this->database);

		// Get record count
		$this->view->total = $r->getRecordsCount($this->view->filters);

		// Get records
		$this->view->rows = $r->getRecords($this->view->filters);

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
	 * Create a new revision
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a revision
	 * 
	 * @return     void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Incoming
		$ids = JRequest::getVar('id', array(0));
		if (is_array($ids) && !empty($ids)) 
		{
			$id = $ids[0];
		}

		$pageid = JRequest::getInt('pageid', 0);
		if (!$pageid) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Missing page ID'),
				'error'
			);
			return;
		}

		$this->view->page = new WikiPage($this->database);
		$this->view->page->loadById($pageid);

		if (is_object($row))
		{
			$this->view->revision = $row;
		}
		else 
		{
			$this->view->revision = new WikiPageRevision($this->database);
			$this->view->revision->load($id);
		}

		if (!$id)
		{
			// Creating new
			$this->view->revision = $this->view->page->getCurrentRevision();
			$this->view->revision->version++;
			$this->view->revision->created_by = $this->juser->get('id');
		}

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
	 * Save a revision
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$revision = JRequest::getVar('revision', array(), 'post', 'none', 2);
		$revision = array_map('trim', $revision);

		// Initiate extended database class
		$row = new WikiPageRevision($this->database);
		if (!$row->bind($revision)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row->id) 
		{
			$row->created = date('Y-m-d H:i:s', time());
		}

		$page = new WikiPage($this->database);
		$page->loadById($row->pageid);

		// Parse text
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => $page->scope,
			'pagename' => $page->pagename,
			'pageid'   => $page->id,
			'filepath' => '',
			'domain'   => $this->_group
		);
		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();
		$row->pagehtml = $p->parse($row->pagetext, $wikiconfig);

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
		$log->action = ($revision['id']) ? 'revision_edited' : 'revision_created';
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $row->pageid,
			JText::_('Revision saved')
		);
	}

	/**
	 * Delete a revision
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		$pageid = JRequest::getInt('pageid', 0);
		$ids = JRequest::getVar('id', array(0));
		if (count($ids) <= 0) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid,
				JText::_('No ID'),
				'warning'
			);
			return;
		}

		// Incoming
		$step = JRequest::getInt('step', 1);
		$step = (!$step) ? 1 : $step;

		// What step are we on?
		switch ($step)
		{
			case 1:
				$this->view->ids = $ids;
				$this->view->pageid = $pageid;

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

				$msg = '';
				if (!empty($ids)) 
				{
					// Create a category object
					$revision = new WikiPageRevision($this->database);

					foreach ($ids as $id)
					{
						// Load the revision
						$revision->load($id);

						// Get a count of all approved revisions
						$count = $revision->getRevisionCount();

						// Can't delete - it's the only approved version!
						if ($count <= 1) 
						{
							$this->setRedirect(
								'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid,
								JText::_('Can not remove only available revision'),
								'error'
							);
							return;
						}

						// Delete it
						$revision->delete($id);

						// Log the action
						$log = new WikiLog($this->database);
						$log->pid = $pageid;
						$log->uid = $this->juser->get('id');
						$log->timestamp = date('Y-m-d H:i:s', time());
						$log->action = 'revision_removed';
						$log->actorid = $this->juser->get('id');
						if (!$log->store()) {
							$this->setError($log->getError());
						}
					}

					$msg = JText::_(count($ids).' revision(s) successfully removed');
				}

				// Set the redirect
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid,
					$msg
				);
			break;
		}
	}

	/**
	 * Set the approval state for a revision
	 * 
	 * @return     void
	 */
	public function approveTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming
		$pageid = JRequest::getInt('pageid', 0);
		$id = JRequest::getInt('id', 0);

		if ($id) 
		{
			// Load the revision, approve it, and save
			$revision = new WikiPageRevision($this->database);
			$revision->load($id);
			$revision->approved = JRequest::getInt('approve', 0);
			if (!$revision->check()) 
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid,
					$revision->getError(),
					'error'
				);
				return;
			}
			if (!$revision->store()) 
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid,
					$revision->getError(),
					'error'
				);
				return;
			}

			// Log the action
			$log = new WikiLog($this->database);
			$log->pid = $pageid;
			$log->uid = $this->juser->get('id');
			$log->timestamp = date('Y-m-d H:i:s', time());
			$log->action = 'revision_approved';
			$log->actorid = $this->juser->get('id');
			if (!$log->store()) 
			{
				$this->setError($log->getError());
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid
		);
	}

	/**
	 * Cancel a task and redirect to main listing
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		// Incoming
		$pageid = JRequest::getInt('pageid', 0);

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid
		);
	}
}

