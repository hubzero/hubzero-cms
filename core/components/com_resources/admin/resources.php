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

namespace Components\Resources\Admin;

$option = \Request::getCmd('option', 'com_resources');
$task = \Request::getWord('task', '');

if (!\User::authorise('core.manage', $option))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include jtables
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'resource.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'type.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'assoc.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'review.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'doi.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'contributor.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'license.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'contributor' . DS . 'role.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'contributor' . DS . 'roletype.php');

// include helpers
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'html.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'utilities.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'tags.php');

// include importer
require_once __DIR__ . DS . 'import' . DS . 'importer.php';

// get controller name
$controllerName = \Request::getCmd('controller', 'items');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'items';
}

\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES'),
	\Route::url('index.php?option=' . $option),
	($controllerName == 'items' && $task != 'orphans')
);
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_ORPHANS'),
	\Route::url('index.php?option=' . $option . '&controller=items&task=orphans'),
	$task == 'orphans'
);
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_TYPES'),
	\Route::url('index.php?option=' . $option . '&controller=types'),
	$controllerName == 'types'
);
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_LICENSES'),
	\Route::url('index.php?option=' . $option . '&controller=licenses'),
	$controllerName == 'licenses'
);
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_AUTHORS'),
	\Route::url('index.php?option=' . $option . '&controller=authors'),
	$controllerName == 'authors'
);
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_ROLES'),
	\Route::url('index.php?option=' . $option . '&controller=roles'),
	$controllerName == 'roles'
);
require_once(dirname(dirname(__DIR__)) . DS . 'com_plugins' . DS . 'admin' . DS . 'helpers' . DS . 'plugins.php');
if (\Components\Plugins\Admin\Helpers\Plugins::getActions()->get('core.manage'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_RESOURCES_PLUGINS'),
		\Route::url('index.php?option=' . $option . '&controller=plugins'),
		$controllerName == 'plugins'
	);
}
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_IMPORT'),
	\Route::url('index.php?option=' . $option . '&controller=import'),
	$controllerName == 'import'
);
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_IMPORTHOOK'),
	\Route::url('index.php?option=' . $option . '&controller=importhooks'),
	$controllerName == 'importhooks'
);

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

