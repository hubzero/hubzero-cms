<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

use Hubzero\Plugin\Loader;
use Hubzero\Events\DispatcherInterface;
use Hubzero\Base\ServiceProvider;

/**
 * Plugin loader service provider
 */
class PluginServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['plugin'] = function($app)
		{
			return new Loader();
		};

		/*$this->app->extend('dispatcher', function($dispatcher, $app)
		{
			$dispatcher->addListenerLoader($app['plugin']);

			return $dispatcher;
		});*/
	}

	/**
	 * Add the plugin loader to the event dispatcher.
	 *
	 * @return  void
	 */
	public function boot()
	{
		if ($this->app->has('dispatcher'))
		{
			$this->app['dispatcher']->addListenerLoader($this->app['plugin']);
		}
	}
}
