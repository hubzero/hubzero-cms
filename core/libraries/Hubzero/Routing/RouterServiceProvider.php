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

namespace Hubzero\Routing;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;
use Hubzero\Http\RedirectResponse;

/**
 * Router service provider
 */
class RouterServiceProvider extends Middleware
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['router'] = function($app)
		{
			return new Manager($app);
		};
	}

	/**
	 * Force SSL if site is configured to and
	 * the connection is not secure.
	 *
	 * @return  void
	 */
	public function boot()
	{
		if (($this->app->isSite() && $this->app['config']->get('force_ssl') == 2)
			|| ($this->app->isAdmin() && $this->app['config']->get('force_ssl') >= 1))
		{
			if (!$this->app['request']->isSecure())
			{
				$uri = str_replace('http:', 'https:', $this->app['request']->getUri());

				$redirect = new RedirectResponse($uri);
				$redirect->setRequest($this->app['request']);
				$redirect->send();

				$this->app->close();
			}
		}
	}

	/**
	 * Handle request in HTTP stack
	 * 
	 * @param   object  $request  HTTP Request
	 * @return  mixed
	 */
	public function handle(Request $request)
	{
		if (!$this->app->runningInConsole())
		{
			$this->app['dispatcher']->trigger('system.onBeforeRoute');

			foreach ($this->app['router']->parse($request->getUri()) as $key => $val)
			{
				$request->setVar($key, $val, 'get');
			}

			$this->app['dispatcher']->trigger('system.onAfterRoute');

			if ($this->app->has('profiler'))
			{
				$this->app['profiler'] ? $this->app['profiler']->mark('afterRoute') : null;
			}
		}

		return $this->next($request);
	}
}