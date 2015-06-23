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

namespace Components\Tools\Admin;

if (!\User::authorise('core.manage', 'com_tools'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'utils.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'helper.php');
require_once(dirname(__DIR__) . DS . 'models' . DS . 'tool.php');

$controllerName = \Request::getCmd('controller', 'pipeline');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'pipeline';
}

\Submenu::addEntry(
	\Lang::txt('COM_TOOLS_PIPELINE'),
	\Route::url('index.php?option=com_tools&controller=pipeline'),
	$controllerName == 'pipeline'
);
\Submenu::addEntry(
	\Lang::txt('COM_TOOLS_HOSTS'),
	\Route::url('index.php?option=com_tools&controller=hosts'),
	$controllerName == 'hosts'
);
\Submenu::addEntry(
	\Lang::txt('COM_TOOLS_HOST_TYPES'),
	\Route::url('index.php?option=com_tools&controller=hosttypes'),
	$controllerName == 'hosttypes'
);
if (\Component::params('com_tools')->get('zones'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_TOOLS_ZONES'),
		\Route::url('index.php?option=com_tools&controller=zones'),
		$controllerName == 'zones'
	);
}
\Submenu::addEntry(
	\Lang::txt('COM_TOOLS_SESSIONS'),
	\Route::url('index.php?option=com_tools&controller=sessions'),
	$controllerName == 'sessions'
);
\Submenu::addEntry(
	\Lang::txt('COM_TOOLS_USER_PREFS'),
	\Route::url('index.php?option=com_tools&controller=preferences'),
	$controllerName == 'preferences'
);

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
