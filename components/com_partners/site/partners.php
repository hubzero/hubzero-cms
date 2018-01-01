<?php
// Declare the namespace.
namespace Components\Partners\Site;

// The "Partner" model pulls in all other models used throughout the
// component(aka the partner_type)
//
// NOTE: We're using the __DIR__ constant. This is a constant
// automatically defined in PHP 5.3+. Its value is the absolute
// path up to the directory that this file is in. Using this 
// instead of a fully, manually delcared path keeps our 
// code a little more flexible, allowing us to move files or even
// entire components with fewer changes.
//important line if you want to use the models
include_once(dirname(__DIR__) . DS . 'models' . DS . 'partner.php');
require_once(dirname(__DIR__) . DS . 'models' . DS . 'partner_type.php');
// Determine which controller we're using.
//
// If no controller is specified, we'll fall back to the "show" 
// controller, seen below as the second argument to getCmd();
$controller = \Request::getCmd('controller', 'partners');

// Make extra sure that controller exists
//
// This is an extra-paranoid check to ensure only an existing
// controller is called. If the specified controller does NOT exist
// we forcefully set the controller name to our default ("show").
//
// Another option might be to simply throw a 404 error. The code below
// has the advantage of making the experience a little smoother to the
// end user but disadvantage in that a technically-incorrect URL will
// resolve to a 200 (success) status.
//
// So, consider the behavior carefully. We're using the default option
// as it'll make development a little smoother for the moment.
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controller . '.php'))
{
	$controller = 'partners';
}
include_once(__DIR__ . DS . 'controllers' . DS . $controller . '.php');

// Build the class name
//
// Class names are namespaced and follow the directory structure:
//
// Components\{Component name}\{Client name}\{Directory name}\{File name}
//
// So, for a controller with the name of "partners" in this component:
//
// /com_partners
//    /site
//        /controllers
//            /partners.php
//
// ... we get the final class name of "Components\Partners\Site\Controllers\Partners".
//
// Typically, directories are plural (controllers, models, tables, helpers).

$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controller));

// Instantiate the controller
$component = new $controllerName();

// This detects the incoming task and executes it if it can. If no task 
// is set, it will execute a default task of "display" which maps to a 
// method of "displayTask" in the controller.
$component->execute();