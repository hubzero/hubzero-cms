<?php
/**
* @version		$Id: controller.php 101 2009-06-21 19:20:27Z happynoodleboy $
* @package		JCE
* @copyright	Copyright (C) 2009 Ryan Demmer. All rights reserved.
* @license		GNU/GPL
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
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
class ConfigController extends JController
{
	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );
		$this->registerTask( 'apply', 'save');
	}

	function display( )
	{
		parent::display();
	}
	
	function cancel( )
	{
		$this->setRedirect( JRoute::_( 'index.php?option=com_jce&client='. $client, false ) );
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$db 	=& JFactory::getDBO();
		$row 	=& JTable::getInstance('plugin');
		
		$task 	= $this->getTask();

		$client = JRequest::getWord( 'client', 'site' );
		
		$query = 'SELECT id'
		. ' FROM #__plugins'
		. ' WHERE element = "jce"'
		;
		$db->setQuery( $query );
		$id = $db->loadResult();
		
		$row->load( intval( $id ) );	
	
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
	
		if ($client == 'admin') {
			$where = "client_id=1";
		} else {
			$where = "client_id=0";
		}
		
		$row->reorder( 'folder = '.$db->Quote( $row->folder ).' AND ordering > -10000 AND ordering < 10000 AND ( '.$where.' )' );
	
		$msg = JText::sprintf( 'Successfully Saved changes to JCE configuration' );		
	
		switch ( $task )
		{
			case 'apply':
				$this->setRedirect( 'index.php?option=com_jce&type=config&client='. $client, $msg );
				break;

			case 'save':
			default:
				$this->setRedirect( 'index.php?option=com_jce&client='. $client, $msg );
				break;
		}
	}
}