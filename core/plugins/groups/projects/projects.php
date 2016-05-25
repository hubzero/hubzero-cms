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
 * Groups Plugin class for projects
 */
class plgGroupsProjects extends \Hubzero\Plugin\Plugin
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

		$this->_config = Component::params('com_projects');
		$this->_database = App::get('db');
	}

	/**
	 * Loads the plugin language file
	 *
	 * @param   string   $extension  The extension for which a language file should be loaded
	 * @param   string   $basePath   The basepath to use
	 * @return  boolean  True, if the file has successfully loaded.
	 */
	public function loadLanguage($extension = '', $basePath = PATH_APP)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->_type . '_' . $this->_name;
		}

		$group = \Hubzero\User\Group::getInstance(Request::getCmd('cn'));
		if ($group && $group->isSuperGroup())
		{
			$basePath = PATH_APP . DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber');
		}

		$lang = \App::get('language');
		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_CORE . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true);
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => $this->_name,
			'title'            => Lang::txt('PLG_GROUPS_PROJECTS'),
			'default_access'   => $this->params->get('plugin_access','members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => 'f03f'
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param      object  $group      Current group
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$return = 'html';
		$active = $this->_name;

		// The output array we're returning
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$return = 'metadata';
			}
		}

		// Load classes
		require_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
			. DS . 'models' . DS . 'project.php');

		// Model
		$this->model = new \Components\Projects\Models\Project();

		$this->_projects = $this->model->table()->getGroupProjectIds(
			$group->get('gidNumber'),
			User::get('id')
		);

		// Set filters
		$this->_filters = array(
			'mine'    => 1,
			'updates' => 1,
			'getowner'=> 1,
			'group'   => $group->get('gidNumber'),
			'sortby'  => Request::getVar('sortby', 'title'),
			'sortdir' => Request::getVar('sortdir', 'ASC')
		);

		//if we want to return content
		if ($return == 'html')
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//get the group members
			$members = $group->get('members');

			// Set some variables so other functions have access
			$this->authorized = $authorized;
			$this->members    = $members;
			$this->group      = $group;
			$this->option     = $option;
			$this->action     = $action;

			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if (User::isGuest()
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			{
				$url = Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active, false, true);

				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url)),
					Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
					'warning'
				);
				return;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array(User::get('id'), $members) && $group_plugin_acl == 'members' && $authorized != 'admin')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}

			// Which view
			$task = $action ? strtolower(trim($action)) : Request::getVar('action', '');

			switch ($task)
			{
				case 'all':     $arr['html'] = $this->_view('all');   break;
				case 'owned':   $arr['html'] = $this->_view('owned'); break;
				case 'updates': $arr['html'] = $this->_updates();     break;
				default:        $arr['html'] = $this->_view('all');   break;
			}
		}

		//get meta
		$arr['metadata'] = array();

		//return total message count
		$arr['metadata']['count'] = count($this->_projects);

		// Return the output
		return $arr;
	}

	/**
	 * On after group membership changes - re-sync with projects
	 *
	 * @param      object  $group      Current group
	 * @return     array
	 */
	public function onAfterStoreGroup($group)
	{
		// Load classes
		require_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
			. DS . 'models' . DS . 'project.php');

		// Model
		$this->model = new \Components\Projects\Models\Project();

		// Get group projects
		$projects = $this->model->table()->getGroupProjects(
			$group->get('gidNumber'),
			User::get('id')
		);

		// Project-group sync
		if ($projects)
		{
			foreach ($projects as $project)
			{
				$this->model->table('Owner')->reconcileGroups($project->id, $project->owned_by_group);
				$this->model->table('Owner')->sysGroup($project->alias, $this->_config->get('group_prefix', 'pr-'));
			}
		}
	}

	/**
	 * View entries
	 *
	 * @param      string $which The type of entries to display
	 * @return     string
	 */
	protected function _view($which = 'owned')
	{
		// Build the final HTML
		$view = $this->view('default', 'browse');

		if ($which == 'all')
		{
			$this->_filters['which'] = 'owned';
			$view->owned = $this->model->entries('group', $this->_filters);

			$this->_filters['which'] = 'other';
			$view->rows = $this->model->entries('group', $this->_filters);
		}
		else
		{
			// Get records
			$options = array('all', 'owned', 'other');
			if (!in_array($which, $options))
			{
				$which = 'owned';
			}
			$this->_filters['which'] = $which;
			$view->rows = $this->model->entries('group', $this->_filters);
		}

		// Get counts
		$view->projectcount = count($this->_projects);
		$view->newcount = $this->model->table()->getUpdateCount($this->_projects, User::get('id'));

		$view->which   = $which;
		$view->filters = $this->_filters;
		$view->config  = $this->_config;
		$view->option  = 'com_projects';
		$view->group   = $this->group;
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
				'folder'  => 'groups',
				'element' => 'projects',
				'name'    => 'updates'
			)
		);

		$view->filters = array('limit' => Request::getVar('limit', 25, 'request'));

		// Get shared updates feed from blog plugin
		$results = Event::trigger( 'projects.onShared', array(
			'feed',
			$this->model,
			$this->_projects,
			User::get('id'),
			$view->filters
		));

		$view->content      = !empty($results) && isset($results[0]) ? $results[0] : NULL;
		$view->newcount     = $this->model->table()->getUpdateCount(
			$this->_projects,
			User::get('id')
		);
		$view->projectcount = count($this->_projects);
		$view->uid      = User::get('id');
		$view->config   = $this->_config;
		$view->group    = $this->group;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}
}
