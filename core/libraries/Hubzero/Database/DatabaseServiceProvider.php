<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

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
			// @FIXME: this isn't pretty, but it will shim the removal of the old mysql library calls from php
			$driver = (Config::get('dbtype') == 'mysql') ? 'pdo' : Config::get('dbtype');

			$options = [
				'driver'   => $driver,
				'host'     => Config::get('host'),
				'user'     => Config::get('user'),
				'password' => Config::get('password'),
				'database' => Config::get('db'),
				'prefix'   => Config::get('dbprefix')
			];

			return Driver::getInstance($options);
		};
	}
}
