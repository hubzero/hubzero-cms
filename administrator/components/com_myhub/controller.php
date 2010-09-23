<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

class MyhubController extends Hubzero_Controller
{
	public function execute()
	{
		// Get the component parameters
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		$this->num_default = 6;
		
		$this->_task = strtolower(JRequest::getVar('task', '', 'default'));
		
		switch ($this->_task) 
		{
			case 'select':    $this->select();    break;
			case 'push':      $this->push();      break;
			
			case 'refresh':   $this->refresh();   break;
			case 'rebuild':   $this->rebuild();   break;
			case 'save':      $this->save();      break;
			case 'addmodule': $this->addmodule(); break;
			case 'customize': $this->manage();    break;
			
			default: $this->manage(); break;
		}
	}

	//----------------------------------------------------------
	//  Views
	//----------------------------------------------------------

	protected function select()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'select') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Include a needed file
		include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php' );
		$jmodule = new JTableModule( $this->database );
		
		$position = ($this->config->get('position')) ? $this->config->get('position') : 'myhub';
		
		// Select all available modules
		$this->database->setQuery( "SELECT m.id, m.title FROM ".$jmodule->getTableName()." AS m WHERE m.position='$position'" );
		$view->modules = $this->database->loadObjectList();
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
	
	//-----------
	
	protected function push() 
	{
		// Incoming
		$module   = JRequest::getInt( 'module', 0 );
		$column   = JRequest::getInt( 'column', 1 );
		$position = JRequest::getVar( 'position', 'first' );
		
		// Ensure we have a module
		if (!$module) {
			echo "<script type=\"text/javascript\"> alert('".JText::_('Error: no module selected')."'); window.history.go(-1); </script>\n";
			return;
		}
		
		// Get all entries that do NOT have the selected module
		$mp = new MyhubPrefs( $this->database );
		$rows = $mp->getPrefs( $module );
		
		// Did we get any results?
		if ($rows) {
			// Loop through the results
			foreach ($rows as $row) 
			{
				// Break the prefs into their columns
				$bits = explode(';',$row->prefs);
				
				// Determine the position and column the module needs to be added to
				if ($position == 'first') {
					$bits[$column] = $module.','.$bits[$column];
				} else {
					$bits[$column] .= ','.$module;
				}
				$prefs = implode(';',$bits);
				
				// Save the updated prefs
				$myhub = new MyhubPrefs( $this->database );
				$myhub->uid = $row->uid;
				$myhub->prefs = $prefs;
				if (!$myhub->check()) {
					$this->setError( $myhub->getError() );
				}
				if (!$myhub->store()) {
					$this->setError( $myhub->getError() );
				}
			}
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}
	
	//-----------

	// The main "My HUB" page
	protected function manage() 
	{
		// Add the CSS
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet($this->_option);
		
		// Add the Javascript if in "personalize" mode
	    if ($this->_task == 'customize') {
			$document =& JFactory::getDocument();
			$document->addScript('/components'.DS.$this->_option.DS.'xsortables.js');
			$document->addScript('/administrator/components'.DS.$this->_option.DS.'myhub.js');
		}

		// Select user's list of modules from database
		$myhub = new MyhubPrefs( $this->database );
		$myhub->load( $this->juser->get('id') );
		
		// Create a default set of preferences
		$myhub->uid = $this->juser->get('id');
		$myhub->prefs = $this->getDefaultModules();
	
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

		$jconfig =& JFactory::getConfig();

		// The number of columns
		$cols = 3;

		// Build HTML
		// Instantiate a new view
		$view = new JView( array('name'=>'manage') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->juser = $this->juser;
		
		$view->cols = $cols;
		$view->mymods = $mymods;
		$view->allmods = $allmods;
		$view->usermods = $usermods;
		if ($this->_task == 'customize') {
			$view->availmods = $this->getUnusedModules($allmods);
		}
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
	
	//----------------------------------------------------------
	// AJAX calls
	//----------------------------------------------------------
	
	// Rebuild the "available modules" list
	protected function rebuild()
	{
		//$id  = $this->save(1);
		$id = JRequest::getVar( 'ids', '' );
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

		$modules = $this->getUnusedModules($allmods);
		
		$view = new JView( array('name'=>'manage', 'layout'=>'list') );
		$view->modules = $modules;
		$view->display();
	}

	//-----------
	// Save preferences (i.e., the list of modules 
	// to be displayed and their locations)
	protected function save()
	{
		// Incoming
		$ids = JRequest::getVar( 'serials', '' );
	
		$component =& JComponentHelper::getComponent( $this->_option );
		if (!trim($component->params)) {
			$component->params = 'defaults='.$ids;
		} else {
			$added = false;
			$p = array();
			$params = explode("\n",$component->params);
			foreach ($params as $param) 
			{
				$bits = explode('=', $param);
				if (trim($bits[0]) == 'defaults') {
					$p[] = 'defaults='.$ids;
					$added = true;
				} else {
					$p[] = $param;
				}
			}
			if (!$added) {
				$p[] = 'defaults='.$ids;
			}
			$component->params = implode("\n",$p);
		}
		
		$this->database->setQuery( "UPDATE #__components SET params='".$component->params."' WHERE `parent`=0 AND `option`='".$this->_option."'");
		if ($this->database->query()) {
			$this->setError( $this->database->getErrorMsg() );
		}
	
		$this->_redirect = 'index.php?option='.$this->_option;
	}
	
	//-----------
	
	protected function cancel() 
	{
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------

	protected function getmodule( $extras=false, $act='' ) 
	{
		// Incoming
		$mid = JRequest::getInt( 'id', 0 );
		$uid = JRequest::getInt( 'uid', 0 );
		
		// Make sure we have a module ID to load
		if (!$mid) {
			echo JText::_('ERROR_NO_MOD_ID');
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
		
		//$module->user = 0;

		// Is it a custom module (i.e., HTML)?
		$view = new JView( array('name'=>'manage', 'layout'=>'container') );
		$view->option = $this->_option;
		$view->module = $module;
		$view->params = $params;
		$view->rendered = $rendered;
		$view->extras = $extras;
		$view->act = $act;
		$view->container = false;
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

	public function outputModules($mods, $uid, $act='') 
	{
		$database =& JFactory::getDBO();
		
		// Get the modules
		$modules = array();
		for ($i=0, $n=count( $mods ); $i < $n; $i++) 
		{
			$myparams = new MyhubParams( $database );
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

				$view = new JView( array('name'=>'manage', 'layout'=>'container') );
				$view->option = 'com_myhub';
				$view->module = $module;
				$view->params = $params;
				$view->rendered = $rendered;
				$view->extras = true;
				$view->act = $act;
				$view->container = true;
				$html .= $view->loadTemplate();
			}
		}
		
		return $html;
	}

	//----------------------------------------------------------
	// Fetchers
	//----------------------------------------------------------

	private function getUnusedModules($mods)
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

	private function getDefaultModules()
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
