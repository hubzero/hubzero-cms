<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//----------------------------------------------------------

error_reporting(E_ALL);
@ini_set('display_errors','1');

// Ensure user has access to this function
$jacl =& JFactory::getacl();
$jacl->addACL( $option, 'manage', 'users', 'super administrator' );
$jacl->addACL( $option, 'manage', 'users', 'administrator' );
$jacl->addACL( $option, 'manage', 'users', 'manager' );
 
// Authorization check
$user = & JFactory::getUser();
if (!$user->authorize( $option, 'manage' )) {
	$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}

// Include scripts
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'tables'.DS.'quotes.php' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'tables'.DS.'selectedquotes.php' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'controller.php' );

// Initiate controller
$controller = new FeedbackController();
$controller->execute();
$controller->redirect();
