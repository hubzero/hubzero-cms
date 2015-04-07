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

use Hubzero\Events\LoaderInterface;
use DirectoryIterator;
use Exception;
use User;
use Lang;

class Loader
{
	/**
	 * A persistent cache of the loaded plugins.
	 *
	 * @var  array
	 */
	protected static $plugins = null;

	/**
	 * Get the plugin data of a specific type if no specific plugin is specified
	 * otherwise only the specific plugin data is returned.
	 *
	 * @param   string  $type    The plugin type, relates to the sub-directory in the plugins directory.
	 * @param   string  $plugin  The plugin name.
	 * @return  mixed   An array of plugin data objects, or a plugin data object.
	 */
	public function load($type, $plugin = null)
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
		$result = $this->load($type, $plugin);

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

			// Load the plugins from the database.
			$plugins = $this->all();

			// Get the specified plugin(s).
			for ($i = 0, $t = count($plugins); $i < $t; $i++)
			{
				if ($plugins[$i]->type == $type && ($plugin === null || $plugins[$i]->name == $plugin))
				{
					self::_import($plugins[$i], $autocreate, $dispatcher);
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
	 * @param   JPlugin      &$plugin     The plugin.
	 * @param   boolean      $autocreate  True to autocreate.
	 * @param   JDispatcher  $dispatcher  Optionally allows the plugin to use a different dispatcher.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	protected static function _import(&$plugin, $autocreate = true, $dispatcher = null)
	{
		static $paths = array();

		$plugin->type = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->type);
		$plugin->name = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->name);

		$path = JPATH_PLUGINS . '/' . $plugin->type . '/' . $plugin->name . '/' . $plugin->name . '.php';

		if (!isset($paths[$path]))
		{
			if (!file_exists($path))
			{
				$paths[$path] = false;
				return;
			}

			if (!isset($paths[$path]))
			{
				require_once $path;
			}

			$paths[$path] = true;

			if ($autocreate)
			{
				// Makes sure we have an event dispatcher
				if (!is_object($dispatcher))
				{
					$dispatcher = Event::getRoot();
				}

				$className = 'plg' . $plugin->type . $plugin->name;
				if (class_exists($className))
				{
					// Instantiate and register the plugin.
					$dispatcher->addListener(new $className((array) $plugin));
				}
			}
		}
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

		$cache = \JFactory::getCache('com_plugins', '');

		$levels = implode(',', User::getAuthorisedViewLevels());

		if (!(self::$plugins = $cache->get($levels)))
		{
			$db = \JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('folder AS type, element AS name, params')
				->from('#__extensions')
				->where('enabled >= 1')
				->where('type =' . $db->Quote('plugin'))
				->where('state >= 0')
				->where('access IN (' . $levels . ')')
				->order('ordering');

			self::$plugins = $db->setQuery($query)->loadObjectList();

			if ($error = $db->getErrorMsg())
			{
				throw new Exception($error, 500);
			}

			$cache->store(self::$plugins, $levels);
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
		return Lang::load(strtolower($extension), $basePath);
	}
}
