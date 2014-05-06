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

$option = JRequest::getCmd('option', 'com_resources');
$task = JRequest::getWord('task', '');

if (!JFactory::getUser()->authorise('core.manage', $option)) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include jtables
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'resource.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'type.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'assoc.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'review.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'doi.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'contributor.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'license.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'role.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'role.type.php');

// include helpers
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'resources.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'utilities.php');
require_once(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'tags.php');

// include importer
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'import' . DS . 'importer.php';

// get controller name
$controllerName = JRequest::getCmd('controller', 'items');
if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'items';
}

JSubMenuHelper::addEntry(
	JText::_('Resources'),
	'index.php?option=' . $option,
	($controllerName == 'items' && $task != 'orphans')
);
JSubMenuHelper::addEntry(
	JText::_('Orphans'),
	'index.php?option=' . $option . '&controller=items&task=orphans',
	$task == 'orphans'
);
JSubMenuHelper::addEntry(
	JText::_('Types'),
	'index.php?option=' . $option . '&controller=types',
	$controllerName == 'types'
);
JSubMenuHelper::addEntry(
	JText::_('Licenses'),
	'index.php?option=' . $option . '&controller=licenses',
	$controllerName == 'licenses'
);
JSubMenuHelper::addEntry(
	JText::_('Authors'),
	'index.php?option=' . $option . '&controller=authors',
	$controllerName == 'authors'
);
JSubMenuHelper::addEntry(
	JText::_('Roles'),
	'index.php?option=' . $option . '&controller=roles',
	$controllerName == 'roles'
);
JSubMenuHelper::addEntry(
	JText::_('Plugins'),
	'index.php?option=' . $option . '&controller=plugins',
	$controllerName == 'plugins'
);
JSubMenuHelper::addEntry(
	JText::_('Import'),
	'index.php?option=' . $option . '&controller=import',
	$controllerName == 'import'
);
JSubMenuHelper::addEntry(
	JText::_('Import Hooks'),
	'index.php?option=' . $option . '&controller=importhooks',
	$controllerName == 'importhooks'
);

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'ResourcesController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

