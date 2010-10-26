<?php
/**
 * @version		$Id: controller.php 47 2009-05-26 18:06:30Z happynoodleboy $
 * @package		Joomla
 * @subpackage	Installer
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
// wrapper for com_installer controller
$language =& JFactory::getLanguage();		
$language->load( 'com_installer', JPATH_ADMINISTRATOR );
require_once( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_installer' .DS. 'controller.php' );
?>