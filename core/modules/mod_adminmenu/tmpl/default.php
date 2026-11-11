<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

/**
 * What this template is given.
 *
 * @var \Modules\Adminmenu\Adminmenu $this
 */

/**
 * Set by the code that includes this file.
 *
 * @var mixed $enabled
 * @var mixed $menu
 */

require $this->getLayoutPath($enabled ? 'default_enabled' : 'default_disabled');

$menu->renderMenu('menu', $enabled ? '' : 'disabled');
