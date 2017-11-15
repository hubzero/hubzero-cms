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

namespace Components\Wishlist\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Item\Comment;
use Components\Wishlist\Models\Wishlist;
use Components\Wishlist\Models\Wish;
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
 * Cotnroller class for comments
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
		$filters = array(
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
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
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
			),
			'item_type' => 'wish',
			'parent'    => 0
		);

		$wish = Wish::oneOrNew($filters['wish']);

		$wishlist = Wishlist::oneOrNew($wish->get('wishlist'));

		// Load child comments of the parents in the first set.
		// This will result in a pagination limit break, but it provides
		// a clearer story of a wish
		$model = Comment::all();

		if ($filters['wish'])
		{
			$model->whereEquals('item_id', $filters['wish']);
		}

		$comments1 = $model
			->whereEquals('item_type', $filters['item_type'])
			->whereEquals('parent', $filters['parent'])
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$comments = array();
		if (count($comments1) > 0)
		{
			$pre    = '<span class="treenode">&#8970;</span>&nbsp;';
			$spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

			foreach ($comments1 as $comment1)
			{
				$comment1->set('prfx', '');
				$comment1->set('wish', $filters['wish']);
				$comments[] = $comment1;

				foreach ($comment1->replies() as $comment2)
				{
					$comment2->set('prfx', $spacer . $pre);
					$comment2->set('wish', $filters['wish']);
					$comments[] = $comment2;

					foreach ($comment2->replies() as $comment3)
					{
						$comment3->set('prfx', $spacer . $spacer . $pre);
						$comment3->set('wish', $filters['wish']);
						$comments[] = $comment3;
					}
				}
			}
		}

		$model = Comment::all();

		if ($filters['wish'])
		{
			$model->whereEquals('item_id', $filters['wish']);
		}

		$total = $model
			->whereEquals('item_type', $filters['item_type'])
			->total();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $comments)
			->set('total', $total)
			->set('wish', $wish)
			->set('wishlist', $wishlist)
			->display();
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
