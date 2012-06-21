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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

error_reporting(E_ALL);
@ini_set('display_errors','1');

if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl = JFactory::getACL();
	$jacl->addACL($option, 'manage', 'users', 'super administrator');
	$jacl->addACL($option, 'manage', 'users', 'administrator');
	$jacl->addACL($option, 'manage', 'users', 'manager');
	
	// Authorization check
	$user = JFactory::getUser();
	if (!$user->authorize($option, 'manage'))
	{
		$app = JFactory::getApplication();
		$app->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
	}
}
else 
{
	if (!JFactory::getUser()->authorise('core.manage', $option)) 
	{
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}

// Include scripts
require_once(JPATH_ROOT . DS . 'components' . DS . $option . DS . 'helpers' . DS . 'imghandler.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'profile.php');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'association.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'members.php');
ximport('Hubzero_View_Helper_Html');
ximport('Hubzero_User_Profile');

$controllerName = JRequest::getCmd('controller', 'members');
if (!file_exists(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'members';
}

JSubMenuHelper::addEntry(
	JText::_('Members'),
	'index.php?option=com_members',
	$controllerName == 'members'
);
JSubMenuHelper::addEntry(
	JText::_('Messaging'),
	'index.php?option=com_members&controller=messages',
	$controllerName == 'messages'
);
JSubMenuHelper::addEntry(
	JText::_('Points'),
	'index.php?option=com_members&controller=points',
	$controllerName == 'points'
);
JSubMenuHelper::addEntry(
	JText::_('Plugins'),
	'index.php?option=' . $option . '&controller=plugins', //'index.php?option=com_plugins&view=plugins&filter_folder=members&filter_type=members'
	$controllerName == 'plugins'
);

if (!file_exists(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'members';
}
require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'MembersController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

