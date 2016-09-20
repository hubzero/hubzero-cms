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

namespace Components\Support\Admin;

if (!\User::authorise('core.manage', 'com_support'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'ticket.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'watching.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'comment.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'message.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'attachment.php');
include_once(dirname(__DIR__) . DS . 'tables' . DS . 'category.php');
include_once(dirname(__DIR__) . DS . 'helpers' . DS . 'utilities.php');
include_once(dirname(__DIR__) . DS . 'helpers' . DS . 'acl.php');

$controllerName = \Request::getCmd('controller', 'tickets');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'tickets';
}

\Submenu::addEntry(
	\Lang::txt('COM_SUPPORT_TICKETS'),
	\Route::url('index.php?option=com_support&controller=tickets'),
	$controllerName == 'tickets'
);
\Submenu::addEntry(
	\Lang::txt('COM_SUPPORT_CATEGORIES'),
	\Route::url('index.php?option=com_support&controller=categories'),
	$controllerName == 'categories'
);
\Submenu::addEntry(
	\Lang::txt('COM_SUPPORT_QUERIES'),
	\Route::url('index.php?option=com_support&controller=queries'),
	$controllerName == 'queries'
);
\Submenu::addEntry(
	\Lang::txt('COM_SUPPORT_MESSAGES'),
	\Route::url('index.php?option=com_support&controller=messages'),
	$controllerName == 'messages'
);
\Submenu::addEntry(
	\Lang::txt('COM_SUPPORT_STATUSES'),
	\Route::url('index.php?option=com_support&controller=statuses'),
	$controllerName == 'statuses'
);
\Submenu::addEntry(
	\Lang::txt('COM_SUPPORT_ABUSE'),
	\Route::url('index.php?option=com_support&controller=abusereports'),
	$controllerName == 'abusereports'
);
\Submenu::addEntry(
	\Lang::txt('COM_SUPPORT_STATS'),
	\Route::url('index.php?option=com_support&controller=stats'),
	$controllerName == 'stats'
);
\Submenu::addEntry(
	\Lang::txt('COM_SUPPORT_ACL'),
	\Route::url('index.php?option=com_support&controller=acl'),
	$controllerName == 'acl'
);

require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

