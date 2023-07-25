<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades\Tests\Mock;

/**
 * Mock Application
 *
 * @codeCoverageIgnore
 */
class Application implements \ArrayAccess
{
	/**
	 * Attributes
	 *
	 * @var  array
	 */
	protected $attributes = array();

	/**
	 * Set a value
	 *
	 * @param   string  $key
	 * @param   mixed   $val
	 * @return  void
	 */
	public function set($key, $val)
	{
		return $this->offsetSet($key, $val);
	}

	/**
	 * Get a value
	 *
	 * @return  mixed
	 */
	public function get($key)
	{
		return $this->offsetGet($key);
	}

	/**
	 * Check a value exists
	 *
	 * @param   string  $key
	 * @return  bool
	 */

	#[\ReturnTypeWillChange]
	public function offsetExists($key)
	{
		return isset($this->attributes[$key]);
	}

	/**
	 * Get a value
	 *
	 * @return  mixed
	 */

	#[\ReturnTypeWillChange]
	public function offsetGet($key)
	{
		return $this->attributes[$key];
	}

	/**
	 * Set a value
	 *
	 * @param   string  $key
	 * @param   mixed   $val
	 * @return  void
	 */

	#[\ReturnTypeWillChange]
	public function offsetSet($key, $val)
	{
		$this->attributes[$key] = $val;
	}

	/**
	 * Unsert a value
	 *
	 * @param   string  $key
	 * @return  void
	 */

	#[\ReturnTypeWillChange]
	public function offsetUnset($key)
	{
		unset($this->attributes[$key]);
	}
}
