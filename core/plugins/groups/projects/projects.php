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

		$this->_config = Component::params('com_projects');
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

		$group = Hubzero\User\Group::getInstance(Request::getCmd('cn'));
		if ($group && $group->isSuperGroup())
		{
			$basePath = PATH_APP . DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber');
		}

		$lang = App::get('language');
		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_CORE . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true);
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => $this->_name,
			'title'            => Lang::txt('PLG_GROUPS_PROJECTS'),
			'default_access'   => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => 'f03f'
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param   object   $group       Current group
	 * @param   string   $option      Name of the component
	 * @param   string   $authorized  User's authorization level
	 * @param   integer  $limit       Number of records to pull
	 * @param   integer  $limitstart  Start of records to pull
	 * @param   string   $action      Action to perform
	 * @param   array    $access      What can be accessed
	 * @param   array    $areas       Active area(s)
	 * @return  array
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

		// Get this area details
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
		require_once Component::path('com_projects') . DS . 'models' . DS . 'project.php';

		// Model
		$this->model = new Components\Projects\Models\Project();

		$this->_projects = $this->model->table()->getGroupProjectIds(
			$group->get('gidNumber'),
			User::get('id')
		);

		// If we want to return content
		if ($return == 'html')
		{
			// Set filters
			$this->_filters = array(
				'mine'     => 1,
				'updates'  => 1,
				'getowner' => 1,
				'group'    => $group->get('gidNumber'),
				'sortby'   => Request::getVar('sortby', 'title'),
				'sortdir'  => Request::getVar('sortdir', 'ASC'),
				'filterby' => Request::getVar('filterby', 'active')
			);
			if (!in_array($this->_filters['filterby'], array('active', 'archived')))
			{
				$this->_filters['filterby'] = 'active';
			}

			// Set group members plugin access level
			$group_plugin_acl = $access[$active];

			// Get the group members
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
				case 'all':
					$arr['html'] = $this->_view('all');
					break;
				case 'owned':
					$arr['html'] = $this->_view('owned');
					break;
				case 'updates':
					$arr['html'] = $this->_updates();
					break;
				case 'update':
					$arr['html'] = $this->_update();
					break;
				default:
					$arr['html'] = $this->_view('all');
					break;
			}
		}

		// get meta
		$arr['metadata'] = array();

		// return total record count
		$arr['metadata']['count'] = count($this->_projects);

		// Return the output
		return $arr;
	}

	/**
	 * On after group membership changes - re-sync with projects
	 *
	 * @param   object  $group  Current group
	 * @return  void
	 */
	public function onAfterStoreGroup($group)
	{
		// Load classes
		require_once Component::path('com_projects') . DS . 'models' . DS . 'project.php';

		// Model
		$this->model = new Components\Projects\Models\Project();

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
				$this->model->table('Owner')->reconcileGroups($project->id, $project->owned_by_group, $project->sync_group);
				$this->model->table('Owner')->sysGroup($project->alias, $this->_config->get('group_prefix', 'pr-'));
			}
		}
	}

	/**
	 * On after group saved
	 *
	 * @param   object  $before  Group before changes
	 * @param   object  $group   Group after changes
	 * @return  void
	 */
	public function onGroupAfterSave($before, $after)
	{
		// Is this group now archived?
		if ($before->published != 2 && $after->published == 2)
		{
			// Load classes
			require_once Component::path('com_projects') . DS . 'models' . DS . 'project.php';

			// Model
			$model = new Components\Projects\Models\Project();

			// Get group projects
			$projects = $model->table()->getGroupProjects(
				$after->get('gidNumber'),
				User::get('id')
			);

			if ($projects)
			{
				// Set projects to archived state
				foreach ($projects as $project)
				{
					if ($project->state == 3)
					{
						continue;
					}

					$model = new Components\Projects\Models\Project($project->id);
					$model->set('state', 3);
					$model->store(false);

					Event::trigger('projects.onProjectAfterSave', array($model));
				}
			}
		}
	}

	/**
	 * View entries
	 *
	 * @param   string  $which  The type of entries to display
	 * @return  string
	 */
	protected function _view($which = 'owned')
	{
		$which = strtolower($which);

		if (!in_array($which, array('all', 'owned', 'other')))
		{
			$which = 'owned';
		}

		// Get records
		if ($which == 'all')
		{
			$this->_filters['which'] = 'owned';
			$owned = $this->model->entries('group', $this->_filters);

			$this->_filters['which'] = 'other';
			$rows = $this->model->entries('group', $this->_filters);
		}
		else
		{
			$this->_filters['which'] = $which;

			$rows = $this->model->entries('group', $this->_filters);
			$owned = null;
		}

		// Build view
		$view = $this->view('default', 'browse')
			->set('rows', $rows)
			->set('owned', $owned)
			->set('projectcount', count($this->_projects))
			->set('newcount', $this->model->table()->getUpdateCount($this->_projects, User::get('id')))
			->set('which', $which)
			->set('filters', $this->_filters)
			->set('config', $this->_config)
			->set('option', 'com_projects')
			->set('group', $this->group)
			->setErrors($this->getErrors());

		return $view->loadTemplate();
	}

	/**
	 * Display updates
	 *
	 * @return  string
	 */
	protected function _updates()
	{
		$filters = array(
			'limit' => Request::getVar('limit', 25, 'request')
		);

		// Get shared updates feed from feed plugin
		$results = Event::trigger('projects.onShared', array(
			'feed',
			$this->model,
			$this->_projects,
			User::get('id'),
			$filters/*,
			in_array(User::get('id'), $this->group->get('managers')),
			array(
				'option' => 'com_groups',
				'task' => 'view',
				'cn' => $this->group->get('cn'),
				'active' => $this->_name,
				'action'=> 'update'
			)*/
		));

		$content = !empty($results) && isset($results[0]) ? $results[0] : null;

		// Build the final HTML
		$view = $this->view('default', 'updates')
			->set('filters', $filters)
			->set('content', $content)
			->set('newcount', $this->model->table()->getUpdateCount(
				$this->_projects,
				User::get('id')
			))
			->set('projectcount', count($this->_projects))
			->set('projects', $this->_projects)
			->set('config', $this->_config)
			->set('group', $this->group)
			->setErrors($this->getErrors());

		return $view->loadTemplate();
	}

	/**
	 * Update one or all projects
	 *
	 * @return  void
	 */
	protected function _update()
	{
		// Check for request forgeries
		Request::checkToken(['post']);

		$managers  = Request::getInt('managers_only', 0, 'post');
		$entry     = trim(Request::getVar('blogentry', '', 'post'));
		$posted    = Date::toSql();
		$posted_by = User::get('id');
		$projectid = Request::getInt('projectid', 0, 'post');

		if (!$projectid)
		{
			$projects = $this->_projects;
		}
		else
		{
			$projects = array($projectid);
		}

		// Add the post to each project
		foreach ($projects as $id)
		{
			$project = new Components\Projects\Models\Project($id);

			Event::trigger('projects.onSharedUpdate', array(
				$project,
				$entry,
				$managers,
				$posted_by,
				$posted
			));
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=projects&action=updates')
		);
	}
}
