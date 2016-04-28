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
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @return  array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		// default areas returned to nothing
		$areas = array();

		// if this is the logged in user show them
		if ($user->get('id') == $member->get('id'))
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
	 * @param   object  $user    Current user
	 * @param   object  $member  Current member page
	 * @param   string  $option  Start of records to pull
	 * @param   array   $areas   Active area(s)
	 * @return  array
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
		$this->projects = $this->model->table()->getUserProjectIds($member->get('id'));

		// Build filters
		$this->filters = array(
			'projects'   => $this->projects,
			'limit'      => $this->params->get('limit', 50),
			'start'      => 0,
			'mine'       => Request::getInt('mine', 0),
			'sortby'     => Request::getWord('sortby', 'due'),
			'sortdir'    => Request::getWord('sortdir', 'ASC'),
			'assignedto' => Request::getInt('mine', 0) ? $member->get('id') : 0,
			'state'      => Request::getInt('state', 0)
		);

		if ($returnhtml)
		{
			$this->user    = $user;
			$this->member  = $member;
			$this->option  = $option;
			$this->database = App::get('db');

			$this->params = \Hubzero\Plugin\Params::getParams($this->member->get('id'), 'members', $this->_name);

			if ($user->get('id') == $member->get('id'))
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
	 * @return  string
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
	 * @return  string
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
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Save item
	 *
	 * @return  string
	 */
	protected function _save()
	{
		if (User::isGuest())
		{
			$this->setError(Lang::txt('MEMBERS_LOGIN_NOTICE'));
			return;
		}

		if (User::get('id') != $this->member->get('id'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_TODO_NOT_AUTHORIZED'));
			return $this->_browse();
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$content   = Request::getVar('content', '');
		$projectid = Request::getInt('projectid', 0);
		$due       = trim(Request::getVar('due', ''));

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
		$objTD = new \Components\Projects\Tables\Todo($this->database);
		$content = rtrim(stripslashes($content));
		$objTD->content    = $content ? $content : $objTD->content;
		$objTD->content    = \Hubzero\Utility\Sanitize::stripAll($objTD->content);
		$objTD->created_by = $this->member->get('id');
		$objTD->created    = Date::toSql();
		$objTD->projectid  = $model->get('id');

		if (strlen($objTD->content) > 255)
		{
			$objTD->details = $objTD->content;
		}
		$objTD->content = \Hubzero\Utility\String::truncate($objTD->content, 255);

		if ($due && $due!= 'mm/dd/yyyy')
		{
			$date = explode('/', $due);
			if (count($date) == 3)
			{
				$month = $date[0];
				$day   = $date[1];
				$year  = $date[2];
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
		$lastorder = $objTD->getLastOrder($model->get('id'));
		$objTD->priority = $lastorder ? $lastorder + 1 : 1;

		// Store content
		if (!$objTD->store())
		{
			$this->setError($objTD->getError());
			return $this->_browse();
		}
		else
		{
			// Record activity
			$aid = $model->recordActivity(
				Lang::txt('PLG_MEMBERS_TODO_ACTIVITY_TODO_ADDED'), $objTD->id, 'to do',
				Route::url('index.php?option=com_projects&alias=' . $model->get('alias') . '&active=todo&action=view&todoid=' . $objTD->id),
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

		App::redirect(
			Route::url($this->member->link() . '&active=' . $this->_name),
			Lang::txt('PLG_MEMBERS_TODO_SAVED')
		);
	}
}
