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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
 *
 * Largely inspired by Joomla's JComponentHelper class.
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

		return ($result->enabled | $this->app->isAdmin());
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
				PATH_APP . DS . 'components' . DS . $result->option,
				PATH_CORE . DS . 'components' . DS . $result->option
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
		// Load template language files.
		$lang = $this->app['language'];
		if ($this->app->has('template'))
		{
			$template = $this->app['template']->template;
			$lang->load('tpl_' . $template, JPATH_BASE, null, false, true);
			$lang->load('tpl_' . $template, JPATH_THEMES . DS . $template, null, false, true);
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

		$client = (isset($this->app['client']->alias) ? $this->app['client']->alias : $this->app['client']->name);

		// Get component path
		if (is_dir(PATH_APP . DS . 'components' . DS . $option . DS . $client))
		{
			// Set path and constants for combined components
			define('JPATH_COMPONENT', PATH_APP . DS . 'components' . DS . $option . DS . $client);
			define('JPATH_COMPONENT_SITE', PATH_APP . DS . 'components' . DS . $option . DS . 'site');
			define('JPATH_COMPONENT_ADMINISTRATOR', PATH_APP . DS . 'components' . DS . $option . DS . 'admin');
		}
		else
		{
			// Set path and constants for combined components
			define('JPATH_COMPONENT', PATH_CORE . DS . 'components' . DS . $option . DS . $client);
			define('JPATH_COMPONENT_SITE', PATH_CORE . DS . 'components' . DS . $option . DS . 'site');
			define('JPATH_COMPONENT_ADMINISTRATOR', PATH_CORE . DS . 'components' . DS . $option . DS . 'admin');
		}

		$path = JPATH_COMPONENT . DS . $file . '.php';

		// If component is disabled throw error
		if (!$this->isEnabled($option) || !file_exists($path))
		{
			$this->app->abort(404, $lang->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}

		// Load common and local language files.
		$lang->load($option, JPATH_COMPONENT, null, false, true) ||
		$lang->load($option, JPATH_BASE, null, false, true);

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
		require_once $path;
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Get component router
	 *
	 * @param   string  $option  Name of the component
	 * @param   string  $client  Client to load the router for
	 * @return  object  Component router
	 */
	public function router($option, $client = null)
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
				$paths[] = $this->path($option) . DS . strtolower($client) . DS . 'router.php';
				$paths[] = $this->path($option) . DS . 'router.php';

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

		$db = $this->app->get('db');
		$query = $db->getQuery(true);
		$query->select('extension_id AS id, element AS "option", params, enabled');
		$query->from('#__extensions');
		$query->where($query->qn('type') . ' = ' . $db->quote('component'));
		$query->where($query->qn('element') . ' = ' . $db->quote($option));
		$db->setQuery($query);

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

		if ($error = $db->getErrorMsg())// || empty(self::$components[$option]))
		{
			throw new Exception($this->app['language']->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_LOADING', $option, $error), 500);
		}

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
