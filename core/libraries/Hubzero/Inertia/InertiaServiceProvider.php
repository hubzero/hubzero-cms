<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Inertia;

use Hubzero\Base\ServiceProvider;

/**
 * Inertia service provider.
 */
class InertiaServiceProvider extends ServiceProvider
{
    /**
     * Register inertia service in container.
     *
     * @return  void
     */
    public function register()
    {
        if (!$this->app->has('inertia')) {
            $this->app['inertia'] = function ($app) {
                return new InertiaService();
            };
        }
    }
}
