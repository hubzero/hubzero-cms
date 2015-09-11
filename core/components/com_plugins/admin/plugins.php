<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Plugins\Admin;

// Access check.
if (!\User::authorise('core.manage', 'com_plugins'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

$task = \Request::getCmd('task');
if (strstr($task, '.'))
{
	\Request::setVar('controller', strstr($task, '.', true));
	\Request::setVar('task', strstr($task, '.'));
}
$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'plugins'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	\App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once(__DIR__ . DS . 'helpers' . DS . 'plugins.php');
require_once(__DIR__ . DS . 'models' . DS . 'plugin.php');
require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
