<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document;

use Closure;
use InvalidArgumentException;
use Exception;

/**
 * Document manager
 */
class Manager
{
	/**
	 * The array of created "types".
	 *
	 * @var  array
	 */
	protected $types = array();

	/**
	 * The acurrent type.
	 *
	 * @var  string
	 */
	protected $type = 'html';

	/**
	 * Create a new manager instance.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->types = array();
	}

	/**
	 * Get a type instance.
	 *
	 * @param   string  $type
	 * @param   array   $options  Associative array of options
	 * @return  mixed
	 */
	public function instance($type = null, $options = array())
	{
		$type = $type ?: $this->getType();

		$signature = serialize(array($type, $options));

		// If the given type has not been created before, we will create the instances
		// here and cache it so we can return it next time very quickly. If there is
		// already a type created by this name, we'll just return that instance.
		if (!isset($this->types[$signature]))
		{
			try
			{
				$document = $this->createType($type, $options);
			}
			catch (Exception $e)
			{
				$document = $this->createType('html', $options);
			}

			$this->types[$signature] = $document;
		}

		return $this->types[$signature];
	}

	/**
	 * Create a new type instance.
	 *
	 * @param   string  $type
	 * @param   array   $options  Associative array of options
	 * @return  object
	 * @throws  InvalidArgumentException
	 */
	protected function createType($type, $options = array())
	{
		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);

		$class = __NAMESPACE__ . '\\Type\\' . ucfirst($type);

		if (!class_exists($class))
		{
			throw new InvalidArgumentException("Type [$type] not supported.");
		}

		return new $class($options);
	}

	/**
	 * Get the current type
	 *
	 * @return  string
	 */
	public function setType($type)
	{
		$this->type = (string) $type;

		return $this;
	}

	/**
	 * Get the current type
	 *
	 * @return  string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get all of the created "types".
	 *
	 * @return  array
	 */
	public function getTypes()
	{
		return $this->types;
	}

	/**
	 * Dynamically call the default type instance.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->instance(), $method), $parameters);
	}
}
