<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

use Hubzero\Auth\Manager;
use Hubzero\Base\ServiceProvider;

/**
 * Authentication service provider
 */
class AuthServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['auth'] = function($app)
		{
			return new Manager($app);
		};
	}
}
