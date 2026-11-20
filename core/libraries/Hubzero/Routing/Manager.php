<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
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
     * @var  \Hubzero\Base\Application
     */
    protected $app;

    /**
     * List of paths to route rules
     *
     * @var  array<mixed>
     */
    protected $paths = [];

    /**
     * The array of created "drivers".
     *
     * @var  array<string, \Hubzero\Routing\Router>
     */
    protected $routers = [];

    /**
     * Create a new manager instance.
     *
     * @param   \Hubzero\Base\Application  $app
     * @param   array<mixed>   $paths
     * @return  void
     */
    public function __construct($app, $paths = [])
    {
        $this->app   = $app;
        $this->paths = (array) $paths;
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
     * @param   string|null  $client
     * @return  \Hubzero\Routing\Router
     */
    public function client($client = null)
    {
        $client = $client ?: $this->getDefaultClient();

        // If the given driver has not been created before, we will create the instances
        // here and cache it so we can return it next time very quickly. If there is
        // already a driver created by this name, we'll just return that instance.
        if (!isset($this->routers[$client])) {
            $this->routers[$client] = $this->createRouter($client);
        }

        return $this->routers[$client];
    }

    /**
     * Create a new client instance.
     *
     * @param   string  $client
     * @return  \Hubzero\Routing\Router
     */
    protected function createRouter($client)
    {
        $prefix = $this->app['request']->getHttpHost();

        $router = new Router([], $prefix);

        $routes = [];

        foreach ($this->paths as $path) {
            $routes[] = $path . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . $client;
            $routes[] = $path . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . ucfirst($client);
        }

        foreach ($routes as $route) {
            $path = $route . DIRECTORY_SEPARATOR . 'routes.php';

            if (file_exists($path)) {
                require $path;
            }
        }

        return $router;
    }

    /**
     * Get all of the created "routers".
     *
     * @return array<mixed>
     */
    public function getRouters()
    {
        return $this->routers;
    }

    /**
     * Dynamically call the router instance.
     *
     * @param   string  $method
     * @param   array<mixed>   $parameters
     * @return  mixed
     */
    public function __call($method, $parameters)
    {
        return $this->client()->$method(...$parameters);
    }

    /**
     * Get the router for a specific client
     *
     * @param   string  $client  The name of the application.
     * @param   string   $url    Absolute or Relative URI to resource.
     * @param   boolean  $xhtml  Replace & by &amp; for XML compilance.
     * @param   integer|null  $ssl    Secure state for the resolved URI.
     *                             1: Make URI secure using global secure site URI.
     *                             0: Leave URI in the same secure state as it was passed to the function.
     *                            -1: Make URI unsecure using the global unsecure site URI.
     * @return  string   The translated humanly readible URL.
     */
    public function urlForClient($client, $url, $xhtml = true, $ssl = null)
    {
        return $this->client($client)->url($url, $xhtml, $ssl);
    }
}
