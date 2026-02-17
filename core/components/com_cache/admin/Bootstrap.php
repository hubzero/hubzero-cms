<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cache\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component bootstrap
 */
class Bootstrap extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{
		// Access check.
		if (!\User::authorise('core.manage', 'com_cache')) {
		    \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
		    return;
		}

		require_once dirname(__DIR__) . DS . 'models' . DS . 'manager.php';
		require_once dirname(__DIR__) . DS . 'helpers' . DS . 'helper.php';
		require_once __DIR__ . DS . 'controllers' . DS . 'cleanser.php';

		// Instantiate controller
		$controller = new Controllers\Cleanser();
		$controller->execute();
	}
}
