<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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
