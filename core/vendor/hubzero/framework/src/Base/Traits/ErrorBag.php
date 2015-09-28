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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Traits;

use Exception;

/**
 * Error Bag trait.
 * Adds basic methods for setting and getting error messages.
 *
 * Methods based on those found in Joomla's JObject class.
 */
trait ErrorBag
{
	/**
	 * An array of error messages or Exception objects.
	 *
	 * @var  array
	 */
	protected $_errors = array();

	/**
	 * Get the most recent error message.
	 *
	 * @param   integer  $i         Option error index.
	 * @param   boolean  $toString  Indicates if error objects should return their error message.
	 * @return  string   Error message
	 */
	public function getError($i = null, $toString = true)
	{
		// Find the error
		if ($i === null)
		{
			// Default, return the last message
			$error = end($this->_errors);
		}
		elseif (!array_key_exists($i, $this->_errors))
		{
			// If $i has been specified but does not exist, return false
			return false;
		}
		else
		{
			$error = $this->_errors[$i];
		}

		// Check if only the string is requested
		if ($error instanceof Exception && $toString)
		{
			return (string) $error;
		}

		return $error;
	}

	/**
	 * Return all errors, if any.
	 *
	 * @return  array  Array of error messages
	 */
	public function getErrors()
	{
		return $this->_errors;
	}

	/**
	 * Add an error message.
	 *
	 * @param   string  $error  Error message.
	 * @param   string  $key    Specific key to set the value to
	 * @return  object
	 */
	public function setError($error, $key=null)
	{
		if ($key)
		{
			$this->_errors[$key] = $error;
		}
		else
		{
			array_push($this->_errors, $error);
		}
		return $this;
	}

	/**
	 * Set the list of errors
	 *
	 * @param   array   $errors  List of Error message.
	 * @return  object
	 */
	public function setErrors($errors)
	{
		$this->_errors = $errors;
		return $this;
	}
}

