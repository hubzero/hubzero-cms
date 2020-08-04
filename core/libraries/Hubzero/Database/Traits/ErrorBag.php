<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
