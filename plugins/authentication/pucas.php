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

ximport('Hubzero_Auth_Domain');
ximport('Hubzero_Auth_Link');

class plgAuthenticationPUCAS
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An array that holds the plugin configuration
	 */
	function plgAuthenticationJoomla(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	public function logout()
	{
		global $PHPCAS_CLIENT, $mainframe;

		if ( !is_object($PHPCAS_CLIENT) )
		{
			require_once(JPATH_SITE.DS.'libraries'.DS.'CAS-1.0.1'.DS.'CAS.php');
			phpCAS::setDebug();
			phpCAS::client(CAS_VERSION_2_0,'www.purdue.edu',443,'/apps/account/cas',false);
		}

		$xhub = Hubzero_Factory::getHub();

		$service = $xhub->getCfg('hubLongURL');

		if (empty($service))
		{
			$service = $_SERVER['HTTP_HOST'];
		}

		$return = '';

		if ($view->return)
			$return = "&return=" . $view->return;

		phpCAS::setFixedServiceURL($service . '/index.php?option=com_user&view=login&authenticator=pucas' . $return);
		phpCAS::setNoCasServerValidation();

		if (phpCAS::isAuthenticated() || phpCAS::checkAuthentication())
		{
			phpCAS::logoutWithUrl($service . '/index.php?option=com_user&view=login&authenticator=pucas' . $return);
		}
	}

	public function status()
	{
		global $PHPCAS_CLIENT, $mainframe;

		$status = array();

		if ( !is_object($PHPCAS_CLIENT) )
		{
			require_once(JPATH_SITE.DS.'libraries'.DS.'CAS-1.0.1'.DS.'CAS.php');
			phpCAS::setDebug();
			phpCAS::client(CAS_VERSION_2_0,'www.purdue.edu',443,'/apps/account/cas',false);
		}

		phpCAS::setNoCasServerValidation();

		if (phpCAS::checkAuthentication())
		{
			$status['username'] = phpCAS::getUser();
		}
		return $status;
	}

	public function login(&$credentials, &$options)
	{
		if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
			$return = base64_decode($return);
			if (!JURI::isInternal($return)) {
				$return = '';
			}
		}

		$options['return'] = $return;
	}

	public function display($view,$tpl)
	{
		global $PHPCAS_CLIENT, $mainframe;

		if ( !is_object($PHPCAS_CLIENT) )
		{
			require_once(JPATH_SITE.DS.'libraries'.DS.'CAS-1.0.1'.DS.'CAS.php');
			phpCAS::setDebug();
			phpCAS::client(CAS_VERSION_2_0,'www.purdue.edu',443,'/apps/account/cas',false);
		}

		$xhub = Hubzero_Factory::getHub();

		$service = $xhub->getCfg('hubLongURL');

		if (empty($service))
		{
			$service = $_SERVER['HTTP_HOST'];
		}

		$return = '';

		if ($view->return)
			$return = "&return=" . $view->return;

		phpCAS::setFixedServiceURL($service . '/index.php?option=com_user&task=login&authenticator=pucas' . $return);
		phpCAS::setNoCasServerValidation();
		phpCAS::forceAuthentication();

		$mainframe->redirect($service . '/index.php?option=com_user&task=login&authenticator=pucas' . $return);
	}

	public function onAuthenticate( $credentials, $options, &$response )
	{
		global $PHPCAS_CLIENT;

		if ( !is_object($PHPCAS_CLIENT) )
		{
			require_once(JPATH_SITE.DS.'libraries'.DS.'CAS-1.0.1'.DS.'CAS.php');
			phpCAS::setDebug();
			phpCAS::client(CAS_VERSION_2_0,'www.purdue.edu',443,'/apps/account/cas',false);
		}

		phpCAS::setNoCasServerValidation();

		if (phpCAS::isAuthenticated())
		{
			$username = phpCAS::getUser();

			$hzal = Hubzero_Auth_Link::find_or_create('authentication','pucas',null,$username);
			$hzal->email = $username . '@purdue.edu';

			$response->auth_link = $hzal;
			$response->type = 'pucas';
			$response->status = JAUTHENTICATE_STATUS_SUCCESS;

			// Grab some details from LDAP and return them

			if( $_ldc_ped = @ldap_connect("ldap://ped.purdue.edu") )
			{
				if (@ldap_bind($_ldc_ped))
				{
					$search_result = @ldap_search($_ldc_ped, "uid=" . phpCAS::getUser() .
						',ou=ped,dc=purdue,dc=edu', '(objectClass=*)' , array('mail','cn'));
                    			$userdetails = @ldap_get_entries($_ldc_ped, $search_result);
					if (!empty($userdetails[0]['mail'][0]))
						$hzal->email = $userdetails[0]['mail'][0];
					if (!empty($userdetails[0]['cn'][0]))
						$response->fullname = ucwords(strtolower($userdetails[0]['cn'][0]));
               			}

				@ldap_close($_ldc_ped);
			}

			if (!empty($hzal->user_id)) {
				$user = JUser::getInstance($hzal->user_id); // Bring this in line with the rest of the system

				$response->username = $user->username;
				$response->email = $user->email;
				$response->fullname = $user->name;
			}
			else {
				$response->username = '-' . $hzal->id; // The Open Group Base Specifications Issue 6, Section 3.426
				$response->email = $response->username . '@invalid'; // RFC2606, section 2
			}

			$hzal->update();

		}
		else
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Not Authenticated..';
		}
	}
}

?>