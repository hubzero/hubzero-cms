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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Site\Controllers;

use Components\Courses\Models;
use Hubzero\Component\SiteController;
use Exception;
use Pathway;
use Request;
use Route;
use Lang;
use App;

/**
 * Courses controller class
 */
class Courses extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		if ($section_id = Request::getInt('section', 0, 'get'))
		{
			$section = Models\Section::getInstance($section_id);
			if ($section->exists())
			{
				$offering = Models\Offering::getInstance($section->get('offering_id'));
				$offering->section($section->get('alias'));

				App::redirect(
					Route::url($offering->link('overview'))
				);
			}
		}

		$this->registerTask('__default', 'intro');

		$this->_authorize('course');

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @param      array $course_pages List of roup pages
	 * @return     void
	 */
	public function _buildPathway($course_pages = array())
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task && $this->_task != 'intro')
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return     void
	 */
	public function _buildTitle()
	{
		//set title used in view
		$this->_title = Lang::txt(strtoupper($this->_option));

		if ($this->_task && $this->_task != 'intro')
		{
			$this->_title .= ': ' . Lang::txt(strtoupper($this->_option . '_' . $this->_task));
		}

		//set title of browser window
		\Document::setTitle($this->_title);
	}

	/**
	 * Display component main page
	 *
	 * @return     void
	 */
	public function introTask()
	{
		//build the title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		// Push some needed scripts to the template
		$model = Models\Courses::getInstance();

		$this->view->popularcourses = $model->courses(array(
			'limit' => 12,
			'sort'  => 'students',
			'state' => 1
		), true);

		// Output HTML
		$this->view->config   = $this->config;
		$this->view->database = $this->database;
		$this->view->title    = $this->_title;

		$this->view->notifications = \Notify::messages('courses');
		$this->view->display();
	}

	/**
	 * Display a list of courses on the site and options for filtering/browsing them
	 *
	 * @return     void
	 */
	public function browseTask()
	{
		// Filters
		$this->view->filters = array(
			'state'  => 1,
			'search' => Request::getVar('search', ''),
			'sortby' => strtolower(Request::getWord('sortby', 'title')),
			'group'  => Request::getVar('group', '')
		);
		if ($this->view->filters['group'])
		{
			$group = \Hubzero\User\Group::getInstance($this->view->filters['group']);
			if ($group)
			{
				$this->view->filters['group_id'] = $group->get('gidNumber');
			}
		}
		if (!in_array($this->view->filters['sortby'], array('alias', 'title', 'popularity')))
		{
			$this->view->filters['sortby'] = 'title';
		}
		switch ($this->view->filters['sortby'])
		{
			case 'popularity':
				$this->view->filters['sort']  = 'students';
				$this->view->filters['sort_Dir'] = 'DESC';
			break;
			case 'title':
			case 'alias':
			default:
				$this->view->filters['sort']  = $this->view->filters['sortby'];
				$this->view->filters['sort_Dir'] = 'ASC';
			break;
		}
		// Filters for returning results
		$this->view->filters['limit']  = Request::getInt('limit', Config::get('list_limit'));
		$this->view->filters['limit']  = ($this->view->filters['limit']) ? $this->view->filters['limit'] : 'all';
		$this->view->filters['start']  = Request::getInt('limitstart', 0);
		$this->view->filters['index']  = strtolower(Request::getWord('index', ''));
		if ($this->view->filters['index'] && !in_array($this->view->filters['index'], array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z')))
		{
			$this->view->filters['index'] = '';
		}
		$this->view->filters['tag'] = Request::getVar('tag', '');

		$model = Models\Courses::getInstance();

		// Get a record count
		$this->view->filters['count'] = true;
		$this->view->total   = $model->courses($this->view->filters);

		// Get records
		$this->view->filters['count'] = false;
		$this->view->courses = $model->courses($this->view->filters);

		//build the title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->model  = $model;
		$this->view->title  = $this->_title;
		$this->view->config = $this->config;
		$this->view->notifications = \Notify::messages('courses');
		$this->view->display();
	}

	/**
	 * Public url for badge info
	 *
	 * @return     void
	 */
	public function badgeTask()
	{
		if ($badge_id = Request::getInt('badge_id', false))
		{
			$badge = new Models\Section\Badge($badge_id);

			if (!$badge->get('id'))
			{
				throw new Exception(Lang::txt('COM_COURSES_BADGE_NOT_FOUND'), 500);
			}
			else
			{
				$this->view->badge  = $badge;
				$this->view->config = $this->config;
				$this->view->action = Request::getWord('action', 'default');
				$this->view->token  = Request::getVar('validation_token', false);
			}
		}
		else
		{
			throw new Exception(Lang::txt('COM_COURSES_BADGE_NOT_FOUND'), 500);
		}

		$this->view->display();
	}

	/**
	 * Set access permissions for a user
	 *
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, false);
		if (!User::isGuest())
		{
			$asset  = $this->_option;
			if ($assetId)
			{
				$asset .= ($assetType != 'component') ? '.' . $assetType : '';
				$asset .= ($assetId) ? '.' . $assetId : '';
			}

			$at = '';
			if ($assetType != 'component')
			{
				$at .= '.' . $assetType;
			}

			// Admin
			$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
			// Permissions
			//$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
		}
	}
}

