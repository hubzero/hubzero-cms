<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

use Hubzero\Cache\Manager;
use Hubzero\Base\ServiceProvider;

/**
 * Cache service provider
 */
class CacheServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['cache'] = function($app)
		{
			return new Manager($app);
		};

		$this->app['cache.store'] = function($app)
		{
			$handler = !$app['config']->get('caching') ? 'none' : $app['config']->get('cache_handler');

			if ($app->isAdmin())
			{
				$handler = 'none';
			}

			return $app['cache']->storage($handler);
		};
	}
}
