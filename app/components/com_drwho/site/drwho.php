<?php
// Declare the namespace.
namespace Components\Drwho\Site;

// We'll load in our controller. This exampel component only has
// one controller, so we can declare it specifically. Frequently
// components will have mroe than one controller as a way to help
// group and organize related code.
//
// Controllers are generally plural in name.
include_once __DIR__ . '/controllers/seasons.php';

// Build the class name
//
// Class names are namespaced and follow the directory structure:
//
// Components\{Component name}\{Client name}\{Directory name}\{File name}
//
// So, for a controller with the name of "seasons" in this component:
//
// /com_drwho
//    /site
//        /controllers
//            /seasons.php
//
// ... we get the final class name of "Components\Drwho\Site\Controllers\Seasons".
//
// Typically, directories are plural (controllers, models, tables, helpers).

$controllerName = __NAMESPACE__ . '\\Controllers\\Seasons';

// Instantiate the controller
$component = new $controllerName();

// This detects the incoming task and executes it if it can. If no task 
// is set, it will execute a default task of "display" which maps to a 
// method of "displayTask" in the controller.
$component->execute();
