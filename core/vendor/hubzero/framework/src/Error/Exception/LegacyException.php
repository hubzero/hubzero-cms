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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Error\Exception;

/**
 * Legacy Exception object.
 */
class LegacyException extends \Exception
{
	/**
	 * Error level.
	 *
	 * @var  string
	 */
	protected $level = null;

	/**
	 * Additional info about the error relevant to the developer,
	 * for example, if a database connect fails, the dsn used
	 *
	 * @var  string
	 */
	protected $info = '';

	/**
	 * Backtrace information.
	 *
	 * @var  mixed
	 */
	protected $backtrace = null;

	/**
	 * Constructor
	 * - used to set up the error with all needed error details.
	 *
	 * @param   string   $msg        The error message
	 * @param   string   $code       The error code from the application
	 * @param   integer  $level      The error level (use the PHP constants E_ALL, E_NOTICE etc.).
	 * @param   string   $info       Optional: The additional error information.
	 * @param   boolean  $backtrace  True if backtrace information is to be collected
	 * @return  void
	 */
	public function __construct($msg, $code = 0, $level = null, $info = null, $backtrace = false)
	{
		$this->level   = $level;
		$this->code    = $code;
		$this->message = $msg;

		if ($info != null)
		{
			$this->info = $info;
		}

		if ($backtrace && is_array($backtrace))
		{
			$this->backtrace = $backtrace;
		}

		parent::__construct($msg, (int) $code);
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property
	 * @param   mixed   $default   The default value
	 * @return  mixed   The value of the property or null
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
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $property  The name of the property
	 * @param   mixed   $value     The value of the property to set
	 * @return  mixed   Previous value of the property
	 */
	public function set($property, $value = null)
	{
		$this->$property = $value;
		return $this;
	}
}
