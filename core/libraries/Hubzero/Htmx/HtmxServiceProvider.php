<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Htmx;

use Hubzero\Base\ServiceProvider;

/**
 * HTMX service provider.
 */
class HtmxServiceProvider extends ServiceProvider
{
    /**
     * @return  void
     */
    public function register()
    {
        if (!$this->app->has('htmx')) {
            $this->app['htmx'] = function ($app) {
                return new HtmxService();
            };
        }
    }
}
