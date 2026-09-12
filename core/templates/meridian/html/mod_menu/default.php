<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die;

/**
 * The navigation is built on the disclosure markup this template inherits
 * from lucent: a list carrying main-nav-bar, each item wrapped in an inner
 * div, and a button beside every link that has pages under it. The module
 * offers that shape behind a parameter, off by default, and the template's
 * styles and scripts address nothing else, so ask for it here rather than
 * leaving each hub to find the setting for itself.
 *
 * The rest of the markup is the module's own.
 */

// phpcs:disable PSR1.Files.SideEffects
$disclosureMenu = true;

require PATH_CORE . '/modules/mod_menu/tmpl/default.php';
