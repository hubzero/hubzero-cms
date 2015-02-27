<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Tags;

if (!\JFactory::getUser()->authorise('core.manage', 'com_tags'))
{
	return \JError::raiseWarning(404, \JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'cloud.php');
require_once(__DIR__ . DS . 'helpers' . DS . 'permissions.php');

$controllerName = \JRequest::getCmd('controller', 'entries');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'entries';
}
$task = \JRequest::getCmd('task', '');

\JSubMenuHelper::addEntry(
	\JText::_('COM_TAGS'),
	\JRoute::_('index.php?option=com_tags'),
	($controllerName == 'entries')
);
\JSubMenuHelper::addEntry(
	\JText::_('COM_TAGS_RELATIONSHIPS'),
	\JRoute::_('index.php?option=com_tags&controller=relationships'),
	($controllerName == 'relationships' && $task != 'meta' && $task != 'updatefocusareas')
);
\JSubMenuHelper::addEntry(
	\JText::_('COM_TAGS_FOCUS_AREAS'),
	\JRoute::_('index.php?option=com_tags&controller=relationships&task=meta'),
	($controllerName == 'relationships' && ($task == 'meta' || $task == 'updatefocusareas'))
);
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_plugins' . DS . 'helpers' . DS . 'plugins.php');
if (\PluginsHelper::getActions()->get('core.manage'))
{
	\JSubMenuHelper::addEntry(
		\JText::_('COM_TAGS_PLUGINS'),
		\JRoute::_('index.php?option=com_plugins&view=plugins&filter_folder=tags&filter_type=tags')
	);
}

// Include scripts
require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

