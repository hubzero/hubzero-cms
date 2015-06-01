<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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