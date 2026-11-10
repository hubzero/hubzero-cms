<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/*
 * The constants the application's entry points define, for a test run,
 * which has no entry point. Each is left alone if something defined it
 * first.
 */

if (!defined('_HZEXEC_')) {
    define('_HZEXEC_', true);
}

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('PATH_ROOT')) {
    define('PATH_ROOT', dirname(__DIR__, 2));
}

if (!defined('PATH_CORE')) {
    define('PATH_CORE', dirname(__DIR__));
}

if (!defined('PATH_APP')) {
    define('PATH_APP', PATH_ROOT . DS . 'app');
}
