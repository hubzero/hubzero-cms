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

namespace Hubzero\Cache;

use InvalidArgumentException;
use Closure;

/**
 * Cache manager
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
	 * The array of resolved cache stores.
	 *
	 * @var  array
	 */
	protected $stores = array();

	/**
	 * Create a new Cache manager instance.
	 *
	 * @param   object  $app
	 * @return  void
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}

	/**
	 * Get a cache store instance by name.
	 *
	 * @param   mixed  $name  string|null
	 * @return  mixed
	 */
	public function storage($name = null)
	{
		$name = $name ?: $this->getDefaultDriver();

		return $this->stores[$name] = (isset($this->stores[$name]) ? $this->stores[$name] : $this->resolve($name));
	}

	/**
	 * Resolve the given storage handler.
	 *
	 * @param   string  $name
	 * @return  object
	 */
	protected function resolve($name)
	{
		$config = $this->getConfig($name);

		if (is_null($config))
		{
			throw new InvalidArgumentException('Cache config is not defined.');
		}

		if (!isset($config['hash']))
		{
			$config['hash']      = $this->app->hash('');
		}
		if (!isset($config['cachebase']))
		{
			$config['cachebase'] = PATH_APP . DS . 'app' . DS . 'cache' . DS . (isset($this->app['client']->alias) ? $this->app['client']->alias : $this->app['client']->name);
		}

		if (isset($this->customCreators[$name]))
		{
			return $this->callCustomCreator($config);
		}
		else
		{
			$class = __NAMESPACE__ . '\\Storage\\' . ucfirst($name);

			if (!class_exists($class))
			{
				throw new InvalidArgumentException("Cache store [{$name}] is not defined.");
			}

			return new $class((array) $config);
		}
	}

	/**
	 * Call a custom driver creator.
	 *
	 * @param  array  $config
	 * @return mixed
	 */
	protected function callCustomCreator($config)
	{
		return $this->customCreators[$config['cache_handler']]($config);
	}

	/**
	 * Get the cache connection configuration.
	 *
	 * @param   string  $name
	 * @return  array
	 */
	protected function getConfig($name)
	{
		return $this->app['config']->get($name, array());
	}

	/**
	 * Get the default cache driver name.
	 *
	 * @return  string
	 */
	public function getDefaultDriver()
	{
		return $this->app['config']['cache_handler'];
	}

	/**
	 * Set the default cache driver name.
	 *
	 * @param   string  $name
	 * @return  void
	 */
	public function setDefaultDriver($name)
	{
		$this->app['config']['cache_handler'] = $name;
	}

	/**
	 * Register a custom driver creator Closure.
	 *
	 * @param   string  $driver
	 * @param   object  $callback
	 * @return  object
	 */
	public function extend($driver, Closure $callback)
	{
		$this->customCreators[$driver] = $callback;

		return $this;
	}

	/**
	 * Retrieve an item from the cache by key.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  mixed
	 */
	public function get($key, $default = null)
	{
		$value = $this->storage()->get($key);

		if (!is_null($value))
		{
			return $value;
		}

		return $default instanceof Closure ? $default() : $default;
	}

	/**
	 * Retrieve an item from the cache and delete it.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  mixed
	 */
	public function pull($key, $default = null)
	{
		$value = $this->get($key, $default);

		$this->storage()->forget($key);

		return $value;
	}

	/**
	 * Dynamically call the default driver instance.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->storage(), $method), $parameters);
	}
}
