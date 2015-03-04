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

namespace Components\Support;

if (!\JFactory::getUser()->authorise('core.manage', 'com_support'))
{
	return \JError::raiseWarning(404, \JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
include_once(__DIR__ . DS . 'tables' . DS . 'ticket.php');
include_once(__DIR__ . DS . 'tables' . DS . 'watching.php');
include_once(__DIR__ . DS . 'tables' . DS . 'comment.php');
include_once(__DIR__ . DS . 'tables' . DS . 'message.php');
include_once(__DIR__ . DS . 'tables' . DS . 'resolution.php');
include_once(__DIR__ . DS . 'tables' . DS . 'attachment.php');
include_once(__DIR__ . DS . 'tables' . DS . 'category.php');
include_once(__DIR__ . DS . 'helpers' . DS . 'utilities.php');
include_once(__DIR__ . DS . 'helpers' . DS . 'acl.php');

$controllerName = \JRequest::getCmd('controller', 'tickets');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'tickets';
}

\JSubMenuHelper::addEntry(
	\JText::_('COM_SUPPORT_TICKETS'),
	\JRoute::_('index.php?option=com_support&controller=tickets'),
	$controllerName == 'tickets'
);
\JSubMenuHelper::addEntry(
	\JText::_('COM_SUPPORT_CATEGORIES'),
	\JRoute::_('index.php?option=com_support&controller=categories'),
	$controllerName == 'categories'
);
\JSubMenuHelper::addEntry(
	\JText::_('COM_SUPPORT_QUERIES'),
	\JRoute::_('index.php?option=com_support&controller=queries'),
	$controllerName == 'queries'
);
\JSubMenuHelper::addEntry(
	\JText::_('COM_SUPPORT_MESSAGES'),
	\JRoute::_('index.php?option=com_support&controller=messages'),
	$controllerName == 'messages'
);
\JSubMenuHelper::addEntry(
	\JText::_('COM_SUPPORT_STATUSES'),
	\JRoute::_('index.php?option=com_support&controller=statuses'),
	$controllerName == 'statuses'
);
\JSubMenuHelper::addEntry(
	\JText::_('COM_SUPPORT_ABUSE_REPORTS'),
	\JRoute::_('index.php?option=com_support&controller=abusereports'),
	$controllerName == 'abusereports'
);
\JSubMenuHelper::addEntry(
	\JText::_('COM_SUPPORT_STATS'),
	\JRoute::_('index.php?option=com_support&controller=stats'),
	$controllerName == 'stats'
);
\JSubMenuHelper::addEntry(
	\JText::_('COM_SUPPORT_ACL'),
	\JRoute::_('index.php?option=com_support&controller=acl'),
	$controllerName == 'acl'
);

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

