<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;

/**
 * Authentication service provider
 */
class AuthServiceProvider extends Middleware
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['auth'] = function($app)
		{
			return new Guard($app);
		};
	}

	/**
	 * Handle request in HTTP stack
	 *
	 * @param   object  $request  HTTP Request
	 * @return  mixed
	 */
	public function handle(Request $request)
	{
		$response = $this->next($request);

		// If CLI then we have to gather all query, post and header values
		// into params for Oauth_Provider's constructor.
		$params = array();

		if ($this->app->runningInConsole())
		{
			$queryvars = $this->app['request']->get('queryvars');
			$postvars  = $this->app['request']->get('postdata');

			if (!empty($queryvars))
			{
				foreach ($queryvars as $key => $value)
				{
					if (isset($queryvars[$key]))
					{
						$params[$key] = $queryvars[$key];
					}
					else if (isset($postvars[$key]))
					{
						$params[$key] = $postvars[$key];
					}
				}
			}

			if (!empty($postvars))
			{
				foreach ($postvars as $key => $value)
				{
					if (isset($queryvars[$key]))
					{
						$params[$key] = $queryvars[$key];
					}
					else if (isset($postvars[$key]))
					{
						$params[$key] = $postvars[$key];
					}
				}
			}

			if (empty($params))
			{
				return false;
			}
		}

		$this->app['authn'] = $this->app['auth']->authenticate($params);

		$this->app['request']->setVar('validApiKey', !empty($this->app['authn']['consumer_key']));

		return $response;
	}
}
