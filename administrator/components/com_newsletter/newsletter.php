<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$option = 'com_support';

if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl =& JFactory::getacl();
	$jacl->addACL($option, 'manage', 'users', 'super administrator');
	$jacl->addACL($option, 'manage', 'users', 'administrator');
	$jacl->addACL($option, 'manage', 'users', 'manager');
}

//include models
require_once( JPATH_COMPONENT . DS . 'tables' . DS . 'newsletter.php' );
require_once( JPATH_COMPONENT . DS . 'tables' . DS . 'template.php' );
require_once( JPATH_COMPONENT . DS . 'tables' . DS . 'primary.php' );
require_once( JPATH_COMPONENT . DS . 'tables' . DS . 'secondary.php' );
require_once( JPATH_COMPONENT . DS . 'tables' . DS . 'mailinglist.php' );
require_once( JPATH_COMPONENT . DS . 'tables' . DS . 'mailinglist.email.php' );
require_once( JPATH_COMPONENT . DS . 'tables' . DS . 'mailing.php' );
require_once( JPATH_COMPONENT . DS . 'tables' . DS . 'mailing.recipient.php' );
require_once( JPATH_COMPONENT . DS . 'tables' . DS . 'mailing.recipient.action.php' );

//include Hubzero Libraries
ximport('Hubzero_Controller');
ximport('Hubzero_Newsletter_Helper');

//instantiate controller
$controllerName = JRequest::getCmd('controller', 'newsletter');
require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'NewsletterController' . ucfirst($controllerName);

//menu items
$menuItems = array(
	'newsletter'  => 'Newsletters',
	'mailing'     => 'Mailings',
	'mailinglist' => 'Lists',
	'template'    => 'Templates',
	'tools'       => 'Tools'
);

//add menu items
foreach($menuItems as $k => $v)
{   
	$active = (JRequest::getCmd('controller', 'newsletter') == $k) ? true : false ;
	JSubMenuHelper::addEntry($v, 'index.php?option=com_newsletter&controller='.$k, $active);
}

//execute controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
