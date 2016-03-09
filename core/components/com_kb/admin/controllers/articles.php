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

namespace Components\Kb\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Html\Parameter;
use Components\Kb\Models\Category;
use Components\Kb\Models\Article;
use Request;
use Config;
use Notify;
use Route;
use User;
use Lang;
use App;

/**
 * Controller class for knowledge base articles
 */
class Articles extends AdminController
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
		$this->registerTask('unpublish', 'state');
		$this->registerTask('publish', 'state');

		parent::execute();
	}

	/**
	 * Display a list of articles
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'category' => Request::getState(
				$this->_option . '.' . $this->_controller . '.category',
				'category',
				0,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
				0,
				'int'
			)
		);

		$articles = Article::all();

		if ($filters['search'])
		{
			$articles->whereLike('title', strtolower((string)$filters['search']));
		}

		if ($filters['category'])
		{
			$articles->whereEquals('category', (int)$filters['category']);
		}

		if ($filters['access'])
		{
			$articles->whereEquals('access', (int)$filters['access']);
		}

		// Get records
		$rows = $articles
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		// Get the sections
		$categories = Category::all()
			->ordered()
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->set('categories', $categories)
			->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
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

			// Load category
			$row = Article::oneOrNew($id);
		}

		// Fail if checked out not by 'me'
		if ($row->get('checked_out') && $row->get('checked_out') != User::get('id'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_KB_CHECKED_OUT'),
				'warning'
			);
			return;
		}

		if ($row->isNew())
		{
			$row->set('created_by', User::get('id'));
			$row->set('created', Date::toSql());
		}

		$params = new Parameter(
			$row->get('params'),
			dirname(dirname(__DIR__)) . DS . 'kb.xml'
		);

		// Get the sections
		$categories = Category::all()
			->ordered()
			->rows();

		/*
		$m = new KbModelAdminArticle();
		$form = $m->getForm();
		*/

		// Output the HTML
		$this->view
			->set('row', $row)
			->set('params', $params)
			->set('categories', $categories)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);

		// Initiate extended database class
		$row = Article::oneOrNew($fields['id'])->set($fields);

		// Get parameters
		$params = Request::getVar('params', array(), 'post');

		$p = $row->param();

		if (is_array($params))
		{
			$txt = array();
			foreach ($params as $k => $v)
			{
				$p->set($k, $v);
			}
			$row->set('params', $p->toString());
		}

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Save the tags
		$row->tag(
			Request::getVar('tags', '', 'post'),
			User::get('id')
		);

		if ($this->getTask() == 'apply')
		{
			Notify::success(Lang::txt('COM_KB_ARTICLE_SAVED'));
			return $this->editTask($row);
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_KB_ARTICLE_SAVED')
		);
	}

	/**
	 * Remove one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$cid = Request::getInt('cid', 0);
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) > 0)
		{
			$row = Article::oneOrFail(intval($id));

			if (!$row->destroy())
			{
				Notify::error($row->getError());
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_KB_ITEMS_REMOVED', count($ids))
		);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		$state = $this->_task == 'publish' ? 1 : 0;

		// Incoming
		$cid = Request::getInt('cid', 0);
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				($state == 1 ? Lang::txt('COM_KB_SELECT_PUBLISH') : Lang::txt('COM_KB_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating an article
			$row = Article::oneOrFail(intval($id));
			$row->set('state', $state);
			$row->save();
		}

		// Set message
		switch ($state)
		{
			case '-1':
				$message = Lang::txt('COM_KB_ARCHIVED', count($ids));
			break;
			case '1':
				$message = Lang::txt('COM_KB_PUBLISHED', count($ids));
			break;
			case '0':
				$message = Lang::txt('COM_KB_UNPUBLISHED', count($ids));
			break;
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($cid ? '&section=' . $cid : ''), false),
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
		$filters = Request::getVar('filters', array());

		if (isset($filters['id']) && $filters['id'])
		{
			// Bind the posted data to the article object and check it in
			$article = Article::oneOrFail($filters['id']);
			$article->checkin();
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Reset the hit count on an entry
	 *
	 * @return  void
	 */
	public function resethitsTask()
	{
		// Incoming
		$cid = Request::getInt('cid', 0);
		$id  = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_KB_NO_ID'),
				'error'
			);
		}

		// Load and reset the article's hits
		$article = Article::oneOrFail($id);
		$article->set('hits', 0);

		if (!$article->save())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$article->getError(),
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
	 * Reset the vote count on an entry
	 *
	 * @return  void
	 */
	public function resetvotesTask()
	{
		// Incoming
		$cid = Request::getInt('cid', 0);
		$id  = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_KB_NO_ID'),
				'error'
			);
		}

		// Load and reset the article's ratings
		$article = Article::oneOrFail($id);
		$article->set('helpful', 0);
		$article->set('nothelpful', 0);

		if (!$article->save())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$article->getError(),
				'error'
			);
			return;
		}

		// Delete all the entries associated with this article
		foreach ($article->votes() as $vote)
		{
			$vote->destroy();
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}
}
