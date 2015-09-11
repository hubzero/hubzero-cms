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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for todo entries
 */
class plgMembersTodo extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onMembersAreas($user, $member)
	{
		//default areas returned to nothing
		$areas = array();

		//if this is the logged in user show them
		if ($user->get('id') == $member->get('uidNumber'))
		{
			$areas = array(
				'todo' => Lang::txt('PLG_MEMBERS_TODO'),
				'icon' => 'f08d'
			);
		}
		return $areas;
	}

	/**
	 * Perform actions when viewing a member profile
	 *
	 * @param      object $user   Current user
	 * @param      object $member Current member page
	 * @param      string $option Start of records to pull
	 * @param      array  $areas  Active area(s)
	 * @return     array
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		// Include models
		require_once(PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'models' . DS . 'project.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'models' . DS . 'todo.php');

		// Get our models
		$this->todo = new \Components\Projects\Models\Todo();
		$this->model = new \Components\Projects\Models\Project();

		// Get member projects
		$this->projects = $this->model->table()->getUserProjectIds($member->get('uidNumber'));

		// Build filters
		$this->filters = array(
			'projects'	 => $this->projects,
			'limit'      => $this->params->get('limit', 50),
			'start'		 => 0,
			'mine'       => Request::getInt('mine', 0),
			'sortby'	 => Request::getWord('sortby', 'due'),
			'sortdir'	 => Request::getWord('sortdir', 'ASC'),
			'assignedto' => Request::getInt('mine', 0) ? $member->get('uidNumber') : 0,
			'state'      => Request::getInt('state', 0)
		);

		if ($returnhtml)
		{
			$this->user    = $user;
			$this->member  = $member;
			$this->option  = $option;
			$this->database = App::get('db');

			$p = new \Hubzero\Plugin\Params($this->database);
			$this->params = $p->getParams($this->member->get('uidNumber'), 'members', $this->_name);

			if ($user->get('id') == $member->get('uidNumber'))
			{
				$this->params->set('access-edit-comment', true);
				$this->params->set('access-delete-comment', true);
			}

			// Append to document the title
			Document::setTitle(Document::getTitle() . ': ' . Lang::txt('PLG_MEMBERS_TODO'));

			// Get and determine task
			$this->task = Request::getVar('action', '');

			switch ($this->task)
			{
				case 'browse':
				default: $arr['html'] = $this->_browse(); break;

				case 'new':
					$arr['html'] = $this->_new();
					break;

				case 'save':
					$arr['html'] = $this->_save();
					break;
			}
		}

		// Get an entry count
		$arr['metadata']['count'] = $this->todo->entries('count', $this->filters);

		return $arr;
	}

	/**
	 * Display a list of todo entries
	 *
	 * @return     string
	 */
	private function _browse()
	{
		$view = $this->view('default', 'browse');
		$view->option   = $this->option;
		$view->member   = $this->member;
		$view->config   = $this->params;
		$view->model    = $this->model;
		$view->todo     = $this->todo;
		$view->filters  = $this->filters;
		$view->projects = $this->projects;

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * New item
	 *
	 * @return	   string
	 */
	protected function _new()
	{
		$view = $this->view('default', 'new');
		$view->option  = $this->option;
		$view->todo    = $this->todo;
		$view->model   = $this->model;
		$view->member  = $this->member;
		$view->config  = $this->params;

		$filters = array(
			'mine'  => 1,
			'active'=> 1,
			'editor'=> 1,
			'sortby'=> 'title'
		);
		$view->projects = $this->model->entries('list', $filters);

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Save item
	 *
	 * @return	   string
	 */
	protected function _save()
	{
		if (User::isGuest())
		{
			$this->setError(Lang::txt('MEMBERS_LOGIN_NOTICE'));
			return;
		}

		if (User::get('id') != $this->member->get("uidNumber"))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_TODO_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$content	= Request::getVar('content', '');
		$projectid  = Request::getInt('projectid', 0);
		$due 		= trim(Request::getVar('due', ''));

		$model = new \Components\Projects\Models\Project($projectid);

		if (!$content)
		{
			$this->setError(Lang::txt('PLG_MEMBERS_TODO_ERROR_PROVIDE_CONTENT'));
			return $this->_browse();
		}

		if (!$model->exists() || !$model->access('content'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_TODO_ERROR_ACCESS_PROJECT'));
			return $this->_browse();
		}

		// Initiate extended database class
		$objTD = new \Components\Projects\Tables\Todo( $this->database );
		$content			= rtrim(stripslashes($content));
		$objTD->content		= $content ? $content : $objTD->content;
		$objTD->content		= \Hubzero\Utility\Sanitize::stripAll($objTD->content);
		$objTD->created_by	= $this->member->get('uidNumber');
		$objTD->created		= Date::toSql();
		$objTD->projectid	= $model->get('id');

		if (strlen($objTD->content) > 255)
		{
			$objTD->details = $objTD->content;
		}
		$objTD->content		= \Hubzero\Utility\String::truncate($objTD->content, 255);

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
						$day='0' . $day;
					}

					if (strlen($month) == 1)
					{
						$month='0' . $month;
					}
					if (checkdate($month, $day, $year))
					{
						$objTD->duedate = Date::of(mktime(0, 0, 0, $month, $day, $year))->toSql();
					}
				}
			}
		}
		else
		{
			$objTD->duedate = '';
		}

		// Get last order
		$lastorder        = $objTD->getLastOrder($model->get('id'));
		$objTD->priority  = $lastorder ? $lastorder + 1 : 1;

		// Store content
		if (!$objTD->store())
		{
			$this->setError( $objTD->getError() );
			return $this->_browse();
		}
		else
		{
			// Record activity
			$aid = $model->recordActivity(
				Lang::txt('PLG_MEMBERS_TODO_ACTIVITY_TODO_ADDED'), $objTD->id, 'to do',
				Route::url('index.php?option=com_projects'
					. '&alias=' . $model->get('alias') . '&active=todo'
					. '&action=view&todoid=' . $objTD->id), 'todo', 1);

			// Store activity ID
			if ($aid)
			{
				$objTD->activityid = $aid;
				$objTD->store();
			}
		}

		App::redirect(
			Route::url($this->member->getLink() . '&active=' . $this->_name),
			Lang::txt('PLG_MEMBERS_TODO_SAVED')
		);
	}
}
