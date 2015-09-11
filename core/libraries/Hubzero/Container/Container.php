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

namespace Hubzero\Container;

use InvalidArgumentException;
use RuntimeException;
use SplObjectStorage;
use ArrayAccess;
use Closure;

/**
 * Container main class.
 *
 * Inspired by Fabien Potencier's Pimple DI class
 */
class Container implements ArrayAccess
{
	private $values = array();
	private $factories;
	private $protected;
	private $frozen = array();
	private $raw = array();
	private $keys = array();

	/**
	 * Instantiate the container.
	 *
	 * Objects and parameters can be passed as argument to the constructor.
	 *
	 * @param  array  $values  The parameters or objects.
	 */
	public function __construct(array $values = array())
	{
		$this->factories = new SplObjectStorage();
		$this->protected = new SplObjectStorage();

		foreach ($values as $key => $value)
		{
			$this->offsetSet($key, $value);
		}
	}

	/**
	 * Sets a parameter or an object.
	 *
	 * Objects must be defined as Closures.
	 *
	 * Allowing any PHP callable leads to difficult to debug problems
	 * as function names (strings) are callable (creating a function with
	 * the same name as an existing parameter would break your container).
	 *
	 * @param   string             $id     The unique identifier for the parameter or object
	 * @param   mixed              $value  The value of the parameter or a closure to define an object
	 * @throws  \RuntimeException  Prevent override of a frozen service
	 */
	public function offsetSet($id, $value)
	{
		if (isset($this->frozen[$id]))
		{
			throw new RuntimeException(sprintf('Cannot override frozen service "%s".', $id));
		}

		$this->values[$id] = $value;
		$this->keys[$id]   = true;
	}

	/**
	 * Gets a parameter or an object.
	 *
	 * @param   string  $id  The unique identifier for the parameter or object
	 * @return  mixed   The value of the parameter or an object
	 */
	public function offsetGet($id)
	{
		if (!isset($this->keys[$id]))
		{
			throw new InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
		}

		// If an instance of the type is currently being managed as a raw value, 
		// protected value, or is a parameter.
		if (
			isset($this->raw[$id])
			|| !is_object($this->values[$id])
			|| isset($this->protected[$this->values[$id]])
			|| !method_exists($this->values[$id], '__invoke')
		)
		{
			return $this->values[$id];
		}

		// If an instance of the type is currently being managed as a multiton we'll
		// return a new instance.
		if (isset($this->factories[$this->values[$id]]))
		{
			return $this->values[$id]($this);
		}

		$raw = $this->values[$id];
		$val = $this->values[$id] = $raw($this);
		$this->raw[$id] = $raw;

		$this->frozen[$id] = true;

		return $val;
	}

	/**
	 * Checks if a parameter or an object is set.
	 *
	 * @param  string  $id  The unique identifier for the parameter or object
	 * @return bool
	 */
	public function offsetExists($id)
	{
		return isset($this->keys[$id]);
	}

	/**
	 * Unsets a parameter or an object.
	 *
	 * @param  string  $id  The unique identifier for the parameter or object
	 */
	public function offsetUnset($id)
	{
		if (isset($this->keys[$id]))
		{
			if (is_object($this->values[$id]))
			{
				unset($this->factories[$this->values[$id]], $this->protected[$this->values[$id]]);
			}

			unset($this->values[$id], $this->frozen[$id], $this->raw[$id], $this->keys[$id]);
		}
	}

	/**
	 * Sets a parameter or an object.
	 *
	 * Objects must be defined as Closures.
	 *
	 * Allowing any PHP callable leads to difficult to debug problems
	 * as function names (strings) are callable (creating a function with
	 * the same name as an existing parameter would break your container).
	 *
	 * @param  string            $id    The unique identifier for the parameter or object
	 * @param  mixed             $value The value of the parameter or a closure to define an object
	 * @throws \RuntimeException Prevent override of a frozen service
	 */
	public function set($id, $value)
	{
		return $this->offsetSet($id, $value);
	}

	/**
	 * Gets a parameter or an object.
	 *
	 * @param   string  $id  The unique identifier for the parameter or object
	 * @return  mixed   The value of the parameter or an object
	 * @throws  \InvalidArgumentException if the identifier is not defined
	 */
	public function get($id)
	{
		return $this->offsetGet($id);
	}

	/**
	 * Checks if a parameter or an object is set.
	 *
	 * @param string $id The unique identifier for the parameter or object
	 *
	 * @return bool
	 */
	public function has($id)
	{
		return $this->offsetExists($id);
	}

	/**
	 * Checks if a parameter or an object is set.
	 *
	 * @param string $id The unique identifier for the parameter or object
	 *
	 * @return bool
	 */
	public function forget($id)
	{
		return $this->offsetUnset($id);
	}

	/**
	 * Marks a callable as being a factory service.
	 *
	 * @param   callable  $callable  A service definition to be used as a factory
	 * @return  callable  The passed callable
	 * @throws  InvalidArgumentException Service definition has to be a closure of an invokable object
	 */
	public function factory($callable)
	{
		if (!is_object($callable) || !method_exists($callable, '__invoke'))
		{
			throw new InvalidArgumentException('Service definition is not a Closure or invokable object.');
		}

		$this->factories->attach($callable);

		return $callable;
	}

	/**
	 * Protects a callable from being interpreted as a service.
	 *
	 * This is useful when you want to store a callable as a parameter.
	 *
	 * @param   callable  $callable A callable to protect from being evaluated
	 * @return  callable  The passed callable
	 * @throws  \InvalidArgumentException Service definition has to be a closure of an invokable object
	 */
	public function protect($callable)
	{
		if (!is_object($callable) || !method_exists($callable, '__invoke'))
		{
			throw new InvalidArgumentException('Callable is not a Closure or invokable object.');
		}

		$this->protected->attach($callable);

		return $callable;
	}

	/**
	 * Gets a parameter or the closure defining an object.
	 *
	 * @param   string  $id  The unique identifier for the parameter or object
	 * @return  mixed   The value of the parameter or the closure defining an object
	 * @throws  \InvalidArgumentException if the identifier is not defined
	 */
	public function raw($id)
	{
		if (!isset($this->keys[$id]))
		{
			throw new InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
		}

		if (isset($this->raw[$id]))
		{
			return $this->raw[$id];
		}

		return $this->values[$id];
	}

	/**
	 * Extends an object definition.
	 *
	 * Useful when you want to extend an existing object definition,
	 * without necessarily loading that object.
	 *
	 * @param   string    $id        The unique identifier for the object
	 * @param   callable  $callable  A service definition to extend the original
	 * @return  callable  The wrapped callable
	 * @throws  \InvalidArgumentException if the identifier is not defined or not a service definition
	 */
	public function extend($id, $callable)
	{
		if (!isset($this->keys[$id]))
		{
			throw new InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
		}

		if (!is_object($this->values[$id]) || !method_exists($this->values[$id], '__invoke'))
		{
			throw new InvalidArgumentException(sprintf('Identifier "%s" does not contain an object definition.', $id));
		}

		if (!is_object($callable) || !method_exists($callable, '__invoke'))
		{
			throw new InvalidArgumentException('Extension service definition is not a Closure or invokable object.');
		}

		$factory = $this->values[$id];

		$extended = function ($c) use ($callable, $factory)
		{
			return $callable($factory($c), $c);
		};

		if (isset($this->factories[$factory]))
		{
			$this->factories->detach($factory);
			$this->factories->attach($extended);
		}

		return $this[$id] = $extended;
	}

	/**
	 * Returns all defined value names.
	 *
	 * @return  array  An array of value names
	 */
	public function keys()
	{
		return array_keys($this->values);
	}

	/**
	 * Registers a service provider.
	 *
	 * @param   object  $provider  A ServiceProviderInterface instance
	 * @param   array   $options   An array of values that customizes the provider
	 * @return  static
	 */
	public function register($provider, $options = array())
	{
		$provider->register($this);

		foreach ($options as $key => $value)
		{
			$this[$key] = $value;
		}

		return $this;
	}
}
