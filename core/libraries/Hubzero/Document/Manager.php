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
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * The current type.
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
			catch (\Exception $e)
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
