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

namespace Components\Citations\Admin;

if (!\User::authorise('core.manage', 'com_citations'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once (dirname(__DIR__) . DS . 'tables' . DS . 'citation.php');
require_once (dirname(__DIR__) . DS . 'tables' . DS . 'association.php');
require_once (dirname(__DIR__) . DS . 'tables' . DS . 'author.php');
require_once (dirname(__DIR__) . DS . 'tables' . DS . 'secondary.php');
require_once (dirname(__DIR__) . DS . 'tables' . DS . 'tags.php');
require_once (dirname(__DIR__) . DS . 'tables' . DS . 'type.php');
require_once (dirname(__DIR__) . DS . 'tables' . DS . 'sponsor.php');
require_once (dirname(__DIR__) . DS . 'tables' . DS . 'format.php');
require_once (dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php');
require_once (dirname(__DIR__) . DS . 'helpers' . DS . 'format.php');
require_once (dirname(__DIR__) . DS . 'models'  . DS . 'format.php');

$controllerName = \Request::getCmd('controller', 'citations');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'citations';
}

\Submenu::addEntry(
	\Lang::txt('CITATIONS'),
	\Route::url('index.php?option=com_citations&controller=citations'),
	($controllerName == 'citations' && \Request::getVar('task', '') != 'stats')
);
\Submenu::addEntry(
	\Lang::txt('CITATION_STATS'),
	\Route::url('index.php?option=com_citations&controller=citations&task=stats'),
	($controllerName == 'citations' && \Request::getVar('task', '') == 'stats')
);
\Submenu::addEntry(
	\Lang::txt('CITATION_TYPES'),
	\Route::url('index.php?option=com_citations&controller=types'),
	$controllerName == 'types'
);
\Submenu::addEntry(
	\Lang::txt('CITATION_SPONSORS'),
	\Route::url('index.php?option=com_citations&controller=sponsors'),
	$controllerName == 'sponsors'
);
\Submenu::addEntry(
	\Lang::txt('CITATION_FORMAT'),
	\Route::url('index.php?option=com_citations&controller=format'),
	$controllerName == 'format'
);

require_once (__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
