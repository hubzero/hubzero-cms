<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cron\Site;

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
		require_once dirname(__DIR__) . DS . 'models' . DS . 'job.php';
		require_once __DIR__ . DS . 'controllers' . DS . 'jobs.php';

		// Instantiate controller
		$controller = new Controllers\Jobs();
		$controller->execute();
	}
}
