<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache;

use InvalidArgumentException;
use Closure;

/**
 * Cache manager
 *
 * Inspired by Laravel's Manager class
 * http://laravel.com
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
			$config['cachebase'] = PATH_APP . DS . 'cache' . DS . (isset($this->app['client']->alias) ? $this->app['client']->alias : $this->app['client']->name);
		}

		if (isset($this->customCreators[$name]))
		{
			$config['cache_handler'] = $name;

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

	/**
	 * Get a list of available cache stores.
	 *
	 * @return  array
	 * @since   2.1.12
	 */
	public static function getStores()
	{
		// Instantiate variables
		$stores = [];

		// Get a list of types, only including php files
		$types = glob(__DIR__ . DIRECTORY_SEPARATOR . 'Storage' . DIRECTORY_SEPARATOR . '*.php');

		// Loop through the types and find the ones that are available
		foreach ($types as $type)
		{
			// Get just the file name
			$type = basename($type);

			// Derive the class name from the type
			$class = __NAMESPACE__ . '\\Storage\\' . str_ireplace('.php', '', ucfirst(trim($type)));

			// If the class doesn't exist...these are not the droids you're looking for...
			if (!class_exists($class))
			{
				continue;
			}

			// Our class exists, so now we just need to know if it passes it's test method
			if (call_user_func_array(array($class, 'isAvailable'), array()))
			{
				// Connector names should not have file extensions
				$stores[] = str_ireplace('.php', '', $type);
			}
		}

		$stores = array_map('strtolower', $stores);

		return $stores;
	}
}
