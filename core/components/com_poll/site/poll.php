<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Poll\Site;

// Require the base controller
require_once __DIR__ . DS . 'controllers' . DS . 'polls.php';

$controller = new Controllers\Polls();
$controller->execute();
