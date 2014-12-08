<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license	  http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Projects todo's
 */
class plgProjectsTodo extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var	   boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Store redirect URL
	 *
	 * @var	   string
	 */
	protected $_referer = NULL;

	/**
	 * Store output message
	 *
	 * @var	   array
	 */
	protected $_message = NULL;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @return	   array   Plugin name and title
	 */
	public function &onProjectAreas()
	{
		$area = array(
			'name' => 'todo',
			'title' => JText::_('COM_PROJECTS_TAB_TODO')
		);

		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param	   object  $project			Project
	 * @param	   integer &$counts
	 * @return	   array   integer
	 */
	public function &onProjectCount( $project, &$counts, $admin = 0 )
	{
		$database = JFactory::getDBO();

		$objTD = new ProjectTodo( $database );
		$counts['todo'] = $objTD->getTodos($project->id, $filters = array('count' => 1));

		if ($admin == 1)
		{
			$counts['todos_completed'] = $objTD->getTodos($project->id, $filters = array(
				'count' => 1,
				'state' => 1)
			);
		}

		return $counts;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param	   object  $project			Project
	 * @param	   string  $option			Component name
	 * @param	   integer $authorized		Authorization
	 * @param	   integer $uid				User ID
	 * @param	   integer $msg				Message
	 * @param	   integer $error			Error
	 * @param	   string  $action			Plugin task
	 * @param	   string  $areas			Plugins to return data
	 * @return	   array   Return array of html
	 */
	public function onProject ( $project, $option, $authorized,
		$uid, $msg = '', $error = '', $action = '', $areas = null )
	{
		$returnhtml = true;

		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'msg'=>'',
			'referer'=>''
		);

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (empty($this->_area) || !in_array($this->_area['name'], $areas)) {
				return;
			}
		}

		// Is the user authorized?
		if (!$authorized && !$project->owner)
		{
			return $arr;
		}

		// Do we have a project ID?
		if (!is_object($project) or !$project->id )
		{
			return $arr;
		}
		else
		{
			$this->_project = $project;
		}

		// Include model
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'models' . DS . 'todo.php');

		// Get our model
		$this->model = new ProjectModelTodo();

		// Are we returning HTML?
		if ($returnhtml)
		{
			// Set vars
			$this->_task		= $action ? $action : JRequest::getVar('action','');
			$this->_todoid		= JRequest::getInt('todoid', 0);
			$this->_database	= JFactory::getDBO();
			$this->_option		= $option;
			$this->_authorized	= $authorized;
			$this->_msg			= $msg;
			if ($error)
			{
				$this->setError( $error );
			}
			$this->_uid = $uid;
			if (!$this->_uid)
			{
				$juser = JFactory::getUser();
				$this->_uid = $juser->get('id');
			}

			switch ($this->_task)
			{
				case 'page':
					default:		$arr['html'] = $this->page();			break;
				case 'save':		$arr['html'] = $this->save();			break;
				case 'changestate': $arr['html'] = $this->save();			break;
				case 'delete':		$arr['html'] = $this->delete();			break;
				case 'assign':		$arr['html'] = $this->save();			break;
				case 'view':
				case 'new':
				case 'edit':		$arr['html'] = $this->item();			break;
				case 'savecomment': $arr['html'] = $this->_saveComment();	break;
				case 'deletecomment':$arr['html'] = $this->_deleteComment();break;
				case 'reorder':
				case 'sortitems':	$arr['html'] = $this->reorder();		break;
			}
		}

		// Return data
		$arr['referer'] = $this->_referer;
		$arr['msg'] = $this->_message;
		return $arr;
	}

	/**
	 * View of items
	 *
	 * @return	   string
	 */
	public function page()
	{
		// Get default view from owner params
		$owner_params = new JParameter( $this->_project->owner_params );
		$defaultView = $owner_params->get('todo_layout', 'pinboard');

		// Incoming
		$layout = JRequest::getVar('l', $defaultView) == 'pinboard' ? 'pinboard' : 'list';

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=> 'projects',
				'element'	=> 'todo',
				'name'		=> 'view',
				'layout'	=> $layout
			)
		);

		// Filters for returning results
		$view->filters = array(
			'projects'	 => array($this->_project->id),
			'limit'		 => JRequest::getInt('limit', $this->params->get('limit', 50)),
			'start'		 => JRequest::getInt('limitstart', 0),
			'todolist'	 => JRequest::getVar('list', ''),
			'state'		 => isset($this->_state) ? $this->_state : JRequest::getVar('state', 0),
			'mine'		 => isset($this->_mine) ? $this->_mine : JRequest::getInt('mine', 0),
			'assignedto' => isset($this->_mine) && $this->_mine == 1  ? $this->_uid : 0,
			'sortby'	 => JRequest::getVar('sortby', 'priority'),
			'sortdir'	 => JRequest::getVar('sortdir', 'ASC'),
			'layout'	 => $layout
		);

		$view->option		= $this->_option;
		$view->database		= $this->_database;
		$view->project		= $this->_project;
		$view->uid			= $this->_uid;
		$view->title		= $this->_area['title'];
		$view->model		= $this->model;

		// Update view preference if changed
		if ($layout != $defaultView)
		{
			$objO = new ProjectOwner( $this->_database );
			$objO->saveParam(
				$this->_project->id,
				$this->_uid,
				$param = 'todo_layout', $layout
			);
		}

		// Get messages and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();

	}

	/**
	 * View of item
	 *
	 * @return	   string
	 */
	public function item()
	{
		// Incoming
		$todoid = $this->_todoid ? $this->_todoid : JRequest::getInt('todoid', 0);
		$layout = ($this->_task == 'edit' || $this->_task == 'new') ? 'edit' : 'default';

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'	  => 'item',
				'layout'  => $layout
			)
		);
		$view->option = $this->option;
		$view->model  = $this->model;
		$view->params = new JParameter($this->_project->params);
		$view->project		= $this->_project;

		// Get team members (to assign items to)
		$objO = new ProjectOwner( $this->_database );
		$view->team = $objO->getOwners($this->_project->id, $tfilters = array('status' => 1));

		if (isset($this->entry) && is_object($this->entry))
		{
			$view->row = $this->entry;
		}
		else
		{
			$view->row = $this->model->entry($todoid);
		}

		if (!$view->row->exists() && $this->_task != 'new')
		{
			return $this->page();
		}

		// Append breadcrumbs
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$pathway->addItem(
				stripslashes($view->row->get('content')),
				JRoute::_('index.php?option=' . $this->_option . a
					. 'alias=' . $this->_project->alias . a . 'active=todo'
					. a . 'action=view') . '/?todoid=' . $todoid
		);

		$view->uid			= $this->_uid;
		$view->title		= $this->_area['title'];
		$view->list			= JRequest::getVar('list', '');
		$view->ajax			= JRequest::getVar('ajax', 0);

		// Get messages and errors
		$view->msg = $this->_msg;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	//----------------------------------------
	// Processors
	//----------------------------------------

	/**
	 * Save item
	 *
	 * @return	   string
	 */
	public function save()
	{
		// Incoming
		$listcolor	= JRequest::getVar('list', '');
		$content	= JRequest::getVar('content', '');
		$todoid		= JRequest::getInt('todoid', 0);
		$newlist	= JRequest::getVar('newlist', '', 'post');
		$newcolor	= JRequest::getVar('newcolor', '', 'post');
		$page		= JRequest::getVar('page', 'list', 'post');
		$assigned	= JRequest::getInt('assigned', 0);
		$mine		= JRequest::getInt('mine', 0);
		$state		= JRequest::getInt('state', 0);
		$ajax		= JRequest::getInt('ajax', 0);
		$task		= $this->_task;

		$new = 0;

		// Check if assignee is owner
		$objO = new ProjectOwner( $this->_database );
		if ($assigned && !$objO->isOwner($assigned, $this->_project->id))
		{
			$assigned = 0;
		}
		if ($mine && !$assigned)
		{
			$assigned = $this->_uid;
		}

		// Initiate extended database class
		$objTD = new ProjectTodo( $this->_database );

		// Load up todo if exists
		if (!$objTD->loadTodo($this->_project->id, $todoid))
		{
			$objTD->created_by	= $this->_uid;
			$objTD->created		= JFactory::getDate()->toSql();
			$objTD->projectid	= $this->_project->id;
			$assigned			= $assigned;
			$new				= 1;
		}
		else
		{
			$content = $content ? $content : $objTD->content;
		}

		// Prevent resubmit
		if ($task == 'save' && $content == '' && $newlist == '')
		{
			$this->_referer = JRoute::_('index.php?option=' . $this->_option . a .
			'alias='.$this->_project->alias . a . 'active=todo');
			return;
		}

		// Save if not empty
		if ($task == 'save' && $content != '')
		{
			$content			= rtrim(stripslashes($content));
			$objTD->content		= $content ? $content : $objTD->content;
			$objTD->content		= \Hubzero\Utility\Sanitize::stripAll($objTD->content);

			// Save access under details
			if (strlen($objTD->content) > 255)
			{
				$objTD->details = $objTD->content;
			}
			$objTD->content		= \Hubzero\Utility\String::truncate($objTD->content, 255);

			$objTD->color		= $listcolor == 'none' ? '' : $listcolor ;
			$objTD->assigned_to = $assigned;
			$objTD->state		= $state;

			// Get due date
			$due = trim(JRequest::getVar('due', ''));

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
							$objTD->duedate = JFactory::getDate(mktime(0, 0, 0, $month, $day, $year))->toSql();
						}
					}
				}
				else
				{
					$this->setError(JText::_('PLG_PROJECTS_TODO_TODO_WRONG_DATE_FORMAT'));
				}
			}
			else
			{
				$objTD->duedate = '';
			}

			// Get last order
			$lastorder = $objTD->getLastOrder($this->_project->id);
			$neworder = $lastorder ? $lastorder + 1 : 1;
			$objTD->priority = $todoid ? $objTD->priority : $neworder;

			// Get list name
			$objTD->todolist = $listcolor == 'none' ? NULL : $objTD->getListName($this->_project->id, $objTD->color);

			// Store content
			if (!$objTD->store())
			{
				$this->setError( $objTD->getError() );
			}
			else
			{
				$this->_msg = $todoid
					?  JText::_('PLG_PROJECTS_TODO_TODO_ITEM_SAVED')
					: JText::_('PLG_PROJECTS_TODO_TODO_NEW_ITEM_SAVED');
			}
		}
		// Assign todo
		else if ($task == 'assign')
		{
			$changed = $objTD->assigned_to == $assigned ? 0 : 1;
			if ($changed)
			{
				$objTD->assigned_to = $assigned;
				$this->_mine = 0; // do not send to My Todo's list

				// Store content
				if (!$objTD->store())
				{
					$this->setError( $objTD->getError() );
				}
				else
				{
					$this->_msg = $mine
						? JText::_('PLG_PROJECTS_TODO_TODO_ASSIGNED_TO_MINE')
						: JText::_('PLG_PROJECTS_TODO_TODO_REASSIGNED');
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
					$objTD->closed = JFactory::getDate()->toSql();
					$objTD->closed_by = $this->_uid;
				}
				// Store content
				if (!$objTD->store())
				{
					$this->setError( $objTD->getError() );
				}
				else
				{
					$this->_msg = $state == 1
						? JText::_('PLG_PROJECTS_TODO_TODO_MARKED_COMPLETED')
						: JText::_('PLG_PROJECTS_TODO_TODO_MARKED_INCOMPLETE');

					if ($state == 1)
					{
						// Record activity
						$objAA = new ProjectActivity ( $this->_database );
						$aid = $objAA->recordActivity($this->_project->id, $this->_uid,
							JText::_('PLG_PROJECTS_TODO_ACTIVITY_TODO_COMPLETED'), $objTD->id, 'to-do',
							JRoute::_('index.php?option=' . $this->_option . a .
							'alias=' . $this->_project->alias . a . 'active=todo'. a .
							'action=view') . '/?todoid=' . $objTD->id, 'todo', 1 );
					}
				}
			}
		}

		// Save new empty list information
		if ($newlist != '' && $newcolor != '')
		{
			$new = 0;
			$newlist = \Hubzero\Utility\Sanitize::stripAll(trim($newlist));
			if (!$objTD->getListName($this->_project->id, $newcolor))
			{
				$objTD				= new ProjectTodo( $this->_database );
				$objTD->created_by	= $this->_uid;
				$objTD->created		= JFactory::getDate()->toSql();
				$objTD->projectid	= $this->_project->id;
				$objTD->content		= 'provisioned';
				$objTD->state		= 2; // inactive
				$objTD->todolist	= $newlist;
				$objTD->color		= $newcolor;

				// Store content
				if (!$objTD->store())
				{
					$this->setError(JText::_('PLG_PROJECTS_TODO_TODO_ERROR_LIST_SAVE'));
				}
				else {
					$this->_msg = JText::_('PLG_PROJECTS_TODO_TODO_LIST_SAVED');
				}
			}
		}

		// Record activity
		$objAA = new ProjectActivity( $this->_database );
		if ($new)
		{
			$aid = $objAA->recordActivity($this->_project->id, $this->_uid,
				JText::_('PLG_PROJECTS_TODO_ACTIVITY_TODO_ADDED'), $objTD->id, 'to-do',
				JRoute::_('index.php?option=' . $this->_option . a .
				'alias=' . $this->_project->alias . a . 'active=todo' . a .
				'action=view') . '/?todoid=' . $objTD->id, 'todo', 1);
			// Store activity ID
			if ($aid)
			{
				$objTD->activityid = $aid;
				$objTD->store();
			}
		}

		// Pass error or success message
		if ($this->getError())
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg)
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}

		// Set redirect path
		if ($page == 'item')
		{
			$url = JRoute::_('index.php?option=' . $this->_option . a .
				'alias='.$this->_project->alias . a . 'active=todo' . a .
				'action=view') . '/?todoid=' . $objTD->id;
		}
		else
		{
			$url = JRoute::_('index.php?option=' . $this->_option . a .
				'alias=' . $this->_project->alias . a . 'active=todo') .
				'/?list=' . $objTD->color;
		}

		// Go to view
		if ($ajax)
		{
			$this->_todoid = $todoid;
			return $page == 'item'
				? $this->item()
				: $this->page();
		}
		else
		{
			$this->_referer = $url;
			return; // redirect
		}
	}

	/**
	 * Delete item
	 *
	 * @return	   string
	 */
	public function delete()
	{
		// Incoming
		$todoid = $this->_todoid;
		$list = JRequest::getVar('dl', '');

		$gobacklist = '';

		// Load todo
		$objTD = new ProjectTodo( $this->_database );
		if ($todoid && $objTD->loadTodo($this->_project->id, $todoid))
		{
			// Get associated commenting activities
			$objC = new ProjectComment( $this->_database );
			$activities = $objC->collectActivities($todoid, "todo" );
			$activities[] = $objTD->activityid;

			// Store list name (for redirect)
			$gobacklist = $objTD->color;

			// Delete todo
			if (!$objTD->deleteTodo($this->_project->id, $todoid))
			{
				$this->setError(JText::_('PLG_PROJECTS_TODO_TODO_DELETED_ERROR'));
			}
			else
			{
				// Delete all associated comments
				$comments = $objC->deleteComments($todoid, "todo");

				// Delete all associated activities
				foreach ($activities as $a)
				{
					$objAA = new ProjectActivity( $this->_database );
					$objAA->loadActivity($a, $this->_project->id);
					$objAA->deleteActivity();
				}

				$this->_msg = JText::_('PLG_PROJECTS_TODO_TODO_DELETED');
			}
		}
		else if ($list && $objTD->getListName($this->_project->id, $list))
		{
			// Are we deleting a list?
			$deleteall = JRequest::getInt('all', 0);

			if ($deleteall)
			{
				// Get all to-do's on list
				$todos = $objTD->getTodos( $this->_project->id, $filters = array('todolist' => $list) );
				if (count($todos) > 0)
				{
					foreach ($todos as $todo)
					{
						if ($objTD->loadTodo($this->_project->id, $todo->id))
						{
							// Get associated commenting activities
							$objC = new ProjectComment( $this->_database );
							$activities = $objC->collectActivities($todo->id, "todo" );
							$activities[] = $objTD->activityid;

							// Delete todo
							if ($objTD->deleteTodo($this->_project->id, $todo->id))
							{
								// Delete all associated comments
								$comments = $objC->deleteComments( $todo->id, "todo" );

								// Delete all associated activities
								foreach ($activities as $a)
								{
									$objAA = new ProjectActivity( $this->_database );
									$objAA->loadActivity( $a, $this->_project->id );
									$objAA->deleteActivity();
								}
							}
						}
					}
				}
			}

			// Clean-up colored items
			$objTD->deleteList( $this->_project->id, $list );
			$this->_msg = JText::_('PLG_PROJECTS_TODO_TODO_LIST_DELETED');
		}

		// Pass success or error message
		if ($this->getError())
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg)
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}

		// Redirect back to todo list
		$url  = JRoute::_('index.php?option=' . $this->_option . a .
			   'alias=' . $this->_project->alias . a . 'active=todo');
		$url .= $gobacklist ? '?list=' . $gobacklist : '';
		$this->_referer = $url;
		return;
	}

	/**
	 * Reorder items
	 *
	 * @return	   string
	 */
	public function reorder()
	{
		// AJAX
		// Incoming
		$newid = JRequest::getInt('newid', 0);
		$oldid = JRequest::getInt('oldid', 0);
		$items = JRequest::getVar( 'item', array(), 'request', 'array' );

		if ($newid && $oldid)
		{
			$objTD1 = new ProjectTodo( $this->_database );
			$objTD1->loadTodo ($this->_project->id, $oldid);

			$objTD2 = new ProjectTodo( $this->_database );
			$objTD2->loadTodo ($this->_project->id, $newid);

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
				$objTD = new ProjectTodo( $this->_database );
				$objTD->loadTodo ($this->_project->id, $item);
				$objTD->priority = $o;
				$objTD->store();
				$o++;
			}
		}

		// Go back to todo list
		return $this->page();
	}

	//----------------------------------------
	// Commenting
	//----------------------------------------

	/**
	 * Delete comment
	 *
	 * @return	   void, redirect
	 */
	protected function _deleteComment()
	{
		// Incoming
		$cid	= JRequest::getInt( 'cid', 0 );
		$todoid = $this->_todoid;

		// Instantiate comment
		$objC = new ProjectComment( $this->_database );

		if ($objC->load($cid))
		{
			$activityid = $objC->activityid;

			// delete comment
			if ($objC->deleteComment())
			{
				$this->_msg = JText::_('PLG_PROJECTS_TODO_COMMENT_DELETED');
			}

			// delete associated activity
			$objAA = new ProjectActivity( $this->_database );
			if ($activityid && $objAA->load($activityid))
			{
				$objAA->deleteActivity();
			}
		}

		// Pass error or success message
		if ($this->getError())
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg)
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}

		// Set redirect path
		$this->_referer = JRoute::_('index.php?option=' . $this->_option . a .
						  'alias=' . $this->_project->alias . a . 'active=todo' . a .
						  'action=view') . '/?todoid='.$todoid;
		return;

	}

	/**
	 * Save comment
	 *
	 * @return	   void, redirect
	 */
	protected function _saveComment()
	{
		// Incoming
		$itemid = JRequest::getInt( 'itemid', 0, 'post' );
		$comment = trim(JRequest::getVar( 'comment', '', 'post' ));
		$parent_activity = JRequest::getInt( 'parent_activity', 0, 'post' );

		// Instantiate comment
		$objC = new ProjectComment( $this->_database );
		if ($comment)
		{
			$objC->itemid = $itemid;
			$objC->tbl = 'todo';
			$objC->parent_activity = $parent_activity;
			$comment = \Hubzero\Utility\String::truncate($comment, 250);
			$objC->comment = \Hubzero\Utility\Sanitize::stripAll($comment);
			$objC->created = JFactory::getDate()->toSql();
			$objC->created_by = $this->_uid;
			if (!$objC->store())
			{
				$this->setError( $objC->getError() );
			}
			else
			{
				$this->_msg = JText::_('PLG_PROJECTS_TODO_COMMENT_POSTED');
			}
			// Get new entry ID
			if (!$objC->id) {
				$objC->checkin();
			}

			// Record activity
			$objAA = new ProjectActivity( $this->_database );
			if ($objC->id )
			{
				$what = JText::_('PLG_PROJECTS_TODO_TODO_ITEM');
				$url = '#tr_'.$parent_activity; // same-page link
				$aid = $objAA->recordActivity( $this->_project->id,
					$this->_uid, JText::_('PLG_PROJECTS_TODO_COMMENTED').' '.JText::_('PLG_PROJECTS_TODO_ON').' '.$what,
					$objC->id, $what, $url, 'quote', 0 );
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
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg)
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}

		// Set redirect path
		$this->_referer = JRoute::_('index.php?option=' . $this->_option . a .
						 'alias=' . $this->_project->alias . a . 'active=todo' . a .
						 'action=view') . '/?todoid=' . $itemid;
		return;
	}
}