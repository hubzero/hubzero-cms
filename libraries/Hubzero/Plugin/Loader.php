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
use Hubzero\Plugin\Loader\Legacy;

/**
 * Plugin loader for the event dispatcher.
 */
class Loader implements LoaderInterface
{
	/**
	 * The loader name.
	 *
	 * @var  string
	 */
	protected $name = 'plugin';

	/**
	 * The default path.
	 *
	 * @var  string
	 */
	protected $defaultPath;

	/**
	 * A cache of whether namespaces and groups exists.
	 *
	 * @var  array
	 */
	protected $loaded = array();

	/**
	 * A cache of whether namespaces and groups exists.
	 *
	 * @var  array
	 */
	protected $plugins;

	/**
	 * Create a new plugin loader.
	 *
	 * @param   string  $defaultPath
	 * @return  void
	 */
	public function __construct($defaultPath)
	{
		$this->defaultPath = $defaultPath;
	}

	/**
	 * Get the event name.
	 *
	 * @return  string  The event name.
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the path for a plugin group
	 *
	 * @param  string  $group
	 * @return string
	 */
	protected function getPath($group)
	{
		if (is_null($group))
		{
			return $this->defaultPath;
		}

		return $this->defaultPath . DS . $group;
	}

	/**
	 * Load the given configuration group.
	 *
	 * @param   string  $group
	 * @param   string  $name
	 * @return  array
	 */
	public function load($group, $name = null)
	{
		$items = array();

		if (isset($this->loaded[$group]))
		{
			return $items;
		}

		// First we'll get the root path to the plugins group which is
		// where all of the plugin files live for that namespace.
		$path = $this->getPath($group);

		if (!is_dir($path))
		{
			return $items;
		}

		foreach ($this->plugins() as $plugin)
		{
			if ($plugin->type != $group)
			{
				continue;
			}

			if ($name && $plugin->name != $name)
			{
				continue;
			}

			if ($obj = $this->getRequire($plugin))
			{
				$items[] = $obj;
			}
		}

		$this->loaded[$group] = true;

		return $items;
	}

	/**
	 * Loads the published plugins.
	 *
	 * @return  array  An array of published plugins
	 */
	protected function plugins()
	{
		if ($this->plugins !== null)
		{
			return $this->plugins;
		}

		$user  = \JFactory::getUser();
		$cache = \JFactory::getCache('com_plugins', '');

		$levels = implode(',', $user->getAuthorisedViewLevels());

		if (!($this->plugins = $cache->get($levels)))
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

			$this->plugins = $db->setQuery($query)->loadObjectList();

			if ($error = $db->getErrorMsg())
			{
				throw new \Exception($error, 500);
			}

			$cache->store($this->plugins, $levels);
		}

		return $this->plugins;
	}

	/**
	 * Determine if the given group exists.
	 *
	 * @param   string   $group
	 * @param   string   $namespace
	 * @return  boolean
	 */
	public function exists($group)
	{
	}

	/**
	 * Get a plugin object
	 *
	 * @param   object  $plugin
	 * @return  object
	 */
	protected function getRequire($plugin)
	{
		$plugin->type = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->type);
		$plugin->name = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->name);

		$path = $this->getPath($plugin->type) . DS . $plugin->name . DS . $plugin->name . '.php';

		if (!file_exists($path))
		{
			return null;
		}

		require_once $path;

		$className = 'plg' . ucfirst($plugin->type) . ucfirst($plugin->name);
		if (!class_exists($className))
		{
			$className = '\\Plugins\\' . ucfirst($plugin->type) . '\\' . ucfirst($plugin->name);
		}

		$dispatcher = null;

		return new $className(new Legacy, (array) $plugin);
	}
}
