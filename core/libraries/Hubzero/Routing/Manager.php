<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
	 * @var  object
	 */
	protected $app;

	/**
	 * List of paths to route rules
	 *
	 * @var  array
	 */
	protected $paths = array();

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
	 * @param   array   $paths
	 * @return  void
	 */
	public function __construct($app, $paths = array())
	{
		$this->app   = $app;
		$this->paths = (array)$paths;
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

		$routes = array();

		foreach ($this->paths as $path)
		{
			$routes[] = $path . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . $client;
			$routes[] = $path . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . ucfirst($client);
		}

		foreach ($routes as $route)
		{
			$path = $route . DIRECTORY_SEPARATOR . 'routes.php';

			if (file_exists($path))
			{
				require $path;
			}
		}

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

	/**
	 * Get the router for a specific client
	 *
	 * @param   string  $client  The name of the application.
	 * @param   string   $url    Absolute or Relative URI to resource.
	 * @param   boolean  $xhtml  Replace & by &amp; for XML compilance.
	 * @param   integer  $ssl    Secure state for the resolved URI.
	 *                             1: Make URI secure using global secure site URI.
	 *                             0: Leave URI in the same secure state as it was passed to the function.
	 *                            -1: Make URI unsecure using the global unsecure site URI.
	 * @return  The translated humanly readible URL.
	 */
	public function urlForClient($client, $url, $xhtml = true, $ssl = null)
	{
		return $this->client($client)->url($url, $xhtml, $ssl);
	}
}
