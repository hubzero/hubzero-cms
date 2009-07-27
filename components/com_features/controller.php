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

class FeaturesController extends JObject
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
		$task = JRequest::getVar( 'task', '' );
		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		// Load the component config
		/*$component =& JComponentHelper::getComponent( $this->_option );
		if (!trim($component->params)) {
			$path = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'config.xml';
			if (!is_file($path)) {
				$path = '';
			}
			$jconfig =& new JParameter( $component->params, $path );
			$data = $jconfig->renderToArray();
			$c = array();
			foreach ($data as $d=>$info) 
			{
				if ($d != '@spacer') {
					$c[] = $d.'='.$info[4];
				}
			}
			$g = implode(n,$c);
			$config =& new JParameter( $g );
		} else {
			$config =& JComponentHelper::getParams( $this->_option );
		}
		$this->config = $config;*/
		
		switch ( $this->getTask() ) 
		{
			case 'delete': $this->delete(); break;
			case 'add':    $this->edit();   break;
			case 'edit':   $this->edit();   break;
			case 'save':   $this->save();   break;
			case 'browse': $this->browse(); break;
			case 'login':  $this->login();  break;

			default: $this->browse(); break;
		}
	}
	
	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}

	//-----------
	
	private function getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}

	//-----------
	
	private function getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function browse()
	{
		// Incoming
		$filters = array();
		$filters['limit']  = JRequest::getInt( 'limit', 25, 'request' );
		$filters['start']  = JRequest::getInt( 'limitstart', 0, 'get' );
		$filters['type']   = JRequest::getVar( 'type', '' );
		
		$authorized = $this->_authorize();
		
		$database =& JFactory::getDBO();
		
		$obj = new FeaturesHistory( $database );
		
		// Get a record count
		$total = $obj->getCount( $filters, $authorized );

		// Get records
		$rows = $obj->getRecords( $filters, $authorized );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );
		
		// Push some styles to the template
		$this->getStyles();
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)) );
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}

		// Output HTML
		echo FeaturesHtml::browse( $database, $rows, $pageNav, $this->_option, $filters, $authorized );
	}
	
	//-----------
	
	protected function add() 
	{
		$this->edit();
	}
	
	//-----------
	
	protected function edit() 
	{
		$database =& JFactory::getDBO();
		
		// Push some styles to the template
		$this->getStyles();
		
		// Set the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
		
		// Load the object
		$row = new FeaturesHistory( $database );
		$row->load( $id );
		
		if ($row->note == 'tools') {
			$row->tbl = 'tools';
		} else if ($row->note == 'nontools') {
			$row->tbl = 'resources';
		}
		
		if (!$row->featured) {
			$row->featured = date("Y").'-'.date("m").'-'.date("d").' 00:00:00';
		}
		
		// Output HTML
		echo FeaturesHtml::edit( $this->_option, $row, $title );
	}
	
	//-----------
	
	protected function save() 
	{
		$database =& JFactory::getDBO();
		
		// Instantiate an object and bind the incoming data
		$row = new FeaturesHistory( $database );
		if (!$row->bind( $_POST )) {
			echo FeaturesHtml::alert( $row->getError() );
			exit();
		}
		
		if ($row->tbl == 'tools') {
			$row->note = 'tools';
			$row->tbl = 'resources';
		} else if ($row->tbl == 'resources') {
			$row->note = 'nontools';
			$row->tbl = 'resources';
		}
		
		// Check content
		if (!$row->check()) {
			echo FeaturesHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo FeaturesHtml::alert( $row->getError() );
			exit();
		}
		
		// Redirect
		$this->_redirect = JRoute::_('index.php?option='.$this->_option);
	}
	
	//-----------
	
	protected function delete() 
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
		
		if ($id) {
			$database =& JFactory::getDBO();
			
			// Delete the object
			$row = new FeaturesHistory( $database );
			$row->delete( $id );
		}
		
		// Redirect
		$this->_redirect = JRoute::_('index.php?option='.$this->_option);
	}
	
	//-----------
	
	private function _authorize()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return true;
		}
		
		//$xuser =& XFactory::getUser();
		$xuser = XProfile::getInstance();
		if (is_object($xuser)) {
			// Check if they're a site admin (from LDAP)
			$app =& JFactory::getApplication();
			if (in_array(strtolower($app->getCfg('sitename')), $xuser->get('admin'))) {
				return true;
			}
		}

		return false;
	}
}
?>