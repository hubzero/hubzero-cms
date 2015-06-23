<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_HZEXEC_') or die();

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
define('COM_MEDIA_BASE', PATH_CORE.'/'.$params->get('image_path', 'images'));
define('COM_MEDIA_BASEURL', Request::root().'/'.$params->get('image_path', 'images'));

	Lang::load('com_media', dirname(__DIR__) . '/admin', null, false, true)
||	Lang::load('com_media', __DIR__, null, false, true);

// Load the admin HTML view
require_once dirname(__DIR__) . '/admin/helpers/media.php';

// Require the base controller
require_once __DIR__.'/controller.php';

// Make sure the user is authorized to view this page
$app = JFactory::getApplication();
$cmd = Request::getCmd('task', null);

if (strpos($cmd, '.') != false)
{
	// We have a defined controller/task pair -- lets split them out
	list($controllerName, $task) = explode('.', $cmd);

	// Define the controller name and path
	$controllerName = strtolower($controllerName);
	$controllerPath = dirname(__DIR__) . '/admin/controllers/'.$controllerName.'.php';

	// If the controller file path exists, include it ... else lets die with a 500 error
	if (file_exists($controllerPath))
	{
		require_once $controllerPath;
	}
	else
	{
		App::abort(500, Lang::txt('JERROR_INVALID_CONTROLLER'));
	}
}
else {
	// Base controller, just set the task :)
	$controllerName = null;
	$task = $cmd;
}

// Set the name for the controller and instantiate it
$controllerClass = 'MediaController'.ucfirst($controllerName);

if (class_exists($controllerClass))
{
	$controller = new $controllerClass();
}
else {
	App::abort(500, Lang::txt('JERROR_INVALID_CONTROLLER_CLASS'));
}

// Set the model and view paths to the administrator folders
$controller->addViewPath(JPATH_COMPONENT_ADMINISTRATOR.'/views');
$controller->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.'/models');

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
