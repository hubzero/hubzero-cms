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
use Hubzero\Http\RedirectResponse;
use Hubzero\Http\Request;
use Hubzero\Http\Response;

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
	 * Indicates if the application has "booted".
	 *
	 * @var  boolean
	 */
	protected $booted = false;

	/**
	 * Array of core services
	 * 
	 * @var  array
	 */
	private static $baseServices = array(
		'Hubzero\Events\EventServiceProvider',
		'Hubzero\Language\TranslationServiceProvider',
		'Hubzero\Database\DatabaseServiceProvider',
		'Hubzero\Plugin\PluginServiceProvider',
		'Hubzero\Debug\ProfilerServiceProvider',
		'Hubzero\Log\LogServiceProvider',
		'Hubzero\Routing\RouterServiceProvider',

		'Hubzero\Filesystem\FilesystemServiceProvider',
	);

	/**
	 * Array of core container aliases
	 * 
	 * @var  array
	 */
	private static $baseAliases = array(
		'App'        => 'Hubzero\Facades\App',
		'Config'     => 'Hubzero\Facades\Config',
		'Request'    => 'Hubzero\Facades\Request',
		'Response'   => 'Hubzero\Facades\Response',
		'Event'      => 'Hubzero\Facades\Event',
		'Route'      => 'Hubzero\Facades\Route',
		'User'       => 'Hubzero\Facades\User',
		'Lang'       => 'Hubzero\Facades\Lang',
		'Log'        => 'Hubzero\Facades\Log',
		'Date'       => 'Hubzero\Facades\Date',

		'Plugin'     => 'Hubzero\Facades\Plugin',
		'Filesystem' => 'Hubzero\Facades\Filesystem',
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
	public function __construct(Request $request = null, Response $response = null)
	{
		parent::__construct();

		$this['request']  = ($request  ?: Request::createFromGlobals());
		$this['response'] = ($response ?: new Response());

		$this->registerBaseServiceProviders();
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
	public function __call($method, $args)
	{
		$method = strtolower($method);
		if (substr($method, 0, 2) == 'is')
		{
			$client = substr($method, 2);

			$name = $this['client']->name;
			if (isset($this['client']->alias))
			{
				$name = $this['client']->alias;
			}
			return ($name == $client);
		}

		throw new RuntimeException(sprintf('Method [%s] not found.', $method));
	}

	/**
	 * Get the version number of the application.
	 *
	 * @return  string
	 */
	public function version()
	{
		return static::VERSION;
	}

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

		// Since service providers can do more than just register callbacks,
		// we need to track the loaded providers for futher use later in the
		// application.
		$this->markAsRegistered($provider);

		// If the application has already booted, we will call this boot method on
		// the provider class so it has an opportunity to do its boot logic and
		// will be ready for any usage by the developer's application logics.
		if ($this->booted) $this->bootProvider($provider);

		return $this;
	}

	/**
	 * Get the registered service provider instance if it exists.
	 *
	 * @param   mixed  $provider  \Hubzero\Base\ServiceProvider|string
	 * @return  mixed  \Hubzero\Base\ServiceProvider|null
	 */
	/*public function getRegistered($provider)
	{
		$name = is_string($provider) ? $provider : get_class($provider);

		if (array_key_exists($name, $this->serviceProviders))
		{
			return $this->serviceProviders[$name];
		}

		return null;
	}*/

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
	 * @param   object  \Hubzero\Base\ServiceProvider
	 * @return  void
	 */
	protected function markAsRegistered($provider)
	{
		$class = get_class($provider);

		$this->serviceProviders[$class] = $provider;
	}

	/**
	 * Detect the application's current environment.
	 *
	 * @param   array|string  $clients
	 * @return  string
	 */
	public function detectClient($clients)
	{
		$args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;

		return $this['client'] = with(new ClientDetector($this['request']))->detect($clients, $args);
	}

	/**
	 * Determine if we are running in the console.
	 *
	 * @return  bool
	 */
	public function runningInConsole()
	{
		return php_sapi_name() == 'cli';
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
	public function redirect($url, $message = null, $type = 'success')
	{
		$redirect = new RedirectResponse($url); //, $status, $headers);
		$redirect->setRequest($this['request']);

		if ($message && $this->has('notification'))
		{
			$this['notification']->message($message, $type);
		}

		$redirect->send();

		$this->close();
	}

	/**
	 * Terminate the application
	 *
	 * @return  void
	 */
	public function close()
	{
		exit();
	}

	/**
	 * Provides a secure hash based on a seed
	 *
	 * @param   string  $seed  Seed string.
	 * @return  string  A secure hash
	 */
	public function hash($seed)
	{
		return md5($this['config']->get('secret') . $seed);
	}

	/**
	 * Boot the application's service providers.
	 *
	 * @return  void
	 */
	public function boot()
	{
		if ($this->booted) return;

		array_walk($this->serviceProviders, function($p)
		{
			$this->bootProvider($p);
		});

		$this->booted = true;
	}

	/**
	 * Boot the given service provider.
	 *
	 * @param   object  $provider
	 * @return  void
	 */
	protected function bootProvider(ServiceProvider $provider)
	{
		if (method_exists($provider, 'boot'))
		{
			return $provider->boot();
		}
	}

	/**
	 * Get only runnable services
	 * 
	 * @param   array  $layers  Unfiltered services
	 * @return  array  Filtered runnable services
	 */
	protected function middleware($services)
	{
		return array_filter($services, function($service)
		{
			return $service instanceof Middleware;
		});
	}

	/**
	 * Application layer is responsible for dispatching request
	 * 
	 * @param   object  $request  Request object
	 * @return  object  Response object
	 */
	public function handle(Request $request)
	{
		return $this['response']->compress($this['config']->get('gzip', false));
	}

	/**
	 * Run the application and send the response.
	 *
	 * @return  void
	 */
	public function run()
	{
		$app = \JFactory::getApplication($this['client']->name);

		$this->boot();

		if (!$this->runningInConsole())
		{
			$this['dispatcher']->trigger('system.onAfterInitialise');

			if ($this->app->has('profiler') && $this->app->get('profiler'))
			{
				$this->app['profiler']->mark('afterInitialise');
			}
		}

		// Create a new stack and bind to application then
		$this['stack'] = new Stack($this);

		// Send request throught stack and finally send response
		$this['stack']
			->send($this['request'])
			->through($this->middleware($this->serviceProviders))
			->then(function($response)
			{
				$response->send();
			});
	}
}
