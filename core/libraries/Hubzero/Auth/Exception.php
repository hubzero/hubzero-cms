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

namespace Hubzero\Auth;

/**
 * Auth Exception object.
 */
class Exception extends \Exception
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
	 * Name of the method the error occurred in
	 * [Available if backtrace is enabled]
	 *
	 * @var  string
	 */
	protected $function = null;

	/**
	 * Name of the class the error occurred in
	 * [Available if backtrace is enabled]
	 *
	 * @var  string
	 */
	protected $class = null;

	/**
	 * Error type.
	 *
	 * @var  string
	 */
	protected $type = null;

	/**
	 * Arguments recieved by the method the error occurred in
	 * [Available if backtrace is enabled]
	 *
	 * @var  array
	 */
	protected $args = array();

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

		if ($backtrace && function_exists('debug_backtrace'))
		{
			$this->backtrace = debug_backtrace();

			for ($i = count($this->backtrace) - 1; $i >= 0; --$i)
			{
				++$i;
				if (isset($this->backtrace[$i]['file']))
				{
					$this->file = $this->backtrace[$i]['file'];
				}
				if (isset($this->backtrace[$i]['line']))
				{
					$this->line = $this->backtrace[$i]['line'];
				}
				if (isset($this->backtrace[$i]['class']))
				{
					$this->class = $this->backtrace[$i]['class'];
				}
				if (isset($this->backtrace[$i]['function']))
				{
					$this->function = $this->backtrace[$i]['function'];
				}
				if (isset($this->backtrace[$i]['type']))
				{
					$this->type = $this->backtrace[$i]['type'];
				}

				$this->args = false;
				if (isset($this->backtrace[$i]['args']))
				{
					$this->args = $this->backtrace[$i]['args'];
				}
				break;
			}
		}

		parent::__construct($msg, (int) $code);
	}

	/**
	 * Returns to error message
	 *
	 * @return  string  Error message
	 */
	public function __toString()
	{
		return $this->message;
	}

	/**
	 * Returns to error message
	 *
	 * @return  string  Error message
	 */
	public function toString()
	{
		return (string) $this;
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
	 * @return  object
	 */
	public function set($property, $value = null)
	{
		$this->$property = $value;

		return $this;
	}
}
