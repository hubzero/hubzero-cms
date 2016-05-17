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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for dashboard
 */
class plgMembersDashboard extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * List of actions
	 *
	 * @var  array
	 */
	protected $_actionMap = array();

	/**
	 * Constructor
	 *
	 * @param   object  $subject  Event dispatcher
	 * @param   array   $config   Optional configurations to be used
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
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @return  array   Plugin name
	 */
	public function onMembersAreas($user, $member)
	{
		//default areas returned to nothing
		$areas = array();

		//if this is the logged in user show them
		if ($user->get('id') == $member->get('id'))
		{
			$areas['dashboard'] = Lang::txt('PLG_MEMBERS_DASHBOARD');
			$areas['icon'] = 'f009';
		}

		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @param   string  $option  Component name
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
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
			$this->params->set('position', 'memberDashboard');

			// include dasboard models
			include_once __DIR__ . DS . 'models' . DS . 'preference.php';

			// add assets
			$this->css();
			$this->js();
			$this->js('gridster', 'system');
			$this->js('resizeEnd.min', 'system');

			// set up some vars
			$this->member   = $member;
			$this->params   = $this->params;
			$this->database = App::get('db');
			$this->option   = $option;
			$this->action   = Request::getVar('action', 'display');

			// build the action
			$doAction = strtolower($this->action) . 'Action';

			// make sure we have that action
			if (in_array($doAction, $this->_actionMap))
			{
				$arr['html'] = $this->$doAction();
			}
			else
			{
				throw new Exception('Members dashboard action does not exist.');
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
		$view = $this->view('default', 'display');

		// load dashboard modules
		$dashboardModules = $this->_loadModules($this->params->get('position', 'memberDashboard'));

		// load user preferences
		$preferences = $this->_loadPreferences();

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
					$params  = new \Hubzero\Config\Registry($module->params);
					$uparams = new \Hubzero\Config\Registry($preference->parameters);
					$params->merge($uparams);
					$module->params = $params->toString();
				}

				$view->modules[] = $module;
			}
		}

		$view->admin  = App::isAdmin();
		$view->params = $this->params;

		// return rendered view
		return $view->loadTemplate();
	}

	/**
	 * Return Module Rendered & Ready For Display
	 *
	 * @return  void
	 */
	public function moduleAction()
	{
		// get module id
		$moduleId = Request::getInt('moduleid', 0);

		// get list of modules
		$modulesList = $this->_loadModules($this->params->get('position', 'memberDashboard'));

		// load user preferences
		$preferences = $this->_loadPreferences();

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
				$params  = new \Hubzero\Config\Registry($module->params);
				$uparams = new \Hubzero\Config\Registry($preference->parameters);
				$params->merge($uparams);
				$module->params = $params->toString();
			}
		}

		// create view object
		$view = $this->view('module', 'display');

		// get application location
		$view->admin  = App::isAdmin();
		$view->module = $module;
		$content      = $view->loadTemplate();

		$stylesheets = array();
		$scripts     = array();
		$document = Document::getHeadData();
		foreach ($document['styleSheets'] as $strSrc => $strAttr)
		{
			if (strstr($strSrc, $module->module))
			{
				$stylesheets[] = $strSrc;
			}
		}
		foreach ($document['scripts'] as $strSrc => $strType)
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
	 * @return  string
	 */
	public function addAction()
	{
		// create view object
		$view = $this->view('default', 'add');

		// load dashboard modules
		$view->modules = $this->_loadModules($this->params->get('position', 'memberDashboard'));

		// load user preferences
		$preferences = $this->_loadPreferences();

		// get list of install member modules
		$view->mymodules = array_map(function($mod)
		{
			return $mod->module;
		}, $preferences);

		// return rendered view
		return $view->loadTemplate();
	}

	/**
	 * Save Module Params for User
	 *
	 * @return  void
	 */
	public function saveAction()
	{
		Request::checkToken(['get', 'post']);

		// get request vars
		$modules = Request::getString('modules', '');

		// make sure we have modules
		if ($modules == '')
		{
			App::abort(500,'Unable to save the users modules.');
			exit();
		}

		// if we have no modules set to an empty string
		// this way we can differentiate between a bad default & a user setting to emtpy
		if ($modules == '[]')
		{
			$modules = '';
		}

		// load member preferences
		$preferences = Plugins\Members\Dashboard\Models\Preference::oneByUser(User::get('id'));

		// update the user preferences
		$preferences->set('uidNumber', User::get('id'));
		$preferences->set('preferences', $modules);
		$preferences->set('modified', Date::toSql());

		// attempt to save
		if (!$preferences->save())
		{
			App::abort(500, 'Unable to save the users modules.');
		}

		// build return
		echo json_encode(array(
			'saved'   => true,
			'modules' => json_decode($modules)
		));
		exit();
	}

	/**
	 * Returns the name of the plugin if it has an admin interface
	 *
	 * @return  string
	 */
	public function onCanManage()
	{
		return $this->_name;
	}

	/**
	 * Event call for managing this plugin's content
	 *
	 * @param   string  $option      Component name
	 * @param   string  $controller  Cotnroller to use
	 * @param   string  $action      Action to perform
	 * @return  string
	 */
	public function onManage($option, $controller='plugins', $action='default')
	{
		$this->option     = $option;
		$this->controller = $controller;
		$this->action     = $action;
		$this->database   = App::get('db');

		// include dasboard models
		include_once __DIR__ . DS . 'models' . DS . 'preference.php';

		// add assets
		Html::behavior('modal');
		$this->css();
		$this->css('dashboard.admin');
		$this->js('dashboard.admin');
		$this->js('gridster', 'system');
		$this->js('resizeEnd.min', 'system');

		// build method name and call action
		$methodName = 'Manage' . ucfirst(strtolower($action)) . 'Action';
		return $this->$methodName();
	}

	/**
	 * Display Main Dashboard Manage
	 *
	 * @return  string
	 */
	public function manageDefaultAction()
	{
		// create view object
		$view = $this->view('default', 'manage');

		// var to hold modules
		$view->modules = array();

		// load dashboard modules
		$dashboardModules = $this->_loadModules($this->params->get('position', 'memberDashboard'));

		// get default prefs
		$preferences = $this->params->get('defaults', array());
		if (is_string($preferences))
		{
			$preferences = json_decode($preferences);
		}

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

		$view->admin = App::isAdmin();

		// return rendered view
		return $view->loadTemplate();
	}

	/**
	 * Save Module Defaults
	 *
	 * @return  void
	 */
	public function manageSaveAction()
	{
		// get request vars
		$modules = Request::getString('modules', '');

		// save an empty set as an empty string
		$modules = ($modules == '[]') ? '' : $modules;

		// set our new defaults
		$this->params->set('defaults', $modules);

		// save
		$query = "UPDATE `#__extensions` SET params=" . $this->database->quote($this->params->toString()) . " WHERE `folder`='members' AND `element`='dashboard'";
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
	 * @return  string
	 */
	public function manageAddAction()
	{
		// create view object
		$view = $this->view('default', 'add');

		// load dashboard modules
		$view->modules = $this->_loadModules($this->params->get('position', 'memberDashboard'));

		// get default prefs
		$preferences = $this->params->get('defaults', '[]');
		$preferences = json_decode($preferences);

		// get list of default modules
		$view->mymodules = array_map(function($mod)
		{
			return $mod->module;
		}, $preferences);

		// return rendered view
		return $view->loadTemplate();
	}

	/**
	 * Return Rendered Module
	 *
	 * @return  void
	 */
	public function manageModuleAction()
	{
		// get module id
		$moduleId = Request::getInt('moduleid', 0);

		// get list of modules
		$modulesList = $this->_loadModules($this->params->get('position', 'memberDashboard'));

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
		$view = $this->view('module', 'display');

		// get application location
		$view->admin  = App::isAdmin();
		$view->module = $module;

		// return content
		echo json_encode(array('html' => $view->loadTemplate()));
		exit();
	}

	/**
	 * Display Push Module View
	 *
	 * @return  string
	 */
	public function managePushAction()
	{
		// create view object
		$view = $this->view('push', 'manage');

		// get list of modules
		$view->modules = $this->_loadModules($this->params->get('position', 'memberDashboard'));

		// return rendered view
		return $view->loadTemplate();
	}

	/**
	 * Push modules to users
	 * 
	 * @return  void
	 */
	public function manageDoPushAction()
	{
		// get request vars
		$module   = Request::getInt('module', null);
		$column   = Request::getInt('column', 1);
		$position = Request::getCmd('position', 'first');
		$width    = Request::getInt('width', 1);
		$height   = Request::getInt('height', 2);

		// make sure we have a module
		if ($module == 0 || $module == null)
		{
			App::abort(406, 'You must provide a module.');
		}

		// load all member preferences
		//$this->database->setQuery("SELECT * from `#__xprofiles_dashboard_preferences`");
		//$memberPreferences = $this->database->loadObjectList();

		$memberPreferences = Plugins\Members\Dashboard\Models\Preference::all()
			->ordered()
			->rows();

		// loop through each member preference and attempt to push module
		foreach ($memberPreferences as $memberPreference)
		{
			// load their member preferences
			$params = array();
			if ((string)$memberPreference->get('preferences') !== '')
			{
				$params = json_decode($memberPreference->get('preferences'));
			}

			// get a list of installed modules
			$modules = array_map(function($param)
			{
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

			$memberPreference->set('preferences', $params);
			$memberPreference->save();

			// update user params
			/*$sql = "UPDATE `#__xprofiles_dashboard_preferences` SET `preferences`=" . $this->database->quote($params) . " WHERE `uidNumber`=" . $memberPreference->uidNumber;
			$this->database->setQuery($sql);
			$this->database->query();*/
		}

		// return message
		echo json_encode(array('module_pushed' => true));
		exit();
	}

	/**
	 * Load Modules for position
	 * 
	 * @param   string  $position  Position to look for
	 * @return  array   Array of modules
	 */
	private function _loadModules($position = '')
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

	/**
	 * Load Member Preferences
	 *
	 * Will load defaults if not intentionally set to empty & no preferences we found
	 * 
	 * @param   integer  $uidNumber  Profile ID number
	 * @return  array    Array of preferences
	 */
	private function _loadPreferences($uidNumber = null)
	{
		// use logged in user
		if ($uidNumber == null)
		{
			$uidNumber = User::get('id');
		}

		// load member preferences
		$model = Plugins\Members\Dashboard\Models\Preference::oneByUser($uidNumber);
		$preferences = $model->get('preferences');

		// no user preferences, use default
		if ($preferences === NULL)
		{
			// get defaults & check if string
			$preferences = $this->params->get('defaults', '[]');
			if (is_string($preferences))
			{
				$preferences = json_decode($preferences);
			}
		}
		// handle user setting dashboard to empty
		else if ($preferences === '')
		{
			$preferences = array();
		}
		// use users settings
		else
		{
			$preferences = json_decode($preferences);
		}

		return $preferences;
	}
}
