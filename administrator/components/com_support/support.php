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

$option = 'com_support';

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
		$app->redirect('index.php', JText::_('ALERTNOTAUTH'));
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
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'ticket.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'watching.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'comment.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'message.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'resolution.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'attachment.php');
include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'category.php');
include_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'utilities.php');
include_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'acl.php');
include_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'html.php');
include_once(JPATH_ROOT . DS . 'components' . DS . $option . DS . 'helpers' . DS . 'tags.php');

$controllerName = JRequest::getCmd('controller', 'tickets');
if (!file_exists(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'tickets';
}

JSubMenuHelper::addEntry(
	JText::_('Tickets'),
	'index.php?option=com_support&controller=tickets',
	$controllerName == 'tickets'
);
JSubMenuHelper::addEntry(
	JText::_('Categories'),
	'index.php?option=com_support&controller=categories',
	$controllerName == 'categories'
);
JSubMenuHelper::addEntry(
	JText::_('Queries'),
	'index.php?option=com_support&controller=queries',
	$controllerName == 'queries'
);
JSubMenuHelper::addEntry(
	JText::_('Messages'),
	'index.php?option=com_support&controller=messages',
	$controllerName == 'messages'
);
JSubMenuHelper::addEntry(
	JText::_('Resolutions'),
	'index.php?option=com_support&controller=resolutions',
	$controllerName == 'resolutions'
);
JSubMenuHelper::addEntry(
	JText::_('Abuse Reports'),
	'index.php?option=com_support&controller=abusereports',
	$controllerName == 'abusereports'
);
JSubMenuHelper::addEntry(
	JText::_('Tag/Groups'),
	'index.php?option=com_support&controller=taggroups',
	$controllerName == 'taggroups'
);
JSubMenuHelper::addEntry(
	JText::_('Stats'),
	'index.php?option=com_support&controller=stats',
	$controllerName == 'stats'
);
JSubMenuHelper::addEntry(
	JText::_('ACL'),
	'index.php?option=com_support&controller=acl',
	$controllerName == 'acl'
);

require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'SupportController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

