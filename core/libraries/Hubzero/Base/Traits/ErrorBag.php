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

