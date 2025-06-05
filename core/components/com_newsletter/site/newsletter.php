<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Site;
use Hubzero\Utility\Arr;
use Request;

require_once dirname(__DIR__) . DS . 'models' . DS . 'newsletter.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'mailinglist.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'mailing.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'emailSubscription.php';


require_once dirname(__DIR__) . DS . 'helpers' . DS . 'helper.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'codeHelper.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'subscriptionsHelper.php';

// determine the controller to use:
$defaultController = 'newsletters';

// controllers from the reply functionality
$controllerNameMap = [
	'email-subscriptions' => 'emailsubscriptions',
	'pages' => 'pages',
	'replies' => 'replies'
];

// if we had a controller request, set it, otherwise set 'newsletters':
$controllerName = Request::getCmd('controller', $defaultController);
if (in_array($controllerName, array_keys($controllerNameMap)))
{
	// from reply component
	$controllerName = Arr::getValue($controllerNameMap, $controllerName);
}

//build controller path and require it
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = $defaultController;
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller and execute
$controller = new $controllerName();
$controller->execute();
