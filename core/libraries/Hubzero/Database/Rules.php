<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * @param   array       $data   The fields to validate
	 * @param   array       $rules  The rules upon which to validate
	 * @return  array|bool
	 * @since   2.0.0
	 **/
	public static function validate($data, $rules)
	{
		$errors = array();

		foreach ($data as $k => $v)
		{
			if (array_key_exists($k, $rules))
			{
				// (Re)set rule variable
				$rule = null;

				if (is_callable($rules[$k]))
				{
					if ($error = call_user_func_array($rules[$k], [$data]))
					{
						$errors[] = $error;
					}
				}
				else if (strpos($rules[$k], '|'))
				{
					$rule = explode('|', $rules[$k]);
				}
				else
				{
					$rule = array($rules[$k]);
				}

				if (isset($rule))
				{
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
		}

		return (count($errors) > 0) ? $errors : true;
	}

	/**
	 * Checks that var isn't empty
	 *
	 * @param   string       $key  The field name
	 * @param   mixed        $var  The field content
	 * @return  bool|string
	 * @since   2.0.0
	 **/
	private static function notempty($key, $var)
	{
		return !empty($var) ? false : "{$key} cannot be empty";
	}

	/**
	 * Checks that var is positive
	 *
	 * @param   string       $key  The field name
	 * @param   mixed        $var  The field content
	 * @return  bool|string
	 * @since   2.0.0
	 **/
	private static function positive($key, $var)
	{
		return ($var >= 0) ? false : "{$key} must be a positive integer";
	}

	/**
	 * Checks that var is non-zero
	 *
	 * @param   string       $key  The field name
	 * @param   mixed        $var  The field content
	 * @return  bool|string
	 * @since   2.0.0
	 **/
	private static function nonzero($key, $var)
	{
		return ($var > 0 || $var < 0) ? false : "{$key} cannot be zero";
	}

	/**
	 * Checks that var is alphabetical
	 *
	 * @param   string       $key  The field name
	 * @param   mixed        $var  The field content
	 * @return  bool|string
	 * @since   2.0.0
	 **/
	private static function alpha($key, $var)
	{
		return (preg_match('/^[[:alpha:] ]+$/', $var)) ? false : "{$key} can only contain alphabetical characters";
	}

	/**
	 * Checks that var is phone
	 *
	 * @param   string       $key  The field name
	 * @param   mixed        $var  The field content
	 * @return  bool|string
	 * @since   2.0.0
	 **/
	private static function phone($key, $var)
	{
		return (\Hubzero\Utility\Validate::phone($var)) ? false : "{$key} does not appear to be a valid phone number";
	}

	/**
	 * Checks that var is email
	 *
	 * @param   string       $key  The field name
	 * @param   mixed        $var  The field content
	 * @return  bool|string
	 * @since   2.0.0
	 **/
	private static function email($key, $var)
	{
		return (\Hubzero\Utility\Validate::email($var)) ? false : "{$key} does not appear to be a valid email address";
	}
}
