<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Help\Site;

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'finder.php';
require_once __DIR__ . DS . 'controllers' . DS . 'help.php';

// Instantiate controller and execute
$controller = new Controllers\Help();
$controller->execute();
$controller->redirect();
