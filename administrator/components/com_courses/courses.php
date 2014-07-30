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

$option = 'com_courses';

if (!JFactory::getUser()->authorise('core.manage', $option))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'courses.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'log.php');

$controllerName = JRequest::getCmd('controller', 'courses');
if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'courses';
}

JSubMenuHelper::addEntry(
	JText::_('COM_COURSES_COURSES'),
	'index.php?option=' .  $option . '&controller=courses',
	(!in_array($controllerName, array('students', 'roles', 'pages')))
);
JSubMenuHelper::addEntry(
	JText::_('COM_COURSES_PAGES'),
	'index.php?option=' .  $option . '&controller=pages&course=0',
	$controllerName == 'pages'
);
JSubMenuHelper::addEntry(
	JText::_('COM_COURSES_STUDENTS'),
	'index.php?option=' .  $option . '&controller=students&offering=0&section=0',
	$controllerName == 'students'
);
JSubMenuHelper::addEntry(
	JText::_('COM_COURSES_ROLES'),
	'index.php?option=' .  $option . '&controller=roles',
	$controllerName == 'roles'
);

require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_plugins' . DS . 'helpers' . DS . 'plugins.php');
$canDo = PluginsHelper::getActions();
if ($canDo->get('core.manage'))
{
	JSubMenuHelper::addEntry(
		JText::_('COM_COURSES_PLUGINS'),
		'index.php?option=com_plugins&view=plugins&filter_folder=courses&filter_type=courses'
	);
}

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'CoursesController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
