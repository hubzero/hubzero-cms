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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * Edit a category
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
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
					include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
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

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			\Notify::error($error);
		}

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

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new Wish($this->database);
		if (!$row->bind($fields))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		$row->anonymous = (isset($fields['anonymous']) && $fields['anonymous']) ? 1 : 0;
		$row->private   = (isset($fields['private']) && $fields['private']) ? 1 : 0;
		$row->accepted  = (isset($fields['accepted']) && $fields['accepted']) ? 1 : 0;

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
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
				$this->setError($page->getError());
				$this->editTask($row);
				return;
			}

			if (!$page->store())
			{
				$this->setError($page->getError());
				$this->editTask($row);
				return;
			}
		}

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option='.$this->_option . '&controller=' . $this->_controller . '&wishlist=' . $row->wishlist, false),
			Lang::txt('COM_WISHLIST_WISH_SAVED')
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
		$wishlist = Request::getInt('wishlist', 0);

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (count($ids) > 0)
		{
			$tbl = new Wish($this->database);

			// Loop through each ID
			foreach ($ids as $id)
			{
				$id = intval($id);

				if (!$tbl->delete($id))
				{
					throw new Exception($tbl->getError(), 500);
				}
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&wishlist=' . $wishlist, false),
			Lang::txt('COM_WISHLIST_ITEMS_REMOVED', count($ids))
		);
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

		// Incoming
		$id = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_WISHLIST_NO_ID'),
				'error'
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
		$row = new Wish($this->database);
		$row->load($id);
		$row->private = $access;

		// Check and store the changes
		if (!$row->check())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
			return;
		}
		if (!$row->store())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
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
	 * Set the state of an entry
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

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

