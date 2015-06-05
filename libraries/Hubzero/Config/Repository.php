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

namespace Hubzero\Config;

use Closure;
use ArrayAccess;

/**
 * Repository class
 *
 *
 */
class Repository extends Registry implements ArrayAccess
{
	/**
	 * The current client type (admin, site, api, etc).
	 *
	 * @var  string
	 */
	protected $client;

	/**
	 * Create a new configuration repository.
	 *
	 * @param   string  $client
	 * @return  void
	 */
	public function __construct($client)
	{
		$this->client = $client;

		// Installation check, and check on removal of the install directory.
		if (!file_exists(PATH_APP . DS . 'configuration.php')
		 || (filesize(PATH_APP . DS . 'configuration.php') < 10))
		{
			if (file_exists(JPATH_INSTALLATION . DS . 'index.php'))
			{
				header('Location: ' . substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], 'index.php')) . 'installation/index.php');
				exit;
			}
			else
			{
				echo 'No configuration file found and no installation code available. Exiting...';
				exit;
			}
		}

		require_once PATH_APP . DS . 'configuration.php';

		if (!class_exists('\\JConfig'))
		{
			echo 'Invalid configuration file. Exiting...';
			exit();
		}

		parent::__construct(new \JConfig);
	}

	/**
	 * Determine if the given configuration value exists.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function has($key)
	{
		$default = microtime(true);

		return $this->get($key, $default) !== $default;
	}

	/**
	 * Determine if the given configuration option exists.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function offsetExists($key)
	{
		return $this->has($key);
	}

	/**
	 * Get a configuration option.
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function offsetGet($key)
	{
		return $this->get($key);
	}

	/**
	 * Set a configuration option.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  void
	 */
	public function offsetSet($key, $value)
	{
		$this->set($key, $value);
	}

	/**
	 * Unset a configuration option.
	 *
	 * @param   string  $key
	 * @return  void
	 */
	public function offsetUnset($key)
	{
		$this->set($key, null);
	}
}
