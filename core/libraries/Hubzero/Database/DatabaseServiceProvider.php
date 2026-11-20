<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
        $this->app['db'] = function ($app) {
            $driver = \Config::get('dbtype');

            $options = [
                'driver'   => $driver,
                'host'     => \Config::get('host'),
                'user'     => \Config::get('user'),
                'password' => \Config::get('password'),
                'database' => \Config::get('db'),
                'prefix'   => \Config::get('dbprefix'),
            ];

            return Driver::getInstance($options);
        };
    }
}
