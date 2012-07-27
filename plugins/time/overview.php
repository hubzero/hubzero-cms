<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Plugin');

/**
 * Overview plugin for time component
 */
class plgTimeOverview extends Hubzero_Plugin
{

	/**
	 * @param  unknown &$subject Parameter description (if any) ...
	 * @param  unknown $config Parameter description (if any) ...
	 * @return void
	 */
	public function plgTimeOverview(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'time', 'overview' );
		$this->_params = new JParameter( $this->_plugin->params );
		$this->loadLanguage();
	}

	/**
	 * Return array of areas related to this plugin (name, title, etc...)
	 * 
	 * @return array Return
	 */
	public function &onTimeAreas()
	{
		$area = array(
			'name'   => 'overview',
			'title'  => JText::_('PLG_TIME_OVERVIEW'),
			'return' => 'html'
		);

		return $area;
	}

	/**
	 * @param    string $action - plugin action to take (default 'view')
	 * @param    string $option - component option
	 * @param    string $active - active tab
	 * @return   array Return   - $arr with HTML of current active plugin
	 */
	public function onTime($action='', $option, $active='')
	{
		// Get this area details
		$this_area = $this->onTimeAreas();

		// Check if the active tab is the current one, otherwise return
		if ($this_area['name'] != $active)
		{
			return;
		}

		$return = 'html';

		// The output array we're returning
		$arr = array(
			'html'=>''
		);

		// Set some values for use later
		$this->_option =  $option;
		$this->action  =  $action;
		$this->db      =  JFactory::getDBO();
		$this->juser   =& JFactory::getUser();

		// Include needed DB class(es)
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'hubs.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'records.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'tasks.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'helpers'.DS.'charts.php');

		// Add some styles to the view
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('time','overview');

		// Get the Joomla document and add google JS API
		$doc =& JFactory::getDocument();
		$doc->addScript('https://www.google.com/jsapi');

		// Generate script to draw chart and push to the page
		$column  = ChartsHTML::drawColumn();
		$pieHubs = ChartsHTML::drawPieHubs();
		$pieUser = ChartsHTML::drawPieUser($this->juser->get('id'));
		$bar     = ChartsHTML::drawBar();
		$doc->addScriptDeclaration($column);
		$doc->addScriptDeclaration($pieHubs);
		$doc->addScriptDeclaration($pieUser);
		$doc->addScriptDeclaration($bar);

		// Only perform the following if this is the active tab/plugin
		if ($return == 'html') {
			switch ($action)
			{
				// Views
				case 'view':  $arr['html'] = $this->_view();    break;
				default:      $arr['html'] = $this->_view();    break;
			}
		}

		// Return the output
		return $arr;
	}

	/**
	 * Primary/default view function
	 * 
	 * @return object Return
	 */
	private function _view()
	{
		$hubs    = new TimeHubs($this->db);
		$tasks   = new TimeTasks($this->db);
		$records = new TimeRecords($this->db);
		
		// Create a new plugin view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'time',
				'element'=>'overview',
				'name'=>'view'
			)
		);

		// Set variables for queries
		$filters['active'] = 1;

		// Get data for the view
		$view->activeHubs  = $hubs->getCount($filters);
		$view->activeTasks = $tasks->getCount($filters);
		$view->totalHours  = $records->getTotalHours();

		// Set a few things for the vew
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
		$view->option        = $this->_option;

		return $view->loadTemplate();
	}

	/**
	 * Set redirect
	 * 
	 * @return void
	 */
	public function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}
}
