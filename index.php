<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
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

(new Hubzero\Base\Application())->run();
