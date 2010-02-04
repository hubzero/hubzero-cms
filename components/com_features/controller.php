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
	
	public function execute()
	{
		$this->_task = JRequest::getVar( 'task', '' );
		
		switch ($this->_task) 
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
	
	private function _getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}

	//-----------
	
	private function _getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function login() 
	{
		$view = new JView( array('name'=>'login') );
		$view->title = JText::_(strtoupper($this->_option)).': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------

	protected function browse()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'browse') );
		$view->title = JText::_(strtoupper($this->_option));
		$view->option = $this->_option;
		
		// Incoming
		$view->filters = array();
		$view->filters['limit']  = JRequest::getInt( 'limit', 25, 'request' );
		$view->filters['start']  = JRequest::getInt( 'limitstart', 0, 'get' );
		$view->filters['type']   = JRequest::getVar( 'type', '' );
		
		// Check if the user is authorized to make changes
		$view->authorized = $this->_authorize();
		
		// Instantiate a FeaturesHistory object
		$database =& JFactory::getDBO();
		$obj = new FeaturesHistory( $database );
		
		// Get a record count
		$view->total = $obj->getCount( $view->filters, $view->authorized );

		// Get records
		$view->rows = $obj->getRecords( $view->filters, $view->authorized );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_option)));
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_option)),'index.php?option='.$this->_option);
		}

		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function add() 
	{
		$this->edit();
	}
	
	//-----------
	
	protected function edit() 
	{
		// Check if they are authorized to make changes
		if (!$this->_authorize()) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'edit') );
		$view->title = JText::_(strtoupper($this->_option)).': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		$view->option = $this->_option;
		
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
		
		// Load the object
		$database =& JFactory::getDBO();
		$view->row = new FeaturesHistory( $database );
		$view->row->load( $id );
		
		if ($view->row->note == 'tools') {
			$view->row->tbl = 'tools';
		} else if ($view->row->note == 'nontools') {
			$view->row->tbl = 'resources';
		}
		
		if (!$view->row->featured) {
			$view->row->featured = date("Y").'-'.date("m").'-'.date("d").' 00:00:00';
		}
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_option)).': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)));
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_option)),'index.php?option='.$this->_option);
		}
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function save() 
	{
		// Check if they are authorized to make changes
		if (!$this->_authorize()) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
		$database =& JFactory::getDBO();
		
		// Instantiate an object and bind the incoming data
		$row = new FeaturesHistory( $database );
		if (!$row->bind( $_POST )) {
			JError::raiseError( 500, $row->getError() );
			return;
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
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		// Redirect
		$this->_redirect = JRoute::_('index.php?option='.$this->_option);
	}
	
	//-----------
	
	protected function delete() 
	{
		// Check if they are authorized to make changes
		if (!$this->_authorize()) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
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

		return false;
	}
}
?>