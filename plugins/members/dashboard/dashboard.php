<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Members Plugin class for dashboard
 */
class plgMembersDashboard extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 *
	 */
	protected $_actionMap = array();

	/**
	 * Overloading Parent Constructor
	 *
	 * @param	array	$config		Optional configurations to be used
	 * @return  void
	 */
	public function __construct($subject, $config)
	{
		// get all public methods ending in 'action'
		$reflectionClass = new ReflectionClass($this);
		foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
		{
			$name = $method->getName();
			if (substr(strtolower($name), -6) == 'action')
			{
				$this->_actionMap[] = $name;
			}
		}

		// call parent constructor
		parent::__construct($subject, $config);
	}

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @return     array   Plugin name
	 */
	public function onMembersAreas($user, $member)
	{
		//default areas returned to nothing
		$areas = array();

		//if this is the logged in user show them
		if ($user->get('id') == $member->get('uidNumber'))
		{
			$areas['dashboard'] = JText::_('PLG_MEMBERS_DASHBOARD');
			$areas['icon'] = 'f009';
		}

		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @param      string  $option Component name
	 * @param      string  $areas  Plugins to return data
	 * @return     array   Return array of html
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
			'html' => '',
			'metadata' => ''
		);

		// Build the final HTML
		if ($returnhtml)
		{
			// include dasboard models
			include_once JPATH_ROOT . DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'models' . DS . 'preferences.php';

			// add assets
			\Hubzero\Document\Assets::addPluginStylesheet('members', 'dashboard');
			\Hubzero\Document\Assets::addPluginScript('members', 'dashboard');
			\Hubzero\Document\Assets::addSystemScript('gridster');
			\Hubzero\Document\Assets::addSystemScript('resizeEnd.min');

			// set up some vars
			$this->member   = $member;
			$this->params   = $this->params;
			$this->juser    = JFactory::getUser();
			$this->database = JFactory::getDBO();
			$this->option   = $option;
			$this->action   = JRequest::getVar('action', 'display');

			// build the action
			$doAction = strtolower($this->action) . 'Action';

			// make sure we have that action
			if (in_array($doAction, $this->_actionMap))
			{
				$arr['html'] = $this->$doAction();
			}
			else
			{
				throw new Exception('Members dashboard action doesnt exist.');
			}
		}

		return $arr;
	}

	/**
	 * Display Member Dashboard
	 *
	 * @return Void
	 */
	public function displayAction()
	{
		// create view object
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'display',
			)
		);

		// load dashboard modules
		$dashboardModules = $this->_loadModules($this->params->get('position', 'memberDashboard'));

		// load member preferences
		$membersDashboardModelPreferences = MembersDashboardModelPreferences::loadForUser($this->juser->get('id'));
		$preferences = json_decode($membersDashboardModelPreferences->get('preferences'));

		// if user doesnt have preferences, get default & store them
		if (!isset($preferences) && !is_array($preferences))
		{
			$preferences = $this->params->get('defaults', '[]');
			$preferences = json_encode(json_decode($preferences));

			$dashboardPreferences = new MembersDashboardModelPreferences();
			$dashboardPreferences->set('uidNumber', $this->juser->get('id'));
			$dashboardPreferences->set('preferences', $preferences);
			$dashboardPreferences->set('modified', JFactory::getDate()->toSql());
			$dashboardPreferences->store();

			// turn back into object for later
			$preferences = json_decode($preferences);
		}

		// var to hold modules
		$view->modules = array();

		// check to see if which modules to display
		foreach ($preferences as $preference)
		{
			if (isset($dashboardModules[$preference->module]))
			{
				// create module objects
				$module                      = $dashboardModules[$preference->module];
				$module->positioning         = new stdClass;
				$module->positioning->col    = $preference->col;
				$module->positioning->row    = $preference->row;
				$module->positioning->size_x = $preference->size_x;
				$module->positioning->size_y = $preference->size_y;

				// merge user params with hub wide params
				if (isset($preference->parameters))
				{
					$params  = new JRegistry($module->params);
					$uparams = new JRegistry($preference->parameters);
					$params->merge($uparams);
					$module->params = $params->toString();
				}

				$view->modules[] = $module;
			}
		}

		$application  = JFactory::getApplication();
		$view->admin  = $application->isAdmin();
		$view->juser  = $this->juser;
		$view->params = $this->params;

		// return rendered view
		return $view->loadTemplate();
	}

	/**
	 * Return Module Rendered & Ready For Display
	 *
	 * @return void
	 */
	public function moduleAction()
	{
		// get module id
		$moduleId = JRequest::getInt('moduleid', 0);

		// get list of modules
		$modulesList = $this->_loadModules($this->params->get('position', 'myhub'));

		// load member preferences
		$membersDashboardModelPreferences = MembersDashboardModelPreferences::loadForUser($this->juser->get('id'));
		$preferences = json_decode($membersDashboardModelPreferences->get('preferences'));

		// get module preferences for moduleid
		$preference = new stdClass;
		foreach ($preferences as $p)
		{
			if ($p->module == $moduleId)
			{
				$preference = $p;
				break;
			}
		}

		// get the module
		$module = null;
		if (in_array($moduleId, array_keys($modulesList)))
		{
			$module                      = $modulesList[$moduleId];
			$module->positioning         = new stdClass;
			$module->positioning->col    = 1;
			$module->positioning->row    = 1;
			$module->positioning->size_x = 1;
			$module->positioning->size_y = 2;

			// merge user params with hub wide params
			if (isset($preference->parameters))
			{
				$params  = new JRegistry($module->params);
				$uparams = new JRegistry($preference->parameters);
				$params->merge($uparams);
				$module->params = $params->toString();
			}
		}

		// create view object
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'display',
				'layout'  => 'module'
			)
		);

		// get application location
		$application  = JFactory::getApplication();
		$view->admin  = $application->isAdmin();
		$view->module = $module;
		$content      = $view->loadTemplate();

		$stylesheets = array();
		$scripts     = array();
		$document = JFactory::getDocument();
		foreach ($document->_styleSheets as $strSrc => $strAttr)
		{
			if (strstr($strSrc, $module->module))
			{
				$stylesheets[] = $strSrc;
			}
		}
		foreach ($document->_scripts as $strSrc => $strType)
		{
			if (strstr($strSrc, $module->module))
			{
				$scripts[] = $strSrc;
			}
		}

		// return content
		echo json_encode(array('html' => $content, 'assets' => array('scripts' => $scripts, 'stylesheets' => $stylesheets)));
		exit();
	}

	/**
	 * Display Add Module View
	 *
	 * @return void
	 */
	public function addAction()
	{
		// create view object
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'add',
			)
		);

		// load dashboard modules
		$view->modules = $this->_loadModules($this->params->get('position', 'myhub'));

		// load member preferences
		$membersDashboardModelPreferences = MembersDashboardModelPreferences::loadForUser($this->juser->get('id'));
		$preferences = json_decode($membersDashboardModelPreferences->get('preferences'));

		// get list of install member modules
		$view->mymodules = array_map(function($mod) {
			return $mod->module;
		}, $preferences);

		// return rendered view
		return $view->loadTemplate();
	}

	/**
	 * Save Module Params for User
	 *
	 * @return void
	 */
	public function saveAction()
	{
		// get request vars
		$modules = JRequest::getString('modules', '');

		// make sure we have modules
		if ($modules == '')
		{
			JError::raiseError(500,'Unable to save the users modules.');
			exit();
		}

		// load member preferences
		$membersDashboardModelPreferences = MembersDashboardModelPreferences::loadForUser($this->juser->get('id'));

		// update the user preferences
		$membersDashboardModelPreferences->set('preferences', $modules);
		$membersDashboardModelPreferences->set('modified', JFactory::getDate()->toSql());

		// attempt to save
		if (!$membersDashboardModelPreferences->store())
		{
			JError::raiseError(500,'Unable to save the users modules.');
			exit();
		}

		// build return
		echo json_encode(array(
			'saved' => true,
			'modules' => $modules
		));
		exit();
	}

	/**
	 * Returns the name of the plugin if it has an admin interface
	 *
	 * @return     string
	 */
	public function onCanManage()
	{
		return $this->_name;
	}

	/**
	 * Event call for managing this plugin's content
	 *
	 * @param      string $option     Component name
	 * @param      string $controller Cotnroller to use
	 * @param      string $action     Action to perform
	 * @return     string
	 */
	public function onManage($option, $controller='plugins', $action='default')
	{
		$this->option     = $option;
		$this->controller = $controller;
		$this->action     = $action;
		$this->database   = JFactory::getDBO();
		$this->juser      = JFactory::getUser();

		// include dasboard models
		include_once JPATH_ROOT . DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'models' . DS . 'preferences.php';

		// add assets
		JHTML::_('behavior.modal');
		\Hubzero\Document\Assets::addPluginStylesheet('members', 'dashboard');
		\Hubzero\Document\Assets::addPluginStylesheet('members', 'dashboard', 'dashboard.admin');
		\Hubzero\Document\Assets::addPluginScript('members', 'dashboard', 'dashboard.admin');
		\Hubzero\Document\Assets::addSystemScript('gridster');
		\Hubzero\Document\Assets::addSystemScript('resizeEnd.min');

		// build method name and call action
		$methodName = 'Manage' . ucfirst(strtolower($action)) . 'Action';
		return $this->$methodName();
	}

	/**
	 * Display Main Dashboard Manage
	 *
	 * @return void
	 */
	public function manageDefaultAction()
	{
		// create view object
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'manage',
			)
		);

		// var to hold modules
		$view->modules = array();

		// load dashboard modules
		$dashboardModules = $this->_loadModules($this->params->get('position', 'myhub'));

		// get default prefs
		$preferences = $this->params->get('defaults', '[]');
		$preferences = json_decode($preferences);

		// check to see if which modules to display
		foreach ($preferences as $preference)
		{
			if (isset($dashboardModules[$preference->module]))
			{
				// create module objects
				$module                      = $dashboardModules[$preference->module];
				$module->positioning         = new stdClass;
				$module->positioning->col    = $preference->col;
				$module->positioning->row    = $preference->row;
				$module->positioning->size_x = $preference->size_x;
				$module->positioning->size_y = $preference->size_y;

				$view->modules[] = $module;
			}
		}

		$application = JFactory::getApplication();
		$view->admin = $application->isAdmin();

		// return rendered view
		return $view->loadTemplate();
	}

	/**
	 * Save Module Defaults
	 *
	 * @return void
	 */
	public function manageSaveAction()
	{
		// get request vars
		$modules = JRequest::getString('modules', '');

		// set our new defaults
		$this->params->set('defaults', $modules);

		// save
		$query = "UPDATE #__extensions SET params=" . $this->database->quote($this->params->toString()) . " WHERE `folder`='members' AND `element`='dashboard'";
		$this->database->setQuery($query);
		if ($this->database->query())
		{
			$this->setError($this->database->getErrorMsg());
		}

		//quit now
		exit();
	}

	/**
	 * Display Add Module View
	 *
	 * @return void
	 */
	public function manageAddAction()
	{
		// create view object
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'add',
			)
		);

		// load dashboard modules
		$view->modules = $this->_loadModules($this->params->get('position', 'myhub'));

		// get default prefs
		$preferences = $this->params->get('defaults', '[]');
		$preferences = json_decode($preferences);

		// get list of default modules
		$view->mymodules = array_map(function($mod) {
			return $mod->module;
		}, $preferences);

		// return rendered view
		return $view->loadTemplate();
	}

	/**
	 * Return Rendered Module
	 *
	 * @return void
	 */
	public function manageModuleAction()
	{
		// get module id
		$moduleId = JRequest::getInt('moduleid', 0);

		// get list of modules
		$modulesList = $this->_loadModules($this->params->get('position', 'myhub'));

		// get the module
		$module = null;
		if (in_array($moduleId, array_keys($modulesList)))
		{
			$module                      = $modulesList[$moduleId];
			$module->positioning         = new stdClass;
			$module->positioning->col    = 1;
			$module->positioning->row    = 1;
			$module->positioning->size_x = 1;
			$module->positioning->size_y = 2;
		}

		// create view object
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'display',
				'layout'  => 'module'
			)
		);

		// get application location
		$application  = JFactory::getApplication();
		$view->admin  = $application->isAdmin();
		$view->module = $module;
		$content      = $view->loadTemplate();

		// return content
		echo json_encode(array('html' => $content));
		exit();
	}

	/**
	 * Display Push Module View
	 *
	 * @return void
	 */
	public function managePushAction()
	{
		// create view object
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'manage',
				'layout'  => 'push'
			)
		);

		// get list of modules
		$view->modules = $this->_loadModules($this->params->get('position', 'myhub'));

		// return rendered view
		return $view->loadTemplate();
	}

	/**
	 * [manageDoPushAction description]
	 * @return [type] [description]
	 */
	public function manageDoPushAction()
	{
		// get request vars
		$module   = JRequest::getInt('module', null);
		$column   = JRequest::getInt('column', 1);
		$position = JRequest::getCmd('position', 'first');
		$width    = JRequest::getInt('width', 1);
		$height   = JRequest::getInt('height', 2);

		// make sure we have a module
		if ($module == 0 || $module == null)
		{
			JError::raiseError(406, 'You must provide a module.');
			return;
		}

		// load all member preferences
		$this->database->setQuery("SELECT * from `#__xprofiles_dashboard_preferences`");
		$memberPreferences = $this->database->loadObjectList();

		// loop through each member preference and attempt to push module
		foreach ($memberPreferences as $memberPreference)
		{
			// load their member preferences
			$params = json_decode($memberPreference->preferences);

			// get a list of installed modules
			$modules = array_map(function($param) {
				return $param->module;
			}, $params);

			// if we already have the module were done
			if (in_array($module, $modules))
			{
				continue;
			}

			// calculate the heights for each column
			$maxForCols = array('1' => 0, '2' => 0, '3' => 0);
			foreach ($params as $param)
			{
				$col    = $param->col;
				$height = $param->size_y;
				$maxForCols[$col] += $height;
			}

			// create new module object
			$newModule = new stdClass;
			$newModule->module = $module;
			$newModule->col    = $column;
			$newModule->size_x = $width;
			$newModule->size_y = $height;

			// add new module
			if ($position == 'last')
			{
				$newModule->row = $maxForCols[$column] + 1;
				array_push($params, $newModule);
			}
			else
			{
				$newModule->row = 1;
				array_unshift($params, $newModule);

				// run through following params (modules) and adjust their position
				$mins = array('1' => 0, '2' => 0, '3' => 0);
				foreach ($params as $param)
				{
					$col = $param->col;
					$min = $param->row + $param->size_y;
					if ($min <= $mins[$col])
					{
						$param->row = $min;
					}
					$mins[$col] += $min;
				}
			}

			// encode params
			$params = json_encode($params);

			// update user params
			$sql = "UPDATE `#__xprofiles_dashboard_preferences` SET `preferences`=" . $this->database->quote($params) . " WHERE `uidNumber`=" . $memberPreference->uidNumber;
			$this->database->setQuery($sql);
			$this->database->query();
		}

		// return message
		echo json_encode(array('module_pushed' => true));
		exit();
	}

	/**
	 * [_loadModules description]
	 * @param  string $position [description]
	 * @return [type]           [description]
	 */
	private function _loadModules( $position = '' )
	{
		$query = "SELECT *
		          FROM `#__modules` AS m
		          WHERE position=" . $this->database->quote($position)  . "
		          AND m.published=1
		          AND m.client_id=0
		          ORDER BY m.ordering";
		$this->database->setQuery($query);
		$modules = $this->database->loadObjectList('id');

		return $modules;
	}
}
