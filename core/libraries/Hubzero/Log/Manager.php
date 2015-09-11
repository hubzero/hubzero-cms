<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Log;

use InvalidArgumentException;
use Monolog\Logger as Monolog;

/**
 * Log manager
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
	 * The registered log configs
	 *
	 * @var  array
	 */
	protected $setup = array();

	/**
	 * The array of created "loggers".
	 *
	 * @var  array
	 */
	protected $loggers = array();

	/**
	 * The default config values
	 *
	 * @var  array
	 */
	protected $defaults = array(
		'path'        => '',
		'file'        => '',
		'format'      => '',
		'level'       => 'debug',
		'dateFormat'  => 'Y-m-d H:i:s',
		'permissions' => 0640
	);

	/**
	 * Create a new manager instance.
	 *
	 * @param   object  $app
	 * @return  void
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}

	/**
	 * Get the default driver name.
	 *
	 * @return  string
	 */
	public function getDefaultLog()
	{
		return 'debug';
	}

	/**
	 * Get a logger instance.
	 *
	 * @param   string  $name
	 * @return  mixed
	 */
	public function logger($name = null)
	{
		$name = $name ?: $this->getDefaultLog();

		// If the given driver has not been created before, we will create the instances
		// here and cache it so we can return it next time very quickly. If there is
		// already a driver created by this name, we'll just return that instance.
		if ( ! isset($this->loggers[$name]))
		{
			$this->loggers[$name] = $this->createLog($name);
		}

		return $this->loggers[$name];
	}

	/**
	 * Check if a logger exists
	 *
	 * @param   string  $name
	 * @return  boolean
	 */
	public function has($name = null)
	{
		return isset($this->setup[$name]);
	}

	/**
	 * Create a new log instance.
	 *
	 * @param   string  $name
	 * @return  mixed
	 * @throws  \InvalidArgumentException
	 */
	protected function createLog($name)
	{
		if (isset($this->setup[$name]))
		{
			$config = array_merge($this->defaults, $this->setup[$name]);

			if (!$config['path'])
			{
				$config['path'] = $this->app['config']->get('log_path');
				if (is_dir('/var/log/hubzero'))
				{
					$config['path'] = '/var/log/hubzero';
				}
			}

			$log = new Writer(
				new Monolog($this->app['config']->get('application_env')),
				$this->app['dispatcher']
			);

			$log->useFiles(
				$config['path'] . DS . $config['file'],
				$config['level'],
				$config['format'],
				$config['dateFormat'],
				$config['permissions']
			);

			return $log;
		}

		throw new InvalidArgumentException("Log [$name] has no configuration values.");
	}

	/**
	 * Register a custom logger.
	 *
	 * @param   string  $name
	 * @param   array   $settings
	 * @return  $this
	 */
	public function register($name, $settings)
	{
		$this->setup[$name] = (array) $settings;

		return $this;
	}

	/**
	 * Get all of the created "loggers".
	 *
	 * @return  array
	 */
	public function getLoggers()
	{
		return $this->loggers;
	}

	/**
	 * Dynamically call the default log instance.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->logger(), $method), $parameters);
	}
}
