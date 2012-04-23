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

jimport( 'joomla.plugin.plugin' );

/**
 * Short description for 'plgXAuthenticationHzldap'
 * 
 * Long description (if any) ...
 */
class plgXAuthenticationHzldap extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param 	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 */
	function plgXAuthenticationHzldap(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Short description for 'logintobreeze'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $login Parameter description (if any) ...
	 * @param      unknown $passwd Parameter description (if any) ...
	 * @return     void
	 */
        function logintobreeze($login, $passwd)
        {
				$buffer = '';
                //Get login and password from a different file and do the stuff here

                //Logout any user first
                $this->logoutofbreeze();

                $login = strtolower($login);
                // Login Current user
                $handle = fopen("http://breeze.nanohub.org/api/xml?action=login&login=$login&password=$passwd","rb");

                // First check if the login was successful
                // else login failed

                //$file = "newfile.xml";
                //if (!$file_handle = fopen($file,"a")) { echo "Cannot open file"; }

                $header = $http_response_header;
                if ($handle) {
                        while (!feof($handle))
                        {
                                $buffer .= fgets($handle, 4096);
                                //echo $buffer.'@';

                                $bt=substr($buffer,1, 7);

                                if ( $bt == 'results'){
                                        //echo $buffer[1];
                                        for ($i=0;$i<100;$i++)
                                        {
                                                //echo $buffer[$i];
                                                //echo $bt[$i];
                                        }
                                }
                        //echo $buffer;
                        //if (!fwrite($file_handle, $buffer)) { echo "Cannot write to file"; }
                        //if (!fwrite($file_handle, '#')) { echo "Cannot write to file"; }
                        }
                }
                //echo $buffer;
                //$temp = explode(' ',$buffer);
                //echo $temp[0];
                //echo "You have successfully written data to $file";
                //fclose($file_handle);

                // Reading Breezesession cookie here
                $header = $http_response_header;
                $header_size=sizeof($http_response_header);

                //echo $temp1;
                for ($i=0;$i<$header_size;$i++)
                {
                        $sub1=substr($header[$i],0, 10);
                        if ( $sub1 == 'Set-Cookie') {
                                $string=$header[$i];
                                $needle=";";
                                $clean = substr("$string",0,strpos($string,$needle));
                                $cookie=ltrim(strrchr($clean,'='),'=');
                        }
                }
                //echo $string;
                //echo '<br/>';
                //echo $cookie;
                //cleaning up
                fclose($handle);

                // setting cookie
                setcookie("breezecookie",$cookie, time()+57600);

                //header('location:cookie.php');
        }

        // Funtion to log out of Breeze

	/**
	 * Short description for 'logoutofbreeze'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
        function logoutofbreeze()
        {
                setcookie("breezecookie","", time()-60000);
                $handle1 = fopen("http://breeze.nanohub.org/api/xml?action=logout","rb");
        }

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param   array 	$credentials Array holding the user credentials
	 * @param 	array   $options     Array of extra options
	 * @param	object	$response	Authentication response object
	 * @return	object	boolean
	 */
	function onAuthenticate( $credentials, $options, &$response )
	{
		// Initialize variables
		$userdetails = null;
		$success = 0;

		// For JLog
		$response->type = 'HUBzero';

		// LDAP does not like Blank passwords (tries to Anon Bind which is bad)
		if (empty($credentials['password']))
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'HUBzero can not have a blank password';
			return false;
		}

        $xhub =& Hubzero_Factory::getHub();

		$hubShortName = $xhub->getCfg('hubShortName','');
		$auth_method   = 'bind';
		$port          = '389';
        $base_dn       = $xhub->getCfg('hubLDAPBaseDN','ou=users,dc=localhost');
        $search_string = 'uid=[search],ou=users,' . $base_dn;
		$users_dn      = 'uid=[username],ou=users,' . $base_dn;
		$use_ldapV3    = 1;
		$no_referrals  = 1;
        $negotiate_tls = $xhub->getCfg('hubLDAPNegotiateTLS','0');
        $username      = $xhub->getCfg('hubLDAPSearchUserDN','uid=search,dc=localhost');
		$password      = $xhub->getCfg('hubLDAPSearchUserPW','');
        $host          = $xhub->getCfg('hubLDAPMasterHost','localhost');

		if (!$port)
			$port = '389';

    	$pattern = "/^\s*(ldap[s]{0,1}:\/\/|)([^:]*)(\:(\d+)|)\s*$/";

    	if (preg_match($pattern, $host, $matches))
		{
			$host = $matches[2];

    		if ($matches[1] == 'ldaps://')
        		$negotiate_tls = false;

    		if (isset($matches[4]) && is_numeric($matches[4]))
        		$port = $matches[4];
		}

        $_ldc = @ldap_connect($host, $port);

		if (!$_ldc)
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Unable to connect to the HUBzero LDAP server';
			return;
		}

		if ( $use_ldapV3 && !@ldap_set_option($_ldc, LDAP_OPT_PROTOCOL_VERSION, 3) )
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'HUBzero LDAP server refuses protocol version 3.';
			return;
		}

		if ( $use_ldapV3 && !@ldap_set_option($_ldc, LDAP_OPT_REFERRALS, $no_referrals) )
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'HUBzero LDAP server refuses to set referrals options.';
			return;
		}

		if ( $use_ldapV3 && $negotiate_tls && !@ldap_start_tls($_ldc))
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'HUBzero LDAP server refuses to start TLS mode.';
			return;
		}

		$attributes = array('mail','cn', 'uid');

		if ($auth_method == 'search')
		{
			if (strlen($username))
				$bindtest = @ldap_bind($username, $password);
			else
				$bindtest = @ldap_bind($_ldc);

			if($bindtest)
			{
				// Search for users DN
				$dn = str_replace("[search]", $credentials['username'], $search_string);

       			$search_result = @ldap_search($_ldc, $dn, '(objectClass=hubAccount)', array('mail','cn','uid'));

				$userdetails = @ldap_get_entries($_ldc, $search_result);

				// Verify Users Credentials
				$success = @ldap_bind($userdetails[0]['dn'][0],$credentials['password']);
			}
			else
			{
				$response->status = JAUTHENTICATE_STATUS_FAILURE;
				$response->error_message = 'Unable to bind to LDAP';
			}
		}
		else if ($auth_method == 'bind')
		{
			$dn = str_replace('[username]', $credentials['username'], $users_dn);

			$success = @ldap_bind($_ldc, $dn, $credentials['password']);

			$search_result = @ldap_search($_ldc, $dn, '(objectClass=hubAccount)', array('mail','cn','uid'));

			$userdetails = @ldap_get_entries($_ldc, $search_result);
		}

		if(!$success)
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Incorrect username/password';
		}
		else
		{
			// Grab some details from LDAP and return them
			if (isset($userdetails[0]['uid'][0])) {
				$response->username = $userdetails[0]['uid'][0];
			}

			if (isset($userdetails[0]['mail'][0])) {
				$response->email = $userdetails[0]['mail'][0];
			}

			if(isset($userdetails[0]['cn'][0])) {
				$response->fullname = $userdetails[0]['cn'][0];
			} else {
				$response->fullname = $credentials['username'];
			}

			$response->password_clear = '';

			// Were good - So say so.
			$response->status        = JAUTHENTICATE_STATUS_SUCCESS;
			$response->error_message = '';
			if ($hubShortName == 'nanoHUB.org')
				$this->logintobreeze( $credentials['username'], $credentials['password'] );
		}

		@ldap_close($_ldc);
	}
}

?>
