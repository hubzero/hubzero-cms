<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'controller.php' );
ximport('Hubzero_User_Profile');
ximport('Hubzero_Bank');

include_once( JPATH_ROOT.DS.'components'.DS.'com_answers'.DS.'helpers'.DS.'economy.php' );
include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'economy.php' );
include_once( JPATH_ROOT.DS.'components'.DS.'com_wishlist'.DS.'helpers'.DS.'economy.php' );

// Initiate controller
$controller = new UserpointsController();
$controller->execute();
$controller->redirect();
