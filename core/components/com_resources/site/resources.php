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

namespace Components\Resources\Site;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'resource.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'type.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'assoc.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'review.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'doi.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'usage.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'tags.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'html.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'helper.php');

require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'tool.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'author.php');

$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'resources'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'resources';
}
require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

