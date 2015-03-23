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

namespace Components\Support\Admin;

if (!\JFactory::getUser()->authorise('core.manage', 'com_support'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'ticket.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'watching.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'comment.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'message.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'resolution.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'attachment.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'category.php');
include_once(dirname(__DIR__) . DS . 'helpers' . DS . 'utilities.php');
include_once(dirname(__DIR__) . DS . 'helpers' . DS . 'acl.php');

$controllerName = \JRequest::getCmd('controller', 'tickets');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'tickets';
}

\JSubMenuHelper::addEntry(
	\Lang::txt('COM_SUPPORT_TICKETS'),
	\Route::url('index.php?option=com_support&controller=tickets'),
	$controllerName == 'tickets'
);
\JSubMenuHelper::addEntry(
	\Lang::txt('COM_SUPPORT_CATEGORIES'),
	\Route::url('index.php?option=com_support&controller=categories'),
	$controllerName == 'categories'
);
\JSubMenuHelper::addEntry(
	\Lang::txt('COM_SUPPORT_QUERIES'),
	\Route::url('index.php?option=com_support&controller=queries'),
	$controllerName == 'queries'
);
\JSubMenuHelper::addEntry(
	\Lang::txt('COM_SUPPORT_MESSAGES'),
	\Route::url('index.php?option=com_support&controller=messages'),
	$controllerName == 'messages'
);
\JSubMenuHelper::addEntry(
	\Lang::txt('COM_SUPPORT_STATUSES'),
	\Route::url('index.php?option=com_support&controller=statuses'),
	$controllerName == 'statuses'
);
\JSubMenuHelper::addEntry(
	\Lang::txt('COM_SUPPORT_ABUSE_REPORTS'),
	\Route::url('index.php?option=com_support&controller=abusereports'),
	$controllerName == 'abusereports'
);
\JSubMenuHelper::addEntry(
	\Lang::txt('COM_SUPPORT_STATS'),
	\Route::url('index.php?option=com_support&controller=stats'),
	$controllerName == 'stats'
);
\JSubMenuHelper::addEntry(
	\Lang::txt('COM_SUPPORT_ACL'),
	\Route::url('index.php?option=com_support&controller=acl'),
	$controllerName == 'acl'
);

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

