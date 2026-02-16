<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Api\Providers;

use Hubzero\Database\Driver;
use Hubzero\Database\Relational;
use Hubzero\Database\Table;
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
            // @FIXME: this isn't pretty, but it will shim the removal of the old mysql library calls from php
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

            if ($app['config']->get('debug')) {
                $driver->enableDebugging();
            }

            if ($app['config']->get('raw_query_mode')) {
                $driver->setRawQueryMode(
                    $app['config']->get('raw_query_mode')
                );
            }

            return $driver;
        };
    }

    /**
     * Boot the service provider
     *
     * @return  void
     */
    public function boot()
    {
        $app = $this->app;

        Table::setDefaultAccess((int) $app['config']->get('access', 1));

        Relational::setUserIdResolver(function () use ($app) {
            if (isset($app['user'])) {
                return (int) $app['user']->get('id', 0);
            }
            return 0;
        });

        Relational::setUserResolver(function (int $id) {
            if (class_exists('\\Hubzero\\User\\User')) {
                return \Hubzero\User\User::oneOrNew($id);
            }
            return null;
        });
    }
}
