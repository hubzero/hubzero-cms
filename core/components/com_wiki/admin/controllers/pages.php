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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Wiki\Models\Page;
use Components\Wiki\Models\Author;
use Request;
use Notify;
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
		$filters = array(
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
			'scope' => Request::getState(
				$this->_option . '.' . $this->_controller . '.scope',
				'scope',
				''
			),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				-1,
				'int'
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
				0,
				'int'
			)
		);

		$scopes = Page::all()
			->select('scope')
			->select('scope_id')
			->group('scope, scope_id')
			->rows();

		$namespaces = Page::all()
			->select('namespace')
			->group('namespace')
			->order('namespace', 'asc')
			->rows();

		$results = Page::all()
			->including(['versions', function ($version){
				$version
					->select('id')
					->select('page_id');
			}])
			->including(['comments', function ($comment){
				$comment
					->select('id')
					->select('page_id');
			}]);

		if ($filters['scope'])
		{
			if (strstr($filters['scope'], ':'))
			{
				$bits = explode(':', $filters['scope']);
				$filters['scope']    = $bits[0];
				$filters['scope_id'] = $bits[1];
			}
			$results->whereEquals('scope', $filters['scope']);

			$filters['scope'] .= ':' . $filters['scope_id'];
		}

		if (isset($filters['scope_id']))
		{
			$results->whereEquals('scope_id', (int)$filters['scope_id']);
		}

		if ($filters['namespace'])
		{
			$results->whereEquals('namespace', $filters['namespace']);
		}

		if ($filters['access'] > 0)
		{
			$results->whereEquals('access', $filters['access']);
		}

		if ($filters['state'] >= 0)
		{
			$results->whereEquals('state', $filters['state']);
		}

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			$results->whereLike('title', $filters['search']);
		}

		// Get records
		$rows = $results
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('scopes', $scopes)
			->set('namespaces', $namespaces)
			->display();
	}

	/**
	 * Edit an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

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
			$row = Page::oneOrNew(intval($id));
		}

		if (!$row->get('id'))
		{
			// Creating new
			$row->set('created_by', User::get('id'));
		}

		// Output the HTML
		$this->view
			->set('row', $row)
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

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$fields = Request::getVar('page', array(), 'post');
		$fields = array_map('trim', $fields);

		$authors = $fields['authors'];
		$tags    = $fields['tags'];
		unset($fields['authors']);
		unset($fields['tags']);

		// Initiate extended database class
		$page = Page::oneOrNew($fields['id'])->set($fields);

		// Get parameters
		$params = Request::getVar('params', array(), 'post');
		if (is_array($params))
		{
			$pparams = new \Hubzero\Config\Registry($page->get('params'));
			$pparams->merge($params);

			$page->set('params', $pparams->toString());
		}

		// Store new content
		if (!$page->save())
		{
			Notify::error($page->getError());
			return $this->editTask($page);
		}

		if (!Author::setForPage($authors, $page->get('id')))
		{
			Notify::error($page->getError());
			return $this->editTask($page);
		}

		$page->tag($tags);

		Notify::success(Lang::txt('COM_WIKI_PAGE_SAVED'));

		if ($this->getTask() == 'apply')
		{
			Request::setVar('id', $page->get('id'));

			return $this->editTask($page);
		}

		// Redirect to main listing
		$this->cancelTask();
	}

	/**
	 * Remove one or more pages
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

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
		}

		$step = Request::getInt('step', 1);
		$step = (!$step) ? 1 : $step;

		// What step are we on?
		switch ($step)
		{
			case 1:
				Request::setVar('hidemainmenu', 1);

				// Output the HTML
				$this->view
					->set('ids', $ids)
					->display();
			break;

			case 2:
				// Check for request forgeries
				Request::checkToken();

				// Check if they confirmed
				$confirmed = Request::getInt('confirm', 0);
				if (!$confirmed)
				{
					Notify::error(Lang::txt('COM_WIKI_CONFIRM_DELETE'));

					// Output the HTML
					$this->view
						->set('ids', $ids)
						->display();
					return;
				}

				$i = 0;

				if (!empty($ids))
				{
					foreach ($ids as $id)
					{
						// Finally, delete the page itself
						$page = Page::oneOrFail(intval($id));

						if (!$page->destroy())
						{
							Notify::error($page->getError());
							continue;
						}

						$i++;
					}
				}

				if ($i)
				{
					Notify::success(Lang::txt('COM_WIKI_PAGES_DELETED', $i));
				}

				// Redirect to main listing
				$this->cancelTask();
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

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$id = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			Notify::warning(Lang::txt('COM_WIKI_ERROR_MISSING_ID'));
		}
		else
		{
			switch ($this->getTask())
			{
				case 'accesspublic':     $access = 0; break;
				case 'accessregistered': $access = 1; break;
				case 'accessspecial':    $access = 2; break;
			}

			// Load the article
			$page = Page::oneOrFail(intval($id));
			$page->set('access', $access);

			// Check and store the changes
			if (!$page->save())
			{
				Notify::error($page->getError());
			}
		}

		// Redirect to main listing
		$this->cancelTask();
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

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$id = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			Notify::warning(Lang::txt('COM_WIKI_ERROR_MISSING_ID'));
		}
		else
		{
			// Load and reset the page hits
			$page = Page::oneOrFail(intval($id));
			$page->set('hits', 0);

			if (!$page->save())
			{
				Notify::error($page->getError());
			}
		}

		// Redirect to main listing
		$this->cancelTask();
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

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$id = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			Notify::warning(Lang::txt('COM_WIKI_ERROR_MISSING_ID'));
		}
		else
		{
			// Load and set state
			$page = Page::oneOrFail(intval($id));
			$page->set('state', Request::getInt('state', 0));

			if (!$page->save())
			{
				Notify::error($page->getError());
			}
		}

		// Redirect to main listing
		$this->cancelTask();
	}
}
