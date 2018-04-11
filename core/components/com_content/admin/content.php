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

namespace Components\Content\Admin;

// Access check.
if (!\User::authorise('core.manage', 'com_content')) 
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . '/models/article.php';
require_once __DIR__ . '/helpers/permissions.php';

$task = Request::getCmd('task');
if (strpos($task, '.') !== false)
{
	$splitTask = explode('.', $task);
	Request::setVar('task', $splitTask[1]);
}
$defaultController = 'articles';
$controllerName = Request::getCmd('controller', $defaultController);

\Submenu::addEntry(
	\Lang::txt('COM_CONTENT_ARTICLES'),
	\Route::url('index.php?option=com_content&controller=' . $defaultController),
	($controllerName == $defaultController)
);
\Submenu::addEntry(
	\Lang::txt('COM_CONTENT_SUBMENU_CATEGORIES'),
	\Route::url('index.php?option=com_categories&extension=com_content')
);

if (!file_exists(__DIR__ . '/controllers/' . $controllerName . '.php'))
{
	$controllerName = $defaultController;
}
require_once __DIR__ . '/controllers/' . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

$controller = new $controllerName();
$controller->execute();
