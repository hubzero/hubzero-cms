<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Cli\Providers;

use Hubzero\Base\ServiceProvider;
use Hubzero\Session\Manager;
use Hubzero\User\User;
use Hubzero\Config\Registry;

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
		$this->app['session'] = function($app)
		{
			// Use 'none' handler for CLI - no persistence needed
			$session = new Manager('none', array(
				'name'   => 'cli_session',
				'expire' => 900
			));

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
		// Nothing is persisted to the database for CLI, but the session still
		// has to carry a user and a state registry. Without them
		// User\Manager::getCurrentUser() builds a throwaway User on every call,
		// so identity never sticks, and the User facade finds a null registry
		// and turns getState()/setState() into silent no-ops.
		if (!$this->app['session']->isNew())
		{
			return;
		}

		$this->app['session']->set('registry', new Registry('session'));

		try
		{
			$this->app['session']->set('user', new User);
		}
		catch (\Hubzero\Database\Exception\ConnectionFailedException $e)
		{
			// No database yet, as during install. Leaving the slot empty is
			// fine: getCurrentUser() falls back to a fresh guest User.
		}
	}
}
