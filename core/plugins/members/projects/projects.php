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
 * Members Plugin class for projects
 */
class plgMembersProjects extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  Event observer
	 * @param   array   $config    Optional config values
	 * @return  void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_config = Component::params('com_projects');
		$this->_database = App::get('db');
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param   object  $user
	 * @param   object  $member
	 * @return  array
	 */
	public function &onMembersAreas($user, $member)
	{
		// default areas returned to nothing
		$areas = array();

		// if this is the logged in user show them
		if ($user->get('id') == $member->get('id'))
		{
			$areas['projects'] = Lang::txt('PLG_MEMBERS_PROJECTS');
			$areas['icon'] = 'f03f';
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
		$returnmeta = true;

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

		// Load classes
		require_once PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'models' . DS . 'project.php';

		// Model
		$this->model = new \Components\Projects\Models\Project();

		// Set filters
		$this->_filters = array(
			'mine'     => 1,
			'updates'  => 1,
			'getowner' => 1,
			'sortby'   => Request::getVar('sortby', 'title'),
			'sortdir'  => Request::getVar('sortdir', 'ASC'),
			'filterby' => Request::getVar('filterby', 'active')
		);
		if (!in_array($this->_filters['filterby'], array('active', 'archived')))
		{
			$this->_filters['filterby'] = 'active';
		}

		// Get a record count
		$this->_total = $this->model->entries('count', $this->_filters);

		$this->_user = $user;

		if ($returnhtml)
		{
			// Which view
			$task = Request::getVar('action', '');

			switch ($task)
			{
				case 'all':
					$arr['html'] = $this->_view('all');
					break;
				case 'group':
					$arr['html'] = $this->_view('group');
					break;
				case 'owned':
					$arr['html'] = $this->_view('owned');
					break;
				case 'updates':
					$arr['html'] = $this->_updates();
					break;
				default:
					$arr['html'] = $this->_view('all');
					break;
			}
		}

		//get meta
		$arr['metadata'] = array();

		$prefix = ($user->get('id') == $member->get('id')) ? 'I have' : $member->get('name') . ' has';
		$title = $prefix . ' ' . $this->_total . ' active projects.';

		//return total message count
		$arr['metadata']['count'] = $this->_total;

		return $arr;
	}

	/**
	 * View entries
	 *
	 * @param   string  $which  The type of entries to display
	 * @return  string
	 */
	protected function _view($which = 'all')
	{
		// Build the final HTML
		$view = $this->view('default', 'browse');

		$view->projects = $this->model->table()->getUserProjectIds($this->_user->get('id'));
		$view->newcount = $this->model->table()->getUpdateCount($view->projects, $this->_user->get('id'));

		if ($which == 'all')
		{
			$this->_filters['which'] = 'owned';
			$view->owned = $this->model->entries('list', $this->_filters);

			$this->_filters['which'] = 'other';
			$view->rows = $this->model->entries('list', $this->_filters);
		}
		else
		{
			// Get records
			$options = array('owned', 'other', 'group');
			if (!in_array($which, $options))
			{
				$which = 'owned';
			}
			$this->_filters['which'] = $which;
			$view->rows = $this->model->entries('list', $this->_filters);
		}

		$view->which   = $which;
		$view->total   = $this->_total;
		$view->user    = $this->_user;
		$view->filters = $this->_filters;
		$view->config  = $this->_config;
		$view->option  = 'com_projects';
		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Display updates
	 *
	 * @return  string
	 */
	protected function _updates()
	{
		// Build the final HTML
		$view = $this->view('default', 'updates');

		// Get all projects user has access to
		$projects = $this->model->table()->getUserProjectIds($this->_user->get('id'));

		$view->filters = array(
			'limit' => Request::getVar('limit', 25, 'request')
		);

		// Get shared updates feed from blog plugin
		$results = Event::trigger('projects.onShared', array(
			'feed',
			$this->model,
			$projects,
			$this->_user->get('id'),
			$view->filters
		));

		$view->content      = !empty($results) && isset($results[0]) ? $results[0] : null;
		$view->newcount     = $this->model->table()->getUpdateCount(
			$projects,
			$this->_user->get('id')
		);
		$view->projectcount = $this->_total;
		$view->uid          = $this->_user->get('id');

		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}
}
