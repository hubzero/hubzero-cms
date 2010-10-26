<?php
/**
 * @version		$Id: jce.php 140 2009-06-27 11:53:32Z happynoodleboy $
 * @package		Joomla Content Editor (JCE)
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
 * @license		GNU/GPL
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');

define('JCE_PATH', JPATH_PLUGINS.DS.'editors'.DS.'jce');
define('JCE_PLUGINS', JCE_PATH.DS.'tiny_mce'.DS.'plugins');
define('JCE_LIBRARIES', JCE_PATH.DS.'libraries');
define('JCE_CLASSES', JCE_LIBRARIES.DS.'classes');

$task = JRequest::getCmd('task');

/*
 * Editor or plugin request.
 */
switch($task) {
 	case 'plugin':
	case 'help':
		require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jce'.DS.'editor.php');
    	exit ();
		break;
	case 'popup':
		require_once (dirname( __FILE__ ).DS.'controller'.DS.'popup.php');
	    JRequest::setVar('view', 'popup');
	    $controller = new JCEControllerPopup();
	    // Perform the Request task
	    $controller->execute($task);
	    $controller->redirect();
		break;
}
?>
