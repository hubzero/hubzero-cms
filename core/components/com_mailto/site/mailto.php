<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Mailto\Site;

require_once __DIR__ . '/helpers/mailto.php';
require_once __DIR__ . '/controllers/mailings.php';

$controller = new Controllers\Mailings();
$controller->execute();
