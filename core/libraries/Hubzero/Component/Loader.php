<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\Component\Router\Legacy;
use Hubzero\Container\Container;
use Hubzero\Config\Registry;
use ReflectionClass;
use Exception;
use stdClass;

/**
 * Component helper class
 */
class Loader
{
	/**
	 * The application implementation.
	 *
	 * @var  object
	 */
	protected $app;

	/**
	 * The component list cache
	 *
	 * @var  array
	 */
	protected static $components = array();

	/**
	 * The component router cache
	 *
	 * @var  array
	 */
	protected static $routers = array();

	/**
	 * Constructor
	 *
	 * @param   object  $app
	 * @return  void
	 */
	public function __construct(Container $app)
	{
		self::$components = array();
		self::$routers    = array();

		$this->app = $app;
	}

	/**
	 * Checks if the component is enabled
	 *
	 * @param   string   $option  The component option.
	 * @param   boolean  $strict  If set and the component does not exist, false will be returned.
	 * @return  boolean
	 */
	public function isEnabled($option, $strict = false)
	{
		$result = $this->load($option, $strict);

		return ($result->enabled == 1);// | $this->app->isAdmin());
	}

	/**
	 * Gets the parameter object for the component
	 *
	 * @param   string   $option  The option for the component.
	 * @param   boolean  $strict  If set and the component does not exist, false will be returned
	 * @return  object   A Registry object.
	 */
	public function params($option, $strict = false)
	{
		return $this->load($option, $strict)->params;
	}

	/**
	 * Get the base path to a component
	 *
	 * @param   string  $option  The name of the component.
	 * @return  string
	 */
	public function path($option)
	{
		$result = $this->load($option);

		if (!isset($result->path))
		{
			$result->path = '';

			$paths = array(
				PATH_APP . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . substr($result->option, 4),
				PATH_APP . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $result->option,
				PATH_CORE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . substr($result->option, 4),
				PATH_CORE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $result->option
			);

			foreach ($paths as $path)
			{
				if (is_dir($path))
				{
					$result->path = $path;
					break;
				}
			}
		}

		return $result->path;
	}

	/**
	 * Make sure component name follows naming conventions
	 *
	 * @param   string  $option  The element value for the extension
	 * @return  string
	 */
	public function canonical($option)
	{
		if (is_array($option))
		{
			$option = implode('', $option);
		}
		$option = preg_replace('/[^A-Z0-9_\.-]/i', '', $option);
		if (substr($option, 0, strlen('com_')) != 'com_')
		{
			$option = 'com_' . $option;
		}
		return $option;
	}

	/**
	 * Render the component.
	 *
	 * @param   string  $option  The component option.
	 * @param   array   $params  The component parameters
	 * @return  object
	 */
	public function render($option, $params = array())
	{
		$client = (isset($this->app['client']->alias) ? $this->app['client']->alias : $this->app['client']->name);

		// Load template language files.
		$lang = $this->app['language'];
		if ($this->app->has('template'))
		{
			$template = $this->app['template']->template;

			$lang->load('tpl_' . $template, PATH_APP . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . $client . DIRECTORY_SEPARATOR . 'language', null, false, true);
			$lang->load('tpl_' . $template, $this->app['template']->path, null, false, true);
		}

		if (empty($option))
		{
			// Throw 404 if no component
			$this->app->abort(404, $lang->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}

		$option = $this->canonical($option);

		// Record the scope
		$scope = $this->app->has('scope') ? $this->app->get('scope') : null;

		// Set scope to component name
		$this->app->set('scope', $option);

		// Build the component path.
		$file   = substr($option, 4);

		// Get component path
		define('PATH_COMPONENT', $this->path($option) . DIRECTORY_SEPARATOR . $client);
		define('PATH_COMPONENT_SITE', $this->path($option) . DIRECTORY_SEPARATOR . 'site');
		define('PATH_COMPONENT_ADMINISTRATOR', $this->path($option) . DIRECTORY_SEPARATOR . 'admin');

		// Legacy compatibility
		// @TODO: Deprecate this!
		define('JPATH_COMPONENT', PATH_COMPONENT);
		define('JPATH_COMPONENT_SITE', PATH_COMPONENT_SITE);
		define('JPATH_COMPONENT_ADMINISTRATOR', PATH_COMPONENT_ADMINISTRATOR);

		$path      = PATH_COMPONENT . DIRECTORY_SEPARATOR . $file . '.php';
		$namespace = '\\Components\\' . ucfirst(substr($option, 4)) . '\\' . ucfirst($client) . '\\Bootstrap';
		$found     = false;

		// Make sure the component is enabled
		if ($this->isEnabled($option))
		{
			// Check to see if the class is autoload-able
			if (class_exists($namespace))
			{
				$found = true;
				$path  = $namespace;

				// Infer the appropriate language path and load from there
				$file  = with(new \ReflectionClass($namespace))->getFileName();
				$bits  = explode(DIRECTORY_SEPARATOR, $file);
				$local = implode(DIRECTORY_SEPARATOR, array_slice($bits, 0, -1));

				// Load local language files
				$lang->load($option, $local, null, false, true);
			}
			else if (file_exists($path))
			{
				$found = true;

				// Load local language files
				$lang->load($option, PATH_COMPONENT, null, false, true);
			}
		}

		// Make sure we found something
		if (!$found)
		{
			$this->app->abort(404, $lang->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND_OR_ENABLED'));
		}

		// Load base language file
		$lang->load($option, PATH_APP, null, false, true);

		// Handle template preview outlining.
		$contents = null;

		// Execute the component.
		$contents = $this->execute($path);

		// Revert the scope
		$this->app->forget('scope');
		$this->app->set('scope', $scope);

		return $contents;
	}

	/**
	 * Execute the component.
	 *
	 * @param   string  $path  The component path.
	 * @return  string  The component output
	 */
	protected function execute($path)
	{
		ob_start();

		if (file_exists($path))
		{
			$this->executePath($path);
		}
		else
		{
			$this->executeBootstrap($path);
		}

		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Execute the component from an old path based component
	 *
	 * @param   string  $path  The component path
	 * @return  void
	 */
	protected function executePath($path)
	{
		require_once $path;
	}

	/**
	 * Execute the component from a new bootstrapped component
	 *
	 * @param   string  $namespace  The namespace of the component to start
	 * @return  void
	 */
	protected function executeBootstrap($namespace)
	{
		with(new $namespace)->start();
	}

	/**
	 * Get component router
	 *
	 * @param   string  $option  Name of the component
	 * @param   string  $client  Client to load the router for
	 * @return  object  Component router
	 */
	public function router($option, $client = null, $version = null)
	{
		$option = $this->canonical($option);
		$client = ($client ? $client : $this->app['client']->alias);
		$key    = $option . $client;

		if (!isset(self::$routers[$key]))
		{
			$compname = ucfirst(substr($option, 4));

			$client = ucfirst($client);

			$legacy = $compname . 'Router';
			$name   = '\\Components\\' . $compname . '\\' . $client . '\\Router';

			if (!class_exists($name) && !class_exists($legacy))
			{
				// Use the component routing handler if it exists
				$paths = array();
				if (!is_null($version))
				{
					$paths[] = $this->path($option) . DIRECTORY_SEPARATOR . strtolower($client) . DIRECTORY_SEPARATOR . 'routerv' . $version . '.php';
				}
				$paths[] = $this->path($option) . DIRECTORY_SEPARATOR . strtolower($client) . DIRECTORY_SEPARATOR . 'router.php';
				$paths[] = $this->path($option) . DIRECTORY_SEPARATOR . 'router.php';

				// Use the custom routing handler if it exists
				foreach ($paths as $path)
				{
					if (file_exists($path))
					{
						require_once $path;
						break;
					}
				}
			}

			if (class_exists($name))
			{
				$reflection = new ReflectionClass($name);

				if (in_array('Hubzero\\Component\\Router\\RouterInterface', $reflection->getInterfaceNames()))
				{
					self::$routers[$key] = new $name;
				}
			}
			else if (class_exists($legacy))
			{
				$reflection = new ReflectionClass($legacy);

				if (in_array('Hubzero\\Component\\Router\\RouterInterface', $reflection->getInterfaceNames()))
				{
					self::$routers[$key] = new $legacy;
				}
			}

			if (!isset(self::$routers[$key]))
			{
				self::$routers[$key] = new Legacy($compname);
			}
		}

		return self::$routers[$key];
	}

	/**
	 * Load the installed components into the components property.
	 *
	 * @param   string   $option  The element value for the extension
	 * @param   boolean  $strict  If set and the component does not exist, the enabled attribute will be set to false.
	 * @return  object
	 */
	public function load($option, $strict = false)
	{
		$option = $this->canonical($option);

		if (isset(self::$components[$option]))
		{
			return self::$components[$option];
		}

		// Do we have a database connection?
		if ($this->app->has('db'))
		{
			$db = $this->app->get('db');

			$query = $db->getQuery()
				->select('extension_id', 'id')
				->select('element', '"option"')
				->select('params')
				->select('enabled')
				->from('#__extensions')
				->whereEquals('type', 'component')
				->whereEquals('element', $option);

			$db->setQuery($query->toString());

			if (!$this->app->has('cache.store') || !($cache = $this->app['cache.store']))
			{
				$cache = new \Hubzero\Cache\Storage\None();
			}

			if (!($data = $cache->get('_system.' . $option)))
			{
				$data = $db->loadObject();
				$cache->put('_system.' . $option, $data, $this->app['config']->get('cachetime', 15));
			}

			self::$components[$option] = $data;

			if ($error = $db->getErrorMsg())
			{
				throw new Exception($this->app['language']->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_LOADING', $option, $error), 500);
			}
		}

		// Create a default object
		if (empty(self::$components[$option]))
		{
			self::$components[$option] = new stdClass;
			self::$components[$option]->option  = $option;
			self::$components[$option]->enabled = $strict ? 0 : 1;
			self::$components[$option]->params  = '';
			self::$components[$option]->id      = 0;
		}

		// Convert the params to an object.
		if (is_string(self::$components[$option]->params))
		{
			self::$components[$option]->params = new Registry(self::$components[$option]->params);
		}

		return self::$components[$option];
	}
}
