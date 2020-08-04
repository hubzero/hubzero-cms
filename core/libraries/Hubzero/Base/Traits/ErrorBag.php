<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		if ($key !== null)
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
