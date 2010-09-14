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
	function onLoginUser($user, $options = array())
	{
		jimport('joomla.user.helper');
		
		$juser = &JFactory::getUser();   // get user from session (might be tmp_user, can't fetch from db)

		if ($juser->get('guest') == '1') // joomla user plugin hasn't run or something went very badly
		{
			$plugins = JPluginHelper::getPlugin('user');
			$xuser_order = false;
			$joomla_order = false;
			$i = 0;
			
			foreach ($plugins as $plugin)
			{
				if ($plugin->name == 'xusers') {
					$xuser_order = $i;
				}
				
				if ($plugin->name == 'joomla') {
					$joomla_order = $i;
				}
				
				$i++;
			}
			
			if ($joomla_order === false) {
				return JError::raiseError('SOME_ERROR_CODE', JText::_('E_JOOMLA_USER_PLUGIN_MISCONFIGURED'));
			}
			
			if ($xuser_order <= $joomla_order) {
				return JError::raiseError('SOME_ERROR_CODE', JText::_('E_HUBZERO_USER_PLUGIN_MISCONFIGURED'));
			}

			return JError::raiseWarning('SOME_ERROR_CODE', JText::_('E_JOOMLA_USER_PLUGIN_FAILED'));
		}

		$authlog = XFactory::getAuthLogger();

		if ($juser->get('id') == '0')
		{
			$authlog->logAuth( $juser->get('id') . ' ' . $_SERVER['REMOTE_ADDR'] . 'auth');
			apache_note('auth','auth');

		}
		else
		{
			$authlog->logAuth( $juser->get('id') . ' [' . $juser->get('username') . '] ' . $_SERVER['REMOTE_ADDR'] . ' login');
			apache_note('auth','login');
		}
		
		// drop a hub cookie

		jimport('joomla.utilities.simplecrypt');
		jimport('joomla.utilities.utility');

		//Create the encryption key, apply extra hardening using the user agent string

		$key = JUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);

		$crypt = new JSimpleCrypt($key);
		$ruser['username'] = $juser->get('username');
		$ruser['id'] = $juser->get('id');
		$rcookie = $crypt->encrypt(serialize($ruser));
		$lifetime = time() + 365*24*60*60;
		setcookie( JUtility::getHash('XHUB_REMEMBER'), $rcookie, $lifetime, '/' );

		/* Mark registration as incomplete so it gets checked on next page load */
		
		$username = $juser->get('username');

		if (isset($user['auth_link']) && is_object($user['auth_link'])) {
			$hzal = $user['auth_link'];
		}
		else {
			$hzal = null;
		}
		
		if ($juser->get('tmp_user')) {
			$email = $juser->get('email');
			
			if ($username[0] == '-') {
				$username = trim($username,'-');
				if ($hzal) {
					$juser->set('username','guest;' . $username);
					$juser->set('email', $hzal->email);
				}
			}
		}
		else
		{
			if ($username[0] == '-') {
				$username = trim($username,'-');
				if ($hzal) {
					$hzal->user_id = $juser->get('id');
					$hzal->update();
				}
			}			
		}
		
		if ($hzal)
			$juser->set('auth_link_id',$hzal->id);
		
		$session =& JFactory::getSession();
		$session->set('registration.incomplete', true);

		return true; 
	}

	/**
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

		$xprofile = XProfile::getInstance( $user['username'] );

		if (!is_object($xprofile))
		{
			$xprofile = new XProfile();
			$xprofile->set('gidNumber', '3000');
			$xprofile->set('gid','public');
			$xprofile->set('uidNumber', $user['id']);
			$xprofile->set('password', $user['password']);
			$xprofile->set('homeDirectory', $hubHomeDir . '/' . $user['username']);
			$xprofile->set('loginShell','/bin/bash');
			$xprofile->set('ftpShell','/usr/lib/sftp-server');
			$xprofile->set('name', $user['name']);
			$xprofile->set('email', $user['email']);
			$xprofile->set('emailConfirmed', '3');
			$xprofile->set('username', $user['username']);
			$xprofile->set('jobsAllowed', 3);
			$xprofile->set('regIP', $_SERVER['REMOTE_ADDR']);
			$xprofile->set('emailConfirmed', -rand(1, pow(2, 31)-1) );
			if (isset($_SERVER['REMOTE_HOST'])) {
				$xprofile->set('regHost', $_SERVER['REMOTE_HOST']);
			}
			$xprofile->set('registerDate', date('Y-m-d H:i:s'));

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
	 * Method is called after user data is deleted from the database
	 *
	 * @param array holds the user data
	 * @param boolean true if user was succesfully stored in the database
	 * @param string message
	 */
	function onAfterDeleteUser($user, $succes, $msg)
	{
		ximport('xprofile');
		ximport('Hubzero_Auth_Link');

		$xprofile = XProfile::getInstance($user['id']);

		if (is_object($xprofile))
			$xprofile->delete();

		Hubzero_Auth_Link::delete_by_user_id($user['id']);
			
		return true;
	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @access public
	 * @param array holds the user data
	 * @return boolean True on success
	 */
	function onLogoutUser($user, $options = array())
	{
		$authlog = XFactory::getAuthLogger();
		$authlog->logAuth( $user['username'] . ' ' . $_SERVER['REMOTE_ADDR'] . ' logout');
		apache_note('auth','logout');
		return true;
	}
}

?>
