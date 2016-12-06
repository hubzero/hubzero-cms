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

namespace Components\Search\Admin;

// Authorization check
if (!\User::authorise('core.manage', 'com_search'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Get the preferred search mechanism
$controller = Request::get('controller', null);
$engine = Request::getWord('controller', null);

// Prevent HUBgraph from being configured
if (strtolower($engine) == 'hubgraph')
{
	$engine = 'basic';
}

if ($engine != 'basic' && $engine != 'hubgraph')
{
	if ($controller == null)
	{
		$controllerName = \Component::params('com_search')->get('engine', 'basic');
	}
	else
	{
		$controllerName = $controller;
	}
}

if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	\App::abort(404, \Lang::txt('Controller not found'));
}
require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'search.php');
require_once(dirname(__DIR__) . DS . 'models' . DS . 'blacklist.php');
require_once(dirname(__DIR__) . DS . 'models' . DS . 'hubtype.php');
require_once(dirname(__DIR__) . DS . 'models' . DS . 'indexqueue.php');
require_once(dirname(__DIR__) . DS . 'models' . DS . 'facet.php');

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
