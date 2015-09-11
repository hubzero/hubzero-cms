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

require_once(PATH_CORE . DS . 'components' . DS . 'com_plugins' . DS . 'admin' . DS . 'helpers' . DS . 'plugins.php');
$canDo = \Components\Plugins\Admin\Helpers\Plugins::getActions();
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
