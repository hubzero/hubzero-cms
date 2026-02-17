<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Login\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Login extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{
		require_once dirname(__DIR__) . DS . 'models' . DS . 'login.php';
		require_once __DIR__ . DS . 'controllers' . DS . 'login.php';

		$controller = new Controllers\Login();
		$controller->execute();
	}
}
