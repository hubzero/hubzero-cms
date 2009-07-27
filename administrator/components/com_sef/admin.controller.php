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

class SefController extends JObject
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
		$section = JRequest::getVar( 'section', '' );
		if ($section) {
			$task = $section;
		}
		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		switch ($this->getTask()) 
		{
			case 'config': $this->config(); break;
			case 'saveconfig': $this->saveconfig(); break;
			
			case 'add':    $this->add();    break;
			case 'new':    $this->add();    break;
			case 'edit':   $this->edit();   break;
			case 'save':   $this->save();   break;
			case 'remove': $this->remove(); break;
			case 'cancel': $this->cancel(); break;
			case 'info':   $this->info();   break;
			case 'browse': $this->browse(); break;
			
			default: $this->browse(); break;
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
	// Tag functions
	//----------------------------------------------------------
	
	protected function info() 
	{
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'readme.inc' );
	}
	
	//-----------
	
	protected function browse()
	{
		$database =& JFactory::getDBO();
		$app =& JFactory::getApplication();
		
		// Get Joomla configuration
		$config = JFactory::getConfig();
		
		// Incoming
		$filters = array();
		$filters['limit']      = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start']      = $app->getUserStateFromRequest($this->_option.'.limitstart', 'limitstart', 0, 'int');
		$filters['catid']      = $app->getUserStateFromRequest($this->_option.'.catid', 'catid', 0, 'int');
		$filters['ViewModeId'] = $app->getUserStateFromRequest($this->_option.'.viewmode', 'viewmode', 0, 'int');
		$filters['SortById']   = $app->getUserStateFromRequest($this->_option.'.sortby', 'sortby', 0, 'int');
		
		// Determine the mode
		$is404mode = false;
		if ($filters['ViewModeId'] == 1) {
			$is404mode = true;
		}

		$lists = array();
		
		// Make the select list for the filter
		$viewmode = array();
		$viewmode[] = JHTML::_('select.option', '0', JText::_('Show SEF Urls'), 'value', 'text');
		$viewmode[] = JHTML::_('select.option', '1', JText::_('Show 404 Log'), 'value', 'text');
		$viewmode[] = JHTML::_('select.option', '2', JText::_('Show Custom Redirects'), 'value', 'text');
		
		$lists['viewmode'] = JHTML::_('select.genericlist', $viewmode, 'viewmode', '', 'value', 'text', $filters['ViewModeId'], false, false );  	

		// Make the select list for the filter
		$orderby = array();
		$orderby[] = JHTML::_('select.option', '0', JText::_('SEF Url (asc)'), 'value', 'text');
		$orderby[] = JHTML::_('select.option', '1', JText::_('SEF Url (desc)'), 'value', 'text');
		if ($is404mode != true) {
  			$orderby[] = JHTML::_('select.option', '2', JText::_('Real Url (asc)'), 'value', 'text');
			$orderby[] = JHTML::_('select.option', '3', JText::_('Real Url (desc)'), 'value', 'text');
		}
		$orderby[] = JHTML::_('select.option', '4', JText::_('Hits (asc)'), 'value', 'text');
		$orderby[] = JHTML::_('select.option', '5', JText::_('Hits (desc)'), 'value', 'text');

		$lists['sortby'] = JHTML::_('select.genericlist', $orderby, 'sortby', '', 'value', 'text', $filters['SortById'], false, false );
		
		// Instantiate a new SefEntry
		$s = new SefEntry( $database );

		// Record count
		$total = $s->getCount( $filters );
		
		// Get records
		$rows = $s->getRecords( $filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		SefHtml::browse( $rows, $lists, $pageNav, $this->_option, $is404mode );
	}

	//-----------
	
	protected function add() 
	{
		$this->edit();
	}

	//-----------

	protected function edit($row=null)
	{
		$database =& JFactory::getDBO();
	
		// Load a tag object if one doesn't already exist
		if (!$row) {
			// Incoming
			$ids = JRequest::getVar('id', array());
			if (!is_array( $ids )) {
				$ids = array();
			}

			$id = (!empty($ids)) ? $ids[0] : 0;
			
			$row = new SefEntry( $database );
			$row->load( $id );

			if (!$id) {
				// do stuff for new records
				$row->dateadd = date("Y-m-d");
			}
		}

		// Output HTML
		SefHtml::edit( $row, $this->_option, $this->getError() );
	}

	//-----------

	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------
	
	protected function save()
	{
		$database =& JFactory::getDBO();
		
		// Load the tag object and bind the incoming data to it
		$row = new SefEntry( $database );
		if (!$row->bind( $_POST )) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}
		
		if (substr($row->oldurl,-1) == DS) {
			$row->oldurl = substr($row->oldurl,0,strlen($row->oldurl)-1);
		}
		
		// Check content
		if (!$row->check()) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}
	
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_( 'SEF saved' );
	}

	//-----------

	protected function remove()
	{
		$database =& JFactory::getDBO();
		
		$ids = JRequest::getVar('id', array());
		if (!is_array( $ids )) {
			$ids = array();
		}
		
		// Make sure we have an ID
		if (empty($ids)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		// Load some needed objects
		$sef = new SefEntry( $database );
		
		foreach ($ids as $id) 
		{
			// Remove the SEF
			$sef->delete( $id );
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_( 'SEF removed' );
	}
}
?>
