<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html;

use Hubzero\Error\Exception\InvalidArgumentException;

/**
 * Utility class for all HTML drawing classes
 *
 * Inspired, in part, by Joomla's JHtml class
 */
class Builder
{
	/**
	 * An array to hold included paths
	 *
	 * @var  array
	 */
	protected static $paths = array();

	/**
	 * An array to hold method references
	 *
	 * @var  array
	 */
	protected static $registry = array();

	/**
	 * Class loader method
	 *
	 * Additional arguments may be supplied and are passed to the sub-class.
	 * Additional include paths are also able to be specified for third-party use
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	public function __call($method, $parameters)
	{
		$func = array_shift($parameters);
		$key  = $method . '.' . $func;

		if (!array_key_exists($key, static::$registry))
		{
			$cls  = __NAMESPACE__ . '\\Builder\\' . ucfirst($method);

			if (!class_exists($cls))
			{
				$cls = $this->find($method);

				if (!class_exists($cls))
				{
					throw new InvalidArgumentException(sprintf('%s %s not found.', $cls, $func), 500);
				}
			}

			$callable = array($cls, $func);

			if (!is_callable($callable))
			{
				throw new InvalidArgumentException(sprintf('%s %s not found.', $cls, $func), 500);
			}

			$this->register($key, $callable);
		}

		$function = static::$registry[$key];

		return call_user_func_array($function, $parameters);
	}

	/**
	 * Registers a function to be called with a specific key
	 *
	 * @param   string   $key       The name of the key
	 * @param   array    $callable  Function or method
	 * @return  boolean  True if the function is callable
	 */
	public function register($key, $callable)
	{
		if (!$this->has($key) && is_callable($callable))
		{
			self::$registry[$key] = $callable;
			return true;
		}

		return false;
	}

	/**
	 * Removes a key for a method from registry.
	 *
	 * @param   string   $key  The name of the key
	 * @return  boolean  True if a set key is unset
	 */
	public function forget($key)
	{
		if (isset(self::$registry[$key]))
		{
			unset(self::$registry[$key]);
			return true;
		}

		return false;
	}

	/**
	 * Test if the key is registered.
	 *
	 * @param   string   $key  The name of the key
	 * @return  boolean  True if the key is registered.
	 */
	public function has($key)
	{
		return isset(self::$registry[$key]);
	}

	/**
	 * Search added paths for a callable class
	 *
	 * @param   string  $cls
	 * @return  string  Fully resolved class name
	 */
	protected function find($cls)
	{
		if (!empty(self::$paths))
		{
			foreach (self::$paths as $path)
			{
				$inc = $path . DS . strtolower($cls) . '.php';

				if (file_exists($inc))
				{
					$code = file_get_contents($inc);

					$tokens = token_get_all($code);

					for ($i = 2; $i < count($tokens); $i++)
					{
						if ($tokens[$i - 2][0] === T_CLASS
						 && $tokens[$i - 1][0] === T_WHITESPACE
						 && $tokens[$i][0] === T_STRING)
						{
							include_once $inc;

							return $tokens[$i][1];
						}
					}
				}
			}
		}

		return null;
	}

	/**
	 * Add a directory where Html should search for helpers. You may
	 * either pass a string or an array of directories.
	 *
	 * @param   string  $path  A path to search.
	 * @return  array   An array with directory elements
	 */
	public function addIncludePath($path = '')
	{
		// Force path to array
		settype($path, 'array');

		// Loop through the path directories
		foreach ($path as $dir)
		{
			if (!empty($dir) && !in_array($dir, self::$paths))
			{
				array_unshift(self::$paths, \Hubzero\Filesystem\Util::normalizePath($dir));
			}
		}

		return self::$paths;
	}
}
