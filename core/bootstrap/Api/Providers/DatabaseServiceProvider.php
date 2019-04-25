<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Api\Providers;

use Hubzero\Database\Driver;
use Hubzero\Base\ServiceProvider;

/**
 * Database service provider
 */
class DatabaseServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['db'] = function($app)
		{
			// @FIXME: this isn't pretty, but it will shim the removal of the old mysql_* calls from php
			$driver = ($app['config']->get('dbtype') == 'mysql') ? 'pdo' : $app['config']->get('dbtype');

			$options = [
				'driver'   => $driver,
				'host'     => $app['config']->get('host'),
				'user'     => $app['config']->get('user'),
				'password' => $app['config']->get('password'),
				'database' => $app['config']->get('db'),
				'prefix'   => $app['config']->get('dbprefix')
			];

			$driver = Driver::getInstance($options);

			if ($app['config']->get('debug'))
			{
				$driver->enableDebugging();
			}

			return $driver;
		};
	}
}
