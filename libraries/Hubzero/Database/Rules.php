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
 * @since     Class available since release 1.3.2
 */

namespace Hubzero\Database;

/**
 * Database validation class
 */
class Rules
{
	/**
	 * Do validation checks on provided data
	 *
	 * @param  array $data  the fields to validate
	 * @param  array $rules the rules upon which to validate
	 * @return array|bool
	 * @since  1.3.2
	 **/
	public static function validate($data, $rules)
	{
		$errors = array();

		foreach ($data as $k => $v)
		{
			if (array_key_exists($k, $rules))
			{
				if (strpos($rules[$k], '|'))
				{
					$rule = explode('|', $rules[$k]);
				}
				else
				{
					$rule = array($rules[$k]);
				}

				foreach ($rule as $r)
				{
					if (method_exists(__CLASS__, $r))
					{
						if ($error = self::$r($k, $v))
						{
							$errors[] = $error;
						}
					}
				}
			}
		}

		return (count($errors) > 0) ? $errors : true;
	}

	/**
	 * Checks that var isn't empty
	 *
	 * @param  string $key the field name
	 * @param  mixed  $var the field content
	 * @return bool|string
	 * @since  1.3.2
	 **/
	private static function notempty($key, $var)
	{
		return !empty($var) ? false : "{$key} cannot be empty";
	}

	/**
	 * Checks that var is positive
	 *
	 * @param  string $key the field name
	 * @param  mixed  $var the field content
	 * @return bool|string
	 * @since  1.3.2
	 **/
	private static function positive($key, $var)
	{
		return ($var >= 0) ? false : "{$key} must be a positive integer";
	}

	/**
	 * Checks that var is non-zero
	 *
	 * @param  string $key the field name
	 * @param  mixed  $var the field content
	 * @return bool|string
	 * @since  1.3.2
	 **/
	private static function nonzero($key, $var)
	{
		return ($var > 0 || $var < 0) ? false : "{$key} cannot be zero";
	}

	/**
	 * Checks that var is alphabetical
	 *
	 * @param  string $key the field name
	 * @param  mixed  $var the field content
	 * @return bool|string
	 * @since  1.3.2
	 **/
	private static function alpha($key, $var)
	{
		return (preg_match('/^[[:alpha:] ]+$/', $var)) ? false : "{$key} can only contain alphabetical characters";
	}

	/**
	 * Checks that var is phone
	 *
	 * @param  string $key the field name
	 * @param  mixed  $var the field content
	 * @return bool|string
	 * @since  1.3.2
	 **/
	private static function phone($key, $var)
	{
		return (\Hubzero\Utility\Validate::phone($var)) ? false : "{$key} does not appear to be a valid phone number";
	}

	/**
	 * Checks that var is email
	 *
	 * @param  string $key the field name
	 * @param  mixed  $var the field content
	 * @return bool|string
	 * @since  1.3.2
	 **/
	private static function email($key, $var)
	{
		return (\Hubzero\Utility\Validate::email($var)) ? false : "{$key} does not appear to be a valid email address";
	}
}