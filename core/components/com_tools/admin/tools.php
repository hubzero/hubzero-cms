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
\Submenu::addEntry(
	\Lang::txt('COM_TOOLS_HANDLERS'),
	\Route::url('index.php?option=com_tools&controller=handlers'),
	$controllerName == 'handlers'
);

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
