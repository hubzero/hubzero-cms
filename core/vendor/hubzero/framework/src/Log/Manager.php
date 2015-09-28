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
