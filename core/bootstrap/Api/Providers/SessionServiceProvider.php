<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Api\Providers;

use Hubzero\Base\ServiceProvider;
use Hubzero\Session\Manager;
use Hubzero\User\User;
use Hubzero\Config\Registry;

/**
 * Session service provider
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
			$handler = $app['config']->get('session_handler', 'none');

			$options = array(
				'name'          => md5($app['config']->get('secret') . 'site'),
				'cookie_domain' => $app['config']->get('cookie_domain', ''),
				'cookie_path'   => $app['config']->get('cookie_path', '/'),
				// Config time is in minutes so we need to do some
				// math to get seconds.
				'expire'        => ($app['config']->get('lifetime') ? $app['config']->get('lifetime') * 60 : 900)
			);

			// If profiling or debugging is turned on...
			if ($app['config']->get('profile') || $app['config']->get('debug'))
			{
				// If we have a profiler...
				if ($app->has('profiler'))
				{
					$options['profiler'] = $app['profiler'];
				}
			}

			// Skip session writes on the API and command line calls
			if ($app['client']->id == 4 || php_sapi_name() == 'cli')
			{
				$options['skipWrites'] = true;
			}

			$session = new Manager($handler, $options);

			if ($session->getState() == 'expired')
			{
				$session->restart();
			}

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
		if (($this->app['config']->get('session_handler') != 'database' && (time() % 2 || $this->app['session']->isNew()))
		 || ($this->app['config']->get('session_handler') == 'database' && $this->app['session']->isNew()))
		{
			if ($this->app['config']->get('session_handler') == 'database' && $this->app->has('db'))
			{
				$db = $this->app['db'];

				$query = $db->getQuery()
					->select('session_id')
					->from('#__session')
					->whereEquals('session_id', $this->app['session']->getId())
					->limit(1)
					->start(0);

				$db->setQuery($query->toString());
				$exists = $db->loadResult();

				// If the session record doesn't exist initialise it.
				if (!$exists)
				{
					$ip = $this->app['request']->ip();

					if ($this->app['session']->isNew())
					{
						$query = $db->getQuery()
							->insert('#__session')
							->values(array(
								'session_id' => $this->app['session']->getId(),
								'client_id'  => (int) $this->app['client']->id,
								'time'       => (int) time(),
								'ip'         => $ip
							));

						$db->setQuery($query->toString());
					}
					else
					{
						$query = $db->getQuery()
							->insert('#__session')
							->values(array(
								'session_id' => $this->app['session']->getId(),
								'client_id'  => (int) $this->app['client']->id,
								'guest'      => (int) $this->app['user']->get('guest'),
								'time'       => (int) $this->app['session']->get('session.timer.start'),
								'userid'     => (int) $this->app['user']->get('id'),
								'username'   => $this->app['user']->get('username'),
								'ip'         => $ip
							));

						$db->setQuery($query->toString());
					}

					// If the insert failed, log the error.
					if (!$db->execute())
					{
						$this->app->abort(500, $db->getErrorMsg());
					}
				}
			}

			// Session doesn't exist yet, so create session variables
			if ($this->app['session']->isNew())
			{
				$this->app['session']->set('registry', new Registry('session'));
				$this->app['session']->set('user', new User);
			}
		}
	}
}
