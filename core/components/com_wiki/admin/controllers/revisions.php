<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Wiki\Models\Book;
use Components\Wiki\Models\Page;
use Components\Wiki\Models\Revision;
use Components\Wiki\Helpers\Parser;
use Components\Wiki\Tables;
use Request;
use Config;
use User;
use Lang;
use Date;
use App;

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
		$this->view->filters = array(
			// Paging
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Sorting
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'version'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			// Filters
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'pageid' => Request::getState(
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
			App::redirect(
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
		Request::checkToken();

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
			$row->set('created', Date::toSql());
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

		App::redirect(
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
			App::redirect(
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
				Request::checkToken();

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
					$db = App::get('db');
					$tbl = new Tables\Revision($db);

					foreach ($ids as $id)
					{
						// Load the revision
						$revision = new Revision($id);

						$pageid = ($pageid ? $pageid : $revision->get('pageid'));

						// Get a count of all approved revisions
						$tbl->pageid = $pageid;
						$count = $tbl->getRevisionCount();

						// Can't delete - it's the only approved version!
						if ($count <= 1)
						{
							App::redirect(
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
				App::redirect(
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
		Request::checkToken('get');

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
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid, false),
					$revision->getError(),
					'error'
				);
				return;
			}
		}

		App::redirect(
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
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . Request::getInt('pageid', 0), false)
		);
	}
}

