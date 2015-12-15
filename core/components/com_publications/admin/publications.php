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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Admin;

if (!\User::authorise('core.manage', 'com_publications'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once(dirname(__DIR__) . DS . 'models' . DS . 'publication.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php');

// get controller name
$controllerName = \Request::getCmd('controller', 'items');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'items';
}

\Submenu::addEntry(
	\Lang::txt('COM_PUBLICATIONS_PUBLICATIONS'),
	\Route::url('index.php?option=com_publications&controller=items'),
	$controllerName == 'items'
);
\Submenu::addEntry(
	\Lang::txt('COM_PUBLICATIONS_LICENSES'),
	\Route::url('index.php?option=com_publications&controller=licenses'),
	$controllerName == 'licenses'
);
\Submenu::addEntry(
	\Lang::txt('COM_PUBLICATIONS_CATEGORIES'),
	\Route::url('index.php?option=com_publications&controller=categories'),
	$controllerName == 'categories'
);
\Submenu::addEntry(
	\Lang::txt('COM_PUBLICATIONS_MASTER_TYPES'),
	\Route::url('index.php?option=com_publications&controller=types'),
	$controllerName == 'types'
);
\Submenu::addEntry(
	\Lang::txt('COM_PUBLICATIONS_BATCH_CREATE'),
	\Route::url('index.php?option=com_publications&controller=batchcreate'),
	$controllerName == 'batchcreate'
);

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
