<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Site;

// The only public-facing task is the tokenized unsubscribe link carried in
// every message; everything else lives in the admin.
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'token.php';

$controllerName = 'unsubscribe';

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';

$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);
$controller = new $controllerName();
$controller->execute();
