<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

use Hubzero\Html\Builder;
use Hubzero\Base\ServiceProvider;

/**
 * HTML Helper service provider
 */
class BuilderServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['html.builder'] = function($app)
		{
			return new Builder();
		};
	}
}
