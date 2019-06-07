<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

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
