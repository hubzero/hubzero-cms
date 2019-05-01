<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cron\Site;

require_once dirname(__DIR__) . DS . 'models' . DS . 'job.php';
require_once __DIR__ . DS . 'controllers' . DS . 'jobs.php';

// Instantiate controller
$controller = new Controllers\Jobs();
$controller->execute();
