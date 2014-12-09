<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Helper class for registration.
 * Use primarily for input validation.
 */
class MembersHelperUtility
{
	/**
	 * Validate organization type
	 *
	 * @param      string $org
	 * @return     boolean 1 = valid, 0 = invalid
	 */
	public static function validateOrgType($org)
	{
		$orgtypes = array('university','precollege','nationallab','industry','government','military','unemployed');

		if (in_array($org, $orgtypes))
		{
			return true;
		}

		return false;
	}

	/**
	 * Check validity of login
	 *
	 * @param      string $login                      - login name to check
	 * @param      bool   $allowNumericFirstCharacter - whether or not to allow first character as number (used for grandfathered accounts)
	 * @return     integer Return
	 */
	public static function validlogin($login, $allowNumericFirstCharacter=false)
	{
		$firstCharClass = ($allowNumericFirstCharacter) ? 'a-z0-9' : 'a-z';

		if (preg_match("/^[" . $firstCharClass . "][_.a-z0-9]{1,31}$/", $login))
		{
			if (self::is_positiveint($login))
			{
				return(0);
			}
			else
			{
				return(1);
			}
		}
		else
		{
			return(0);
		}
	}

	/**
	 * Check if an integer is positive
	 *
	 * @param      integer $x
	 * @return     boolean 1 = valid, 0 = invalid
	 */
	public static function is_positiveint($x)
	{
		if (is_numeric($x) && intval($x) == $x && $x >= 0)
		{
			return(true);
		}
		return(false);
	}

	/**
	 * Validate a password
	 *
	 * @param      unknown $password
	 * @return     boolean 1 = valid, 0 = invalid
	 */
	public static function validpassword($password)
	{
		if (preg_match("/^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]+$/", $password))
		{
			return true;
		}
		return false;
	}

	/**
	 * Validate an email address
	 *
	 * @param      unknown $email
	 * @return     boolean 1 = valid, 0 = invalid
	 */
	public static function validemail($email)
	{
		if (preg_match("/^[_\+\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/", $email))
		{
			return true;
		}
		return false;
	}

	/**
	 * Validate a URL
	 *
	 * @param      string $url
	 * @return     integer 1 = valid, 0 = invalid
	 */
	public static function validurl($url)
	{
		$ptrn = '/([a-z0-9_\-]{1,5}:\/\/)?(([a-z0-9_\-]{1,}):([a-z0-9_\-]{1,})\@)?((www\.)|([a-z0-9_\-]{1,}\.)+)?([a-z0-9_\-]{2,})(\.[a-z]{2,4})(\/([a-z0-9_\-]{1,}\/)+)?([a-z0-9_\-]{1,})?(\.[a-z]{2,})?(\?)?(((\&)?[a-z0-9_\-]{1,}(\=[a-z0-9_\-]{1,})?)+)?/';
		if (preg_match($ptrn, $url))
		{
			return(1);
		}
		return(0);
	}

	/**
	 * Validate a phone number
	 *
	 * @param      string $phone
	 * @return     integer 1 = valid, 0 = invalid
	 */
	public static function validphone($phone)
	{
		if (preg_match("/^[\ \#\*\+\:\,\.0-9-]*$/", $phone))
		{
			return(1);
		}
		return(0);
	}

	/**
	 * Validate text
	 *
	 * @param      string $text Text to validate
	 * @return     integer 1 = valid, 0 = invalid
	 */
	public static function validtext($text)
	{
		if (!strchr($text, "	"))
		{
			return(1);
		}
		return(0);
	}

	/**
	 * Validate ORCID
	 *
	 * @param      string $orcid ORCID
	 * @return     integer 1 = valid, 0 = invalid
	 */
	public static function validorcid($orcid)
	{
		if (preg_match("/^[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}$/", $orcid))
		{
			return(1);
		}
		return(0);
	}

	/**
	 * Short description for 'genemailconfirm'
	 *
	 * Long description (if any) ...
	 *
	 * @return     integer Return description (if any) ...
	 */
	public static function genemailconfirm()
	{
		return(-rand(1, pow(2, 31)-1)); // php5 in debian etch returns negative values if i don't subtract 1 from this max
	}

	/**
	 * Short description for 'userpassgen'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $length Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function userpassgen($length = 8)
	{
		$genpass = '';
		$salt = "abchefghjkmnpqrstuvwxyz0123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		while ($i < $length)
		{
			$num = rand() % 33;
			$tmp = substr($salt, $num, 1);
			$genpass = $genpass . $tmp;
			$i++;
		}
		return($genpass);
	}

	/**
	 * Check to see if the email confirmation code is still an active code
	 *
	 * @param      $code - (int) email confirmation code
	 * @return     bool
	 */
	public static function isActiveCode($code)
	{
		$db = JFactory::getDBO();

		$query = "SELECT `uidNumber` FROM `#__xprofiles` WHERE emailConfirmed = ".$db->quote('-'.$code)." LIMIT 1";
		$db->setQuery($query);
		$result = $db->loadResult();

		return ($result) ? true : false;
	}
}

