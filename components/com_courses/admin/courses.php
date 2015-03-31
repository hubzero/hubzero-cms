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

namespace Components\Courses\Admin;

if (!\User::authorise('core.manage', 'com_courses'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'log.php');

$controllerName = \Request::getCmd('controller', 'courses');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'courses';
}

\Submenu::addEntry(
	\Lang::txt('COM_COURSES_COURSES'),
	\Route::url('index.php?option=com_courses&controller=courses'),
	(!in_array($controllerName, array('students', 'roles', 'pages')))
);
\Submenu::addEntry(
	\Lang::txt('COM_COURSES_PAGES'),
	\Route::url('index.php?option=com_courses&controller=pages&course=0'),
	$controllerName == 'pages'
);
\Submenu::addEntry(
	\Lang::txt('COM_COURSES_STUDENTS'),
	\Route::url('index.php?option=com_courses&controller=students&offering=0&section=0'),
	$controllerName == 'students'
);
\Submenu::addEntry(
	\Lang::txt('COM_COURSES_ROLES'),
	\Route::url('index.php?option=com_courses&controller=roles'),
	$controllerName == 'roles'
);

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_plugins' . DS . 'admin' . DS . 'helpers' . DS . 'plugins.php');
$canDo = \PluginsHelper::getActions();
if ($canDo->get('core.manage'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_COURSES_PLUGINS'),
		\Route::url('index.php?option=com_plugins&view=plugins&filter_folder=courses&filter_type=courses')
	);
}

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
