<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Login\Site;

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
		// Maintian backwards compatibility
		if ($view = \Request::getCmd('view')) {
		    if ($view != 'login' && !\Request::getCmd('task')) {
		        \Request::setVar('task', $view);
		    }
		}

		require_once __DIR__ . '/controllers/auth.php';

		$controller = new Controllers\Auth();
		$controller->execute();
	}
}
