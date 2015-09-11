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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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