<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		'permissions' => 0640,
		'dispatcher'  => null
	);

	/**
	 * Create a new manager instance.
	 *
	 * @param   string  $path
	 * @return  void
	 */
	public function __construct($path = null)
	{
		if ($path)
		{
			$this->defaults['path'] = (string) $path;
		}
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
		if (! isset($this->loggers[$name]))
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
				throw new InvalidArgumentException("Log path not specified for [$name].");
			}

			if (!$config['file'])
			{
				throw new InvalidArgumentException("Log file name not specified for [$name].");
			}

			$log = new Writer(
				new Monolog($name),
				$config['dispatcher']
			);

			$log->useFiles(
				$config['path'] . DIRECTORY_SEPARATOR . $config['file'],
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
