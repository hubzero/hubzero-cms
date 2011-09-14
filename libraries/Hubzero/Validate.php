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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Hubzero_Validate'
 * 
 * Long description (if any) ...
 */
class Hubzero_Validate
{

	/**
	 * Short description for 'is_any_integer'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $x Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	static public function is_any_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x);
	}

	/**
	 * Short description for 'is_positive_integer'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $x Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	static public function is_positive_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x && $x > 0);
	}

	/**
	 * Short description for 'is_nonnegative_integer'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $x Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	static public function is_nonnegative_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x && $x >= 0);
	}

	/**
	 * Short description for 'is_nonpositive_integer'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $x Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	static public function is_nonpositive_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x && $x <= 0);
	}

	/**
	 * Short description for 'is_negative_integer'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $x Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	static public function is_negative_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x && $x < 0);
	}

	/**
	 * Short description for 'is_reserved_username'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $x Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	static public function is_reserved_username($x)
	{
		$reserved_users = array(
			"adm",
			"alfred",
			"apache",
			"backup",
			"bin",
			"canna",
			"condor",
			"condor-util",
			"daemon",
			"debian-exim",
			"exim",
			"ftp",
			"games",
			"ganglia",
			"gnats",
			"gopher",
			"halt",
			"ibrix",
			"invigosh",
			"irc",
			"ldap",
			"list",
			"lp",
			"mail",
			"mailnull",
			"man",
			"nagios",
			"nanohub",
			"netdump",
			"news",
			"nfsnobody",
			"noaccess",
			"nobody",
			"nscd",
			"ntp",
			"operator",
			"openldap",
			"pcap",
			"postgres",
			"proxy",
			"pvm",
			"root",
			"rpc",
			"rpcuser",
			"rpm",
			"sag",
			"shutdown",
			"smmsp",
			"sshd",
			"statd",
			"sync",
			"sys",
			"uucp",
			"vcsa",
			"www",
			"www-data",
			 "xfs"
			);

		if (in_array(strtolower($x), $reserved_users))
			return true;

		return false;
	}

	/**
	 * Short description for 'validlogin'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $login Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function validlogin($login)
	{
		if (eregi("^[0-9a-zA-Z]+[_0-9a-zA-Z]*$", $login)) {
			if (Hubzero_Validate::is_positive_integer($login)) {
				return(0);
			} else {
				return(1);
			}
		} else {
			return(0);
		}
	}

	/**
	 * Short description for 'validpassword'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $password Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function validpassword($password)
	{
		if (eregi("^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]+$", $password)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Short description for 'validemail'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $email Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function validemail($email)
	{
		if (eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Short description for 'validurl'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $url Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function validurl($url)
	{
		if (eregi("^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]*$", $url)) {
			return(1);
		} else {
			return(0);
		}
	}

	/**
	 * Short description for 'validphone'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $phone Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function validphone($phone)
	{
		if (eregi("^[\ \#\*\+\:\,\.0-9-]*$", $phone)) {
			return(1);
		} else {
			return(0);
		}
	}

	/**
	 * Short description for 'validtext'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function validtext($text)
	{
		if (!strchr($text, "	")) {
			return(1);
		} else {
			return(0);
		}
	}

	/**
	 * Short description for 'validateOrgType'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $org Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
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

		if (in_array($org,$orgtypes))
            return true;

        return false;
    }
}

