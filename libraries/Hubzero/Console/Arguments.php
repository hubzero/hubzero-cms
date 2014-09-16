<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console;

use Hubzero\Console\Exception\UnsupportedCommandException;
use Hubzero\Console\Exception\UnsupportedTaskException;
use Hubzero\Console\Exception\InvalidPropertyException;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Console arguments class
 **/
class Arguments
{
	/**
	 * Raw command line arguments (PHP $argv)
	 *
	 * @var array
	 **/
	private $raw = NULL;

	/**
	 * Class name - command to execute
	 *
	 * @var string
	 **/
	private $class = NULL;

	/**
	 * Task name - class method to execute
	 *
	 * @var string
	 **/
	private $task = NULL;

	/**
	 * Array of additional options being passed to the command
	 *
	 * @var array
	 **/
	private $opts = NULL;

	/**
	 * Constructor
	 *
	 * Set raw arguments and initiate parsing
	 *
	 * @param  (object) $arguments
	 * @return void
	 **/
	public function __construct($arguments)
	{
		$this->raw = $arguments;
	}

	/**
	 * Simple getter for class properties
	 *
	 * Throws invalid property exception if property isn't found
	 *
	 * @param  (string) $var - property to retrieve
	 * @return void
	 **/
	public function get($var)
	{
		if (isset($this->{$var}))
		{
			return $this->{$var};
		}
		else
		{
			throw new InvalidPropertyException("Property {$var} does not exists.");
		}
	}

	/**
	 * Getter for those additional options that a given command may use
	 *
	 * @param  (string) $key     - option name to retieve value for
	 * @param  (mixed)  $default - default value for option
	 * @return void
	 **/
	public function getOpt($key, $default=false)
	{
		return (isset($this->opts[$key])) ? $this->opts[$key] : $default;
	}

	/**
	 * Get all opts
	 *
	 * @return (array) - options
	 **/
	public function getOpts()
	{
		return $this->opts;
	}

	/**
	 * Setter for additional options for a given command
	 *
	 * @param  (string) $key
	 * @param  (mixed)  $value
	 * @return void
	 **/
	public function setOpt($key, $value)
	{
		$this->opts[$key] = $value;
	}

	/**
	 * Parse the raw arguments into command, task, and additional options
	 *
	 * @return void
	 **/
	public function parse()
	{
		if (isset($this->raw) && count($this->raw) > 0)
		{
			// Take the first argument as command to be run - defaults to help
			$command = (isset($this->raw[1])) ? $this->raw[1] : 'Help';
			$class   = __NAMESPACE__ . '\\Command\\' . ucfirst($command);

			// Make sure class exists
			if (class_exists($class))
			{
				$this->class = $class;
			}
			else
			{
				throw new UnsupportedCommandException("Unknown command: {$command}.");
			}

			// Take the second argument and use that as the task to be run - defaults to execute
			$task = (isset($this->raw[2]) && substr($this->raw[2], 0, 1) != "-") ? $this->raw[2] : 'execute';

			// Make sure task exists
			if (method_exists($class, $task))
			{
				$this->task = $task;
			}
			else
			{
				throw new UnsupportedTaskException("{$command} does not support the {$task} method.");
			}

			// Parse the remaining args for command options/arguments
			for ($i = 2; $i < count($this->raw); $i++)
			{
				// Ignore the second arg if we used it above as task
				if ($i == 2 && substr($this->raw[$i], 0, 1) != "-")
				{
					continue;
				}

				// Args with an "=" will use the value before as key and the value after as value
				if (strpos($this->raw[$i], "=") !== false)
				{
					$parts = explode("=", $this->raw[$i], 2);
					$key   = preg_replace("/^([-]{1,2})/", "", $parts[0]);
					$value = ($parts[1]);

					if (isset($this->opts[$key]))
					{
						$this->opts[$key] = (array)$this->opts[$key];
						array_push($this->opts[$key], $value);
					}
					else
					{
						$this->opts[$key] = $value;
					}

					continue;
				}
				// Args with a dash but no equals sign will be considered TRUE if present
				elseif (strpos($this->raw[$i], "-") !== false)
				{
					// Try to catch clumped arguments (ex: -if as shorthand for -i -f)
					if (preg_match("/^-([[:alpha:]]{2,})/", $this->raw[$i], $matches))
					{
						if (isset($matches[1]))
						{
							foreach (str_split($matches[1], 1) as $k)
							{
								$this->opts[$k] = true;
							}
						}

						continue;
					}
					else
					{
						$key   = preg_replace("/^([-]{1,2})/", "", $this->raw[$i]);
						$value = true;
					}
				}
				// Otherwise, we'll just save the arg as a single word and individual commands may use them
				else
				{
					$key   = $i;
					$value = $this->raw[$i];
				}

				$this->opts[$key] = $value;
			}
		}
	}
}