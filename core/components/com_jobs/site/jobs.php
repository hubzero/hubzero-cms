<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Site;

include_once \Component::path('com_services') . DS . 'models' . DS . 'service.php';
include_once \Component::path('com_services') . DS . 'models' . DS . 'subscription.php';

require_once dirname(__DIR__) . DS . 'models' . DS . 'job.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'admin.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'application.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'category.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'employer.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'job.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'prefs.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'resume.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'seeker.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'shortlist.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'stats.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'type.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'html.php';

$controllerName = \Request::getCmd('controller', 'jobs');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'jobs';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
