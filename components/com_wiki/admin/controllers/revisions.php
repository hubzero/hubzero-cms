<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Wiki\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Wiki\Models\Book;
use Components\Wiki\Models\Page;
use Components\Wiki\Models\Revision;
use Components\Wiki\Helpers\Parser;
use Components\Wiki\Tables;

/**
 * Controller class for wiki page revisions
 */
class Revisions extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		define('WIKI_SUBPAGE_SEPARATOR', $this->config->get('subpage_separator', '/'));
		define('WIKI_MAX_PAGENAME_LENGTH', $this->config->get('max_pagename_length', 100));

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display all revisions for a page in the wiki(s)
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = \JFactory::getApplication();

		$this->view->filters = array(
			// Paging
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Sorting
			'sort' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'version'
			),
			'sort_Dir' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			// Filters
			'search' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'pageid' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.pageid',
				'pageid',
				0,
				'int'
			),
			'state' => array(0, 1, 2)
		);
		$this->view->filters['sortby'] = $this->view->filters['sort']  . ' ' . $this->view->filters['sort_Dir'];

		$this->view->page = new Page(intval($this->view->filters['pageid']));

		// Get record count
		$this->view->total = $this->view->page->revisions('count', $this->view->filters);

		// Get records
		$this->view->rows = $this->view->page->revisions('list', $this->view->filters);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit a revision
	 *
	 * @param   object  $row  Record
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		$pageid = Request::getInt('pageid', 0);
		if (!$pageid)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_WIKI_ERROR_MISSING_ID'),
				'error'
			);
			return;
		}

		$this->view->page = new Page(intval($pageid));

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$row = new Revision($id);
		}

		$this->view->revision = $row;

		if (!$this->view->revision->exists())
		{
			// Creating new
			$this->view->revision = $this->view->page->revision('current');
			$this->view->revision->set('version', $this->view->revision->get('version') + 1);
			$this->view->revision->set('created_by', User::get('id'));
			$this->view->revision->set('id', 0);
			$this->view->revision->set('pageid', $this->view->page->get('id'));
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
	 * Save a revision
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$revision = Request::getVar('revision', array(), 'post', 'none', 2);
		$revision = array_map('trim', $revision);

		// Initiate extended database class
		$row = new Revision($revision['id']);
		$before = $row->get('approved');
		if (!$row->bind($revision))
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row->exists())
		{
			$row->set('created', \JFactory::getDate()->toSql());
		}

		$page = new Page(intval($row->get('pageid')));

		// Parse text
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => $page->get('scope'),
			'pagename' => $page->get('pagename'),
			'pageid'   => $page->get('id'),
			'filepath' => '',
			'domain'   => $this->_group
		);

		$p = Parser::getInstance();
		$row->set('pagehtml', $p->parse($row->get('pagetext'), $wikiconfig));

		// Store new content
		if (!$row->store())
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Get the most recent revision and compare to the set "current" version
		if ($before != 1 && $row->get('approved') == 1)
		{
			$page->revisions('list', array(), true)->last();
			if ($page->revisions()->current()->get('id') == $row->get('id'))
			{
				// The newly approved revision is now the most current
				// So, we need to update the page's version_id
				$page->set('version_id', $page->revisions()->current()->get('id'));
				$page->store(false, 'revision_approved');
			}
			else
			{
				$page->log('revision_approved');
			}
		}

		// Set the redirect
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $row->get('pageid'), false),
			Lang::txt('COM_WIKI_REVISION_SAVED')
		);
	}

	/**
	 * Delete a revision
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		$pageid = Request::getInt('pageid', 0);

		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid, false),
				Lang::txt('COM_WIKI_ERROR_MISSING_ID'),
				'warning'
			);
			return;
		}

		// Incoming
		$step = Request::getInt('step', 1);
		$step = (!$step) ? 1 : $step;

		// What step are we on?
		switch ($step)
		{
			case 1:
				Request::setVar('hidemainmenu', 1);

				$this->view->ids = $ids;
				$this->view->pageid = $pageid;

				// Set any errors
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}

				// Output the HTML
				$this->view->display();
			break;

			case 2:
				// Check for request forgeries
				Request::checkToken() or jexit('Invalid Token');

				// Check if they confirmed
				$confirmed = Request::getInt('confirm', 0);
				if (!$confirmed)
				{
					// Instantiate a new view
					$this->view->ids = $ids;

					$this->setMessage(Lang::txt('COM_WIKI_CONFIRM_DELETE'), 'error');

					// Output the HTML
					$this->view->display();
					return;
				}

				$msg = '';
				if (!empty($ids))
				{
					foreach ($ids as $id)
					{
						// Load the revision
						$revision = new Revision($id);

						// Get a count of all approved revisions
						$count = $revision->getRevisionCount();

						// Can't delete - it's the only approved version!
						if ($count <= 1)
						{
							$this->setRedirect(
								Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid, false),
								Lang::txt('COM_WIKI_ERROR_CANNOT_REMOVE_REVISION'),
								'error'
							);
							return;
						}

						// Delete it
						$revision->delete();
					}

					$msg = Lang::txt('COM_WIKI_PAGES_DELETED', count($ids));
				}

				// Set the redirect
				$this->setRedirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid, false),
					$msg
				);
			break;
		}
	}

	/**
	 * Set the approval state for a revision
	 *
	 * @return  void
	 */
	public function approveTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or jexit('Invalid Token');

		// Incoming
		$pageid = Request::getInt('pageid', 0);
		$id = Request::getInt('id', 0);

		if ($id)
		{
			// Load the revision, approve it, and save
			$revision = new Revision($id);
			$revision->set('approved', Request::getInt('approve', 0));

			if (!$revision->store())
			{
				$this->setRedirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid, false),
					$revision->getError(),
					'error'
				);
				return;
			}
		}

		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid, false)
		);
	}

	/**
	 * Cancel a task and redirect to main listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . Request::getInt('pageid', 0), false)
		);
	}
}

