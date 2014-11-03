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

if (!JFactory::getUser()->authorise('core.manage', $option))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'forum.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'section.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'category.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'thread.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');

$controllerName = JRequest::getCmd('controller', 'sections');
if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'sections';
}

JSubMenuHelper::addEntry(
	JText::_('COM_FORUM_SECTIONS'),
	'index.php?option=' .  $option . '&controller=sections',
	($controllerName == 'sections')
);
JSubMenuHelper::addEntry(
	JText::_('COM_FORUM_CATEGORIES'),
	'index.php?option=' .  $option . '&controller=categories&section_id=-1',
	($controllerName == 'categories')
);
JSubMenuHelper::addEntry(
	JText::_('COM_FORUM_THREADS'),
	'index.php?option=' .  $option . '&controller=threads&category_id=-1',
	($controllerName == 'threads')
);

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'ForumController' . ucfirst($controllerName);

// initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
