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
 *
 * Calls it does not define are passed to the default storage.
 *
 * @mixin Storage\None
 */
class Manager
{
    /**
     * The application instance (only its services are read)
     *
     * @var  \Hubzero\Container\Container
     */
    protected $app;

    /**
     * The array of resolved cache stores.
     *
     * @var  array<mixed>
     */
    protected $stores = [];

    /**
     * The array of custom creators
     *
     * @var  array<mixed>
     *
     */
    public $customCreators = [];

    /**
     * Create a new Cache manager instance.
     *
     * @param   \Hubzero\Container\Container  $app
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

        if (is_null($config)) {
            throw new InvalidArgumentException('Cache config is not defined.');
        }

        if (!isset($config['hash'])) {
            // As Application::hash('') makes it, for a plain container too
            $config['hash']      = $this->app instanceof \Hubzero\Base\Application
                ? $this->app->hash('')
                : md5((string) $this->app['config']->get('secret'));
        }
        if (!isset($config['cachebase'])) {
            $config['cachebase'] = PATH_APP
                . DS
                . 'cache'
                . DS
                . (isset($this->app['client']->alias) ? $this->app['client']->alias : $this->app['client']->name);
        }

        if (isset($this->customCreators[$name])) {
            $config['cache_handler'] = $name;

            return $this->callCustomCreator($config);
        } else {
            $class = __NAMESPACE__ . '\\Storage\\' . ucfirst($name);

            if (!class_exists($class)) {
                throw new InvalidArgumentException("Cache store [{$name}] is not defined.");
            }

            return new $class((array) $config);
        }
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array<mixed>  $config
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
     * @return  mixed  The store configuration as an array; null or a scalar when it is missing or malformed
     */
    protected function getConfig($name)
    {
        $config = $this->app['config']->get($name, []);

        // A group set at runtime through dot-notation comes back as an
        // object; resolve() indexes the config as an array
        return is_object($config) ? (array) $config : $config;
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
     * @param   \Closure $callback
     * @return  $this
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

        if (!is_null($value)) {
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
     * @param   array<mixed>   $parameters
     * @return  mixed
     */
    public function __call($method, $parameters)
    {
        return $this->storage()->$method(...$parameters);
    }

    /**
     * Get a list of available cache stores.
     *
     * @return  array<mixed>
     * @since   2.1.12
     */
    public static function getStores()
    {
        // Instantiate variables
        $stores = [];

        // Get a list of types, only including php files
        $types = glob(__DIR__ . DIRECTORY_SEPARATOR . 'Storage' . DIRECTORY_SEPARATOR . '*.php');

        // Loop through the types and find the ones that are available
        foreach ($types ?: [] as $type) {
            // Get just the file name
            $type = basename($type);

            // Derive the class name from the type
            $class = __NAMESPACE__ . '\\Storage\\' . str_ireplace('.php', '', ucfirst(trim($type)));

            // If the class doesn't exist...these are not the droids you're looking for...
            if (!class_exists($class)) {
                continue;
            }

            // Our class exists, so now we just need to know if it passes it's test method
            if ($class::isAvailable()) {
                // Connector names should not have file extensions
                $stores[] = str_ireplace('.php', '', $type);
            }
        }

        return array_map('strtolower', $stores);
    }
}
