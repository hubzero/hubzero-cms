<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
