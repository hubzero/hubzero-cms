<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

require $this->getLayoutPath($enabled ? 'default_enabled' : 'default_disabled');

$menu->renderMenu('menu', $enabled ? '' : 'disabled');
