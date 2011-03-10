<?php
/**
 * Joomla! 1.5 component Joomdle
 *
 * @version $Id: joomdle.php 2009-04-17 03:54:05 svn $
 * @author Antonio Durán Terrés
 * @package Joomdle
 * @license GNU/GPL
 *
 * Shows information about Moodle courses
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('stylesheet', 'joomdle.css', 'administrator/components/com_joomdle/assets/css/');
/*
 * Define constants for all pages
 */
define( 'COM_JOOMDLE_DIR', 'images'.DS.'joomdle'.DS );
define( 'COM_JOOMDLE_BASE', JPATH_ROOT.DS.COM_JOOMDLE_DIR );
define( 'COM_JOOMDLE_BASEURL', JURI::root().str_replace( DS, '/', COM_JOOMDLE_DIR ));

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

// Require the base controller
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';

// Initialize the controller
$controller = new JoomdleController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();
?>
