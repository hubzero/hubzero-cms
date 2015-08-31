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

namespace Hubzero\Plugin;

use Hubzero\Events\DispatcherInterface;
use Hubzero\Events\LoaderInterface;
use Hubzero\Config\Registry;
use Exception;
use User;
use Lang;

/**
 * Plugin loader
 *
 * Inspired, in part, by Joomla's JPluginHelper class
 */
class Loader implements LoaderInterface
{
	/**
	 * A persistent cache of the loaded plugins.
	 *
	 * @var  array
	 */
	protected static $plugins = null;

	/**
	 * Get the event name.
	 *
	 * @return  string  The event name.
	 */
	public function getName()
	{
		return 'plugins';
	}

	/**
	 * Get the plugin data of a specific type if no specific plugin is specified
	 * otherwise only the specific plugin data is returned.
	 *
	 * @param   string  $type    The plugin type, relates to the sub-directory in the plugins directory.
	 * @param   string  $plugin  The plugin name.
	 * @return  mixed   An array of plugin data objects, or a plugin data object.
	 */
	public function loadListeners($type)
	{
		$results = array();
		$plugins = (array) $this->byType($type);

		foreach ($plugins as $p)
		{
			if ($result = $this->init($p))
			{
				$results[] = $result;
			}
		}

		return $results;
	}

	/**
	 * Get the params for a specific plugin
	 *
	 * @param   string  $type    The plugin type, relates to the sub-directory in the plugins directory.
	 * @param   string  $plugin  The plugin name.
	 * @return  object
	 */
	public function params($type, $plugin)
	{
		$result = $this->byType($type, $plugin);

		if (!$result || empty($result))
		{
			$result = new stdClass;
			$result->params = '';
		}

		if (is_string($result->params))
		{
			$result->params = new Registry($result->params);
		}

		return $result->params;
	}

	/**
	 * Get the plugin data of a specific type if no specific plugin is specified
	 * otherwise only the specific plugin data is returned.
	 *
	 * @param   string  $type    The plugin type, relates to the sub-directory in the plugins directory.
	 * @param   string  $plugin  The plugin name.
	 * @return  mixed   An array of plugin data objects, or a plugin data object.
	 */
	public function byType($type, $plugin = null)
	{
		$result = array();

		foreach ($this->all() as $p)
		{
			// Is this the right plugin?
			if ($p->type == $type)
			{
				if ($plugin && $p->name == $plugin)
				{
					$result = $p;
					break;
				}

				$result[] = $p;
			}
		}

		return $result;
	}

	/**
	 * Checks if a plugin is enabled.
	 *
	 * @param   string   $type    The plugin type, relates to the sub-directory in the plugins directory.
	 * @param   string   $plugin  The plugin name.
	 * @return  boolean
	 */
	public function isEnabled($type, $plugin = null)
	{
		$result = $this->byType($type, $plugin);

		return (!empty($result));
	}

	/**
	 * Loads all the plugin files for a particular type if no specific plugin is specified
	 * otherwise only the specific plugin is loaded.
	 *
	 * @param   string   $type        The plugin type, relates to the sub-directory in the plugins directory.
	 * @param   string   $plugin      The plugin name.
	 * @param   boolean  $autocreate  Autocreate the plugin.
	 * @param   object   $dispatcher  Optionally allows the plugin to use a different dispatcher.
	 * @return  boolean  True on success.
	 */
	public function import($type, $plugin = null, $autocreate = true, $dispatcher = null)
	{
		static $loaded = array();

		// check for the default args, if so we can optimise cheaply
		$defaults = false;
		if (is_null($plugin) && $autocreate == true && is_null($dispatcher))
		{
			$defaults = true;
		}

		if (!isset($loaded[$type]) || !$defaults)
		{
			$results = null;

			// Makes sure we have an event dispatcher
			if (!($dispatcher instanceof DispatcherInterface))
			{
				$dispatcher = \App::get('dispatcher');
			}

			// Get the specified plugin(s).
			foreach ($this->all() as $plug)
			{
				if ($plug->type == $type && ($plugin === null || $plug->name == $plugin))
				{
					if ($p = $this->init($plug, $autocreate)) //, $dispatcher))
					{
						$dispatcher->addListener($p);
					}
					$results = true;
				}
			}

			// Bail out early if we're not using default args
			if (!$defaults)
			{
				return $results;
			}
			$loaded[$type] = $results;
		}

		return $loaded[$type];
	}

	/**
	 * Loads the plugin file.
	 *
	 * @param   object   $plugin  The plugin data.
	 * @return  boolean  True on success.
	 */
	protected function init(&$plugin, $autocreate = true, $dispatcher = null)
	{
		static $paths = array();

		$plugin->type = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->type);
		$plugin->name = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->name);

		$p = array(
			'app'  => PATH_APP . DS . 'plugins' . DS . $plugin->type . DS . $plugin->name . DS . $plugin->name . '.php',
			'core' => PATH_CORE . DS . 'plugins' . DS . $plugin->type . DS . $plugin->name . DS . $plugin->name . '.php'
		);

		foreach ($p as $path)
		{
			if (!isset($paths[$path]))
			{
				if (!file_exists($path))
				{
					$paths[$path] = false;
					continue;
				}

				if (!isset($paths[$path]))
				{
					require_once $path;
				}

				$paths[$path] = true;

				if ($autocreate)
				{
					// Makes sure we have an event dispatcher
					if (!($dispatcher instanceof DispatcherInterface))
					{
						$dispatcher = new \JDispatcher();
					}

					$className = 'plg' . $plugin->type . $plugin->name;

					if (class_exists($className))
					{
						// Instantiate and register the plugin.
						return new $className($dispatcher, (array) $plugin);
					}
				}
			}
		}

		return null;
	}

	/**
	 * Loads the published plugins.
	 *
	 * @return  array  An array of published plugins
	 */
	public function all()
	{
		if (self::$plugins !== null)
		{
			return self::$plugins;
		}

		if (!\App::has('cache.store') || !($cache = \App::get('cache.store')))
		{
			$cache = new \Hubzero\Cache\Storage\None();
		}

		$levels = implode(',', User::getAuthorisedViewLevels());

		if (!(self::$plugins = $cache->get('com_plugins.' . $levels)))
		{
			$db = \App::get('db');
			$query = $db->getQuery(true);

			$query->select('folder AS type, element AS name, protected, params')
				->from('#__extensions')
				->where('enabled >= 1')
				->where('type =' . $db->quote('plugin'))
				->where('state >= 0')
				->where('access IN (' . $levels . ')')
				->order('ordering');

			self::$plugins = $db->setQuery($query)->loadObjectList();

			if ($error = $db->getErrorMsg())
			{
				throw new Exception($error, 500);
			}

			$cache->put('com_plugins.' . $levels, self::$plugins, \App::get('config')->get('cachetime', 15));
		}

		return self::$plugins;
	}

	/**
	 * Loads the language file for a plugin
	 *
	 * @param   string   $extension  Plugin name
	 * @param   string   $basePath   Path to load from
	 * @return  boolean
	 */
	public function language($extension, $basePath = PATH_CORE)
	{
		return \App::get('language')->load(strtolower($extension), $basePath);
	}
}
