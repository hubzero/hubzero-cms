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

namespace Hubzero\Document;

use Closure;
use InvalidArgumentException;

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
			$this->types[$signature] = $this->createType($type, $options);
		}

		return $this->types[$signature];
	}

	/**
	 * Create a new type instance.
	 *
	 * @param   string  $type
	 * @param   array   $options  Associative array of options
	 * @return  object
	 * @throws  \InvalidArgumentException
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
