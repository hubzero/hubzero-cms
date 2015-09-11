<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
		$prefix = $this->app['request']->getHost() . ($this->app['request']->getPort() ? ':' . $this->app['request']->getPort() : '');

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
