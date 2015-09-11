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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
 */

namespace Hubzero\Database\Traits;

/**
 * Error message bag for shared error handling logic
 */
trait ErrorBag
{
	/**
	 * Errors that have been declared
	 *
	 * @var  array
	 **/
	private $errors = array();

	/**
	 * Sets all errors at once, overwritting any existing errors
	 *
	 * @param   array  $errors  The errors to set
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function setErrors($errors)
	{
		$this->errors = $errors;
		return $this;
	}

	/**
	 * Adds error to the existing set
	 *
	 * @param   string  $error  The error to add
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function addError($error)
	{
		$this->errors[] = $error;
		return $this;
	}

	/**
	 * Returns all errors
	 *
	 * @return  array
	 * @since   2.0.0
	 **/
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Returns the first error
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function getError()
	{
		return (isset($this->errors[0])) ? $this->errors[0] : '';
	}
}