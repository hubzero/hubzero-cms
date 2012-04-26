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
 * Short description for 'Hubzero_Registration_Helper'
 * 
 * Long description (if any) ...
 */
class Hubzero_Registration_Helper
{

	/**
	 * Short description for 'genemailconfirm'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     integer Return description (if any) ...
	 */
	public function genemailconfirm()
	{
	    return(-rand(1, pow(2, 31)-1)); // php5 in debian etch returns negative values if i don't subtract 1 from this max 
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
        $orgtypes = array('university','precollege','nationallab','industry','government','military','unemployed');

		if (in_array($org,$orgtypes))
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

	/**
	 * Short description for 'is_positiveint'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $x Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function is_positiveint($x)
	{
		if (is_numeric($x) && intval($x) == $x && $x >= 0) {
			return(true);
		} else {
			return(false);
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
		if (preg_match("/^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]+$/", $password)) {
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
		if (preg_match("/^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/", $email)) {
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
		$ptrn = '/([a-z0-9_\-]{1,5}:\/\/)?(([a-z0-9_\-]{1,}):([a-z0-9_\-]{1,})\@)?((www\.)|([a-z0-9_\-]{1,}\.)+)?([a-z0-9_\-]{3,})(\.[a-z]{2,4})(\/([a-z0-9_\-]{1,}\/)+)?([a-z0-9_\-]{1,})?(\.[a-z]{2,})?(\?)?(((\&)?[a-z0-9_\-]{1,}(\=[a-z0-9_\-]{1,})?)+)?/';
		if (preg_match($ptrn, $url)) {
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
		if (preg_match("/^[\ \#\*\+\:\,\.0-9-]*$/", $phone)) {
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
	 * Short description for 'userpassgen'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $length Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'select_form'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $formdata Parameter description (if any) ...
	 * @param      array $errors Parameter description (if any) ...
	 * @return     void
	 */
	public function select_form($formdata = array(), $errors = array())
	{
		$result = include 'components/com_myaccount/select.html.php';
	}

	/**
	 * Short description for 'registration_form'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $task Parameter description (if any) ...
	 * @param      unknown &$xregistration Parameter description (if any) ...
	 * @return     void
	 */
	public function registration_form($task, &$xregistration)
	{
		$result = include 'components/com_myaccount/registration.html.php';
	}

	/**
	 * Short description for 'recovery_form'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $email Parameter description (if any) ...
	 * @param      array $errors Parameter description (if any) ...
	 * @return     void
	 */
	public function recovery_form($email, $errors = array())
	{
		$result = include 'components/com_myaccount/recovery.html.php';
	}

	/**
	 * Short description for 'raiselimits_form'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $resource Parameter description (if any) ...
	 * @param      unknown $admin Parameter description (if any) ...
	 * @param      unknown $target_xprofile Parameter description (if any) ...
	 * @return     void
	 */
	public function raiselimits_form($resource, $admin, $target_xprofile)
	{
		$result = include 'components/com_myaccount/raiselimits.html.php';
	}

	/**
	 * Short description for 'delete_form'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$HTTP_POST_VARS Parameter description (if any) ...
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      boolean $confirmSingleParent Parameter description (if any) ...
	 * @return     void
	 */
	public function delete_form(&$HTTP_POST_VARS, $uid, $confirmSingleParent = false)
	{
		$result = include 'components/com_myaccount/delete.html.php';
	}
}

