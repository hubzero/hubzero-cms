<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console;

use Hubzero\Base\ServiceProvider;
use Hubzero\Component\Loader;

/**
 * Component loader service provider
 */
class ComponentServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider
     *
     * @return  void
     */
    public function register()
    {
        $this->app['component'] = function ($app) {
            return new Loader($app);
        };
    }
}
