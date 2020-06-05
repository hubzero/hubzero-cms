<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

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
