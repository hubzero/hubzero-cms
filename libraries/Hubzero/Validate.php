<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class Hubzero_Validate 
{
	static public function is_any_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x);
	}
	
	//-----------

	static public function is_positive_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x && $x > 0);
	}
	
	//-----------

	static public function is_nonnegative_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x && $x >= 0);
	}
	
	//-----------

	static public function is_nonpositive_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x && $x <= 0);
	}
	
	//-----------

	static public function is_negative_integer($x)
	{
		return (is_numeric($x) && intval($x) == $x && $x < 0);
	}
	
	//-----------
	
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
	
	//-----------
	
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
	
	//-----------
	
	public function validpassword($password) 
	{
		if (eregi("^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]+$", $password)) {
			return true;
		} else {
			return false;
		}
	}
	
	//-----------
	
	public function validemail($email) 
	{
		if (eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email)) {
			return true;
		} else {
			return false;
		}
	}
	
	//-----------
	
	public function validurl($url) 
	{
		if (eregi("^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]*$", $url)) {
			return(1);
		} else {
			return(0);
		}
	}
	
	//-----------

	public function validphone($phone) 
	{
		if (eregi("^[\ \#\*\+\:\,\.0-9-]*$", $phone)) {
			return(1);
		} else {
			return(0);
		}
	}
	
	//-----------

	public function validtext($text) 
	{
		if (!strchr($text, "	")) {
			return(1);
		} else {
			return(0);
		}
	}
	
	//-----------
	
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
