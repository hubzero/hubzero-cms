<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

use Hubzero\Notification\Handler;
use Hubzero\Notification\Storage\Session;
use Hubzero\Base\ServiceProvider;

/**
 * Notification service provider
 */
class NotificationServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['notification'] = function($app)
		{
			return new Handler(
				new Session($app['session'])
			);
		};
	}
}
