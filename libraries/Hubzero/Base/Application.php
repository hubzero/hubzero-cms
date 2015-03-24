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

namespace Hubzero\Base;

use Hubzero\Container\Container;
use Hubzero\Error\Exception\NotAuthorizedException;
use Hubzero\Error\Exception\NotFoundException;
use Hubzero\Error\Exception\RuntimeException;
use Hubzero\Facades\Facade;
use Hubzero\Http\Request;

/**
 * Application class
 */
class Application extends Container
{
	/**
	 * The framework version.
	 *
	 * @var  string
	 */
	const VERSION = '2.0.0-dev';

	/**
	 * Array of core services
	 * 
	 * @var  array
	 */
	private static $baseServices = array(
		'Hubzero\Events\EventServiceProvider',
		'Hubzero\Routing\RouterServiceProvider',
		'Hubzero\Log\LogServiceProvider',
		'Hubzero\Component\ComponentServiceProvider',
	);

	/**
	 * Array of core container aliases
	 * 
	 * @var  array
	 */
	private static $baseAliases = array(
		'App'       => 'Hubzero\Facades\App',
		'Config'    => 'Hubzero\Facades\Config',
		'Request'   => 'Hubzero\Facades\Request',
		'Route'     => 'Hubzero\Facades\Route',
		'User'      => 'Hubzero\Facades\User',
		'Lang'      => 'Hubzero\Facades\Lang',
		'Log'       => 'Hubzero\Facades\Log',
		'Component' => 'Hubzero\Facades\Component',
	);

	/**
	 * All of the registered service providers.
	 *
	 * @var  array
	 */
	protected $serviceProviders = array();

	/**
	 * Create a new application instance.
	 *
	 * @return  void
	 */
	public function __construct(Request $request = null)
	{
		parent::__construct();

		$this['request'] = ($request ?: Request::createFromGlobals());

		$this->registerBaseServiceProviders();

		//$this->registerBaseFacades();
	}

	/**
	 * Dynamically access application services.
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function __get($key)
	{
		return $this[$key];
	}

	/**
	 * Dynamically set application services.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  void
	 */
	public function __set($key, $value)
	{
		$this[$key] = $value;
	}

	/**
	 * Handle dynamic, static calls to the object.
	 *
	 * @param   string  $method
	 * @param   array   $args
	 * @return  mixed
	 */
	/*public function __call($method, $args)
	{
		$method = strtolower($method);
		if (substr($method, 0, 2) == 'is')
		{
			$client = substr($method, 2);
			return ($this['client'] == $client);
		}

		throw new RuntimeException(sprintf('Method [%s] not found.', $method));
	}*/

	/**
	 * Register all of the base service providers.
	 *
	 * @return  void
	 */
	protected function registerBaseServiceProviders()
	{
		// Load all services we know of now
		foreach (static::$baseServices as $service)
		{
			$this->register(new $service($this));
		}
	}

	/**
	 * Register facades with the autoloader
	 * 
	 * @return  void
	 */
	public function registerBaseFacades($aliases = array())
	{
		// Set the application to resolve Facades
		Facade::setApplication($this);

		// Create aliaes for runtime
		Facade::createAliases(array_merge(
			static::$baseAliases,
			$aliases
		));
	}

	/**
	 * Register a service provider with the application.
	 *
	 * @param  mixed   $provider  \Hubzero\Base\ServiceProvider|string
	 * @param  array   $options
	 * @param  bool    $force
	 * @return object
	 */
	public function register($provider, $options = array()) //, $force = false)
	{
		/*if ($registered = $this->getRegistered($provider) && !$force)
		{
			return $registered;
		}*/

		// If the given "provider" is a string, we will resolve it, passing in the
		// application instance automatically for the developer. This is simply
		// a more convenient way of specifying your service provider classes.
		if (is_string($provider))
		{
			$provider = $this->resolveProviderClass($provider);
		}

		$provider->register();

		// Once we have registered the service we will iterate through the options
		// and set each of them on the application so they will be available on
		// the actual loading of the service objects and for developer usage.
		foreach ($options as $key => $value)
		{
			$this[$key] = $value;
		}

		return $this;
	}

	/**
	 * Get the registered service provider instance if it exists.
	 *
	 * @param   mixed  $provider  \Hubzero\Base\ServiceProvider|string
	 * @return  mixed  \Hubzero\Base\ServiceProvider|null
	 */
	public function getRegistered($provider)
	{
		$name = is_string($provider) ? $provider : get_class($provider);

		if (array_key_exists($name, $this->serviceProviders))
		{
			return $this->serviceProviders[$name];
		}

		return null;
	}

	/**
	 * Resolve a service provider instance from the class name.
	 *
	 * @param   string  $provider
	 * @return  object  \Hubzero\Base\ServiceProvider
	 */
	protected function resolveProviderClass($provider)
	{
		return new $provider($this);
	}

	/**
	 * Mark the given provider as registered.
	 *
	 * @param  object  \Hubzero\Base\ServiceProvider
	 * @return void
	 */
	protected function markAsRegistered($provider)
	{
		$class = get_class($provider);

		$this->serviceProviders[$class] = $provider;
	}

	/**
	 * Detect the application's current environment.
	 *
	 * @param  array|string  $clients
	 * @return string
	 */
	public function detectClient($clients)
	{
		$args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;

		return $this['client'] = with(new ClientDetector($_SERVER['REQUEST_URI']))->detect($clients, $args);
	}

	/**
	 * Abort
	 *
	 * @param   integer  $code
	 * @param   string   $message
	 * @return  void
	 */
	public function abort($code, $message='')
	{
		switch ($code)
		{
			case 404:
				throw new NotFoundException($message, $code);
			break;

			case 403:
				throw new NotAuthorizedException($message, $code);
			break;

			default:
				throw new RuntimeException($message, $code);
			break;
		}
	}

	/**
	 * Redirect current request to new request (sub requests)
	 * 
	 * @param   string  $url     Url to redirect to
	 * @param   string  $message  Message to display on redirect.
	 * @param   array   $type     Message type.
	 * @return  void
	 */
	public function redirect($url, $message = null, $type = null)
	{
		\JFactory::getApplication()->redirect($url, $message, $type);
	}

	/**
	 * Run the application and send the response.
	 *
	 * @return void
	 */
	public function run()
	{
		if (JPROFILE)
		{
			$_PROFILER = \JProfiler::getInstance($this['client']);
		}

		$app = \JFactory::getApplication($this['client']);

		// Mark afterLoad in the profiler.
		JPROFILE ? $_PROFILER->mark('afterLoad') : null;

		// Initialise the application.
		$app->initialise(
			$app->isAdmin() ? array('language' => $app->getUserState('application.lang')) : array()
		);

		// Mark afterIntialise in the profiler.
		JPROFILE ? $_PROFILER->mark('afterInitialise') : null;

		// Route the application.
		$app->route();

		// Mark afterRoute in the profiler.
		JPROFILE ? $_PROFILER->mark('afterRoute') : null;

		// Dispatch the application.
		$app->dispatch();

		// Mark afterDispatch in the profiler.
		JPROFILE ? $_PROFILER->mark('afterDispatch') : null;

		// Render the application.
		$app->render();

		// Mark afterRender in the profiler.
		JPROFILE ? $_PROFILER->mark('afterRender') : null;

		// Return the response.
		echo $app;
	}
}
