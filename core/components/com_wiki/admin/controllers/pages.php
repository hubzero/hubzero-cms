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
use Request;
use Config;
use User;
use Lang;
use App;

/**
 * Controller class for wiki pages
 */
class Pages extends AdminController
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
		$this->registerTask('accesspublic', 'access');
		$this->registerTask('accessregistered', 'access');
		$this->registerTask('accessspecial', 'access');

		parent::execute();
	}

	/**
	 * Display all pages in the wiki(s)
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->filters = array(
			'authorized' => true,
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
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			// Filters
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'namespace' => Request::getState(
				$this->_option . '.' . $this->_controller . '.namespace',
				'namespace',
				''
			),
			'group' => Request::getState(
				$this->_option . '.' . $this->_controller . '.group',
				'group',
				''
			),
			'state' => array(0, 1, 2)
		);
		$this->view->filters['sortby'] = $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'];

		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$p = new Book();

		// Get record count
		$this->view->total = $p->pages('count', $this->view->filters);

		// Get records
		$this->view->rows  = $p->pages('list', $this->view->filters);

		$this->view->groups = $p->groups();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit an entry
	 *
	 * @return  void
	 */
	public function editTask($row = null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the article
			$row = new Page(intval($id));
		}

		$this->view->row = $row;

		if (!$this->view->row->exists())
		{
			// Creating new
			$this->view->row->set('created_by', User::get('id'));
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
	 * Save changes to an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$page = Request::getVar('page', array(), 'post');
		$page = array_map('trim', $page);

		// Initiate extended database class
		$row = new Page(intval($page['id']));
		if (!$row->bind($page))
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Get parameters
		$params = Request::getVar('params', array(), 'post');
		if (is_array($params))
		{
			$pparams = new \Hubzero\Config\Registry($row->get('params'));
			$pparams->merge($params);

			$row->set('params', $pparams->toString());
		}

		// Store new content
		if (!$row->store(true))
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row->updateAuthors($page['authors']))
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$row->tag($page['tags']);

		if ($this->getTask() == 'apply')
		{
			Request::setVar('id', $row->get('id'));

			return $this->editTask($row);
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_WIKI_PAGE_SAVED')
		);
	}

	/**
	 * Remove one or more pages
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Incoming
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_WIKI_ERROR_MISSING_ID'),
				'warning'
			);
			return;
		}

		$step = Request::getInt('step', 1);
		$step = (!$step) ? 1 : $step;

		// What step are we on?
		switch ($step)
		{
			case 1:
				Request::setVar('hidemainmenu', 1);

				// Instantiate a new view
				$this->view->ids = $ids;

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

				if (!empty($ids))
				{
					foreach ($ids as $id)
					{
						// Finally, delete the page itself
						$page = new Page(intval($id));
						if (!$page->delete())
						{
							$this->setError($page->getError());
						}
					}
				}

				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('COM_WIKI_PAGES_DELETED', count($ids))
				);
			break;
		}
	}

	/**
	 * Set the access level
	 *
	 * @return  void
	 */
	public function accessTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Incoming
		$id = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_WIKI_ERROR_MISSING_ID'),
				'warning'
			);
			return;
		}

		switch ($this->getTask())
		{
			case 'accesspublic':     $access = 0; break;
			case 'accessregistered': $access = 1; break;
			case 'accessspecial':    $access = 2; break;
		}

		// Load the article
		$row = new Page(intval($id));
		$row->set('access', $access);

		// Check and store the changes
		if (!$row->store())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
			return;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Reset the page hits
	 *
	 * @return  void
	 */
	public function resethitsTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_WIKI_ERROR_MISSING_ID'),
				'warning'
			);
			return;
		}

		// Load and reset the article's hits
		$page = new Page(intval($id));
		$page->set('hits', 0);

		if (!$page->store())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$page->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Set the state for a page
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Incoming
		$id = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_WIKI_ERROR_MISSING_ID'),
				'warning'
			);
			return;
		}

		// Load and reset the article's hits
		$page = new Page(intval($id));
		$page->set('state', Request::getInt('state', 0));

		if (!$page->store())
		{
			$this->setMessage($page->getError(), 'error');
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}
}

