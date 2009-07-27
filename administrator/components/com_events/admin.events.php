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

error_reporting(E_ALL);
@ini_set('display_errors','1');

$jacl =& JFactory::getACL();
$jacl->addACL( $option, 'manage', 'users', 'super administrator' );
$jacl->addACL( $option, 'manage', 'users', 'administrator' );

// Ensure user has access to this function
$juser = & JFactory::getUser();
if (!$juser->authorize($option, 'manage')) {
	$app =& JFactory::getApplication();
	$app->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}

require_once(JPATH_ROOT.DS.'components'.DS.$option.DS.'events.class.php');
require_once(JPATH_ROOT.DS.'components'.DS.$option.DS.'events.date.php');
require_once(JPATH_ROOT.DS.'components'.DS.$option.DS.'events.repeat.php');
require_once(JPATH_ROOT.DS.'components'.DS.$option.DS.'events.category.php');
require_once(JPATH_ROOT.DS.'components'.DS.$option.DS.'events.tags.php');
require_once(JPATH_ROOT.DS.'components'.DS.$option.DS.'events.config.php');
require_once(JPATH_COMPONENT.DS.'admin.events.html.php');
require_once(JPATH_COMPONENT.DS.'admin.controller.php');

// Instantiate controller
$controller = new EventsController();
$controller->execute();
$controller->redirect();
?>