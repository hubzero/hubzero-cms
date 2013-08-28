<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Hubzero helper class for input validation
 */
class Hubzero_Validate
{
	/**
	 * Is value an integer?
	 * 
	 * @param      unknown $x Value to check
	 * @return     boolean True if valid, false if invalid
	 */
	static public function is_any_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x);
	}

	/**
	 * Is value a positive integer?
	 * 
	 * @param      integer $x Value to check
	 * @return     boolean True if valid, false if invalid
	 */
	static public function is_positive_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x && $x > 0);
	}

	/**
	 * Is value a non-negative integer?
	 * 
	 * @param      integer $x Value to check
	 * @return     boolean True if valid, false if invalid
	 */
	static public function is_nonnegative_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x && $x >= 0);
	}

	/**
	 * Is value a non-positive integer?
	 * 
	 * @param      integer $x Value to check
	 * @return     boolean True if valid, false if invalid
	 */
	static public function is_nonpositive_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x && $x <= 0);
	}

	/**
	 * Is value a negative integer?
	 * 
	 * @param      integer $x Value to check
	 * @return     boolean True if valid, false if invalid
	 */
	static public function is_negative_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x && $x < 0);
	}

	/**
	 * Check if a username is a reserved name
	 * 
	 * @param      string $x Username to check
	 * @return     boolean True if valid, false if invalid
	 */
	static public function is_reserved_username($x)
	{
		$reserved_users = array(
			'adm',
			'alfred',
			'apache',
			'backup',
			'bin',
			'canna',
			'condor',
			'condor-util',
			'daemon',
			'debian-exim',
			'exim',
			'ftp',
			'games',
			'ganglia',
			'gnats',
			'gopher',
			'gridman',
			'halt',
			'httpd',
			'ibrix',
			'invigosh',
			'irc',
			'ldap',
			'list',
			'lp',
			'mail',
			'mailnull',
			'man',
			'mysql',
			'nagios',
			'netdump',
			'news',
			'nfsnobody',
			'noaccess',
			'nobody',
			'nscd',
			'ntp',
			'operator',
			'openldap',
			'pcap',
			'postgres',
			'proxy',
			'pvm',
			'root',
			'rpc',
			'rpcuser',
			'rpm',
			'sag',
			'shutdown',
			'smmsp',
			'sshd',
			'statd',
			'sync',
			'sys',
			'submit',
			'uucp',
			'vncproxy',
			'vncproxyd',
			'vcsa',
			'wheel',
			'www',
			'www-data',
			'xfs'
		);

		if (in_array(strtolower($x), $reserved_users))
		{
			return true;
		}

		return false;
	}

	/**
	 * Validate login
	 * 
	 * @param      string $login Username to validate
	 * @return     boolean True if valid, false if invalid
	 */
	public function validlogin($login)
	{
		if (preg_match("#^[0-9a-zA-Z]+[_0-9a-zA-Z]*$#i", $login)) 
		{
			if (Hubzero_Validate::is_positive_integer($login)) 
			{
				return false;
			} 
			else 
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Validate password
	 * 
	 * @param      string $password Password to validate
	 * @return     boolean True if valid, false if invalid
	 */
	public function validpassword($password)
	{
		if (preg_match("#^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]+$#i", $password)) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Validate email address
	 * 
	 * @param      string $email Address to validate
	 * @return     boolean True if valid, false if invalid
	 */
	public function validemail($email)
	{
		if (preg_match("#^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$#i", $email)) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Validate URL
	 * 
	 * @param      string $url URL to validate
	 * @return     boolean True if valid, false if invalid
	 */
	public function validurl($url)
	{
		if (preg_match("#^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]*$#i", $url)) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Validate phone number
	 * 
	 * @param      string $phone Phone number to validate
	 * @return     boolean True if valid, false if invalid
	 */
	public function validphone($phone)
	{
		if (preg_match('#^[\ \#\*\+\:\,\.0-9-]*$#i', $phone)) 
		{
			return true;
		} 
		return false;
	}

	/**
	 * Validate text
	 * 
	 * @param      string $text Text to validate
	 * @return     boolean True if valid, false if invalid
	 */
	public function validtext($text)
	{
		if (!strchr($text, "	")) 
		{
			return true;
		} 
		return false;
	}

	/**
	 * Validate the organization type
	 * 
	 * @param      string $org Organization type
	 * @return     boolean True if organization is an allowed type
	 */
	public function validateOrgType($org)
    {
		$orgtypes = array(
			'university',
			'universityundergraduate',
			'universitygraduate',
			'universityfaculty',
			'universitystaff',
			'precollege',
			'precollegestudent',
			'nationallab',
			'industry',
			'government',
			'military',
			'unemployed'
		);

		if (in_array($org, $orgtypes))
		{
			return true;
		}
		return false;
	}
}

