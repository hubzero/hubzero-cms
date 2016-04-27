<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

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

			if ($app['config']->get('force_ssl') == 2)
			{
				$options['force_ssl'] = true;
			}

			// If profiling or debugging is turned on...
			if ($app['config']->get('profile') || $app['config']->get('debug'))
			{
				// If we have a profiler and the client is Admin or Site...
				if ($app->has('profiler'))
				{
					$options['profiler'] = $app['profiler'];
				}
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

				$query = $db->getQuery(true);
				$query->select($query->qn('session_id'))
					->from($query->qn('#__session'))
					->where($query->qn('session_id') . ' = ' . $query->q($this->app['session']->getId()));

				$db->setQuery($query, 0, 1);
				$exists = $db->loadResult();

				// If the session record doesn't exist initialise it.
				if (!$exists)
				{
					$query->clear();

					$ip = $this->app['request']->ip();

					if ($this->app['session']->isNew())
					{
						$query->insert($query->qn('#__session'))
							->columns($query->qn('session_id') . ', ' . $query->qn('client_id') . ', ' . $query->qn('time') .  ', ' . $query->qn('ip'))
							->values($query->q($this->app['session']->getId()) . ', ' . (int) $this->app['client']->id . ', ' . $query->q((int) time()) . ', ' . $query->q($ip));
						$db->setQuery($query);
					}
					else
					{
						$query->insert($query->qn('#__session'))
							->columns(
								$query->qn('session_id') . ', ' . $query->qn('client_id') . ', ' . $query->qn('guest') . ', ' .
								$query->qn('time') . ', ' . $query->qn('userid') . ', ' . $query->qn('username') .  ', ' . $query->q('ip')
							)
							->values(
								$query->q($this->app['session']->getId()) . ', ' . (int) $this->app['client']->id . ', ' . (int) $this->app['user']->get('guest') . ', ' .
								$query->q((int) $this->app['session']->get('session.timer.start')) . ', ' . (int) $this->app['user']->get('id') . ', ' . $query->q($this->app['user']->get('username')) .  ', ' . $query->q($ip)
							);

						$db->setQuery($query);
					}

					// If the insert failed, exit the application.
					if ($this->app['client']->id != 4 && !$db->execute())
					{
						exit($db->getErrorMsg());
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
