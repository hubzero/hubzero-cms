<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Site;

// Include publication model
$componentPath = Component::path('com_publications');
$sitePath = "$componentPath/site";

require_once "$componentPath/models/publication.php";
require_once "$componentPath/tables/logs.php";
require_once "$componentPath/helpers/usage.php";
require_once "$componentPath/helpers/resourceMapGenerator.php";

$view = Request::getCmd('view', 'publications');
$controllerName = Request::getCmd('controller', $view);
$task = Request::getCmd('task', $view);

if (!file_exists("$sitePath/controllers/$controllerName.php"))
{
	$controllerName = 'publications';
	Request::setVar('task', $task);
}

require_once "$sitePath/controllers/$controllerName.php";
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
