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

class XPollController
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $_error = NULL;
	
	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		//Set the controller name
		if (empty( $this->_name ))
		{
			if (isset($config['name']))  {
				$this->_name = $config['name'];
			}
			else
			{
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
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
		switch ( $this->getTask() ) 
		{
			case 'add':       $this->edit();     break;
			case 'edit':      $this->edit();     break;
			case 'save':      $this->save();     break;
			case 'remove':    $this->remove();   break;
			case 'reset':     $this->resetit();  break;
			case 'cancel':    $this->cancel();   break;
			case 'publish':   $this->publish(1); break;
			case 'unpublish': $this->publish(0); break;
			case 'open':      $this->open(1);    break;
			case 'close':     $this->open(0);    break;
			case 'browse':    $this->browse();   break;
			
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
	// Views
	//----------------------------------------------------------

	protected function browse() 
	{
		$database =& JFactory::getDBO();
		$app =& JFactory::getApplication();

		// Get configuration
		$config = JFactory::getConfig();

		// Incoming
		$filters = array();
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start'] = JRequest::getInt('limitstart', 0);

		$p = new XPollPoll( $database );
		
		// Get a record count
		$total = $p->getCount( $filters );
		
		// Retrieve all the records
		$rows = $p->getRecords( $filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		XPollHtml::browse( $rows, $pageNav, $this->_option );
	}

	//-----------

	protected function edit() 
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Incoming (expecting an array)
		$cid = JRequest::getVar( 'cid', array(0) );
		if (!is_array( $cid )) {
			$cid = array(0);
		}
		$uid = $cid[0];

		// Load the poll
		$row = new XPollPoll( $database );
		$row->load( $uid );

		// Fail if not checked out by 'me'
		if ($row->checked_out && $row->checked_out <> $juser->get('id')) {
			$this->_redirect = 'index.php?option='. $this->_option;
			$this->_message = JText::_( 'XPOLL_ERROR_CHECKED_OUT' );
			return;
		}

		$options = array();

		// Are we editing existing or creating new?
		if ($uid) {
			// Editing existing
			// Check it out
			$row->checkout( $juser->get('id') );
			
			// Load the poll's options
			$xpdata = new XPollData( $database );
			$options = $xpdata->getPollOptions( $uid, true );
		} else {
			// Creating new
			// Set the log time to the default
			$row->lag = 3600*24;
		}

		// Get selected pages
		if ($uid) {
			$xpmenu = new XPollMenu( $database );
			$lookup = $xpmenu->getMenuIds( $row->id );
		} else {
			$lookup = array( JHTML::_('select.option', 0, JText::_('ALL'), 'value', 'text') );
		}

		// Build the html select list
		$lists = array();
		//$lists['select'] = mosAdminMenus::MenuLinks( $lookup, 1, 1 );
		$soptions = JHTML::_('menu.linkoptions', $lookup, NULL, 1);
		if (empty( $lookup )) {
			$lookup = array( JHTML::_('select.option',  -1 ) );
		}
		$lists['select'] = JHTML::_('select.genericlist', $soptions, 'selections[]', 'class="inputbox" size="15" multiple="multiple"', 'value', 'text', $lookup, 'selections' );

		// Output HTML
		XPollHtml::edit( $row, $options, $lists, $this->_option );
	}

	//-----------

	protected function save() 
	{
		$database =& JFactory::getDBO();

		// Save the poll parent information
		$row = new XPollPoll( $database );
		if (!$row->bind( $_POST )) {
			echo XPollHtml::alert( $row->getError() );
			exit();
		}
		$isNew = ($row->id == 0);
		if (!$row->check()) {
			echo XPollHtml::alert( $row->getError() );
			exit();
		}
		if (!$row->store()) {
			echo XPollHtml::alert( $row->getError() );
			exit();
		}
		$row->checkin();
		
		// Incoming poll options
		$options = JRequest::getVar( 'polloption', array(), 'post' );

		foreach ($options as $i=>$text) 
		{
			// 'slash' the options
			if (!get_magic_quotes_gpc()) {
				$text = addslashes( $text );
			}
		
			if (trim($text) != '') {
				$xpdata = new XPollData( $database );
				if (!$isNew) {
					$xpdata->id = $i;
				}
				$xpdata->pollid = $row->id;
				$xpdata->text = trim($text);
				if (!$xpdata->check()) {
					echo XPollHtml::alert( $xpdata->getError() );
					exit();
				}
				if (!$xpdata->store()) {
					echo XPollHtml::alert( $xpdata->getError() );
					exit();
				}
			}
		}

		// Remove old menu entries for this poll
		$xpmenu = new XPollMenu( $database );
		$xpmenu->deleteEntries( $row->id );
		
		// Update the menu visibility
		$selections = JRequest::getVar( 'selections', array(), 'post' );
		
		for ($i=0, $n=count($selections); $i < $n; $i++) 
		{
			$xpmenu->insertEntry( $row->id, $selections[$i] );
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option;
	}

	//-----------

	protected function resetit()
	{
		$database =& JFactory::getDBO();
		
		// Incoming (we're expecting an array)
		$ids = JRequest::getVar( 'cid', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}

		// Make sure we have IDs to work with
		if (!is_array( $ids ) || count( $ids ) < 1) {
			echo XPollHtml::alert( JText::_( 'XPOLL_ERROR_NO_SELECTION_TO_RESET' ) );
			exit();
		}

		// Loop through the IDs
		$juser =& JFactory::getUser();
		$xpdate = new XPollDate( $database );
		foreach ($ids as $id) 
		{
			// Load the poll
			$row = new XPollPoll( $database );
			$row->load( $id );
			
			// Only alter items not checked out or checked out by 'me'
			if ($row->checked_out == 0 || $row->checked_out == $juser->get('id')) {
				// Delete the Date entries
				$xpdate->deleteEntries( $id );
				
				// Reset voters to 0 and save
				$row->voters = 0;
				if (!$row->check()) {
					echo XPollHtml::alert( $row->getError() );
					exit();
				}
				if (!$row->store()) {
					echo XPollHtml::alert( $row->getError() );
					exit();
				}
				$row->checkin( $id );
			}
		}

		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option;
	}

	//-----------

	protected function remove() 
	{
		$database =& JFactory::getDBO();

		// Incoming (expecting an array)
		$ids = JRequest::getVar( 'cid', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}

		$msg = '';
		// Make sure we have IDs to work with
		if (count($ids) > 0) {
			$poll = new XPollPoll( $database );
			
			// Loop through the array of IDs and delete
			foreach ($ids as $id) 
			{
				if (!$poll->delete( $id )) {
					$msg .= $poll->getError();
				}
			}
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option;
		$this->_message = $msg;
	}

	//-----------

	protected function publish( $publish=1 ) 
	{
		$database =& JFactory::getDBO();

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar( 'cid', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}

		// Make sure we have IDs to work with
		if (!is_array( $ids ) || count( $ids ) < 1) {
			if ($publish) {
				echo XPollHtml::alert( JText::_( 'XPOLL_ERROR_NO_SELECTION_TO_PUBLISH' ) );
			} else {
				echo XPollHtml::alert( JText::_( 'XPOLL_ERROR_NO_SELECTION_TO_UNPUBLISH' ) );
			}
			exit;
		}

		$juser =& JFactory::getUser();
		
		// Loop through the IDs
		foreach ($ids as $id) 
		{
			// Load the poll
			$row = new XPollPoll( $database );
			$row->load( $id );
			
			// Only alter items not checked out or checked out by 'me'
			if ($row->checked_out == 0 || $row->checked_out == $juser->get('id')) {
				// Reset voters to 0 and save
				$row->published = $publish;
				if (!$row->check()) {
					echo XPollHtml::alert( $row->getError() );
					exit();
				}
				if (!$row->store()) {
					echo XPollHtml::alert( $row->getError() );
					exit();
				}
				$row->checkin( $id );
			}
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option;
	}

	//-----------

	protected function open( $open=1 ) 
	{
		$database =& JFactory::getDBO();

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar( 'cid', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}

		// Make sure we have IDs to work with
		if (!is_array( $ids ) || count( $ids ) < 1) {
			if ($publish) {
				echo XPollHtml::alert( JText::_( 'XPOLL_ERROR_NO_SELECTION_TO_OPEN' ) );
			} else {
				echo XPollHtml::alert( JText::_( 'XPOLL_ERROR_NO_SELECTION_TO_CLOSE' ) );
			}
			exit;
		}
		
		$juser =& JFactory::getUser();
		
		// Loop through the IDs
		foreach ($ids as $id) 
		{
			// Load the poll
			$row = new XPollPoll( $database );
			$row->load( $id );
			
			// Only alter items not checked out or checked out by 'me'
			if ($row->checked_out == 0 || $row->checked_out == $juser->get('id')) {
				// Reset voters to 0 and save
				$row->open = $open;
				if (!$row->check()) {
					echo XPollHtml::alert( $row->getError() );
					exit();
				}
				if (!$row->store()) {
					echo XPollHtml::alert( $row->getError() );
					exit();
				}
				$row->checkin( $id );
			}
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option;
	}

	//-----------

	protected function cancel() 
	{
		$database =& JFactory::getDBO();
		
		// Check the poll in
		$row = new XPollPoll( $database );
		$row->bind( $_POST );
		$row->checkin();
		
		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option;
	}
}
?>