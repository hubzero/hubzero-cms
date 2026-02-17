<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Login\Admin;

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
		require_once dirname(__DIR__) . DS . 'models' . DS . 'login.php';
		require_once __DIR__ . DS . 'controllers' . DS . 'login.php';

		$controller = new Controllers\Login();
		$controller->execute();
	}
}
