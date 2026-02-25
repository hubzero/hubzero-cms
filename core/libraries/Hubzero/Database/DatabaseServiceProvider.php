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
        $this->app['db'] = function ($app) {
            $driver = \Hubzero\Facades\Config::get('dbtype');

            $options = [
                'driver'   => $driver,
                'host'     => \Hubzero\Facades\Config::get('host'),
                'user'     => \Hubzero\Facades\Config::get('user'),
                'password' => \Hubzero\Facades\Config::get('password'),
                'database' => \Hubzero\Facades\Config::get('db'),
                'prefix'   => \Hubzero\Facades\Config::get('dbprefix')
            ];

            return Driver::getInstance($options);
        };
    }
}
