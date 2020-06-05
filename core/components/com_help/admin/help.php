<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Help\Admin;

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'finder.php';
require_once __DIR__ . DS . 'controllers' . DS . 'help.php';

// Instantiate controller and execute
$controller = new Controllers\Help();
$controller->execute();
$controller->redirect();
