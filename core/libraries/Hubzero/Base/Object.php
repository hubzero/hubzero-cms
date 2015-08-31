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

namespace Hubzero\Base;

use Hubzero\Base\Traits\ErrorBag;
use Exception;

/**
 * Object Class
 *
 * This class allows for simple but smart objects with get and set methods
 * and an internal error handler.
 *
 * Based off of Joomla's JObject
 */
class Object
{
	use ErrorBag;

	/**
	 * Class constructor, overridden in descendant classes.
	 *
	 * @param   mixed  $properties  Either and associative array or another
	 *                              object to set the initial properties of the object.
	 * @return  void
	 */
	public function __construct($properties = null)
	{
		if ($properties !== null)
		{
			$this->setProperties($properties);
		}
	}

	/**
	 * Magic method to convert the object to a string gracefully.
	 *
	 * @return  string  The classname.
	 */
	public function __toString()
	{
		return get_class($this);
	}

	/**
	 * Sets a default value if not alreay assigned
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 * @return  mixed
	 */
	public function def($property, $default = null)
	{
		$value = $this->get($property, $default);
		return $this->set($property, $value);
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 * @return  mixed    The value of the property.
	 */
	public function get($property, $default = null)
	{
		if (isset($this->$property))
		{
			return $this->$property;
		}
		return $default;
	}

	/**
	 * Returns an associative array of object properties.
	 *
	 * @param   boolean  $public  If true, returns only the public properties.
	 * @return  array
	 */
	public function getProperties($public = true)
	{
		$vars = get_object_vars($this);
		if ($public)
		{
			foreach ($vars as $key => $value)
			{
				if ('_' == substr($key, 0, 1))
				{
					unset($vars[$key]);
				}
			}
		}

		return $vars;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 * Returns $this so set() can be chained
	 *
	 *    $object->set('foo', $bar)
	 *           ->set('bar', $foo)
	 *           ->doSomething();
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set.
	 * @return  object
	 */
	public function set($property, $value)
	{
		$this->$property = $value;
		return $this; // So we can do method chaining!
	}

	/**
	 * Set the object properties based on a named array/hash.
	 *
	 * @param   mixed  $properties  Either an associative array or another object.
	 * @return  boolean
	 */
	public function setProperties($properties)
	{
		if (is_array($properties) || is_object($properties))
		{
			foreach ((array) $properties as $k => $v)
			{
				// Use the set function which might be overridden.
				$this->set($k, $v);
			}
			return true;
		}

		return false;
	}
}
