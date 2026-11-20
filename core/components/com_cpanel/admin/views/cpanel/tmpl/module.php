<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Module;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;

// no direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View $this
 */

$name = Request::getString('module');

if ($name && !User::isGuest()) {
    if (substr($name, 0, 4) != 'mod_') {
        $name = 'mod_' . $name;
    }

    $module = Module::byName($name);
    echo $module ? Module::render($module) : '';
}
