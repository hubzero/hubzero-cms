<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Files\Providers;

use Hubzero\Events\Dispatcher;
use Hubzero\Events\Debug\TraceableDispatcher;
use Hubzero\Config\Repository;
use Hubzero\Base\ServiceProvider;

/**
 * Event service provider
 */
class EventServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['dispatcher'] = function($app)
		{
			$dispatcher = new Dispatcher();

			if ($app['config'] instanceof Repository)
			{
				if ($app['config']->get('debug'))
				{
					$dispatcher = new TraceableDispatcher($dispatcher);
				}
			}

			return $dispatcher;
		};
	}
}
