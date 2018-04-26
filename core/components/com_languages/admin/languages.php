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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Admin;

use Submenu;
use Lang;
use App;

// Access check.
if (!\User::authorise('core.manage', 'com_languages'))
{
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'utilities.php';

$controllerName = \Request::getCmd('controller', 'installed');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	App::abort(404, Lang::txt('Controller not found.'));
}

Submenu::addEntry(
	Lang::txt('COM_LANGUAGES_SUBMENU_INSTALLED_SITE'),
	Route::url('index.php?option=com_languages&controller=installed&client=0'),
	($controllerName == 'installed')
);
Submenu::addEntry(
	Lang::txt('COM_LANGUAGES_SUBMENU_INSTALLED_ADMINISTRATOR'),
	Route::url('index.php?option=com_languages&controller=installed&client=1'),
	($controllerName == 'installed' && Request::getInt('client'))
);
Submenu::addEntry(
	Lang::txt('COM_LANGUAGES_SUBMENU_CONTENT'),
	Route::url('index.php?option=com_languages&controller=languages'),
	($controllerName == 'languages')
);
Submenu::addEntry(
	Lang::txt('COM_LANGUAGES_SUBMENU_OVERRIDES'),
	Route::url('index.php?option=com_languages&controller=overrides'),
	($controllerName == 'overrides')
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// initiate controller
$controller = new $controllerName();
$controller->execute();
