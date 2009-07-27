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

//----------------------------------------------------------

class MyhubController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;

	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
		$this->_option = 'com_'.$this->_name;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	
	//-----------
	
	private function getTask() 
	{
		$task = strtolower(JRequest::getVar('task', '', 'default'));
		$this->_task = $task;
		return $task;
	}
	
	public function execute()
	{
		// Get the component parameters
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		$this->num_default = 6;
		
		switch ($this->getTask()) 
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

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message );
		}
	}

	//----------------------------------------------------------
	//  Views
	//----------------------------------------------------------

	protected function select()
	{
		$database =& JFactory::getDBO();
		
		include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php' );
		$jmodule = new JTableModule( $database );
		
		$position = ($this->config->get('position')) ? $this->config->get('position') : 'myhub';
		
		// Select all available modules
		$query = "SELECT m.id, m.title 
					FROM ".$jmodule->getTableName()." AS m 
					WHERE m.position='$position'";
					
		$database->setQuery( $query );
		$modules = $database->loadObjectList();
		
		// Output HTML
		MyhubHtml::select( $this->_option, $modules );
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
			echo MyhubHtml::alert('Error: no module selected');
			return;
		}
		
		$database =& JFactory::getDBO();
		
		// Get all entries that do NOT have the selected module
		$mp = new MyhubPrefs( $database );
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
				$myhub = new MyhubPrefs( $database );
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
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
		
		// Add the Javascript if in "personalize" mode
	    if ($this->_task == 'customize') {
			$document =& JFactory::getDocument();
			$document->addScript('/components'.DS.$this->_option.DS.'xsortables.js');
			$document->addScript('/administrator/components'.DS.$this->_option.DS.'admin.myhub.js');
		}
		
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Select user's list of modules from database
		$myhub = new MyhubPrefs( $database );
		$myhub->load( $juser->get('id') );
		
		// Create a default set of preferences
		$myhub->uid = $juser->get('id');
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
		//$html  = MyhubHtml::writeOptions( $this->_option, $this->_task );
		$html  = '<div class="main section">'.n;
		if ($this->_task != 'customize') {
			$html .= '<form action="index.php?option='.$this->_option.'" method="post" name="adminForm" id="cpnlc">'.n;
			$html .= t.'<input type="hidden" name="task" value="" />'.n;
			$html .= '</form>'.n;
		}
		$html .= '<table id="droppables">'.n;
		$html .= t.'<tbody>'.n;
		$html .= t.t.'<tr>'.n;
		
		// Initialize customization abilities
		if ($this->_task == 'customize') {
			$availmods = $this->getUnusedModules($allmods);
		
			// Get the control panel
			$html .= t.t.t.'<td id="modules-dock" style="vertical-align: top;">'.n;
			$html .= MyhubHtml::controlpanel($this->_option, $availmods, $usermods, $juser->get('id'));
			$html .= t.t.t.'</td>'.n;
		}
		// Loop through each column and output modules assigned to each one
		for ( $c = 0; $c < $cols; $c++ ) 
		{
			$html .= t.t.t.'<td class="sortable" id="sortcol_'.$c.'">'.n;
			$html .= $this->output_modules($mymods[$c], $juser->get('id'), $this->_task);
			$html .= t.t.t.'</td>'.n;
		}
		$html .= t.t.'</tr>'.n;
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;
		$html .= '<input type="hidden" name="uid" id="uid" value="'.$juser->get('id').'" />'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		echo $html;
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
		
		echo MyhubHtml::moduleList( $modules );
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
		
		$database =& JFactory::getDBO();
		$database->setQuery( "UPDATE #__components SET params='".$component->params."' WHERE `parent`=0 AND `option`='".$this->_option."'");
		if ($database->query()) {
			$this->setError( $database->getErrorMsg() );
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
		$database =& JFactory::getDBO();
		
		// Incoming
		$mid = JRequest::getInt( 'id', 0 );
		$uid = JRequest::getInt( 'uid', 0 );
		
		// Make sure we have a module ID to load
		if (!$mid) {
			echo MyhubHtml::error( JText::_('ERROR_NO_MOD_ID') );
			return;
		}
		
		// Get the module from the database
		$myparams = new MyhubParams( $database );
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
		echo MyhubHtml::moduleContainer( $module, $params, $rendered, false, $extras, $database, $this->_option, $act );
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

				$html .= MyhubHtml::moduleContainer( $module, $params, $rendered, true, true, $database, $this->_option, $act );
			}
		}
		
		return $html;
	}

	//----------------------------------------------------------
	// Fetchers
	//----------------------------------------------------------

	private function getUnusedModules($mods)
	{
		$database =& JFactory::getDBO();
	
		//jimport('joomla.database.table.module');
		include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php' );
		$jmodule = new JTableModule( $database );
	
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

		$database->setQuery( $query );
		$modules = $database->loadObjectList();
	
		return $modules;
	}

	//-----------

	private function getDefaultModules()
	{
		$string = '';
		
		if ($this->config->get('defaults')) {
			$string = $this->config->get('defaults');
		} else {
			$database =& JFactory::getDBO();
			
			$position = ($this->config->get('position')) ? $this->config->get('position') : 'myhub';
			
			include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php' );
			$jmodule = new JTableModule( $database );
			
			$query = "SELECT m.id 
						FROM ".$jmodule->getTableName()." AS m 
						WHERE m.position='".$position."' AND m.published='1' AND m.client_id='0' 
						ORDER BY m.ordering LIMIT ".$this->num_default;
			$database->setQuery( $query );
			$modules = $database->loadObjectList();

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
?>
