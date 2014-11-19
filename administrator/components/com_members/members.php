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

$option = 'com_members';

if (!JFactory::getUser()->authorise('core.manage', $option))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'imghandler.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'profile.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'association.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'password_rules.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'password_blacklist.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'quotas_classes.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'users_quotas.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'members.php');

$controllerName = JRequest::getCmd('controller', 'members');
if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'members';
}

$canDo = MembersHelper::getActions('component');

JSubMenuHelper::addEntry(
	JText::_('COM_MEMBERS'),
	'index.php?option=com_members',
	$controllerName == 'members'
);
JSubMenuHelper::addEntry(
	JText::_('COM_MEMBERS_MENU_ONLINE'),
	'index.php?option=com_members&controller=whosonline',
	$controllerName == 'whosonline'
);
JSubMenuHelper::addEntry(
	JText::_('COM_MEMBERS_MENU_MESSAGING'),
	'index.php?option=com_members&controller=messages',
	$controllerName == 'messages'
);
if (JComponentHelper::getParams($option)->get('bankAccounts'))
{
	JSubMenuHelper::addEntry(
		JText::_('COM_MEMBERS_MENU_POINTS'),
		'index.php?option=com_members&controller=points',
		$controllerName == 'points'
	);
}
JSubMenuHelper::addEntry(
	JText::_('COM_MEMBERS_MENU_PLUGINS'),
	'index.php?option=' . $option . '&controller=plugins',
	$controllerName == 'plugins'
);

if ($canDo->get('core.admin'))
{
	JSubMenuHelper::addEntry(
		JText::_('COM_MEMBERS_PASSWORDS'),
		'index.php?option=com_members&controller=passwordrules',
		($controllerName == 'passwordrules' || $controllerName == 'passwordblacklist')
	);
}

JSubMenuHelper::addEntry(
	JText::_('COM_MEMBERS_MENU_QUOTAS'),
	'index.php?option=com_members&controller=quotas',
	$controllerName == 'quotas'
);
JSubMenuHelper::addEntry(
	JText::_('COM_MEMBERS_MENU_REGISTRATION'),
	'index.php?option=' .  $option . '&controller=registration',
	(in_array($controllerName, array('registration', 'organizations', 'employers', 'incremental', 'premis')))
);

if ($canDo->get('core.admin'))
{
	JSubMenuHelper::addEntry(
		JText::_('COM_MEMBERS_MENU_IMPORT'),
		'index.php?option=com_members&controller=import',
		($controllerName == 'import' || $controllerName == 'importhooks')
	);
}

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'MembersController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

