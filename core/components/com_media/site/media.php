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

namespace Components\Media\Site;

use Component;
use Request;
use User;
use Lang;
use App;

$params = Component::params('com_media');

// Make sure the user is authorized to view this page
$asset = Request::getCmd('asset');
$author = Request::getCmd('author');
if (!$asset or
		!User::authorise('core.edit', $asset)
	&&	!User::authorise('core.create', $asset)
	&&	count(User::getAuthorisedCategories($asset, 'core.create')) == 0
	&&	!(User::get('id') == $author && User::authorise('core.edit.own', $asset)))
{
	return App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Set the path definitions
define('COM_MEDIA_BASE', PATH_APP . '/' . $params->get('image_path', 'images'));
define('COM_MEDIA_BASEURL', Request::root().'/'.$params->get('image_path', 'images'));

	Lang::load('com_media', dirname(__DIR__) . '/admin', null, false, true)
||	Lang::load('com_media', __DIR__, null, false, true);

// Load the admin HTML view
require_once dirname(__DIR__) . '/admin/helpers/media.php';

// Make sure the user is authorized to view this page
$cmd = Request::getCmd('task', null);
$controllerName = 'media';

if (strpos($cmd, '.') != false)
{
	// We have a defined controller/task pair -- lets split them out
	list($controllerName, $task) = explode('.', $cmd);

	// Define the controller name and path
	$controllerName = strtolower($controllerName);
	$controllerPath = dirname(__DIR__) . '/admin/controllers/' . $controllerName . '.php';

	// If the controller file path exists, include it ... else lets die with a 500 error
	if (file_exists($controllerPath))
	{
		require_once $controllerPath;
	}
	else
	{
		App::abort(402, Lang::txt('JERROR_INVALID_CONTROLLER'));
	}
}

// Set the name for the controller and instantiate it
$controllerClass = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

if (!class_exists($controllerClass))
{
	App::abort(402, Lang::txt('JERROR_INVALID_CONTROLLER_CLASS'));
}

// Perform the Request task
$controller = new $controllerClass();
$controller->execute();
