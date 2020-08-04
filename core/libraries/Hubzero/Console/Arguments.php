<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console;

use Hubzero\Console\Config;
use Hubzero\Console\Exception\UnsupportedCommandException;
use Hubzero\Console\Exception\UnsupportedTaskException;
use Hubzero\Console\Exception\InvalidPropertyException;

/**
 * Console arguments class
 **/
class Arguments
{
	/**
	 * Raw command line arguments (PHP $argv)
	 *
	 * @var  array
	 **/
	private $raw = null;

	/**
	 * Class name - command to execute
	 *
	 * @var  string
	 **/
	private $class = null;

	/**
	 * Task name - class method to execute
	 *
	 * @var  string
	 **/
	private $task = null;

	/**
	 * Array of additional options being passed to the command
	 *
	 * @var  array
	 **/
	private $opts = null;

	/**
	 * Registered list of namespaces in which to search for commands
	 *
	 * @var  array
	 **/
	private static $commandNamespaces = [];

	/**
	 * Constructor
	 *
	 * Set raw arguments
	 *
	 * @param   object  $arguments  The command arguments
	 * @return  void
	 **/
	public function __construct($arguments)
	{
		$this->raw = $arguments;
		self::registerNamespace(__NAMESPACE__ . '\\Command');
	}

	/**
	 * Simple getter for class properties
	 *
	 * Throws invalid property exception if property isn't found
	 *
	 * @param   string  $var  The property to retrieve
	 * @return  void
	 **/
	public function get($var)
	{
		if (isset($this->{$var}))
		{
			return $this->{$var};
		}
		else
		{
			throw new InvalidPropertyException("Property {$var} does not exists.");
		}
	}

	/**
	 * Getter for those additional options that a given command may use
	 *
	 * @param   string  $key      Option name to retieve value for
	 * @param   mixed   $default  Default value for option
	 * @return  void
	 **/
	public function getOpt($key, $default = false)
	{
		return (isset($this->opts[$key])) ? $this->opts[$key] : $default;
	}

	/**
	 * Get all opts
	 *
	 * @return  array
	 **/
	public function getOpts()
	{
		return $this->opts;
	}

	/**
	 * Setter for additional options for a given command
	 *
	 * @param   string  $key    The argument to set
	 * @param   mixed   $value  The argument value to give it
	 * @return  void
	 **/
	public function setOpt($key, $value)
	{
		$this->opts[$key] = $value;
	}

	/**
	 * Delete option
	 *
	 * @param   string  $key  The argument to remove
	 * @return  void
	 **/
	public function deleteOpt($key)
	{
		unset($this->opts[$key]);
	}

	/**
	 * Parse the raw arguments into command, task, and additional options
	 *
	 * @return  void
	 **/
	public function parse()
	{
		if (isset($this->raw) && count($this->raw) > 0)
		{
			$class = isset($this->raw[1]) ? $this->raw[1] : 'help';
			$task  = (isset($this->raw[2]) && substr($this->raw[2], 0, 1) != "-") ? $this->raw[2] : 'execute';

			$this->class = self::routeCommand($class);
			$this->task  = self::routeTask($class, $this->class, $task);

			// Parse the remaining args for command options/arguments
			for ($i = 2; $i < count($this->raw); $i++)
			{
				// Ignore the second arg if we used it above as task
				if ($i == 2 && substr($this->raw[$i], 0, 1) != "-")
				{
					continue;
				}

				// Args with an "=" will use the value before as key and the value after as value
				if (strpos($this->raw[$i], "=") !== false)
				{
					$parts = explode("=", $this->raw[$i], 2);
					$key   = preg_replace("/^([-]{1,2})/", "", $parts[0]);
					$value = ($parts[1]);

					if (isset($this->opts[$key]))
					{
						$this->opts[$key] = (array)$this->opts[$key];
						array_push($this->opts[$key], $value);
					}
					else
					{
						$this->opts[$key] = $value;
					}

					continue;
				}
				// Args with a dash but no equals sign will be considered TRUE if present
				elseif (substr($this->raw[$i], 0, 1) == '-')
				{
					// Try to catch clumped arguments (ex: -if as shorthand for -i -f)
					if (preg_match("/^-([[:alpha:]]{2,})/", $this->raw[$i], $matches))
					{
						if (isset($matches[1]))
						{
							foreach (str_split($matches[1], 1) as $k)
							{
								$this->opts[$k] = true;
							}
						}

						continue;
					}
					else
					{
						$key   = preg_replace("/^([-]{1,2})/", "", $this->raw[$i]);
						$value = true;
					}
				}
				// Otherwise, we'll just save the arg as a single word and individual commands may use them
				else
				{
					$key   = $i;
					$value = $this->raw[$i];
				}

				$this->opts[$key] = $value;
			}
		}
	}

	/**
	 * Registers a location to look for commands
	 *
	 * @param   string  $namespace  The namespace location to use
	 * @param   array   $paths      Optional paths to load from
	 * @return  $this
	 **/
	public static function registerNamespace($namespace, $paths = array())
	{
		self::$commandNamespaces[$namespace] = (array)$paths;
	}

	/**
	 * Routes command to the proper file based on the input given
	 *
	 * @param   string  $command  The command to route
	 * @return  void
	 **/
	public static function routeCommand($command = 'help')
	{
		// Aliases take precedence, so parse for them first
		if ($aliases = Config::get('aliases'))
		{
			if (array_key_exists($command, $aliases))
			{
				if (strpos($aliases->$command, '::') !== false)
				{
					$bits      = explode('::', $aliases->$command);
					$command   = $bits[0];
					$aliasTask = $bits[1];
				}
				else
				{
					$command = $aliases->$command;
				}
			}
		}

		foreach (self::$commandNamespaces as $namespace => $paths)
		{
			// Check if we're targeting a namespaced command
			$bits = [];
			if (strpos($command, ':'))
			{
				$bits = explode(':', $command);
			}
			else
			{
				$bits[] = $command;
			}

			$bits = array_map('ucfirst', $bits);

			// Replace any inset placeholders
			for ($i = 0; $i < count($bits); $i++)
			{
				$loc = $i + 1;
				if (strpos($namespace, "{\$$loc}"))
				{
					$namespace = str_replace("{\$$loc}", $bits[$i], $namespace);
					if (!empty($paths))
					{
						foreach ($paths as $p => $path)
						{
							$paths[$p] = str_replace("{\$$loc}", $bits[$i], $path);
						}
					}
					unset($bits[$i]);
				}
			}

			// Add any remaining bits to the end of the command namespace
			if (count($bits) > 0)
			{
				$namespace .= '\\' . implode('\\', $bits);

				if (!empty($paths))
				{
					foreach ($paths as $p => $path)
					{
						$paths[$p] .= '/' . implode('/', $bits);
					}
				}
			}

			// Check for existence
			if (!class_exists($namespace) && !empty($paths))
			{
				foreach ($paths as $path)
				{
					$path = strtolower($path);
					if (file_exists($path . '.php'))
					{
						require_once $path . '.php';
						break;
					}
				}
			}

			if (class_exists($namespace))
			{
				$class = $namespace;
				break;
			}
		}

		if (!isset($class))
		{
			throw new UnsupportedCommandException("Unknown command: {$command}.");
		}

		return $class;
	}

	/**
	 * Routes task to the proper method based on the input given
	 *
	 * @param   string  $command  The command to route
	 * @param   string  $class    The class deduced from routeCommand
	 * @param   string  $task     The task to route
	 * @return  void
	 **/
	public static function routeTask($command, $class, $task = 'execute')
	{
		// Aliases take precedence, so parse for them first
		if ($aliases = Config::get('aliases'))
		{
			if (array_key_exists($command, $aliases))
			{
				if (strpos($aliases->$command, '::') !== false)
				{
					$bits = explode('::', $aliases->$command);
					$task = $bits[1];
				}
			}
		}

		// Make sure task exists
		if (!method_exists($class, $task))
		{
			throw new UnsupportedTaskException("{$class} does not support the {$task} method.");
		}

		return $task;
	}
}
