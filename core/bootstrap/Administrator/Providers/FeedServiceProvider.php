<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

use Hubzero\Base\ServiceProvider;

/**
 * Feed Reader service provider
 */
class FeedServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['feed.parser'] = function($app)
		{
			$cache  = PATH_APP . DS . 'cache';
			$cache .= DS . (isset($app['client']->alias) ? $app['client']->alias : $app['client']->name);

			$reader = new \SimplePie();
			$reader->enable_cache(false);
			$reader->force_feed(true);

			return $reader;
		};
	}
}
