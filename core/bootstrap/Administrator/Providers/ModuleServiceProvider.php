<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

use Hubzero\Module\Loader;
use Hubzero\Base\ServiceProvider;

/**
 * Module loader service provider
 */
class ModuleServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['module'] = function($app)
		{
			return new Loader($app, $app['profiler']);
		};
	}
}
