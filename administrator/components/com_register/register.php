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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$option = 'com_register';

// Authorization check
if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl = JFactory::getACL();
	$jacl->addACL($option, 'manage', 'users', 'super administrator');
	$jacl->addACL($option, 'manage', 'users', 'administrator');
	$jacl->addACL($option, 'manage', 'users', 'manager');

	$user = JFactory::getUser();
	if (!$user->authorize($option, 'manage'))
	{
		$app = JFactory::getApplication();
		$app->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
	}
	include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'component.php');
}
else 
{
	if (!JFactory::getUser()->authorise('core.manage', $option)) 
	{
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
	include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'extension.php');
}

// Include scripts
include_once(JPATH_ROOT . DS . 'components' . DS .  $option . DS . 'tables' . DS . 'organizationtype.php');
include_once(JPATH_ROOT . DS . 'components' . DS .  $option . DS . 'tables' . DS . 'organization.php');

$controllerName = JRequest::getCmd('controller', 'config');

if (!file_exists(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'config';
}

JSubMenuHelper::addEntry(
	JText::_('Config'), 
	'index.php?option=' .  $option . '&controller=config',
	($controllerName == 'config')
);
JSubMenuHelper::addEntry(
	JText::_('Organizations'), 
	'index.php?option=' .  $option . '&controller=organizations',
	($controllerName == 'organizations')
);
JSubMenuHelper::addEntry(
	JText::_('Employer Types'), 
	'index.php?option=' .  $option . '&controller=employers', 
	($controllerName == 'employers')
);
JSubMenuHelper::addEntry(
	JText::_('Incremental Registration'), 
	'index.php?option=' .  $option . '&controller=incremental',
	($controllerName == 'incremental')
);
JSubMenuHelper::addEntry(
	JText::_('PREMIS Data Import'), 
	'index.php?option=' .  $option . '&controller=premis',
	($controllerName == 'premis')
);

require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'RegisterController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

