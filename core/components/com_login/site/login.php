<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Login\Site;

require_once __DIR__ . '/controllers/auth.php';

$controller = new Controllers\Auth();
$controller->execute();
