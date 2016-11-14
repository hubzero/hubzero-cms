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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Helpers;

/**
 * Helper class for registration.
 * Use primarily for input validation.
 */
class Utility
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
	 * Validate name
	 *
	 * @param      string $name the name to validate
	 * @return     integer 1 = valid, 0 = invalid
	 */
	public static function validname($name)
	{
		// Exclude all non-printable characters and the ':'
		// ':' can mess up ldap entries
		if (preg_match("/^[^:\p{C}]*$/u", $name))
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
	 * sendConfirmEmail 
	 * 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function sendConfirmEmail($user, $xregistration)
	{
		$baseURL = rtrim(Request::base(), '/');

		$subject  = Config::get('sitename').' '.Lang::txt('COM_MEMBERS_REGISTER_EMAIL_CONFIRMATION');
		
		$eview = new \Hubzero\Mail\View(array(
			'name'   => 'emails',
			'layout' => 'create'
		));
		$eview->option        = 'com_members';//$this->_option; //com_members
		$eview->controller    = 'register'; //$this->_controller; //register
		$eview->sitename      = Config::get('sitename');
		$eview->xprofile      = $user;
		$eview->baseURL       = $baseURL;
		$eview->xregistration = $xregistration;
		
		$msg = new \Hubzero\Mail\Message();
		$msg->setSubject($subject)
		    ->addTo($user->get('email'), $user->get('name'))
		    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
		    ->addHeader('X-Component', 'com_members');
		
		$message = $eview->loadTemplate(false);
		$message = str_replace("\n", "\r\n", $message);
		
		$msg->addPart($message, 'text/plain');
		
		$eview->setLayout('create_html');
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);
		
		$msg->addPart($message, 'text/html');
		
		if (!$msg->send())
		{
			$this->setError(Lang::txt('COM_MEMBERS_REGISTER_ERROR_EMAILING_CONFIRMATION'/*, $hubMonitorEmail*/));
			// @FIXME: LOG ERROR SOMEWHERE
			return false;
		}
		else
		{
			return true;
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
		$db = \App::get('db');

		$query = "SELECT `id` FROM `#__users` WHERE `activation` = " . $db->quote('-' . $code) . " LIMIT 1";
		$db->setQuery($query);
		$result = $db->loadResult();

		return ($result) ? true : false;
	}
}

