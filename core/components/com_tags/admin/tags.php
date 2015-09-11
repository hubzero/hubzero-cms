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

namespace Components\Tags\Admin;

if (!\User::authorise('core.manage', 'com_tags'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once(dirname(__DIR__) . DS . 'models' . DS . 'cloud.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php');

$controllerName = \Request::getCmd('controller', 'entries');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'entries';
}
$task = \Request::getCmd('task', '');

\Submenu::addEntry(
	\Lang::txt('COM_TAGS'),
	\Route::url('index.php?option=com_tags'),
	($controllerName == 'entries')
);
\Submenu::addEntry(
	\Lang::txt('COM_TAGS_RELATIONSHIPS'),
	\Route::url('index.php?option=com_tags&controller=relationships'),
	($controllerName == 'relationships' && $task != 'meta' && $task != 'updatefocusareas')
);
\Submenu::addEntry(
	\Lang::txt('COM_TAGS_FOCUS_AREAS'),
	\Route::url('index.php?option=com_tags&controller=relationships&task=meta'),
	($controllerName == 'relationships' && ($task == 'meta' || $task == 'updatefocusareas'))
);
require_once(dirname(dirname(__DIR__)) . DS . 'com_plugins' . DS . 'admin' . DS . 'helpers' . DS . 'plugins.php');
if (\Components\Plugins\Admin\Helpers\Plugins::getActions()->get('core.manage'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_TAGS_PLUGINS'),
		\Route::url('index.php?option=com_plugins&view=plugins&filter_folder=tags&filter_type=tags')
	);
}

// Include scripts
require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

