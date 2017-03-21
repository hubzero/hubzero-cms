<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.	 If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package	  hubzero-cms
 * @author	  Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	  http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Include model
include_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
	. DS . 'models' . DS . 'todo.php');

/**
 * Projects todo's
 */
class plgProjectsTodo extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var	 boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Component name
	 *
	 * @var  string
	 */
	protected $_option = 'com_projects';

	/**
	 * Store internal message
	 *
	 * @var	 array
	 */
	protected $_msg = null;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @return  array  Plugin name and title
	 */
	public function &onProjectAreas($alias = null)
	{
		$area = array(
			'name'    => 'todo',
			'title'   => Lang::txt('COM_PROJECTS_TAB_TODO'),
			'submenu' => null,
			'show'    => true,
			'icon'    => 'f08d'
		);

		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param   object   $model   Project
	 * @param   integer  $admin
	 * @return  array    integer
	 */
	public function &onProjectCount($model, $admin = 0)
	{
		$database = App::get('db');

		$objTD = new \Components\Projects\Tables\Todo($database);
		$counts['todo'] = $objTD->getTodos($model->get('id'), $filters = array('count' => 1));

		if ($admin == 1)
		{
			$counts['todos_completed'] = $objTD->getTodos($model->get('id'), $filters = array(
				'count' => 1,
				'state' => 1)
			);
		}

		return $counts;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param   object  $model   Project model
	 * @param   string  $action  Plugin task
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onProject($model, $action = '', $areas = null)
	{
		$returnhtml = true;

		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas))
			{
				return;
			}
		}

		// Check that project exists
		if (!$model->exists())
		{
			return $arr;
		}

		// Check authorization
		if (!$model->access('member'))
		{
			return $arr;
		}

		// Model
		$this->model = $model;

		// Are we returning HTML?
		if ($returnhtml)
		{
			// Get our To do model
			$this->todo = new \Components\Projects\Models\Todo();

			// Set vars
			$this->_task     = $action ? $action : Request::getVar('action','');
			$this->_todoid   = Request::getInt('todoid', 0);
			$this->_database = App::get('db');
			$this->_uid      = User::get('id');

			switch ($this->_task)
			{
				case 'save':
					$arr['html'] = $this->save();
					break;
				case 'changestate':
					$arr['html'] = $this->save();
					break;
				case 'delete':
					$arr['html'] = $this->delete();
					break;
				case 'assign':
					$arr['html'] = $this->save();
					break;
				case 'view':
				case 'new':
				case 'edit':
					$arr['html'] = $this->item();
					break;
				case 'savecomment':
					$arr['html'] = $this->_saveComment();
					break;
				case 'deletecomment':
					$arr['html'] = $this->_deleteComment();
					break;
				case 'reorder':
				case 'sortitems':
					$arr['html'] = $this->reorder();
					break;
				case 'page':
				default:
					$arr['html'] = $this->page();
					break;
			}
		}

		// Return data
		return $arr;
	}

	/**
	 * Event call to get side content for main project page
	 *
	 * @param   object  $model
	 * @return  string
	 */
	public function onProjectMiniList($model)
	{
		if (!$model->exists() || !$model->access('content'))
		{
			return false;
		}

		// Get our To do model
		$this->todo = new \Components\Projects\Models\Todo();

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'todo',
				'name'    => 'mini'
			)
		);

		// Filters for returning results
		$view->filters = array(
			'projects' => array($model->get('id')),
			'limit'    => $model->config()->get('sidebox_limit', 5),
			'start'    => 0,
			'sortby'   => 'due',
			'sortdir'  => 'ASC'
		);

		$view->items = $this->todo->entries('list', $view->filters);
		$view->model = $model;
		return $view->loadTemplate();
	}

	/**
	 * To do in multiple projects (members/groups plugins)
	 *
	 * @param   string   $area
	 * @param   object   $model
	 * @param   array    $projects
	 * @param   integer  $uid
	 * @param   array    $filters    Query filters
	 * @return  array
	 */
	public function onShared($area, $model, $projects, $uid, $filters)
	{
		// Check if our area is the one we want to return results for
		if ($area != 'todo')
		{
			return;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'todo',
				'name'    => 'view',
				'layout'  => 'shared'
			)
		);
		$view->limit    = isset($filters['limit']) ? $filters['limit'] : 0;
		$view->filters  = $filters;
		$view->uid      = $uid;
		$view->model    = $model;
		$view->database = App::get('db');
		$view->todo     = new \Components\Projects\Models\Todo();

		return $view->loadTemplate();
	}

	/**
	 * View of items
	 *
	 * @return  string
	 */
	public function page()
	{
		// Get default view from owner params
		$member = $this->model->member();
		$mparams = new \Hubzero\Html\Parameter($member ? $member->params : '');
		$defaultView = $mparams->get('todo_layout', 'pinboard');

		// Incoming
		$layout = Request::getVar('l', $defaultView) == 'pinboard' ? 'pinboard' : 'list';
		$mine = isset($this->_mine) ? $this->_mine : Request::getInt('mine', 0);

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'todo',
				'name'    => 'view',
				'layout'  => $layout
			)
		);

		// Filters for returning results
		$view->filters = array(
			'projects'   => array($this->model->get('id')),
			'limit'      => Request::getInt('limit', $this->params->get('limit', 50)),
			'start'      => Request::getInt('limitstart', 0),
			'todolist'   => Request::getWord('list', ''),
			'state'      => isset($this->_state) ? $this->_state : Request::getInt('state', 0),
			'mine'       => $mine,
			'assignedto' => $mine == 1  ? $this->_uid : 0,
			'sortby'     => Request::getVar('sortby', 'priority'),
			'sortdir'    => Request::getVar('sortdir', 'ASC'),
			'layout'     => $layout
		);

		$view->option   = $this->_option;
		$view->database = $this->_database;
		$view->model    = $this->model;
		$view->uid      = $this->_uid;
		$view->title    = $this->_area['title'];
		$view->todo     = $this->todo;

		// Update view preference if changed
		if ($layout != $defaultView)
		{
			$objO = $this->model->table('Owner');
			$objO->saveParam(
				$this->model->get('id'),
				$this->_uid,
				$param = 'todo_layout', $layout
			);
		}

		return $view->loadTemplate();
	}

	/**
	 * View of item
	 *
	 * @return  string
	 */
	public function item()
	{
		// Incoming
		$todoid = $this->_todoid ? $this->_todoid : Request::getInt('todoid', 0);
		$layout = ($this->_task == 'edit' || $this->_task == 'new') ? 'edit' : 'default';

		// Check permission
		if ($this->_task == 'edit' && !$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'item',
				'layout'  => $layout
			)
		);
		$view->option = $this->option;
		$view->todo   = $this->todo;
		$view->params = $this->model->params;
		$view->model  = $this->model;

		// Get team members (to assign items to)
		$objO = $this->model->table('Owner');
		$view->team = $objO->getOwners($this->model->get('id'), $tfilters = array('status' => 1));

		if (isset($this->entry) && is_object($this->entry))
		{
			$view->row = $this->entry;
		}
		else
		{
			$view->row = $this->todo->entry($todoid);
		}

		if (!$view->row->exists() && $this->_task != 'new')
		{
			return $this->page();
		}

		// Append breadcrumbs
		Pathway::append(
			stripslashes(\Hubzero\Utility\String::truncate($view->row->get('content'), 40)),
			Route::url($this->model->link('todo') . '&action=view&todoid=' . $todoid)
		);

		$view->uid   = $this->_uid;
		$view->title = $this->_area['title'];
		$view->list  = Request::getVar('list', '');
		$view->ajax  = Request::getVar('ajax', 0);

		return $view->loadTemplate();
	}

	/**
	 * Save item
	 *
	 * @return  string
	 */
	public function save()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$listcolor = Request::getVar('list', '');
		$content   = Request::getVar('content', '');
		$todoid    = Request::getInt('todoid', 0);
		$newlist   = Request::getVar('newlist', '', 'post');
		$newcolor  = Request::getVar('newcolor', '', 'post');
		$page      = Request::getVar('page', 'list', 'post');
		$assigned  = Request::getInt('assigned', 0);
		$mine      = Request::getInt('mine', 0);
		$state     = Request::getInt('state', 0);
		$ajax      = Request::getInt('ajax', 0);
		$task      = $this->_task;

		$new = 0;

		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Check if assignee is owner
		$objO = $this->model->table('Owner');
		if ($assigned && !$objO->isOwner($assigned, $this->model->get('id')))
		{
			$assigned = 0;
		}
		if ($mine && !$assigned)
		{
			$assigned = $this->_uid;
		}

		// Initiate extended database class
		$objTD = new \Components\Projects\Tables\Todo($this->_database);

		// Load up todo if exists
		if (!$objTD->loadTodo($this->model->get('id'), $todoid))
		{
			$objTD->created_by = $this->_uid;
			$objTD->created    = Date::toSql();
			$objTD->projectid  = $this->model->get('id');
			$assigned          = $assigned;
			$new               = 1;
		}
		else
		{
			$content = $content ? $content : $objTD->content;
		}

		// Prevent resubmit
		if ($task == 'save' && $content == '' && $newlist == '')
		{
			App::redirect($this->model->link('todo'));
			return;
		}

		// Save if not empty
		if ($task == 'save' && $content != '')
		{
			$content = rtrim(stripslashes($content));
			$objTD->content = $content ? $content : $objTD->content;
			$objTD->content = \Hubzero\Utility\Sanitize::stripAll($objTD->content);

			// Save access under details
			if (strlen($objTD->content) > 255)
			{
				$objTD->details = $objTD->content;
			}
			$objTD->content     = \Hubzero\Utility\String::truncate($objTD->content, 255);

			$objTD->color       = $listcolor == 'none' ? '' : $listcolor;
			$objTD->assigned_to = $assigned;
			$objTD->state       = $state;

			// Get due date
			$due = trim(Request::getVar('due', ''));

			if ($due && $due!= 'mm/dd/yyyy')
			{
				$date = explode('/', $due);
				if (count($date) == 3)
				{
					$month	= $date[0];
					$day	= $date[1];
					$year	= $date[2];
					if (intval($month) && intval($day) && intval($year))
					{
						if (strlen($day) == 1)
						{
							$day='0'.$day;
						}

						if (strlen($month) == 1)
						{
							$month='0'.$month;
						}
						if (checkdate($month, $day, $year))
						{
							$objTD->duedate = Date::of(mktime(0, 0, 0, $month, $day, $year))->toSql();
						}
					}
				}
				else
				{
					$this->setError(Lang::txt('PLG_PROJECTS_TODO_TODO_WRONG_DATE_FORMAT'));
				}
			}
			else
			{
				$objTD->duedate = '';
			}

			// Get last order
			$lastorder = $objTD->getLastOrder($this->model->get('id'));
			$neworder = $lastorder ? $lastorder + 1 : 1;
			$objTD->priority = $todoid ? $objTD->priority : $neworder;

			// Get list name
			$objTD->todolist = $listcolor == 'none' ? null : $objTD->getListName($this->model->get('id'), $objTD->color);

			// Store content
			if (!$objTD->store())
			{
				$this->setError($objTD->getError());
			}
			else
			{
				$this->_msg = $todoid
					? Lang::txt('PLG_PROJECTS_TODO_TODO_ITEM_SAVED')
					: Lang::txt('PLG_PROJECTS_TODO_TODO_NEW_ITEM_SAVED');
			}
		}
		// Assign todo
		elseif ($task == 'assign')
		{
			$changed = $objTD->assigned_to == $assigned ? 0 : 1;
			if ($changed)
			{
				$objTD->assigned_to = $assigned;
				$this->_mine = 0; // do not send to My Todo's list

				// Store content
				if (!$objTD->store())
				{
					$this->setError($objTD->getError());
				}
				else
				{
					$this->_msg = $mine
						? Lang::txt('PLG_PROJECTS_TODO_TODO_ASSIGNED_TO_MINE')
						: Lang::txt('PLG_PROJECTS_TODO_TODO_REASSIGNED');
				}
			}
		}
		// Complete todo
		else if ($task == 'changestate')
		{
			$changed = $objTD->state == $state ? 0 : 1;
			if ($changed)
			{
				$objTD->state = $state;
				if ($state == 1)
				{
					$objTD->closed = Date::toSql();
					$objTD->closed_by = $this->_uid;
				}
				// Store content
				if (!$objTD->store())
				{
					$this->setError($objTD->getError());
				}
				else
				{
					$this->_msg = $state == 1
						? Lang::txt('PLG_PROJECTS_TODO_TODO_MARKED_COMPLETED')
						: Lang::txt('PLG_PROJECTS_TODO_TODO_MARKED_INCOMPLETE');

					if ($state == 1)
					{
						// Record activity
						$aid = $this->model->recordActivity(
							Lang::txt('PLG_PROJECTS_TODO_ACTIVITY_TODO_COMPLETED'),
							$objTD->id,
							'to do',
							Route::url('index.php?option=' . $this->_option . '&alias=' . $this->model->get('alias') . '&active=todo' . '&action=view&todoid=' . $objTD->id),
							'todo',
							1
						);
					}
				}
			}
		}

		// Save new empty list information
		if ($newlist != '' && $newcolor != '')
		{
			$new = 0;
			$newlist = \Hubzero\Utility\Sanitize::stripAll(trim($newlist));
			if (!$objTD->getListName($this->model->get('id'), $newcolor))
			{
				$objTD = new \Components\Projects\Tables\Todo($this->_database);
				$objTD->created_by = $this->_uid;
				$objTD->created    = Date::toSql();
				$objTD->projectid  = $this->model->get('id');
				$objTD->content    = 'provisioned';
				$objTD->state      = 2; // inactive
				$objTD->todolist   = $newlist;
				$objTD->color      = $newcolor;

				// Store content
				if (!$objTD->store())
				{
					$this->setError(Lang::txt('PLG_PROJECTS_TODO_TODO_ERROR_LIST_SAVE'));
				}
				else {
					$this->_msg = Lang::txt('PLG_PROJECTS_TODO_TODO_LIST_SAVED');
				}
			}
		}

		// Record activity
		if ($new)
		{
			$aid = $this->model->recordActivity(
				Lang::txt('PLG_PROJECTS_TODO_ACTIVITY_TODO_ADDED'),
				$objTD->id,
				'to do',
				Route::url('index.php?option=' . $this->_option . '&alias=' . $this->model->get('alias') . '&active=todo' . '&action=view&todoid=' . $objTD->id),
				'todo',
				1
			);
			// Store activity ID
			if ($aid)
			{
				$objTD->activityid = $aid;
				$objTD->store();
			}
		}

		// Set redirect path
		if ($page == 'item')
		{
			$url = Route::url('index.php?option=' . $this->_option . '&alias='.$this->model->get('alias') . '&active=todo' . '&action=view&todoid=' . $objTD->id);
		}
		else
		{
			$url = Route::url('index.php?option=' . $this->_option . '&alias=' . $this->model->get('alias') . '&active=todo&list=' . $objTD->color);
		}

		// Go to view
		if ($ajax)
		{
			$this->_todoid = $todoid;
			return $page == 'item'
				? $this->item()
				: $this->page();
		}

		// Pass error or success message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect(Route::url($url));
	}

	/**
	 * Delete item
	 *
	 * @return  string
	 */
	public function delete()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Incoming
		$todoid = $this->_todoid;
		$list = Request::getVar('dl', '');

		$gobacklist = '';

		// Load todo
		$objTD = new \Components\Projects\Tables\Todo($this->_database);
		if ($todoid && $objTD->loadTodo($this->model->get('id'), $todoid))
		{
			// Get associated commenting activities
			$objC = new \Components\Projects\Tables\Comment($this->_database);
			$activities = $objC->collectActivities($todoid, "todo");
			$activities[] = $objTD->activityid;

			// Store list name (for redirect)
			$gobacklist = $objTD->color;

			// Delete todo
			if (!$objTD->deleteTodo($this->model->get('id'), $todoid))
			{
				$this->setError(Lang::txt('PLG_PROJECTS_TODO_TODO_DELETED_ERROR'));
			}
			else
			{
				// Delete all associated comments
				$comments = $objC->deleteComments($todoid, "todo");

				// Delete all associated activities
				foreach ($activities as $a)
				{
					$objAA = $this->model->table('Activity');
					$objAA->loadActivity($a, $this->model->get('id'));
					$objAA->deleteActivity();
				}

				$this->_msg = Lang::txt('PLG_PROJECTS_TODO_TODO_DELETED');
			}
		}
		elseif ($list && $objTD->getListName($this->model->get('id'), $list))
		{
			// Are we deleting a list?
			$deleteall = Request::getInt('all', 0);

			if ($deleteall)
			{
				// Get all to-do's on list
				$todos = $objTD->getTodos($this->model->get('id'), $filters = array('todolist' => $list));
				if (count($todos) > 0)
				{
					foreach ($todos as $todo)
					{
						if ($objTD->loadTodo($this->model->get('id'), $todo->id))
						{
							// Get associated commenting activities
							$objC = new \Components\Projects\Tables\Comment($this->_database);
							$activities = $objC->collectActivities($todo->id, "todo");
							$activities[] = $objTD->activityid;

							// Delete todo
							if ($objTD->deleteTodo($this->model->get('id'), $todo->id))
							{
								// Delete all associated comments
								$comments = $objC->deleteComments($todo->id, "todo");

								// Delete all associated activities
								foreach ($activities as $a)
								{
									$objAA = new \Components\Projects\Tables\Activity($this->_database);
									$objAA->loadActivity($a, $this->model->get('id'));
									$objAA->deleteActivity();
								}
							}
						}
					}
				}
			}

			// Clean-up colored items
			$objTD->deleteList($this->model->get('id'), $list);
			$this->_msg = Lang::txt('PLG_PROJECTS_TODO_TODO_LIST_DELETED');
		}

		// Pass error or success message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect back to todo list
		$url  = Route::url($this->model->link('todo'));
		$url .= $gobacklist ? '?list=' . $gobacklist : '';
		App::redirect($url);
		return;
	}

	/**
	 * Reorder items
	 *
	 * @return  string
	 */
	public function reorder()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			return $this->page();
		}

		// AJAX
		// Incoming
		$newid = Request::getInt('newid', 0);
		$oldid = Request::getInt('oldid', 0);
		$items = Request::getVar('item', array());

		if ($newid && $oldid)
		{
			$objTD1 = new \Components\Projects\Tables\Todo($this->_database);
			$objTD1->loadTodo($this->model->get('id'), $oldid);

			$objTD2 = new \Components\Projects\Tables\Todo($this->_database);
			$objTD2->loadTodo($this->model->get('id'), $newid);

			$priority1 = $objTD1->priority;
			$priority2 = $objTD2->priority;

			$objTD2->priority = $priority1;
			$objTD1->priority = $priority2;

			$objTD1->store();
			$objTD2->store();
		}
		elseif (!empty($items))
		{
			$o = 1;
			foreach ($items as $item)
			{
				$objTD = new \Components\Projects\Tables\Todo($this->_database);
				$objTD->loadTodo($this->model->get('id'), $item);
				$objTD->priority = $o;
				$objTD->store();
				$o++;
			}
		}

		// Go back to todo list
		return $this->page();
	}

	/**
	 * Delete comment
	 *
	 * @return  void  redirect
	 */
	protected function _deleteComment()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Incoming
		$cid    = Request::getInt('cid', 0);
		$todoid = $this->_todoid;

		// Instantiate comment
		$objC = new \Components\Projects\Tables\Comment($this->_database);

		if ($objC->load($cid))
		{
			$activityid = $objC->activityid;

			// delete comment
			if ($objC->deleteComment())
			{
				$this->_msg = Lang::txt('PLG_PROJECTS_TODO_COMMENT_DELETED');
			}

			// delete associated activity
			$objAA = new \Components\Projects\Tables\Activity($this->_database);
			if ($activityid && $objAA->load($activityid))
			{
				$objAA->deleteActivity();
			}
		}

		// Pass error or success message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect(Route::url($this->model->link('todo') . '&action=view&todoid=' . $todoid));
		return;
	}

	/**
	 * Save comment
	 *
	 * @return  void  redirect
	 */
	protected function _saveComment()
	{
		// Check for request forgeries
		Request::checkToken();

		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
			return;
		}

		// Incoming
		$itemid = Request::getInt('itemid', 0, 'post');
		$comment = trim(Request::getVar('comment', '', 'post'));
		$parent_activity = Request::getInt('parent_activity', 0, 'post');

		// Clean-up
		$comment = \Hubzero\Utility\Sanitize::stripScripts($comment);
		$comment = \Hubzero\Utility\Sanitize::stripImages($comment);
		$comment = \Hubzero\Utility\String::truncate($comment, 800);

		// Instantiate comment
		$objC = new \Components\Projects\Tables\Comment($this->_database);
		if ($comment)
		{
			$objC->itemid          = $itemid;
			$objC->tbl             = 'todo';
			$objC->parent_activity = $parent_activity;
			$objC->comment         = $comment;
			$objC->created         = Date::toSql();
			$objC->created_by      = $this->_uid;
			if (!$objC->store())
			{
				$this->setError($objC->getError());
			}
			else
			{
				$this->_msg = Lang::txt('PLG_PROJECTS_TODO_COMMENT_POSTED');
			}
			// Get new entry ID
			if (!$objC->id)
			{
				$objC->checkin();
			}

			// Record activity
			if ($objC->id)
			{
				$what = Lang::txt('COM_PROJECTS_TODO_ITEM');
				$url  = Route::url($this->model->link('todo') . '&action=view&todoid=' . $itemid);
				$aid  = $this->model->recordActivity(
					Lang::txt('COM_PROJECTS_COMMENTED') . ' ' . Lang::txt('COM_PROJECTS_ON') . ' ' . $what,
					$objC->id,
					$what,
					$url,
					'quote',
					0
				);
			}

			// Store activity ID
			if ($aid)
			{
				$objC->activityid = $aid;
				$objC->store();
			}
		}

		// Pass error or success message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			\Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect(Route::url($this->model->link('todo') . '&action=view&todoid=' . $itemid));
		return;
	}
}
