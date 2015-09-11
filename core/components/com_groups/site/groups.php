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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Site;

// include tables
require_once dirname(__DIR__) . DS . 'tables' . DS . 'reason.php';

// include models
require_once dirname(__DIR__) . DS . 'models' . DS . 'tags.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'log' . DS . 'archive.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'page' . DS . 'archive.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'module' . DS . 'archive.php';

// include helpers
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'view.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'pages.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'document.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'template.php';

//include abstract controller
require_once __DIR__ . DS . 'controllers' . DS . 'base.php';

//build controller path and name
$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'groups'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'groups';
}
require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller and execute
$controller = new $controllerName();
$controller->execute();
$controller->redirect();