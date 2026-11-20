<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
