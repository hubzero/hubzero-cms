<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cpanel\Admin;

/**
 * Component bootstrap
 */
class Bootstrap
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	public function start()
	{
		// No access check.
		require_once __DIR__ . DS . 'controllers' . DS . 'cpanel.php';

		// Instantiate controller
		$controller = new Controllers\Cpanel();
		$controller->execute();
	}
}
