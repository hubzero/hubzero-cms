<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Files.SideEffects

namespace Components\Members\Site;

$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'profiles'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
    $controllerName = 'profiles';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
