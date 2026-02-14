<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Cli\Providers;

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
        $this->app['db'] = function ($app) {
            $options = [
                'driver'   => $app['config']->get('dbtype'),
                'host'     => $app['config']->get('host'),
                'user'     => $app['config']->get('user'),
                'password' => $app['config']->get('password'),
                'database' => $app['config']->get('db'),
                'prefix'   => $app['config']->get('dbprefix')
            ];

            $driver = Driver::getInstance($options);

            if ($app['config']->get('raw_query_mode')) {
                $driver->setRawQueryMode(
                    $app['config']->get('raw_query_mode')
                );
            }

            return $driver;
        };
    }
}
