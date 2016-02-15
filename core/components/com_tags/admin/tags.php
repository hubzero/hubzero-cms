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

