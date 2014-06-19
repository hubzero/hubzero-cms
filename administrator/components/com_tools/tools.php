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
defined('_JEXEC') or die( 'Restricted access' );

$option = JRequest::getCmd('option', 'com_tools');

if (!JFactory::getUser()->authorise('core.manage', $option))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'mw.utils.php');
require_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'tool.php');
require_once(JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'helper.php');

$controllerName = JRequest::getCmd('controller', 'pipeline');
if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'pipeline';
}

JSubMenuHelper::addEntry(
	JText::_('COM_TOOLS_PIPELINE'),
	'index.php?option=com_tools&controller=pipeline',
	$controllerName == 'pipeline'
);
JSubMenuHelper::addEntry(
	JText::_('COM_TOOLS_HOSTS'),
	'index.php?option=com_tools&controller=hosts',
	$controllerName == 'hosts'
);
JSubMenuHelper::addEntry(
	JText::_('COM_TOOLS_HOST_TYPES'),
	'index.php?option=com_tools&controller=hosttypes',
	$controllerName == 'hosttypes'
);
if (JComponentHelper::getParams('com_tools')->get('zones'))
{
	JSubMenuHelper::addEntry(
		JText::_('COM_TOOLS_ZONES'),
		'index.php?option=com_tools&controller=zones',
		$controllerName == 'zones'
	);
}
JSubMenuHelper::addEntry(
	JText::_('COM_TOOLS_SESSIONS'),
	'index.php?option=com_tools&controller=sessions',
	$controllerName == 'sessions'
);

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'ToolsController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
