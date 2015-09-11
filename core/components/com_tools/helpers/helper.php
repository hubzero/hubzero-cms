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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * Short description for 'makeArray'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $string Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function makeArray($string='')
	{
		$string = preg_replace('# #',',',$string);
		$arr = preg_split('#,#',$string);
		//$arr = $this->cleanArray($arr);
		$arr = ContribtoolHelper::cleanArray($arr);
		$arr = array_unique($arr);

		return $arr;
	}

	/**
	 * Short description for 'cleanArray'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $array Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function cleanArray($array)
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
	 * Short description for 'check_validInput'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $field Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function check_validInput($field)
	{
		if (preg_match("#^[_0-9a-zA-Z.:-]+$#i", $field) or $field=='')
		{
			return(0);
		}
		else
		{
			return(1);
		}
	}

	/**
	 * Short description for 'getLicenses'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $database Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getLicenses($database)
	{
		$database->setQuery("SELECT text, name, title FROM #__tool_licenses ORDER BY ordering ASC");
		return $database->loadObjectList();
	}

	/**
	 * Short description for 'transform'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $array Parameter description (if any) ...
	 * @param      unknown $label Parameter description (if any) ...
	 * @param      array $newarray Parameter description (if any) ...
	 * @return     array Return description (if any) ...
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
	 * Short description for 'getLogins'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $uids Parameter description (if any) ...
	 * @param      array $logins Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getLogins($uids, $logins = array())
	{
		if (is_array($uids))
		{
			foreach ($uids as $uid)
			{
				$user = \User::getInstance($uid);
				if ($user)
				{
					$logins[] = $user->get('username');
				}
			}
		}
		return $logins;
	}

	/**
	 * Short description for 'record_view'
	 *
	 * Long description (if any) ...
	 *
	 * @param      mixed $database Parameter description (if any) ...
	 * @param      string $ticketid Parameter description (if any) ...
	 * @return     void
	 */
	public function record_view($database, $ticketid)
	{
		$when = Date::toSql();

		$sql = "SELECT * FROM #__tool_statusviews WHERE ticketid='" . $ticketid . "' AND uid=" . \User::get('id');
		$database->setQuery($sql);
		$found = $database->loadObjectList();
		if ($found)
		{
			$elapsed = strtotime($when) - strtotime($found[0]->viewed);
			$database->setQuery("UPDATE #__tool_statusviews SET viewed='" . $when . "', elapsed='" . $elapsed . "' WHERE ticketid='" . $ticketid . "' AND uid=" . \User::get('id'));
			if (!$database->query())
			{
				echo "<script type=\"text/javascript\"> alert('" . $database->getErrorMsg() . "');</script>\n";
				exit;
			}
		}
		else
		{
			$database->setQuery("INSERT INTO #__tool_statusviews (uid, ticketid, viewed, elapsed) VALUES (" . \User::get('id') . ", '" . $ticketid . "', '" . $when . "', '500000')");
			if (!$database->query())
			{
				echo "<script type=\"text/javascript\"> alert('" . $database->getErrorMsg()."');</script>\n";
				exit;
			}
		}
	}
}
