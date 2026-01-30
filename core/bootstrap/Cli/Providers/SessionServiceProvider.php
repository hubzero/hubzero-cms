<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Cli\Providers;

use Hubzero\Base\ServiceProvider;
use Hubzero\Session\Manager;

/**
 * Session service provider for CLI
 *
 * CLI commands don't need real sessions (sessions are for web browser state).
 * This provides a minimal in-memory session for compatibility with code that
 * expects $app['session'] to exist, without any database operations.
 */
class SessionServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return  void
     */
    public function register()
    {
        $this->app['session'] = function ($app) {
            // Use 'none' handler for CLI - no persistence needed
            $session = new Manager('none', [
                'name'   => 'cli_session',
                'expire' => 900
            ]);

            return $session;
        };
    }

    /**
     * Boot the service provider.
     *
     * @return  void
     */
    public function boot()
    {
        // No-op for CLI - we don't need to persist sessions to database
        // CLI commands are stateless single-shot executions
    }
}
