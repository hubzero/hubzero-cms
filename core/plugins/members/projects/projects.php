<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2015 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_config = Component::params('com_projects');
		$this->_database = JFactory::getDBO();
	}

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
			$areas['projects'] = Lang::txt('PLG_MEMBERS_PROJECTS');
			$areas['icon'] = 'f03f';
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
		require_once(PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'models' . DS . 'project.php');

		// Model
		$this->model = new \Components\Projects\Models\Project();

		// Set filters
		$this->_filters = array(
			'mine'    => 1,
			'updates' => 1,
			'getowner'=> 1,
			'sortby'  => Request::getVar('sortby', 'title'),
			'sortdir' => Request::getVar('sortdir', 'ASC')
		);

		// Get a record count
		$this->_total = $this->model->entries('count', $this->_filters);

		$this->_user  = $user;

		if ($returnhtml)
		{
			// Which view
			$task = Request::getVar('action', '');

			switch ($task)
			{
				case 'all':     $arr['html'] = $this->_view('all');   break;
				case 'group':   $arr['html'] = $this->_view('group'); break;
				case 'owned':   $arr['html'] = $this->_view('owned'); break;
				case 'updates': $arr['html'] = $this->_updates();     break;
				default:        $arr['html'] = $this->_view('all');   break;
			}
		}

		//get meta
		$arr['metadata'] = array();

		$prefix = ($user->get('id') == $member->get("uidNumber")) ? 'I have' : $member->get('name') . ' has';
		$title = $prefix . ' ' . $this->_total . ' active projects.';

		//return total message count
		$arr['metadata']['count'] = $this->_total;

		return $arr;
	}

	/**
	 * View entries
	 *
	 * @param      string $which The type of entries to display
	 * @return     string
	 */
	protected function _view($which = 'all')
	{
		// Build the final HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'members',
				'element' => 'projects',
				'name'    => 'browse'
			)
		);

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
	 * @return     string
	 */
	protected function _updates()
	{
		// Build the final HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'members',
				'element' => 'projects',
				'name'    => 'updates'
			)
		);

		// Get all projects user has access to
		$projects = $this->model->table()->getUserProjectIds($this->_user->get('id'));

		$view->filters = array('limit' => Request::getVar('limit', 25, 'request'));

		// Get shared updates feed from blog plugin
		$results = Event::trigger( 'projects.onShared', array(
			'feed',
			$this->model,
			$projects,
			$this->_user->get('id'),
			$view->filters
		));

		$view->content      = !empty($results) && isset($results[0]) ? $results[0] : NULL;
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
