<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$option = 'com_forum';

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

	require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'section.php');
	require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'category.php');
	require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'thread.php');
}

require_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'section.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'forum.php');

$controllerName = JRequest::getCmd('controller', 'sections');
if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'sections';
}
switch ($controllerName)
{
	case 'sections':
		JSubMenuHelper::addEntry(JText::_('Sections'), 'index.php?option=' .  $option . '&controller=sections', true);
		JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=' .  $option . '&controller=categories&section_id=-1');
		JSubMenuHelper::addEntry(JText::_('Threads'), 'index.php?option=' .  $option . '&controller=threads&category_id=-1');
	break;
	
	case 'categories':
		JSubMenuHelper::addEntry(JText::_('Sections'), 'index.php?option=' .  $option . '&controller=sections');
		JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=' .  $option . '&controller=categories&section_id=-1', true);
		JSubMenuHelper::addEntry(JText::_('Threads'), 'index.php?option=' .  $option . '&controller=threads&category_id=-1');
	break;
	
	case 'threads':
	default:
		JSubMenuHelper::addEntry(JText::_('Sections'), 'index.php?option=' .  $option . '&controller=sections');
		JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=' .  $option . '&controller=categories&section_id=-1');
		JSubMenuHelper::addEntry(JText::_('Threads'), 'index.php?option=' .  $option . '&controller=threads&category_id=-1', true);
	break;
}
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'ForumController' . ucfirst($controllerName);

// initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
