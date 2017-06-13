<?php
namespace Components\Partners\Admin;

// This is a permissions check to make sure the current logged-in
// user has permission to even access this component. Components
// can be blocked from users in a specific access group. This is 
// particularly important for components that can have potentially
// dramatic effects on users and the site (such as the members 
// component or plugin manager).
if (!\User::authorise('core.manage', 'com_partners'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// The "Show" model pulls in all other models used throughout the component.
//
// NOTE: We're using the __DIR__ constant. This is a constant
// automatically defined in PHP 5.3+. Its value is the absolute
// path up to the directory that this file is in.

//this is very important, need to require once in order to access the class, dirname(__dir__) takes you up one level
require_once(dirname(__DIR__) . DS . 'models' . DS . 'partner.php');
require_once(dirname(__DIR__) . DS . 'models' . DS . 'partner_type.php');
// Get the permissions helper.
//
// This is a class that tries into the permissions ACL and 
// we'll use it to determine if the current logged-in user has 
// permission to perform certain actions such as edit, delete, etc.
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php');

// Make extra sure that controller exists
//
// This is an extra-paranoid check to ensure only an existing
// controller is called. If the specified controller does NOT exist
// we forcefully set the controller name to our default ("seasons").
//
// Another option might be to simply throw a 404 error. The code below
// has the advantage of making the experience a little smoother to the
// end user but disadvantage in that a technically-incorrect URL will
// resolve to a 202 (success) status.
//
// So, consider the behavior carefully. We're using the default option
// as it'll make development a little smoother for the moment.
$controllerName = \Request::getCmd('controller', 'partners');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'partners';
}
require_once(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php');

// Add some submenu items
//
// These submenu items are outputted by the toolbar module (mod_toolbar)
// loaded by the template. Since we'll have multiple controllers to manage
// seasons and characters, we'll add a menu item for each controller.
\Submenu::addEntry(
	\Lang::txt('COM_PARTNERS'),
	\Route::url('index.php?option=com_partners&controller=partners'),
	($controllerName == 'partners')
);
\Submenu::addEntry(
	\Lang::txt('COM_PARTNERS_PARTNER_TYPES'),
	\Route::url('index.php?option=com_partners&controller=partner_types'),
	($controllerName == 'partner_types')
);

// Build the class name
//
// Class names are namespaced and follow the directory structure:
//
// Components\{Component name}\{Client name}\{Directory name}\{File name}
//
// So, for a controller with the name of "show" in this component:
//
// /com_partners
//    /site
//        /controllers
//            /partners.php
//
// ... we get the final class name of "Components\Partners\Site\Controllers\Partners".
//
// Typically, directories are plural (controllers, models, tables, helpers).
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();

// This detects the incoming task and executes it if it can. If no task 
// is set, it will execute a default task of "display" which maps to a 
// method of "displayTask" in the controller.
$controller->execute();
