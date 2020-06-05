<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$name = Request::getString('module');

if ($name && !User::isGuest())
{
	if (substr($name, 0, 4) != 'mod_')
	{
		$name = 'mod_' . $name;
	}

	$module = Module::byName($name);
	echo Module::render($module);
}
