<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * Short description for 'ContribtoolHelper'
 *
 * Long description (if any) ...
 */
class ContribtoolHelper
{
	/**
	 * Turn a string into an array
	 *
	 * @param   string  $string
	 * @return  array
	 */
	public static function makeArray($string='')
	{
		$string = preg_replace('# #', ',', $string);
		$arr = preg_split('#,#', $string);

		$arr = self::cleanArray($arr);
		$arr = array_unique($arr);

		return $arr;
	}

	/**
	 * Clean array
	 *
	 * @param   array  $array
	 * @return  array
	 */
	public static function cleanArray($array)
	{
		foreach ($array as $key => $value)
		{
			$value = trim($value);
			if ($value == '')
			{
				unset($array[$key]);
			}
		}
		return $array;
	}

	/**
	 * Check if an input is valid
	 *
	 * @param   string   $field
	 * @return  integer
	 */
	public static function check_validInput($field)
	{
		if (preg_match("#^[_0-9a-zA-Z.:-]+$#i", $field) or $field == '')
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}

	/**
	 * Get a list of licenses
	 *
	 * @param   object  $database
	 * @return  array
	 */
	public static function getLicenses($database)
	{
		$database->setQuery("SELECT text, name, title FROM `#__tool_licenses` ORDER BY ordering ASC");
		return $database->loadObjectList();
	}

	/**
	 * Transform an array
	 *
	 * @param   array   $array
	 * @param   string  $label
	 * @param   array   $newarray
	 * @return  array
	 */
	public static function transform($array, $label, $newarray=array())
	{
		if (count($array) > 0)
		{
			foreach ($array as $a)
			{
				if (is_object($a))
				{
					$newarray[] = $a->$label;
				}
				else
				{
					$newarray[] = $a;
				}
			}
		}

		return $newarray;
	}

	/**
	 * Get usernames for a list of IDs
	 *
	 * @param   array  $uids
	 * @param   array  $logins
	 * @return  arra
	 */
	public static function getLogins($uids, $logins = array())
	{
		if (is_array($uids))
		{
			foreach ($uids as $uid)
			{
				$user = \User::getInstance($uid);
				if ($user && $user->get('username'))
				{
					$logins[] = $user->get('username');
				}
			}
		}
		return $logins;
	}

	/**
	 * Record a tool status view
	 *
	 * @param   object   $database
	 * @param   integer  $ticketid
	 * @return  mixed
	 */
	public static function record_view($database, $ticketid)
	{
		$when = Date::toSql();

		$sql = "SELECT * FROM `#__tool_statusviews` WHERE ticketid=" . $database->quote($ticketid) . " AND uid=" . $database->quote(\User::get('id'));
		$database->setQuery($sql);
		$found = $database->loadObjectList();
		if ($found)
		{
			$elapsed = strtotime($when) - strtotime($found[0]->viewed);
			$database->setQuery("UPDATE `#__tool_statusviews` SET viewed=" . $database->quote($when) . ", elapsed=" . $database->quote($elapsed) . " WHERE ticketid=" . $database->quote($ticketid) . " AND uid=" . $database->quote(\User::get('id')));
			if (!$database->query())
			{
				return $database->getErrorMsg();
			}
		}
		else
		{
			$database->setQuery("INSERT INTO `#__tool_statusviews` (uid, ticketid, viewed, elapsed) VALUES (" . \User::get('id') . ", " . $database->quote($ticketid) . ", " . $database->quote($when) . ", " . $database->quote(500000) . ")");
			if (!$database->query())
			{
				return $database->getErrorMsg();
			}
		}

		return '';
	}
}
