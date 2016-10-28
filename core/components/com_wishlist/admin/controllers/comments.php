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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Item\Comment;
use Components\Wishlist\Tables\Wishlist;
use Components\Wishlist\Tables\Wish;
use Exception;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use User;
use Date;
use App;

/**
 * Cotnroller class for wishes
 */
class Comments extends AdminController
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
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');
		$this->registerTask('publicize', 'anon');
		$this->registerTask('anonymize', 'anon');

		parent::execute();
	}

	/**
	 * Display a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'wish' => Request::getState(
				$this->_option . '.' . $this->_controller . '.wish',
				'wish',
				0,
				'int'
			),
			// Get sorting variables
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
			// Get paging variables
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
			)
		);
		$this->view->filters['sortby'] = $this->view->filters['sort'];
		if (!$this->view->filters['wish'])
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('Missing wish ID'),
				'error'
			);
			return;
		}

		$this->view->wish = new Wish($this->database);
		$this->view->wish->load($this->view->filters['wish']);

		$this->view->wishlist = new Wishlist($this->database);
		$this->view->wishlist->load($this->view->wish->wishlist);

		// Get records
		// add the appropriate filters and apply them
		$filters = array(
			'item_type' => 'wish',
			'parent'    => 0,
			'search'    => $this->view->filters['search']
		);
		if ($this->view->filters['wish'] > 0)
		{
			$filters['item_id'] = $this->view->filters['wish'];
		}
		if (isset($this->view->filters['sort']))
		{
			$filter['sort'] = $this->view->filters['sort'];
			if (isset($this->view->filters['sort_Dir']))
			{
				$filters['sort_Dir'] = $this->view->filters['sort_Dir'];
			}
		}

		if (isset($this->view->filters['limit']))
		{
			$filters['limit'] = $this->view->filters['limit'];
		}
		if (isset($this->view->filters['start']))
		{
			$filters['start'] = $this->view->filters['start'];
		}

		/*
		 * Load child comments of the parents in the first set.
		 * This will result in a pagination limit break, but it provides
		 * a clearer story of a wish
		 */
		$comments1 = Comment::all()
			->whereEquals('item_type', $filters['item_type'])
			->whereEquals('parent', $filters['parent'])
			->ordered()
			->paginated()
			->rows();

		$comments = array();
		if (count($comments1) > 0)
		{
			$pre    = '<span class="treenode">&#8970;</span>&nbsp;';
			$spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

			foreach ($comments1 as $comment1)
			{
				$comment1->set('prfx', '');
				$comment1->set('wish', $this->view->filters['wish']);
				$comments[] = $comment1;

				foreach ($comment1->replies() as $comment2)
				{
					$comment2->set('prfx', $spacer . $pre);
					$comment2->set('wish', $this->view->filters['wish']);
					$comments[] = $comment2;

					foreach ($comment2->replies() as $comment3)
					{
						$comment3->set('prfx', $spacer . $spacer . $pre);
						$comment3->set('wish', $this->view->filters['wish']);
						$comments[] = $comment3;
					}
				}
			}
		}

		$this->view->total = Comment::all()
			->whereEquals('item_type', $filters['item_type'])
			->total();
		$this->view->rows  = $comments;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit an entry
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$wish = Request::getInt('wish', 0);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load category
			$row = Comment::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set('item_type', 'wish');
			$row->set('item_id', $wish);
			$row->set('created', Date::toSql());
			$row->set('created_by', User::get('id'));
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->set('wish', $wish)
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

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = Comment::blank()->set($fields);

		$row->set('anonymous', (isset($fields['anonymous']) && $fields['anonymous']) ? 1 : 0);

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_WISHLIST_COMMENT_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		Request::setVar('wish', $row->item_id);

		$this->cancelTask();
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

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$wish = Request::getInt('wish', 0);
		$ids  = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Loop through each ID
		$i = 0;
		foreach ($ids as $id)
		{
			$comment = Comment::oneOrFail(intval($id));

			if (!$comment->destroy())
			{
				Notify::error($comment->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_WISHLIST_ITEMS_REMOVED', $i));
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Set the state of an entry
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = $this->getTask() == 'publish' ? 1 : 0;

		// Incoming
		$wish = Request::getInt('wish', 0);
		$ids  = Request::getVar('id', array());
		$ids  = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			Notify::error($state == 1 ? Lang::txt('COM_WISHLIST_SELECT_PUBLISH') : Lang::txt('COM_WISHLIST_SELECT_UNPUBLISH'));
			return $this->cancelTask();
		}

		// Update record(s)
		$i = 0;
		foreach ($ids as $id)
		{
			// Updating a category
			$row = Comment::oneOrFail($id);
			$row->set('state', $state);
			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		// Set message
		if ($i)
		{
			switch ($state)
			{
				case '-1':
					Notify::success(Lang::txt('COM_WISHLIST_ARCHIVED', $i));
				break;
				case '1':
					Notify::success(Lang::txt('COM_WISHLIST_PUBLISHED', $i));
				break;
				case '0':
					Notify::success(Lang::txt('COM_WISHLIST_UNPUBLISHED', $i));
				break;
			}
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Set the anonymous state of an entry
	 *
	 * @return  void
	 */
	public function anonTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = $this->getTask() == 'anonymize' ? 1 : 0;

		// Incoming
		$wish = Request::getInt('wish', 0);
		$ids  = Request::getVar('id', array());
		$ids  = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			return $this->cancelTask();
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating a category
			$row = Comment::oneOrFail($id);
			$row->set('anonymous', $state);
			if (!$row->save())
			{
				Notify::error($row->getError());
			}
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$wish = Request::getInt('wish', 0);

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&wish=' . $wish, false)
		);
	}
}

