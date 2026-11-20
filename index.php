<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

define('PATH_ROOT', $_SERVER['DOCUMENT_ROOT'] ?? __DIR__);
define('PATH_CORE', $_ENV['PATH_CORE'] ?? PATH_ROOT . '/core');

// Check if vendor dependencies are installed
if (!file_exists(PATH_CORE . '/vendor/autoload.php')) {
    // Run the bootstrap installer to handle missing vendor
    require PATH_CORE . '/bootstrap/Install/web/bootstrap.php';
    exit;
}

require PATH_CORE . '/vendor/autoload.php';
require_once PATH_CORE . '/bootstrap/app.php';

(new Hubzero\Base\Application())->run();
