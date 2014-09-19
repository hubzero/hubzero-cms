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

namespace Hubzero\Console\Command\Log;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Log base class
 **/
class Base
{
	/**
	 * Fields available in this log and their default visibility
	 *
	 * @var array
	 **/
	protected static $fields = array();

	/**
	 * If dates/times are present, how are they format
	 *
	 * @var string
	 **/
	protected static $dateFormat = null;

	/**
	 * Check if given field is valid
	 *
	 * @return (bool)
	 **/
	public static function isField($field)
	{
		return (array_key_exists($field, static::$fields));
	}

	/**
	 * Get date format string
	 *
	 * @return (string) $dateFormat
	 **/
	public static function getDateFormat()
	{
		return static::$dateFormat;
	}

	/**
	 * Get log path
	 *
	 * @return (string) $path - log path
	 **/
	public static function path()
	{
		return false;
	}

	/**
	 * Show current output format
	 *
	 * @param  (object) $output - output object
	 * @return void
	 **/
	public static function format($output)
	{
		$output->addString('The profile log has the following format (');
		$output->addString('* indicates visible field', array('color'=>'blue'));
		$output->addLine('):');

		$i = 0;
		foreach (static::$fields as $field => $status)
		{
			if ($i != 0)
			{
				$output->addString(' ');
			}

			$output->addString('<');

			if ($i < 10)
			{
				$output->addString($i . ':');
			}

			if ($status)
			{
				$output->addString('*' . $field, array('color'=>'blue'));
			}
			else
			{
				$output->addString($field);
			}

			$output->addString('>');
			$i++;
		}

		$output->addSpacer()->addSpacer();
	}

	/**
	 * Toggle field visibility
	 *
	 * @return (bool)
	 **/
	public static function toggle($field, $status=null)
	{
		// If we're toggling field based on position in array
		if (strlen($field) == 1 && is_numeric($field))
		{
			$i = 0;
			foreach (static::$fields as $f => $s)
			{
				if ($i == $field)
				{
					if (!isset($status))
					{
						$status = ($s) ? false : true;
					}

					static::$fields[$f] = $status;

					// Return textual description of what happened
					return (($status) ? 'Showing ' : 'Hiding ') . $f;
				}
				$i++;
			}
		}
		// All fields either on or off
		else if ($field == 'all')
		{
			foreach (static::$fields as $f => $s)
			{
				static::$fields[$f] = $status;
			}

			return (($status) ? 'Showing ' : 'Hiding ') . 'all fields';
		}
		// Toggling comma-separated list of fields
		else if (strpos($field, ','))
		{
			$fields = explode(',', $field);
			$valid  = array();

			foreach ($fields as $f)
			{
				$f = trim($f);
				if (isset(static::$fields[$f]))
				{
					$valid[] = $f;
					static::$fields[$f] = $status;
				}
			}

			$return = (($status) ? 'Showing ' : 'Hiding ');
			if (empty($valid))
			{
				$return = 'No valid fields provided';
			}
			else
			{
				$return .= implode(', ', $valid);
			}

			return $return;
		}
		// Toggling single field
		else if (isset(static::$fields[$field]))
		{
			static::$fields[$field] = $status;

			return (($status) ? 'Showing ' : 'Hiding ') . $field;
		}
		// Who knows what's going on here!
		else
		{
			return false;
		}
	}

	/**
	 * Parse log line
	 *
	 * @param  (string) $line     - log line
	 * @param  (object) $output   - output object
	 * @param  (array)  $settings - settings to honor
	 * @return void
	 **/
	public static function parse($line, $output, $settings)
	{
		$bits     = explode(' ', $line);
		$index    = 0;
		$i_used   = 0;
		$style    = null;
		$exceeded = false;

		// First loop through and see if any of our thresholds are exceeded
		if (is_array($settings['threshold']))
		{
			foreach (static::$fields as $field => $show)
			{
				if (isset($settings['threshold'][$field]) && trim($bits[$index]) > $settings['threshold'][$field])
				{
					$exceeded[] = $field;
				}

				$index++;
			}
		}

		// Reset index
		$index = 0;

		// Loop through and do actual output
		foreach (static::$fields as $field => $show)
		{
			if ($show)
			{
				if ($exceeded)
				{
					$style = array('color'=>'red');

					if (in_array($field, $exceeded))
					{
						$style = 'error';
					}
				}

				if ($i_used != 0)
				{
					$output->addString(' ');
				}

				$value = trim($bits[$index]);

				// See if we need to change date format
				if (isset(static::$dateFormat))
				{
					$d = \DateTime::createFromFormat(static::$dateFormat, $value);
					if ($d && $d->format(static::$dateFormat) == $value)
					{
						$value = $d->format($settings['dateFormat']);
					}
				}

				$output->addString($value, $style);

				// Increment used count
				$i_used++;
			}

			// Increment total count
			$index++;
		}

		$output->addSpacer();

		// See if the total time exceeds the given threshold
		if ($exceeded && !$settings['noBeep'])
		{
			$output->beep();
		}
	}
}