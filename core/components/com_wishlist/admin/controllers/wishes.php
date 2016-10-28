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
use Components\Wishlist\Tables\Wishlist;
use Components\Wishlist\Tables\Wish;
use Components\Wishlist\Tables\Wish\Plan;
use Components\Wishlist\Tables\Owner;
use Components\Wishlist\Tables\OwnerGroup;
use Components\Wishlist\Models\Tags;
use Exception;
use stdClass;
use Request;
use Notify;
use Config;
use Route;
use Lang;
use User;
use App;

/**
 * Cotnroller class for wishes
 */
class Wishes extends AdminController
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
		$this->registerTask('grant', 'state');
		$this->registerTask('pending', 'state');
		$this->registerTask('accesspublic', 'access');
		$this->registerTask('accessregistered', 'access');
		$this->registerTask('accessspecial', 'access');

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
			'wishlist' => Request::getState(
				$this->_option . '.' . $this->_controller . '.wishlist',
				'wishlist',
				0,
				'int'
			),
			'filterby' => Request::getState(
				$this->_option . '.' . $this->_controller . '.filterby',
				'filterby',
				'all'
			),
			'tag' => Request::getState(
				$this->_option . '.' . $this->_controller . '.tag',
				'tag',
				''
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'subject'
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
		if (!$this->view->filters['wishlist'])
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=lists', false),
				Lang::txt('Missing list ID'),
				'error'
			);
			return;
		}
		$this->view->filters['sortby'] = $this->view->filters['sort'];

		$this->view->wishlist = new Wishlist($this->database);
		$this->view->wishlist->load($this->view->filters['wishlist']);

		$obj = new Wish($this->database);

		// Get record count
		$this->view->total = $obj->get_count($this->view->filters['wishlist'], $this->view->filters, true);

		// Get records
		$this->view->rows = $obj->get_wishes($this->view->filters['wishlist'], $this->view->filters, true);

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		$this->view->wishlist = Request::getInt('wishlist', 0);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load category
			$row = new Wish($this->database);
			$row->load($id);
		}

		$this->view->row = $row;

		if (!$this->view->row->id)
		{
			$this->view->row->wishlist = $this->view->wishlist;
		}
		else if (!$this->view->wishlist)
		{
			$this->view->wishlist = $this->view->row->wishlist;
		}

		/*
		$m = new Models\AdminWish();
		$this->view->form = $m->getForm();
		*/

		$obj = new Wishlist($this->database);
		$filters = array();
		$filters['sort'] = 'title';
		$filters['sort_Dir'] = 'ASC';
		$this->view->lists = $obj->getRecords($filters);

		// who are list owners?
		$this->admingroup = $this->config->get('group', 'hubadmin');

		$objOwner = new Owner($this->database);
		$objG     = new OwnerGroup($this->database);

		$this->view->ownerassignees = array();
		$this->view->ownerassignees[-1] = array();
		$none = new stdClass;
		$none->id = '-1';
		$none->name = Lang::txt('COM_WISHLIST_SELECT');
		$this->view->ownerassignees[-1][] = $none;

		$this->view->assignees = null;

		if ($this->view->lists)
		{
			foreach ($this->view->lists as $k => $list)
			{
				if ($list->category == 'resource')
				{
					include_once(\Component::path('com_resources') . DS . 'tables' . DS . 'resource.php');
					$list->resource = new \Components\Resources\Tables\Resource($this->database);
					$list->resource->load($list->referenceid);
				}
				$this->view->ownerassignees[$list->id] = array();

				$none = new stdClass;
				$none->id = '0';
				$none->name = Lang::txt('COM_WISHLIST_NONE');

				$this->view->ownerassignees[$list->id][] = $none;

				$owners = $objOwner->get_owners($list->id, $this->admingroup, $list);
				if (count($owners['individuals']) > 0)
				{
					$query = "SELECT a.id, a.name FROM `#__users` AS a WHERE a.block = '0' AND a.id IN (" . implode(',', $owners['individuals']) . ") ORDER BY a.name";
					$this->database->setQuery($query);

					$users = $this->database->loadObjectList();

					foreach ($users as $row2)
					{
						$this->view->ownerassignees[$list->id][] = $row2;
					}

					if ($list->id == $this->view->row->wishlist)
					{
						$this->view->assignees = $this->view->ownerassignees[$list->id];
					}
				}
			}
		}

		// Get the plan for this wish
		$objPlan = new Plan($this->database);
		$plan = $objPlan->getPlan($this->view->row->id);
		$this->view->plan = $plan ? $plan[0] : $objPlan;

		// Get tags on this wish
		include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'tags.php');
		$tagging = new Tags($this->view->row->id);
		$this->view->tags = $tagging->render('string');

		// Output the HTML
		$this->view
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
		$row = new Wish($this->database);
		if (!$row->bind($fields))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		$row->anonymous = (isset($fields['anonymous']) && $fields['anonymous']) ? 1 : 0;
		$row->private   = (isset($fields['private']) && $fields['private']) ? 1 : 0;
		$row->accepted  = (isset($fields['accepted']) && $fields['accepted']) ? 1 : 0;

		// Check content
		if (!$row->check())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->store())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'tags.php');
		$tagging = new Tags($row->id);
		$tagging->setTags($fields['tags'], User::get('id'));

		$plan = Request::getVar('plan', array(), 'post', 'none', 2);
		$plan['create_revision'] = isset($plan['create_revision']) ? $plan['create_revision'] : 0;
		$plan['wishid'] = ($plan['wishid'] ? $plan['wishid'] : $row->id);

		// Initiate extended database class
		$page = new Plan($this->database);
		if (!$fields['id'])
		{
			// New page - save it to the database
			$old = new Plan($this->database);
		}
		else
		{
			// Existing page - load it up
			$page->load($plan['id']);

			// Get the revision before changes
			$old = $page;
		}

		$page->bind($plan);

		if ($plan['create_revision'] && rtrim(stripslashes($old->pagetext)) != rtrim(stripslashes($page->pagetext)))
		{
			$page->version = $page->version + 1;
			$page->id = 0;
		}

		if ($page->pagetext)
		{
			$page->version = ($page->version ? $page->version : $page->version + 1);

			if (!$page->check())
			{
				Notify::error($page->getError());
				return $this->editTask($row);
			}

			if (!$page->store())
			{
				Notify::error($page->getError());
				return $this->editTask($row);
			}
		}

		Notify::success(Lang::txt('COM_WISHLIST_WISH_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option='.$this->_option . '&controller=' . $this->_controller . '&wishlist=' . $row->wishlist, false)
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

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		$i = 0;
		if (count($ids) > 0)
		{
			$tbl = new Wish($this->database);

			// Loop through each ID
			foreach ($ids as $id)
			{
				$id = intval($id);

				if (!$tbl->delete($id))
				{
					Notify::error($tbl->getError());
					continue;
				}

				$i++;
			}
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_WISHLIST_ITEMS_REMOVED', $i));
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Set the access level of an article
	 *
	 * @return  void
	 */
	public function accessTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$id = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			Notify::error(Lang::txt('COM_WISHLIST_NO_ID'));
			return $this->cancelTask();
		}

		switch ($this->getTask())
		{
			case 'accesspublic':     $access = 0; break;
			case 'accessregistered': $access = 1; break;
			case 'accessspecial':    $access = 2; break;
		}

		// Load the article
		$row = new Wish($this->database);
		$row->load($id);
		$row->private = $access;

		// Check and store the changes
		if (!$row->check())
		{
			Notify::error($row->getError());
			return $this->cancelTask();
		}
		if (!$row->store())
		{
			Notify::error($row->getError());
			return $this->cancelTask();
		}

		// Set the redirect
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

		$state = $this->getTask() == 'grant' ? 1 : 0;

		// Incoming
		$cid = Request::getInt('cid', 0);
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				($state == 1 ? Lang::txt('COM_WISHLIST_SELECT_PUBLISH') : Lang::txt('COM_WISHLIST_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating a category
			$row = new Wish($this->database);
			$row->load($id);
			$row->status = $state;
			$row->store();
		}

		// Set message
		switch ($state)
		{
			case '-1':
				$message = Lang::txt('COM_WISHLIST_TRASHED', count($ids));
			break;
			case '1':
				$message = Lang::txt('COM_WISHLIST_ITEMS_GRANTED', count($ids));
			break;
			case '0':
				$message = Lang::txt('COM_WISHLIST_ITEMS_PENDING', count($ids));
			break;
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($cid ? '&id=' . $cid : ''), false),
			$message
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$wishlist = Request::getInt('wishlist', 0);

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&wishlist=' . $wishlist, false)
		);
	}
}

