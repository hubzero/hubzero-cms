<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

ximport('misc_func');

class XRegistrationHelper
{
	function genemailconfirm() 
	{
	    return(-rand(1, pow(2, 31)-1)); // php5 in debian etch returns negative values if i don't subtract 1 from this max 
	}
	
	function validateOrgType($org)
    {
        $orgtypes = array('university','precollege','nationallab','industry','government','military','unemployed');
        
		if (in_array($org,$orgtypes))
            return true;

        return false;
    }

	/*function validlogin($login)
	{
        if (eregi("^[a-zA-Z][0-9a-zA-Z]+[_0-9a-zA-Z]*$", $login)) 
        	return(true);

		return false;
	}*/
	function validlogin($login) 
	{
		if (eregi("^[0-9a-zA-Z]+[_0-9a-zA-Z]*$", $login)) {
			if (is_positiveint($login)) {
				return(0);
			} else {
				return(1);
			}
		} else {
			return(0);
		}
	}
	
	function validpassword($password) 
	{
		if (eregi("^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]+$", $password)) {
			return true;
		} else {
			return false;
		}
	}
	
	function validemail($email) 
	{
		if (eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email)) {
			return true;
		} else {
			return false;
		}
	}
	
	function validurl($url) 
	{
		if (eregi("^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]*$", $url)) {
			return(1);
		} else {
			return(0);
		}
	}

	function validphone($phone) 
	{
		if (eregi("^[\ \#\*\+\:\,\.0-9-]*$", $phone)) {
			return(1);
		} else {
			return(0);
		}
	}

	function validtext($text) 
	{
		if (!strchr($text, "	")) {
			return(1);
		} else {
			return(0);
		}
	}
	
	function userpassgen($length = 8) 
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

	// display various forms. placeholders until we develop a template override system
	// for them.

	function select_form($formdata = array(), $errors = array())
	{
		$result = include 'components/com_myaccount/select.html.php';
	}

	function registration_form($task, &$xregistration) 
	{
		$result = include 'components/com_myaccount/registration.html.php';
	}

	function recovery_form($email, $errors = array())
	{
		$result = include 'components/com_myaccount/recovery.html.php';
	}

	function raiselimits_form($resource, $admin, $target_xuser)
	{
		$result = include 'components/com_myaccount/raiselimits.html.php';
	}

	function delete_form(&$HTTP_POST_VARS, $uid, $confirmSingleParent = false)
	{
		$result = include 'components/com_myaccount/delete.html.php';
	}
}
