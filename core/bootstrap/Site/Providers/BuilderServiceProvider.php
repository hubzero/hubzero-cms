<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

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
