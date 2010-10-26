<?php
/**
 * @version		$Id: controller.php 47 2009-05-26 18:06:30Z happynoodleboy $
 * @package		Joomla
 * @subpackage	Config
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.controller' );

/**
 * Plugins Component Controller
 *
 * @package		Joomla
 * @subpackage	Plugins
 * @since 1.5
 */
class GroupsController extends JController
{
	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );

		$this->registerTask( 'apply', 		'save');
		$this->registerTask( 'unpublish', 	'publish');
		$this->registerTask( 'edit' , 		'display' );
		$this->registerTask( 'add' , 		'display' );
		$this->registerTask( 'remove' , 	'remove' );
		$this->registerTask( 'addusers' , 	'display' );
		$this->registerTask( 'removeusers' ,'display' );
		$this->registerTask( 'orderup'   , 	'order' );
		$this->registerTask( 'orderdown' , 	'order' );
		$this->registerTask( 'legend' , 	'display' );
		$this->registerTask( 'copy' , 		'copy' );		
	}

	function display( )
	{
		switch($this->getTask())
		{
			case 'add'     :
			case 'edit'    :
			{
				JRequest::setVar( 'layout', 'form' );
				JRequest::setVar( 'view', 'group' );	
				JRequest::setVar( 'hidemainmenu', 1 );	
			} break;
			case 'addusers'     :
			case 'removeusers'  :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'view', 'users' );
			} break;
			case 'legend':
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'view', 'legend' );
			} break;
		}

		parent::display();
	}
	
	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$cid     = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select a Group to remove' ) );
		}

		$cids = implode( ',', $cid );

		$query = 'DELETE FROM #__jce_groups'
			. ' WHERE id IN ( '.$cids.' )'
			;
		$db->setQuery( $query );
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg() );
		}

		$this->setRedirect( 'index.php?option=com_jce&type=group' );
	}
	
	function copy()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$cid    = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		$n		= count( $cid );
		if ($n == 0) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}
		
		$row 	=& JTable::getInstance('groups', 'JCETable');
		
		foreach ($cid as $id){
			// load the row from the db table
			$row->load( (int) $id );
			$row->name 			= JText::sprintf( 'Copy of %s', $row->name );
			$row->id 			= 0;
			$row->published 	= 0;

			if (!$row->check()) {
				return JError::raiseWarning( 500, $row->getError() );
			}
			if (!$row->store()) {
				return JError::raiseWarning( 500, $row->getError() );
			}
			$row->checkin();
			$row->reorder( 'ordering='.$db->Quote( $row->ordering ) );
		}
		$msg = JText::sprintf( 'Items Copied', $n );
		$this->setRedirect( 'index.php?option=com_jce&type=group', $msg );
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		require_once( dirname( __FILE__ ) .DS. 'helper.php' );

		$db   	=& JFactory::getDBO();
		$row 	=& JTable::getInstance('groups', 'JCETable');
		$task 	= $this->getTask();
		
		$pid 		= JRequest::getVar( 'pid', array(), 'post', 'array' );
		$components = JRequest::getVar( 'components', array(), 'post', 'array' );
		$types 		= JRequest::getVar( 'types', array(), 'post', 'array' );
		$users 		= JRequest::getVar( 'users', array(), 'post', 'array' );

		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
		}
		
		if( substr( $row->rows, -1, 1 ) == ';' ){
			$row->rows = substr( $row->rows, 0, -1 );
		}
		
		$row->types 		= implode( ',', $types );
		$row->components 	= implode( ',', $components );
		$row->users 		= implode( ',', $users );
		
		$query = "SELECT id"
		. " FROM #__jce_plugins"
		. " WHERE published = 1"
		. " AND type = 'plugin'"
		. " AND id IN (". str_replace( ';', ',', $row->rows ) .")"
		;
		$db->setQuery( $query );
		$ids = $db->loadResultArray();
		
		if( !$ids ){
			$ids = array();
		}
						
		$row->plugins = implode( ',', array_merge( $pid, $ids ) );
		
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();

		switch ( $task )
		{
			case 'apply':
				$msg = JText::sprintf( 'Successfully Saved changes to Group', $row->name );
				$this->setRedirect( 'index.php?option=com_jce&type=group&view=group&task=edit&cid[]='. $row->id, $msg );
				break;

			case 'save':
			default:
				$msg = JText::sprintf( 'Successfully Saved Group', $row->name );
				$this->setRedirect( 'index.php?option=com_jce&type=group', $msg );
				break;
		}
	}

	function publish( ){
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$cid     = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$publish = ( $this->getTask() == 'publish' ? 1 : 0 );

		if (count( $cid ) < 1) {
			$action = $publish ? JText::_( 'publish' ) : JText::_( 'unpublish' );
			JError::raiseError(500, JText::_( 'Select a Group to '.$action ) );
		}

		$cids = implode( ',', $cid );

		$query = 'UPDATE #__jce_groups SET published = '.(int) $publish
			. ' WHERE id IN ( '.$cids.' )'
			. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id').' ))'
			;
		$db->setQuery( $query );
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg() );
		}

		if (count( $cid ) == 1) {
			$row =& JTable::getInstance('groups', 'JCETable');
			$row->checkin( $cid[0] );
		}

		$this->setRedirect( 'index.php?option=com_jce&type=group' );
	}
	
	function order(  )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db =& JFactory::getDBO();

		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		$uid    = $cid[0];
		$inc    = ( $this->getTask() == 'orderup' ? -1 : 1 );

		$row =& JTable::getInstance('groups', 'JCETable');
		$row->load( $uid );
		//$row->move( $inc, ' AND ordering > -10000 AND ordering < 10000' );
		$row->move( $inc );

		$this->setRedirect( 'index.php?option=com_jce&type=group' );
	}
	
	function saveorder( )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		$db			=& JFactory::getDBO();
		$total		= count( $cid );
		$order 		= JRequest::getVar( 'order', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order, array(0));

		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		$row 		=& JTable::getInstance('groups', 'JCETable');
		$conditions = array();

		// update ordering values
		for ( $i=0; $i < $total; $i++ )
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg() );
				}
				// remember to updateOrder this group
				$condition = ' AND ordering > -10000 AND ordering < 10000';
				$found = false;
				foreach ( $conditions as $cond )
				{
					if ($cond[1]==$condition) {
						$found = true;
						break;
					}
				}
				if (!$found) $conditions[] = array($row->id, $condition);
			}
		}

		// execute updateOrder for each group
		foreach ( $conditions as $cond ) {
			$row->load( $cond[0] );
			$row->reorder( $cond[1] );
		}

		$msg 	= JText::_( 'New ordering saved' );
		$this->setRedirect( 'index.php?option=com_jce&type=group', $msg );
	}
	
	function cancel( ){
		$this->setRedirect( JRoute::_( 'index.php?option=com_jce', false ) );
	}

	function cancelEdit( )
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$db =& JFactory::getDBO();
		$row =& JTable::getInstance('groups', 'JCETable');
		$row->bind(JRequest::get('post'));
		$row->checkin();

		$this->setRedirect( JRoute::_( 'index.php?option=com_jce&type=group', false ) );
	}
}