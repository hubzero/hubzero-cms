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
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

class MyhubController extends Hubzero_Controller
{
	public function execute()
	{
		$this->num_default = 6;
		
		$this->_task = JRequest::getVar( 'task', '' );
		
		if ($this->juser->get('guest')) {
			$this->_task = '';
		} else {
			$this->_task = ($this->_task) ? $this->_task : 'view';
		}
		
		switch ($this->_task) 
		{
			case 'refresh':    $this->refresh();    break;
			case 'rebuild':    $this->rebuild();    break;
			case 'restore':    $this->restore();    break;
			case 'save':       $this->save();       break;
			case 'saveparams': $this->saveparams(); break;
			case 'addmodule':  $this->addmodule();  break;
			case 'view':       $this->view();       break;
	
			default: $this->restricted(); break;
		}
	}

	//-----------

	protected function _buildPathway($question=null) 
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
		if ($this->_act && $this->_act == 'customize') {
			$pathway->addItem(
				JText::_('COM_MYHUB_PERSONALIZE'),
				'index.php?option='.$this->_option.'&act=customize'
			);
		}
	}
	
	//-----------
	
	protected function _buildTitle($question=null) 
	{
		$juser =& JFactory::getUser();
		$jconfig =& JFactory::getConfig();
		
		$this->_title = JText::sprintf(strtoupper($this->_option).'_TITLE',$jconfig->getValue('config.sitename'));
		if (!$juser->get('guest')) {
			$this->_title .= ': '.$juser->get('name');
		}
		if ($this->_act && $this->_act == 'customize') {
			$this->_title .= ': '.JText::_('COM_MYHUB_PERSONALIZE');
		}

		$document =& JFactory::getDocument();
		$document->setTitle( $this->_title );
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------
	
	// Display a login form
	protected function restricted()
	{
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Instantiate a view
		$view = new JView( array('name'=>'login') );
		$view->title = JText::_(strtoupper($this->_option)).': '.JText::_('COM_MYHUB_LOGIN');
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------
	// Restore the "My HUB" page to default settings
	protected function restore()
	{
		// Load the user's info

		// Ensure we found a user
		if (!$this->juser->get('guest')) {
			// Instantiate object, assign default preferences, and save
			$myhub = new MyhubPrefs( $this->database );
			$myhub->load( $this->juser->get('id') );
			$myhub->prefs = $this->_getDefaultModules();
			if (!$myhub->check()) {
				$this->setError( $myhub->getError() );
			}
			if (!$myhub->store()) {
				$this->setError( $myhub->getError() );
			}
		}
		
		$this->view();
	}

	//-----------
	// The main "My HUB" page
	protected function view()
	{
		// Add the CSS
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet($this->_option);
		
		// Add the Javascript if in "personalize" mode
		$this->_act = JRequest::getVar( 'act', '' );
	    if ($this->_act == 'customize') {
			$document =& JFactory::getDocument();
			$document->addScript('components'.DS.$this->_option.DS.'xsortables.js');
			$document->addScript('components'.DS.$this->_option.DS.'myhub.js');
		}
		
		// Set the title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Make sure we actually loaded someone
		if ($this->juser->get('guest')) {
			JError::raiseError( 404, JText::_('COM_MY_HUB_ERROR_NO_USER') );
			return;
		}
		
		// Select user's list of modules from database
		$myhub = new MyhubPrefs( $this->database );
		$myhub->load( $this->juser->get('id') );
		
		// No preferences found
		if (trim($myhub->prefs) == '') {
			// Create a default set of preferences
			$myhub->uid = $this->juser->get('id');
			$myhub->prefs = $this->_getDefaultModules();
			if ($this->_act == 'customize' && $this->config->get('allow_customization') != 1) {
				if (!$myhub->check()) {
					$this->setError( $myhub->getError() );
				}
				if (!$myhub->create()) {
					$this->setError( $myhub->getError() );
				}
			}
		}
	
		// Splits string into columns
		$mymods = split(';',$myhub->prefs);
		// Save array of columns for later work
		$usermods = $mymods;
	
		// Splits each column into modules, listed by the order they should appear
		for ( $i = 0; $i < count($mymods); $i++ ) 
		{
			$mymods[$i] = split(',',$mymods[$i]);
		}

		// Build a list of all modules being used by this user 
		// so we know what to exclude from the list of modules they can still add
		$allmods = array();
		for ( $i = 0; $i < count($mymods); $i++ ) 
		{
			$allmods = array_merge($allmods, $mymods[$i]);
		}

		// The number of columns
		$cols = 3;

		// Instantiate a view
		$view = new JView( array('name'=>'view') );
		$view->option = $this->_option;
		$view->title = $this->_title;
		$view->act = $this->_act;
		$view->config = $this->config;
		$view->usermods = $usermods;
		$view->juser = $this->juser;
		
		if ($this->_act == 'customize' && $this->config->get('allow_customization') != 1) {
			$view->availmods = $this->_getUnusedModules($allmods);
		} else {
			$view->availmods = null;
		}
		
		$view->columns = array();
		for ($c = 0; $c < $cols; $c++) 
		{
			$view->columns[$c] = $this->output_modules($mymods[$c], $this->juser->get('id'), $this->_act);
		}
		
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// AJAX calls
	//----------------------------------------------------------
	
	// Rebuild the "available modules" list
	protected function rebuild()
	{
		$id  = $this->save(1);
		
		$ids = split(';',$id);
		for ( $i = 0; $i < count($ids); $i++ ) 
		{
			$ids[$i] = split(',',$ids[$i]);
		}
		
		$allmods = array();
		for ( $i = 0; $i < count($ids); $i++ ) 
		{
			$allmods = array_merge($allmods, $ids[$i]);
		}

		// Instantiate a view
		$view = new JView( array('name'=>'view','layout'=>'modulelist') );
		$view->modules = $this->_getUnusedModules($allmods);
		$view->display();
	}

	//-----------
	// Save preferences (i.e., the list of modules 
	// to be displayed and their locations)
	protected function save( $rtrn=0 )
	{
		// Incoming
		$uid = JRequest::getInt( 'uid', 0 );
		$ids = JRequest::getVar( 'ids', '' );
	
		// Ensure we have a user ID ($uid)
		if (!$uid) {
			if ($rtrn) {
				return $ids;
			}
		}
		
		// Instantiate object, bind data, and save
		$myhub = new MyhubPrefs( $this->database );
		$myhub->load( $uid );
		$myhub->prefs = $ids;
		$myhub->modified = date( "Y-m-d H:i:s" );
		if (!$myhub->check()) {
			$this->setError( $myhub->getError() );
		}
		if (!$myhub->store()) {
			$this->setError( $myhub->getError() );
		}
		if ($rtrn) {
			return $ids;
		}
	}

	//-----------
	// Save the parameters for a module
	protected function saveparams()
	{
		// Incoming
		$uid = JRequest::getInt( 'uid', 0 );
		$mid = JRequest::getVar( 'id', '' );
		$update = JRequest::getInt( 'update', 0 );
		$params = JRequest::getVar( 'params', array() );
		
		// Process parameters
		$newparams = array();
		foreach ($params as $aKey => $aValue)
		{
			$newparams[] = $aKey.'='.$aValue;
		}

		// Instantiate object, bind data, and save
		$myparams = new MyhubParams( $this->database );
		$myparams->loadParams( $uid, $mid );
		if (!$myparams->params) {
			$myparams->uid = $uid;
			$myparams->mid = $mid;
			$new = true;
		} else {
			$new = false;
		}
		$myparams->params = implode($newparams,"\n");
		if (!$myparams->check()) {
			$this->setError( $myparams->getError() );
		}
		if (!$myparams->storeParams($new)) {
			$this->setError( $myparams->getError() );
		}
		
		if ($update) {
			$this->getmodule();
		}
	}
	
	//-----------

	protected function getmodule( $extras=false, $act='' ) 
	{
		// Instantiate a view
		$view = new JView( array('name'=>'view','layout'=>'modulecontainer') );
		
		// Incoming
		$mid = JRequest::getInt( 'id', 0 );
		$uid = JRequest::getInt( 'uid', 0 );
		
		// Make sure we have a module ID to load
		if (!$mid) {
			$view->setError( JText::_('COM_MYHUB_ERROR_NO_MOD_ID') );
			$view->display();
			return;
		}

		// Get the module from the database
		$myparams = new MyhubParams( $this->database );
		$module = $myparams->loadModule( $uid, $mid );

		// If the user has special prefs, load them.
		// Otherwise, load default prefs
		if ($module->myparams != '') {
			$params =& new JParameter( $module->myparams );
		} else {
			$params =& new JParameter( $module->params );
		}
		
		if ($params) {
			$rendered = false; //$this->render( $params, $mainframe->getPath( 'mod0_xml', $module->module ), 'module' );
		}
		$module->params = $module->myparams;

		// Output the module
		$view->module = $module;
		$view->params = $params;
		$view->container = false;
		$view->extras = $extras;
		$view->database = $this->database;
		$view->option = $this->_option;
		$view->act = $act;
		$view->config = $this->config;
		$view->rendered = $rendered;
		$view->display();
	}
	
	//-----------
	// Reload the contents of a module
	protected function refresh()
	{
		$this->getmodule( false, '' );
	}
	
	//-----------
	// Builds the HTML for a module
	// NOTE: this is different from the method above in that
	// it also builds the title, parameters form, and container
	// for the module
	protected function addmodule()
	{
		$this->getmodule( true, 'customize' );
	}

	//----------------------------------------------------------
	// outputs a group of modules, each contained in a list item
	//----------------------------------------------------------

	protected function output_modules($mods, $uid, $act='') 
	{
		// Get the modules
		$modules = array();
		for ($i=0, $n=count( $mods ); $i < $n; $i++) 
		{
			$myparams = new MyhubParams( $this->database );
			$modules[] = $myparams->loadModule( $uid, $mods[$i] );
		}

		$html = '';

		// Loop through the modules and output
		foreach ($modules as $module) 
		{
			if (isset($module->published)) {
				if ($module->published != 1) {
					continue;
				}
				
				$rendered = false;
				// if the user has special prefs, load them. Otherwise, load default prefs
				if ($module->myparams != '') {
					$params =& new JParameter( $module->myparams );
					$module->params .= $module->myparams;
				} else {
					$params =& new JParameter( $module->params );
				}

				if ($params) {
					$rendered = false; //$this->render( $params, $mainframe->getPath( 'mod0_xml', $module->module ), 'module' );
				}

				// Instantiate a view
				$view = new JView( array('name'=>'view','layout'=>'modulecontainer') );
				$view->module = $module;
				$view->params = $params;
				$view->container = true;
				$view->extras = true;
				$view->database = $this->database;
				$view->option = $this->_option;
				$view->act = $act;
				$view->config = $this->config;
				$view->rendered = $rendered;
				$html .= $view->loadTemplate();
			}
		}
		
		return $html;
	}

	//----------------------------------------------------------
	// Fetchers
	//----------------------------------------------------------

	private function _getUnusedModules($mods)
	{
		include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php' );
		$jmodule = new JTableModule( $this->database );
	
		$position = ($this->config->get('position')) ? $this->config->get('position') : 'myhub';
	
		$querym = '';
		$query = "SELECT id, title, module"
				. " FROM ".$jmodule->getTableName()." AS m"
				. " WHERE m.position='".$position."' AND m.published='1' AND m.client_id='0' AND (";
		for ($i=0, $n=count( $mods ); $i < $n; $i++) 
		{
			$querym .= " id!='".$mods[$i]."' AND";
		}
		$querym = substr($querym, 0, strlen($querym)-4);
		$query .= $querym;
		$query .= ") ORDER BY ordering";

		$this->database->setQuery( $query );
		$modules = $this->database->loadObjectList();
	
		return $modules;
	}

	//-----------

	private function _getDefaultModules()
	{
		$string = '';
		
		if ($this->config->get('defaults')) {
			$string = $this->config->get('defaults');
		} else {
			$position = ($this->config->get('position')) ? $this->config->get('position') : 'myhub';
			
			include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php' );
			$jmodule = new JTableModule( $this->database );
			
			$query = "SELECT m.id 
						FROM ".$jmodule->getTableName()." AS m 
						WHERE m.position='".$position."' AND m.published='1' AND m.client_id='0' 
						ORDER BY m.ordering LIMIT ".$this->num_default;
			$this->database->setQuery( $query );
			$modules = $this->database->loadObjectList();

			if ($modules) {
				$i = 0;
				foreach ($modules as $module)
				{
					$i++;
					$string .= $module->id;
					if ($i == 2) {
						$i = 0;
						$string .= ';';
					} else {
						$string .= ',';
					}
				}
				$string = substr($string, 0, strlen($string)-1);
			}
		}
		
		return $string;
	}
}

