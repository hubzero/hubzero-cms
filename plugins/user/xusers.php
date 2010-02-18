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

jimport('joomla.event.plugin');

class plgUserXusers extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param array $config An array that holds the plugin configuration
	 */
	function plgUserXusers(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	/**
	* This method should handle any login logic and report back to the subject
	*
	* @access public
	* @param array holds the user data
	* @param array array holding options (remember, autoregister, group)
	* @return boolean True on success
	*/

	/**
	 * This method will return a user object
	 *
	 * If options['autoregister'] is true, if the user doesn't exist yet he will be created
	 *
	 * @access public
	 * @param array holds the user data
	 * @param array array holding options (remember, autoregister, group)
	 * @return object A JUser object
	 * @since 1.5
	 */

	function &_getUser($user, $options = array(), $allowTemp = false)
	{
		$instance = new JUser();
		if($id = intval(JUserHelper::getUserId($user['username']))) {
			$instance->load($id);
			return $instance;
		}
		
		//TODO : move this out of the plugin
		jimport('joomla.application.component.helper');
		$config = &JComponentHelper::getParams( 'com_users' );
		$usertype = $config->get( 'new_usertype', 'Registered' );
		
		$acl =& JFactory::getACL();
		
		$instance->set( 'id', 0 );
		$instance->set( 'name', $user['fullname'] );
		$instance->set( 'username', $user['username'] );
		$instance->set( 'password_clear', $user['password_clear'] );
		$instance->set( 'email', $user['email'] ); // Result should contain an email (check)
		$instance->set( 'gid', $acl->get_group_id( '', $usertype));
		$instance->set( 'usertype', $usertype );
		
		//If autoregister is set let's register the user
		$autoregister = isset($options['autoregister']) ? $options['autoregister'] : $this->params->get('autoregister', 1);
		
		if($autoregister)
		{
			if(!$instance->save()) {
				return JError::raiseError('500: Unable to register user ' . $user['email'], $instance->getError());
			}
		} else if ($allowTemp) {
			// No existing user and autoregister off, this is a temporary user.
			$instance->set( 'tmp_user', true );
		}
		else
			return JError::raiseError('SOME_ERROR_CODE', 'User does not exist');

		return $instance;
	}

	function onLoginUser($user, $options = array())
	{
		jimport('joomla.user.helper');
		
		$authlog = XFactory::getAuthLogger();

		$realm = isset($options['domain']) ? $options['domain'] : '';
		$username = isset($user['username']) ? $user['username'] : '';

		#SS
      		if (($domain = JFactory::getSession()->get('session.xauth.domain')))
			$realm = $domain;
		#/SS

		if (empty($realm) || ($realm == 'hzldap')) // local username
		{
			$instance =& $this->_getUser($user, $options);

			if ($instance === false)
			{
				$authlog->logAuth( $username . ' ' . $_SERVER['REMOTE_ADDR'] . 'login_failed');

				return JError::raiseError('SOME_ERROR_CODE', 'xHUB Internal Error: JUser record unavailable');
			}
		}
		else
		{
			$autoregister = isset($options['autoregister']) ? $options['autoregister'] : $this->params->get('autoregister', 1);

			$uid = XUserHelper::getXDomainUserId($username, $realm);

			$instance =& $this->_getUser($user, $options); // create user

			if ($instance === false)
			{
				$authlog->logAuth( $username . ' ' . $_SERVER['REMOTE_ADDR'] . 'login_failed');

				return JError::raiseError('SOME_ERROR_CODE', 'xHUB Internal Error: JUser record unavailable');
			}

			$parts = explode(':', $username);

			if (count($parts) > 1)
			{
				$realm_id = intval($parts[0]);

				if ($realm_id < 0)
				{
					$realm_id = - $realm_id;

					$realm_username = pack("H*", $parts[1]);

					XUserHelper::setXDomainUserId($realm_username, $realm, $instance->get('id'));
				}
			}
		}

		$authlog->logAuth( $instance->get('id') . ' [' . $instance->get('username') . '] ' . $_SERVER['REMOTE_ADDR'] . ' login');

		// drop a hub cookie

		jimport('joomla.utilities.simplecrypt');
		jimport('joomla.utilities.utility');

		//Create the encryption key, apply extra hardening using the user agent string

		$key = JUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);

		$crypt = new JSimpleCrypt($key);
		$ruser['username'] = $instance->get('username');
		$ruser['id'] = $instance->get('id');
		$rcookie = $crypt->encrypt(serialize($ruser));
		$lifetime = time() + 365*24*60*60;
		setcookie( JUtility::getHash('XHUB_REMEMBER'), $rcookie, $lifetime, '/' );

		/* Mark registration as incomplete so it gets checked on next page load */

		$session =& JFactory::getSession();
		$session->set('registration.incomplete', true);

		return true; 
	}

	/**
	 * Example store user method
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param array holds the new user data
	 * @param boolean true if a new user is stored
	 * @param boolean true if user was succesfully stored in the database
	 * @param string message
	 */
	function onAfterStoreUser($user, $isnew, $succes, $msg)
	{
		ximport('xprofile');

		$xhub =& XFactory::getHub();
		$hubHomeDir = $xhub->getCfg('hubHomeDir');

		$xprofile = XProfile::getInstance( $user['id'] );

		if (!is_object($xprofile))
		{
			$xprofile = new XProfile();
			$xprofile->set('gidNumber', '3000');
			$xprofile->set('gid','public');
			$xprofile->set('uidNumber', $user['id']);
			$xprofile->set('userPassword', $user['password']);
			$xprofile->set('homeDirectory', $hubHomeDir . '/' . $user['username']);
			$xprofile->set('loginShell','/bin/bash');
			$xprofile->set('ftpShell','/usr/lib/sftp-server');
			$xprofile->set('name', $user['name']);
			$xprofile->set('email', $user['email']);
			$xprofile->set('emailConfirmed', '3');
			$xprofile->set('username', $user['username']);
			$result = $xprofile->create();

			if (!$result)
			{
				return JError::raiseError('500', 'xHUB Internal Error: Unable to create XProfile record');
			}
		}
		else
		{
			$update = false;
			
			if ($xprofile->get('username') != $user['username'])
			{
				$xprofile->set('username', $user['username']);
				$update = true;
			}
	
			if ($xprofile->get('name') != $user['name'])
			{
				$xprofile->set('name', $user['name']);
				$update = true;
			}
	
			if ($xprofile->get('email') != $user['email'])
			{
				$xprofile->set('email', $user['email']);
				$xprofile->set('emailConfirmed', 0);
				$update = true;
			}

			if ($xprofile->get('emailConfirmed') == '')
			{
				$xprofile->set('emailConfirmed', '3');
				$update = true;
			}

			if ($update) {
				$xprofile->update();
			}
		}
	}

	/**
	 * Example store user method
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param array holds the user data
	 * @param boolean true if user was succesfully stored in the database
	 * @param string message
	 */
	function onAfterDeleteUser($user, $succes, $msg)
	{
		ximport('xprofile');
		ximport('xuserhelper');

		XUserHelper::deleteXDomainUserId($user['id']);

		$xprofile = XProfile::getInstance($user['id']);

		if (is_object($xprofile))
			$xprofile->deactivate();

		return true;
	}


	function onLogoutUser($user, $options = array())
	{
		$authlog = XFactory::getAuthLogger();
		$authlog->logAuth( $user['username'] . ' ' . $_SERVER['REMOTE_ADDR'] . ' logout');

		return true;
	}
}

?>
