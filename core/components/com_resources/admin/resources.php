<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

