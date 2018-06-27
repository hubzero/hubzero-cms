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

namespace Components\Groups\Admin;

if (!\User::authorise('core.manage', 'com_groups'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'tables' . DS . 'group.php';

// Include tables
require_once dirname(__DIR__) . DS . 'tables' . DS . 'reason.php';

// include models
require_once dirname(__DIR__) . DS . 'models' . DS . 'tags.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'log' . DS . 'archive.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'page' . DS . 'archive.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'module' . DS . 'archive.php';

// Include Helpers
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'gitlab.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'view.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'pages.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'document.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'template.php';

// build controller path
$controllerName = \Request::getCmd('controller', 'manage');
if (!file_exists(__DIR__. DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'manage';
}

\Submenu::addEntry(
	\Lang::txt('COM_GROUPS_MENU_GROUPS'),
	\Route::url('index.php?option=com_groups'),
	($controllerName != 'imports' && $controllerName != 'importhooks' && $controllerName != 'customfields')
);
if (\User::authorise('core.admin', 'com_groups'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_GROUPS_MENU_IMPORT'),
		\Route::url('index.php?option=com_groups&controller=imports'),
		($controllerName == 'imports' || $controllerName == 'importhooks')
	);
	\Submenu::addEntry(
		\Lang::txt('COM_GROUPS_MENU_CUSTOMFIELDS'),
		\Route::url('index.php?option=com_groups&controller=customfields'),
		($controllerName == 'customfields')
	);
}

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
