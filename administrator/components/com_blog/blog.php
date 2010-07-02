<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

error_reporting(E_ALL);
@ini_set('display_errors','1');

// Set access levels
$jacl =& JFactory::getACL();
$jacl->addACL( $option, 'manage', 'users', 'super administrator' );
$jacl->addACL( $option, 'manage', 'users', 'administrator' );
$jacl->addACL( $option, 'manage', 'users', 'manager' );

// Authorization check
$user = & JFactory::getUser();
if (!$user->authorize( $option, 'manage' )) {
	$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}

// Include scripts
require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'tables'.DS.'blog.entry.php' );
require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'tables'.DS.'blog.comment.php' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'blog.html.php' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'controller.php' );

// Initiate controller
$controller = new BlogController();
$controller->execute();
$controller->redirect();
