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

error_reporting(E_ALL);
@ini_set('display_errors','1');

$jacl =& JFactory::getACL();
$jacl->addACL($option, 'manage', 'users', 'super administrator');
$jacl->addACL($option, 'manage', 'users', 'administrator');
$jacl->addACL($option, 'manage', 'users', 'manager');

// Ensure user has access to this function
$juser =& JFactory::getUser();
if (!$juser->authorize($option, 'manage')) 
{
	$app =& JFactory::getApplication();
	$app->redirect('index.php');
}

// Include scripts
include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'component.php');
include_once(JPATH_ROOT . DS . 'components' . DS .  $option . DS . 'tables' . DS . 'organizationtype.php');
include_once(JPATH_ROOT . DS . 'components' . DS .  $option . DS . 'tables' . DS . 'organization.php');

$controllerName = JRequest::getCmd('controller', 'config');

switch ($controllerName)
{
	case 'employers':
		JSubMenuHelper::addEntry(JText::_('Config'), 'index.php?option=' .  $option . '&controller=config');
		JSubMenuHelper::addEntry(JText::_('Organizations'), 'index.php?option=' .  $option . '&controller=organizations');
		JSubMenuHelper::addEntry(JText::_('Employer Types'), 'index.php?option=' .  $option . '&controller=employers', true);
	break;
	
	case 'organizations':
		JSubMenuHelper::addEntry(JText::_('Config'), 'index.php?option=' .  $option . '&controller=config');
		JSubMenuHelper::addEntry(JText::_('Organizations'), 'index.php?option=' .  $option . '&controller=organizations', true);
		JSubMenuHelper::addEntry(JText::_('Employer Types'), 'index.php?option=' .  $option . '&controller=employers');
	break;
	
	case 'config':
	default:
		JSubMenuHelper::addEntry(JText::_('Config'), 'index.php?option=' .  $option . '&controller=config', true);
		JSubMenuHelper::addEntry(JText::_('Organizations'), 'index.php?option=' .  $option . '&controller=organizations');
		JSubMenuHelper::addEntry(JText::_('Employer Types'), 'index.php?option=' .  $option . '&controller=employers');
	break;
}

require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'RegisterController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

