<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Whatsnew\Site;

// Include files
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'period.php';
require_once __DIR__ . DS . 'controllers' . DS . 'results.php';

// Instantiate controller
$controller = new Controllers\Results();
$controller->execute();
