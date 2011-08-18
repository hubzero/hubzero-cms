<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class Hubzero_Registration_Helper
{
	public function genemailconfirm() 
	{
	    return(-rand(1, pow(2, 31)-1)); // php5 in debian etch returns negative values if i don't subtract 1 from this max 
	}
	
	//-----------
	
	public function validateOrgType($org)
    {
        $orgtypes = array('university','precollege','nationallab','industry','government','military','unemployed');
        
		if (in_array($org,$orgtypes))
            return true;

        return false;
    }

	//-----------

	public function validlogin($login) 
	{
		if (preg_match("/^[0-9a-zA-Z]+[_0-9a-zA-Z\.]*$/i", $login)) {
			if (Hubzero_Registration_Helper::is_positiveint($login)) {
				return(0);
			} else {
				return(1);
			}
		} else {
			return(0);
		}
	}
	
	//-----------
	
	public function is_positiveint($x) 
	{
		if (is_numeric($x) && intval($x) == $x && $x >= 0) {
			return(true);
		} else {
			return(false);
		}
	}
	
	//-----------
	
	public function validpassword($password) 
	{
		if (preg_match("/^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]+$/", $password)) {
			return true;
		} else {
			return false;
		}
	}
	
	//-----------
	
	public function validemail($email) 
	{
		if (preg_match("/^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/", $email)) {
			return true;
		} else {
			return false;
		}
	}
	
	//-----------
	
	public function validurl($url) 
	{
		if (preg_match("/^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]*$/", $url)) {
			return(1);
		} else {
			return(0);
		}
	}
	
	//-----------

	public function validphone($phone) 
	{
		if (preg_match("/^[\ \#\*\+\:\,\.0-9-]*$/", $phone)) {
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
	
	public function userpassgen($length = 8) 
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

	public function select_form($formdata = array(), $errors = array())
	{
		$result = include 'components/com_myaccount/select.html.php';
	}
	
	//-----------

	public function registration_form($task, &$xregistration) 
	{
		$result = include 'components/com_myaccount/registration.html.php';
	}
	
	//-----------

	public function recovery_form($email, $errors = array())
	{
		$result = include 'components/com_myaccount/recovery.html.php';
	}
	
	//-----------

	public function raiselimits_form($resource, $admin, $target_xprofile)
	{
		$result = include 'components/com_myaccount/raiselimits.html.php';
	}
	
	//-----------

	public function delete_form(&$HTTP_POST_VARS, $uid, $confirmSingleParent = false)
	{
		$result = include 'components/com_myaccount/delete.html.php';
	}
}

