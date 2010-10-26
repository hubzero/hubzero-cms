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
class PluginsController extends JController
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
		$this->registerTask( 'orderup'   , 	'order' );
		$this->registerTask( 'orderdown' , 	'order' );
	}

	function display( )
	{
		switch($this->getTask())
		{
			case 'add'     :
			case 'edit'    :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'layout', 'form' );
				JRequest::setVar( 'view', 'plugin' );
			} break;
		}

		parent::display();
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$db   	=& JFactory::getDBO();
		$row 	=& JTable::getInstance('plugin', 'JCETable');
		$task 	= $this->getTask();

		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();

		$row->reorder( 'type = '.$db->Quote($row->type).' AND ordering > -10000 AND ordering < 10000' );

		switch ( $task )
		{
			case 'apply':
				$msg = JText::sprintf( 'Successfully Saved changes to Plugin', $row->title );
				$this->setRedirect( 'index.php?option=com_jce&type=plugin&view=plugin&task=edit&cid[]='. $row->id, $msg );
				break;

			case 'save':
			default:
				$msg = JText::sprintf( 'Successfully Saved Plugin', $row->title );
				$this->setRedirect( 'index.php?option=com_jce&type=plugin', $msg );
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
			JError::raiseError(500, JText::_( 'Select a plugin to '.$action ) );
		}

		$cids = implode( ',', $cid );

		$query = 'UPDATE #__jce_plugins SET published = '.(int) $publish
			. ' WHERE id IN ( '.$cids.' )'
			. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id').' ))'
			;
		$db->setQuery( $query );
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg() );
		}

		if (count( $cid ) == 1) {
			$row =& JTable::getInstance('plugin', 'JCETable');
			$row->checkin( $cid[0] );
		}

		$this->setRedirect( 'index.php?option=com_jce&type=plugin' );
	}
	
	function cancel( ){
		$this->setRedirect( JRoute::_( 'index.php?option=com_jce', false ) );
	}

	function cancelEdit( )
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$db =& JFactory::getDBO();
		$row =& JTable::getInstance('plugin', 'JCETable');
		$row->bind(JRequest::get('post'));
		$row->checkin();

		$this->setRedirect( JRoute::_( 'index.php?option=com_jce&type=plugin', false ) );
	}
	
	function order( )
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$db =& JFactory::getDBO();

		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));		
		$uid    = $cid[0];
		$inc    = ( $this->getTask() == 'orderup' ? -1 : 1 );

		$row =& JTable::getInstance('plugin', 'JCETable');
		$row->load( $uid );
		$row->move( $inc, 'name='.$db->Quote($row->name).' AND ordering > -10000 AND ordering < 10000' );

		$this->setRedirect( 'index.php?option=com_jce&type=plugin' );
	}

	function saveorder( )
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		$db			=& JFactory::getDBO();
		$total		= count( $cid );
		$order 		= JRequest::getVar( 'order', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order, array(0));

		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		$row =& JTable::getInstance('plugin', 'JCETable');
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
				$condition = 'type = '.$db->Quote($row->type).' AND ordering > -10000 AND ordering < 10000';
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
		$this->setRedirect( 'index.php?option=com_jce&type=plugin', $msg );
	}
}