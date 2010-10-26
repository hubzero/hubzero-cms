<?php
/**
* @version		$Id: plugin.php 103 2009-06-21 19:21:18Z happynoodleboy $
* @package		JCE
* @copyright	Copyright (C) 2009 Ryan Demmer. All rights reserved.
* @license		GNU/GPL
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Table class
require_once( JPATH_COMPONENT .DS. 'plugins' .DS. 'plugin.php' );
// Controller
require_once( JPATH_COMPONENT .DS. 'plugins' .DS. 'controller.php' );

// Create the controller
$controller	= new PluginsController( array(
	'base_path' =>  JPATH_COMPONENT .DS. 'plugins' 
) );

$controller->execute( JRequest::getCmd( 'task' ) );
$controller->redirect();
?>
