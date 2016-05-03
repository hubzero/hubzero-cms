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

/**
 * Router manager
 */
class Manager
{
	/**
	 * The application instance.
	 *
	 * @var  object
	 */
	protected $app;

	/**
	 * The array of created "drivers".
	 *
	 * @var  array
	 */
	protected $routers = array();

	/**
	 * Create a new manager instance.
	 *
	 * @param   object  $app
	 * @return  void
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}

	/**
	 * Get the default client name.
	 *
	 * @return string
	 */
	public function getDefaultClient()
	{
		return $this->app['client']->name;
	}

	/**
	 * Get a client instance.
	 *
	 * @param   string  $client
	 * @return  object
	 */
	public function client($client = null)
	{
		$client = $client ?: $this->getDefaultClient();

		// If the given driver has not been created before, we will create the instances
		// here and cache it so we can return it next time very quickly. If there is
		// already a driver created by this name, we'll just return that instance.
		if (!isset($this->routers[$client]))
		{
			$this->routers[$client] = $this->createRouter($client);
		}

		return $this->routers[$client];
	}

	/**
	 * Create a new client instance.
	 *
	 * @param   string  $client
	 * @return  object
	 */
	protected function createRouter($client)
	{
		$prefix = $this->app['request']->getHttpHost();

		$router = new Router(array(), $prefix);

		$routes = PATH_CORE . DS . 'bootstrap' . DS . $client .  DS . 'routes.php';

		if (file_exists($routes)) require $routes;

		$routes = PATH_APP . DS . 'bootstrap' . DS . $client .  DS . 'routes.php';

		if (file_exists($routes)) require $routes;

		return $router;
	}

	/**
	 * Get all of the created "routers".
	 *
	 * @return array
	 */
	public function getRouters()
	{
		return $this->routers;
	}

	/**
	 * Dynamically call the router instance.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->client(), $method), $parameters);
	}
}
