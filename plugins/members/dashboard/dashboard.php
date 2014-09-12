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

jimport('joomla.plugin.plugin');
ximport('Hubzero_Plugin');

/**
 * Members Plugin class for dashboard
 */
class plgMembersDashboard extends Hubzero_Plugin
{
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

		$this->loadLanguage();
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
			include_once(JPATH_ROOT . DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'tables' . DS . 'params.php');
			include_once(JPATH_ROOT . DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'tables' . DS . 'prefs.php');

			$this->_act = JRequest::getVar('act', 'customize');

			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('members', 'dashboard');

			ximport('Hubzero_Plugin_View');
			$this->view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => 'dashboard',
					'name'    => 'display'
				)
			);
			$this->view->member = $this->member = $member;
			$this->view->act = $this->_act;
			$this->view->config = $this->params;

			$this->juser = $this->view->juser = JFactory::getUser();
			$this->database = $this->view->database = JFactory::getDBO();
			$this->option = $this->view->option = $option;

			$action = JRequest::getVar('action', '');
			switch ($action)
			{
				case 'refresh':    $this->refresh();    break;
				case 'rebuild':    $this->rebuild();    break;
				case 'restore':    $this->restore();    break;
				case 'save':       $this->save();       break;
				case 'saveparams': $this->saveparams(); break;
				case 'addmodule':  $this->addmodule();  break;

				default: $this->dashboard(); break;
			}

			if ($this->getError()) 
			{
				$this->view->setError($this->getError());
			}

			$arr['html'] = $this->view->loadTemplate();
		}

		// Build the HTML meant for the "profile" tab's metadata overview
		if ($returnmeta) 
		{
			//build the metadata
		}

		return $arr;
	}

	/**
	 * Displays a dashboard (columns of selected modules)
	 * 
	 * @return     void
	 */
	public function dashboard()
	{
		if ($this->params->get('allow_customization', 0) != 1) 
		{
			ximport('Hubzero_Document');
			if (!JPluginHelper::isEnabled('system', 'jquery'))
			{
				Hubzero_Document::addPluginScript('members', 'dashboard', 'xsortables');
			}
			Hubzero_Document::addPluginScript('members', 'dashboard');
		}

		$this->num_default = $this->params->get('defaultNumber', 6);

		$myhub = new MyhubPrefs($this->database);
		$myhub->load($this->juser->get('id'));

		// Get all modules
		$mp = new MyhubParams($this->database);
		$this->modules = $mp->loadPosition($this->member->get('uidNumber'), $this->params->get('position', 'myhub'));

		// No preferences found
		if (trim($myhub->prefs) == '') 
		{
			// Create a default set of preferences
			$myhub->uid = $this->member->get('uidNumber');
			$myhub->prefs = $this->_getDefaultModules();
			if ($this->params->get('allow_customization', 0) != 1) 
			{
				if (!$myhub->check()) 
				{
					$this->setError($myhub->getError());
				}
				if (!$myhub->create()) 
				{
					$this->setError($myhub->getError());
				}
			}
		}

		// Splits string into columns
		/*$mymods = explode(';', $myhub->prefs);
		// Save array of columns for later work
		$usermods = $mymods;

		// Splits each column into modules, listed by the order they should appear
		for ($i = 0; $i < count($mymods); $i++)
		{
			if (!trim($mymods[$i])) 
			{
				continue;
			}
			$mymods[$i] = explode(',', $mymods[$i]);
		}*/
		$mymods = $this->_processList($myhub->prefs);
		
		$usermods = array();
		foreach ($mymods as $ky => $arr)
		{
			$usermods[$ky] = (is_array($arr)) ? implode(',', $arr) : '';
		}

		// Build a list of all modules being used by this user 
		// so we know what to exclude from the list of modules they can still add
		$allmods = array();

		for ($i = 0; $i < count($mymods); $i++)
		{
			if (is_array($mymods[$i]))
			{
				$allmods = array_merge($allmods, $mymods[$i]);
			}
		}
		
		//check to see if we have any 
		$mymods = $this->_resolveDeletedModules($this->modules, $mymods);

		// The number of columns
		$cols = 3;

		// Instantiate a view
		$this->view->usermods = $usermods;

		if ($this->params->get('allow_customization', 0) != 1) 
		{
			$this->view->availmods = $this->_getUnusedModules($allmods);
		} else {
			$this->view->availmods = null;
		}

		$this->view->columns = array();
		for ($c = 0; $c < $cols; $c++)
		{
			if (!isset($mymods[$c]))
			{
				$mymods[$c] = array();
			}
			$this->view->columns[$c] = $this->output_modules($mymods[$c], $this->member->get('uidNumber'), $this->_act);
		}
	}

	/**
	 * Convert a preference string into a multi-column list of IDs
	 * 
	 * @param      string $strng PReference string
	 * @return     array
	 */
	protected function _processList($strng)
	{
		// Splits string into columns
		if (strstr($strng, ';'))
		{
			$cols = explode(';', $strng);
		}
		else 
		{
			$cols = array($strng);
		}

		// Splits each column into modules, listed by the order they should appear
		for ($i = 0; $i < count($cols); $i++)
		{
			if (!trim($cols[$i])) 
			{
				continue;
			}
			$cols[$i] = explode(',', $cols[$i]);
			$cols[$i] = array_map('intval', $cols[$i]);
		}

		return $cols;
	}

	/**
	 * Outputs a group of modules, each contained in a list item
	 * 
	 * @param      array $mods Parameter description (if any) ...
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      string $act Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	protected function output_modules($mods, $uid, $act='')
	{
		$html = '';

		if (!$this->modules || !is_array($this->modules))
		{
			return $html;
		}
		if (!is_array($mods) || count($mods) <= 0)
		{
			return $html;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Loop through the modules and output
		foreach ($mods as $mod)
		{
			if (isset($this->modules[$mod]) && isset($this->modules[$mod]->published)) 
			{
				$module = $this->modules[$mod];
				if ($module->published != 1) 
				{
					continue;
				}

				$rendered = false;
				// if the user has special prefs, load them. Otherwise, load default prefs
				if ($module->myparams != '') 
				{
					$params = new $paramsClass($module->myparams);
					$module->params .= $module->myparams;
				} 
				else 
				{
					$params = new $paramsClass($module->params);
				}

				if ($params) 
				{
					$rendered = false; //$this->render($params, $mainframe->getPath('mod0_xml', $module->module), 'module');
				}

				// Instantiate a view
				$view = new Hubzero_Plugin_View(
					array(
						'folder'  => 'members',
						'element' => 'dashboard',
						'name'    => 'module'
					)
				);
				$view->module = $module;
				$view->params = $params;
				$view->container = true;
				$view->extras = true;
				$view->database = $this->database;
				$view->option = $this->option;
				$view->act = $act;
				$view->config = $this->params;
				$view->rendered = $rendered;
				$view->juser = $this->juser;

				$app =& JFactory::getApplication();
				$view->admin = $app->isAdmin();

				$html .= $view->loadTemplate();
			}
		}

		return $html;
	}

	/**
	 * Rebuild the "available modules" list
	 * 
	 * @return     void
	 */
	protected function rebuild()
	{
		$id  = $this->save(1);

		$ids = explode(';',$id);
		if (!is_array($ids))
		{
			$ids = array(
				'',
				'',
				''
			);
		}
		for ($i = 0; $i < count($ids); $i++)
		{
			if (!trim($ids[$i])) 
			{
				$ids[$i] = array();
				continue;
			}
			$ids[$i] = explode(',', $ids[$i]);
		}

		$allmods = array();
		for ($i = 0; $i < count($ids); $i++)
		{
			$allmods = array_merge($allmods, $ids[$i]);
		}

		// Instantiate a view
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'list',
			)
		);
		$this->view->modules = $this->_getUnusedModules($allmods);
	}

	/**
	 * Save preferences (i.e., the list of modules to be displayed and their locations)
	 * 
	 * @param      integer $rtrn Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function save($rtrn=0)
	{
		$app =& JFactory::getApplication();
		if ($app->isAdmin())
		{
			return $this->saveTask($rtrn);
		}

		// Incoming
		$ids = JRequest::getVar('mids', '');

		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'data'
			)
		);

		$uid = $this->member->get('uidNumber');

		// Ensure we have a user ID ($uid)
		if (!$uid) 
		{
			if ($rtrn) 
			{
				return $ids;
			}
		}

		// Instantiate object, bind data, and save
		$myhub = new MyhubPrefs($this->database);
		$myhub->load($uid);
		$myhub->prefs = $ids;
		$myhub->modified = date("Y-m-d H:i:s");
		if (!$myhub->check()) 
		{
			$this->setError($myhub->getError());
		}
		if (!$myhub->store()) 
		{
			$this->setError($myhub->getError());
		}
		$this->view->data = $ids;

		if ($rtrn) 
		{
			return $ids;
		}
	}

	/**
	 * Save the parameters for a module
	 * 
	 * @return     void
	 */
	protected function saveparams()
	{
		// Incoming
		$uid = $this->member->get('uidNumber');
		$mid = JRequest::getVar('mid', '');
		$update = JRequest::getInt('update', 0);
		$params = JRequest::getVar('params', array());

		// Process parameters
		$newparams = array();
		foreach ($params as $aKey => $aValue)
		{
			$newparams[] = $aKey . '=' . $aValue;
		}

		// Instantiate object, bind data, and save
		$myparams = new MyhubParams($this->database);
		$myparams->loadParams($uid, $mid);
		if (!$myparams->params) 
		{
			$myparams->uid = $uid;
			$myparams->mid = $mid;
			$new = true;
		} 
		else 
		{
			$new = false;
		}
		$myparams->params = implode($newparams, "\n");
		if (!$myparams->check()) 
		{
			$this->setError($myparams->getError());
		}
		if (!$myparams->storeParams($new)) 
		{
			$this->setError($myparams->getError());
		}

		if ($update) 
		{
			$this->getmodule();
		}
		else 
		{
			exit();
		}
	}

	/**
	 * Displays a specific module
	 * 
	 * @param      boolean $extras Flag to display module params
	 * @param      string  $act    Parameter description (if any) ...
	 * @return     void
	 */
	protected function getmodule($extras=false, $act='')
	{
		// Instantiate a view
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'module'
			)
		);
		
		$app =& JFactory::getApplication();
		
		// Incoming
		$mid = JRequest::getInt('mid', 0);
		$uid = ($app->isAdmin()) ? $this->juser->get('id') : $this->member->get('uidNumber');

		// Make sure we have a module ID to load
		if (!$mid) 
		{
			$this->setError(JText::_('PLG_MEMBERS_DASHBOARD_ERROR_NO_MOD_ID'));
			return;
		}

		// Get the module from the database
		$myparams = new MyhubParams($this->database);
		$module = $myparams->loadModule($uid, $mid);

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// If the user has special prefs, load them.
		// Otherwise, load default prefs
		if ($module->myparams != '') 
		{
			$params = new $paramsClass($module->myparams);
		} 
		else 
		{
			$params = new $paramsClass($module->params);
		}

		if ($params) 
		{
			$rendered = false; //$this->render($params, $mainframe->getPath('mod0_xml', $module->module), 'module');
		}
		$module->params = $module->myparams;

		// Output the module
		$this->view->module = $module;
		$this->view->params = $params;
		$this->view->container = false;
		$this->view->extras = $extras;
		$this->view->rendered = $rendered;
		$this->view->config = $this->params;
		$this->view->act = $this->_act;
		$this->view->option = $this->option;
		$this->view->juser = $this->juser;
		$this->view->database = $this->database;
		if (!$app->isAdmin())
		{
			$this->view->member = $this->member;
		}

		$this->view->admin = $app->isAdmin();
	}

	/**
	 * Reload the contents of a module
	 * 
	 * @return     void
	 */
	protected function refresh()
	{
		$this->getmodule(false, '');
	}

	/**
	 * Builds the HTML for a module
	 * 
	 * NOTE: this is different from the method above in that
	 * it also builds the title, parameters form, and container
	 * for the module
	 * 
	 * @return     void
	 */
	protected function addmodule()
	{
		$myhub = new MyhubPrefs($this->database);
		$myhub->load($this->member->get('uidNumber'));

		$mid = JRequest::getInt('mid', 0);

		$mods = array();
		$cols = $this->_processList($myhub->prefs);
		foreach ($cols as $col)
		{
			if (!is_array($col))
			{
				continue;
			}
			foreach ($col as $arr)
			{
				$mods[] = $arr;
			}
		}

		if (in_array($mid, $mods))
		{
			// This module already exists
			$app =& JFactory::getApplication();
			if (!$app->isAdmin())
			{
				return 'ERROR';
			}
		}

		$this->getmodule(true, 'customize');
	}

	/**
	 * Build a list of unused modules
	 * 
	 * @param      array $mods Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function _getUnusedModules($mods)
	{
		// Get all modules
		if (!isset($this->modules) || !$this->modules)
		{
			$mp = new MyhubParams($this->database);
			$this->modules = $mp->loadPosition($this->juser->get('id'), $this->params->get('position', 'myhub'));
		}

		$modules = array();

		if ($this->modules)
		{
			foreach ($this->modules as $module)
			{
				if (!in_array($module->id, $mods))
				{
					$modules[] = $module;
				}
			}
		}

		return $modules;
	}

	/**
	 * Build a list of the default modules
	 * 
	 * @return     string Return description (if any) ...
	 */
	private function _getDefaultModules()
	{
		$string = '';

		if ($this->params->get('defaults')) 
		{
			$string = $this->params->get('defaults');
		} 
		else 
		{
			$position = $this->params->get('position', 'myhub');

			if ($this->modules)
			{
				$i = 0;
				$k = 0;
				$j = 0;
				$mods = array();
				$num = $this->params->get('defaultNumber', 6);
				foreach ($this->modules as $module)
				{
					$i++;
					$j++;
					if ($j <= $num)
					{
						if (!isset($mods[$k]) || !is_array($mods[$k]))
						{
							$mods[$k] = array();
						}
						$mods[$k][] = $module->id;
						if ($i == 2) 
						{
							$i = 0;
							$mods[$k] = implode(',', $mods[$k]);
							$k++;
						}
					}
				}
				$string = (!empty($mods)) ? implode(';', $mods) : $string;
			}
		}

		return $string;
	}

	/**
	 * loop through each column of modules then through each module in each column to 
	 * see if that module id exists in available modules if doesn't exist unset from user module prefs
	 * 
	 * @param      array $hub_modules  Dashboard modules list
	 * @param      array $user_modules Current user's module list
	 * @return     array
	 */
	private function _resolveDeletedModules($hub_modules, $user_modules)
	{
		// get the id's foreach module for the 'myhub/dashboard' position
		$modules = array();
		foreach ($hub_modules as $hub_module)
		{
			$modules[] = $hub_module->id;
		}

		// loop through each column of modules then through each module in each column to see if that module id exists in available modules
		// if doesn't exist unset from user module prefs
		$prefs = '';
		foreach ($user_modules as $column => $user_module)
		{
			if (is_array($user_module))
			{
				foreach ($user_module as $k => $v)
				{
					if (!in_array($v, $modules))
					{
						unset($user_modules[$column][$k]);
					}
					else
					{
						$prefs .= $v . ",";
					}
				}
			}
			$prefs .= ';';
		}

		//we need to rewrite the user myhub prefs
		$myhub = new MyhubPrefs($this->database);
		$myhub->load($this->member->get('uidNumber'));
		$myhub->prefs = rtrim(str_replace(",;", ";", $prefs), ";");
		$myhub->modified = date("Y-m-d H:i:s");
		if (!$myhub->check()) 
		{
			$this->setError($myhub->getError());
		}
		if (!$myhub->store()) 
		{
			$this->setError($myhub->getError());
		}

		return $user_modules;
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
	 * @param      string $task       Task to perform
	 * @return     string
	 */
	public function onManage($option, $controller='plugins', $task='default')
	{
		$task = ($task) ?  $task : 'default';

		ximport('Hubzero_Plugin_View');
		include_once(JPATH_ROOT . DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'tables' . DS . 'params.php');
		include_once(JPATH_ROOT . DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'tables' . DS . 'prefs.php');

		$this->option = $option;
		$this->controller = $controller;
		$this->task = $task;
		$this->database = JFactory::getDBO();
		$this->juser = JFactory::getUser();
		$this->_act = JRequest::getVar('act', 'customize');

		$method = strtolower($task) . 'Task';

		return $this->$method();
	}

	/**
	 * Displays the default module layout
	 * 
	 * @return     string
	 */
	public function defaultTask()
	{
		// Instantiate a view
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'admin',
				'layout'  => 'default'
			)
		);
		$this->view->option = $this->option;
		$this->view->controller = $this->controller;
		$this->view->task = $this->task;
		$this->view->juser = $this->juser;

		$document =& JFactory::getDocument();
		$document->addStylesheet(DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'dashboard.css');
		$document->addScript(DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'xsortables.js');
		$document->addScript(DS . 'plugins' . DS . 'members' . DS . 'dashboard' . DS . 'dashboard.admin.js');

		// Select user's list of modules from database
		$myhub = new MyhubPrefs($this->database);
		$myhub->load($this->juser->get('id'));

		// Get all modules
		$mp = new MyhubParams($this->database);
		$this->modules = $mp->loadPosition($this->juser->get('id'), $this->params->get('position', 'myhub'));

		// Create a default set of preferences
		$myhub->uid = $this->juser->get('id');
		$myhub->prefs = $this->_getDefaultModules();

		$this->num_default = $this->params->get('defaultNumber', 6);

		// Splits string into columns
		$mymods = explode(';', $myhub->prefs);
		// Save array of columns for later work
		$usermods = $mymods;

		// Splits each column into modules, listed by the order they should appear
		for ($i = 0; $i < count($mymods); $i++)
		{
			if (!trim($mymods[$i])) {
				continue;
			}
			$mymods[$i] = explode(',', $mymods[$i]);
		}

		// Build a list of all modules being used by this user 
		// so we know what to exclude from the list of modules they can still add
		$allmods = array();
		
		for ($i = 0; $i < count($mymods); $i++)
		{
			if (is_array($mymods[$i]))
			{
				$allmods = array_merge($allmods, $mymods[$i]);
			}
		}

		// The number of columns
		$cols = 3;

		// Instantiate a view
		$this->view->usermods = $usermods;

		if ($this->params->get('allow_customization', 0) != 1) 
		{
			$this->view->availmods = $this->_getUnusedModules($allmods);
		} else {
			$this->view->availmods = null;
		}

		$this->view->columns = array();
		for ($c = 0; $c < $cols; $c++)
		{
			$this->view->columns[$c] = $this->output_modules($mymods[$c], $this->juser->get('id'), 'customize');
		}

		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		return $this->view->loadTemplate();
	}

	/**
	 * Select a module to push to all users
	 * 
	 * @return     string
	 */
	public function selectTask()
	{
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => 'dashboard',
				'name'    => 'admin',
				'layout'  => 'select'
			)
		);
		$this->view->option = $this->option;
		$this->view->controller = $this->controller;
		$this->view->task = $this->task;
		$this->view->juser = $this->juser;

		// Include a needed file
		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'module.php');
		$jmodule = new JTableModule($this->database);

		$position = $this->params->get('position', 'myhub');

		// Select all available modules
		$this->database->setQuery("SELECT m.id, m.title FROM " . $jmodule->getTableName() . " AS m WHERE m.position='$position'");
		$this->view->modules = $this->database->loadObjectList();

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		return $this->view->loadTemplate();
	}

	/**
	 * Push a module to all users
	 * 
	 * @return     string
	 */
	public function pushTask() 
	{
		// Incoming
		$module   = JRequest::getInt('module', 0);
		$column   = JRequest::getInt('column', 1);
		$position = JRequest::getVar('position', 'first');

		// Ensure we have a module
		if (!$module) 
		{
			echo "<script type=\"text/javascript\"> alert('".JText::_('Error: no module selected')."'); window.history.go(-1); </script>\n";
			return;
		}

		// Get all entries that do NOT have the selected module
		$mp = new MyhubPrefs($this->database);
		$rows = $mp->getPrefs($module);
		
		// Did we get any results?
		if ($rows) 
		{
			// Loop through the results
			foreach ($rows as $row) 
			{
				// Break the prefs into their columns
				$bits = explode(';', $row->prefs);

				// Determine the position and column the module needs to be added to
				if ($position == 'first') 
				{
					$bits[$column] = $module . ',' . $bits[$column];
				} 
				else 
				{
					$bits[$column] .= ',' . $module;
				}
				$prefs = implode(';', $bits);

				// Save the updated prefs
				$myhub = new MyhubPrefs($this->database);
				$myhub->uid   = $row->uid;
				$myhub->prefs = $prefs;
				if (!$myhub->check()) 
				{
					$this->setError($myhub->getError());
				}
				if (!$myhub->store()) 
				{
					$this->setError($myhub->getError());
				}
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=com_members&controller=plugins' . $this->_controller . '&task=manage&plugin=dashboard',
			JText::_('Module successfully pushed')
		);
		return;
	}

	/**
	 * Redirect
	 *
	 * @return	void
	 */
	public function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}

	/**
	 * Save preferences (i.e., the list of modules 
	 * to be displayed and their locations)
	 * 
	 * @return     string
	 */
	public function saveTask($rtrn=0)
	{
		// Incoming
		$ids = JRequest::getVar('mids', '');

		$this->params->set('defaults', $ids);
		
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$query = "UPDATE #__extensions SET params='" . $this->params->toString() . "' WHERE `folder`='members' AND `element`='dashboard'";
		}
		else 
		{
			$query = "UPDATE #__plugins SET params='" . $this->params->toString() . "' WHERE `folder`='members' AND `element`='dashboard'";
		}
		$this->database->setQuery($query);
		if ($this->database->query()) 
		{
			$this->setError($this->database->getErrorMsg());
		}

		if ($rtrn) 
		{
			return $ids;
		}
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		// Redirect
		$this->setRedirect(
			'index.php?option=com_members&controller=plugins' . $this->_controller . '&task=manage&plugin=dashboard'
		);
		return;
	}

	/**
	 * Rebuild the "available modules" list
	 * 
	 * @return     void
	 */
	protected function rebuildTask()
	{
		$this->rebuild();
		return $this->view->loadTemplate();
	}

	/**
	 * Builds the HTML for a module
	 * 
	 * NOTE: this is different from the method above in that
	 * it also builds the title, parameters form, and container
	 * for the module
	 * 
	 * @return     void
	 */
	protected function addmoduleTask()
	{
		ximport('Hubzero_User_Profile');
		$this->member = Hubzero_User_Profile::getInstance(JFactory::getUser()->get('id'));
		if ($this->addmodule() == 'ERROR')
		{
			return 'ERROR';
		}
		return $this->view->loadTemplate();
	}
}
