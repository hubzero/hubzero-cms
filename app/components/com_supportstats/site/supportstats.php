<?php

namespace Components\Supportstats\Site;

$defaultControllerName = "outstandingtickets";

$defaultTask = "list";

$controllerName = \Request::getCmd("controller", $defaultControllerName);

if (!file_exists(Component::path('com_supportstats') . "/site/controllers/$controllerName.php"))
{
	$controllerName = $defaultControllerName;
}

include_once(Component::path('com_supportstats') . "/site/controllers/$controllerName.php");

$controllerName = __NAMESPACE__ . "\\Controllers\\" . ucfirst(strtolower($controllerName));

$controller = new $controllerName();

$controller->registerDefaultTask($defaultTask);

$controller->execute();
