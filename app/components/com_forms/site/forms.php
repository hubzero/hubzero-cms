<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Site;

use Hubzero\Utility\Arr;
use Request;

$componentPath = Component::path('com_forms');
$defaultControllerName = 'forms';
$controllerName = Request::getCmd('controller', $defaultControllerName);
$controllerNameMap = [
	'admin' => 'formsAdmin',
	'emailRespondents' => 'respondentEmails',
	'feedComments' => 'feedComments',
	'fill' => 'fieldResponses',
	'forms' => 'forms',
	'tagResponses' => 'responseTags',
	'pages' => 'formPages',
	'queries' => 'queries',
	'responses' => 'formResponses',
	'steps' => 'formPrereqs',
	'usersPages' => 'usersFormPages',
	'usersSteps' => 'usersFormPrereqs'
];

$mappedName = Arr::getValue($controllerNameMap, $controllerName, $defaultControllerName);
$controllerPath = "$componentPath/site/controllers/$mappedName.php";

if (!file_exists($controllerPath))
{
	$controller = $defaultControllerName;
}

require_once "$componentPath/site/controllers/$mappedName.php";

$namespacedName = __NAMESPACE__ . "\\Controllers\\$mappedName";

$controller = new $namespacedName();
$controller->execute();
