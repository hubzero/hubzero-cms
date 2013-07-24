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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.event.plugin');

/**
 * User plugin for hub users
 */
class plgUserXusers extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	public function onUserLogin($user, $options = array())
	{
		return $this->onLoginUser($user, $options);
	}

	/**
	* This method should handle any login logic and report back to the subject
	*
	* @access public
	* @param array holds the user data
	* @param array array holding options (remember, autoregister, group)
	* @return boolean True on success
	*/
	public function onLoginUser($user, $options = array())
	{
		jimport('joomla.user.helper');

		$juser = &JFactory::getUser();   // get user from session (might be tmp_user, can't fetch from db)

		if ($juser->get('guest') == '1') { // joomla user plugin hasn't run or something went very badly

			$plugins = JPluginHelper::getPlugin('user');
			$xuser_order = false;
			$joomla_order = false;
			$i = 0;

			foreach ($plugins as $plugin) {
				
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

		// log login to auth log
		Hubzero_Factory::getAuthLogger()->logAuth($juser->get('id') . ' [' . $juser->get('username') . '] ' . $_SERVER['REMOTE_ADDR'] . ' login');
		
		// correct apache log data
		apache_note('auth','login');

		// update session tracking with new data
		$session = &JFactory::getSession();
		
		$session->set('tracker.user_id', $juser->get('id'));
		
		$session->set('tracker.username', $juser->get('username'));
		
		if ($session->get('tracker.sid') == '')
		{
			$session->set('tracker.sid', $session->getId());
		}

		$session->set('tracker.psid', $session->get('tracker.sid'));
					
		if ($session->get('tracker.rsid') == '')
		{
			$session->set('tracker.rsid', $session->getId());
		}
		
		if ( ($session->get('tracker.user_id') != $juser->get('id')) || ($session->get('tracker.ssid') == '') )
		{
			$session->set('tracker.ssid', $session->getId());
		}
		
		if (empty($user['type']))
		{
			$session->clear('session.authenticator');
		}
		else
		{
			$session->set('session.authenticator', $user['type']);
		}
		
		if (isset($options['silent']) && $options['silent'])
		{
			$session->set('session.source','cookie');
		}
		else
		{
			$session->set('session.source','user');
		}
		
		// update tracking data with changes related to login		
		jimport('joomla.utilities.simplecrypt');
		jimport('joomla.utilities.utility');
		
		$hash = JUtility::getHash( JFactory::getApplication()->getName().':tracker');
		
		$crypt = new JSimpleCrypt();
		
		$tracker = array();
		$tracker['user_id'] = $session->get('tracker.user_id');
		$tracker['username'] = $session->get('tracker.username');
		$tracker['sid']  = $session->getId();
		$tracker['rsid'] = $session->get('tracker.rsid', $tracker['sid']);
		$tracker['ssid'] = $session->get('tracker.ssid', $tracker['sid']);
		$cookie = $crypt->encrypt(serialize($tracker));
		$lifetime = time() + 365*24*60*60;
		setcookie($hash, $cookie, $lifetime, '/');
		
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
		else {
			
			if ($username[0] == '-') {
				
				$username = trim($username, '-');
				
				if ($hzal) {
					$hzal->user_id = $juser->get('id');
					$hzal->update();
				}
			}
		}

		if ($hzal) {
			$juser->set('auth_link_id',$hzal->id);
			$session->set('linkaccount', true);
		}

		$session =& JFactory::getSession();
		$session->set('registration.incomplete', true);

		return true;
	}

	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		return $this->onAfterStoreUser($user, $isnew, $success, $msg);
	}

	/**
	 * Method is called after user data is stored in the database
	 *
	 * @param array holds the new user data
	 * @param boolean true if a new user is stored
	 * @param boolean true if user was succesfully stored in the database
	 * @param string message
	 */
	public function onAfterStoreUser($user, $isnew, $succes, $msg)
	{
		ximport('Hubzero_User_Profile');
		ximport('Hubzero_User_Password');

		$xprofile = Hubzero_User_Profile::getInstance($user['id']);

		if (!is_object($xprofile)) {
						
			$params =& JComponentHelper::getParams('com_members');
		
			$hubHomeDir = rtrim($params->get('homedir'),'/');
		
			if (empty($hubHomeDir)) {
				// @FIXME: this is legacy joomla, should be replaced with correct solution
				JLoader::register('JTableComponent', JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'component.php');
				
				// try to deduce a viable home directory based on sitename or live_site
				$jconfig = JFactory::getConfig();
				$sitename = strtolower($jconfig->getValue('config.sitename'));
				$sitename = preg_replace('/^http[s]{0,1}:\/\//','',$sitename,1);
				$sitename = trim($sitename,'/ ');
				$sitename_e = explode('.', $sitename, 2);
				if (isset($sitename_e[1])) {
					$sitename = $sitename_e[0];
				}
				if (!preg_match("/^[a-zA-Z]+[\-_0-9a-zA-Z\.]+$/i", $sitename)) {
					$sitename = '';
				}
				if (empty($sitename)) {
					$sitename = strtolower(JURI::base());
					$sitename = preg_replace('/^http[s]{0,1}:\/\//','',$sitename,1);
					$sitename = trim($sitename,'/ ');
					$sitename_e = explode('.', $sitename, 2);
					if (isset($sitename_e[1])) {
						$sitename = $sitename_e[0];
					}
					if (!preg_match("/^[a-zA-Z]+[\-_0-9a-zA-Z\.]+$/i", $sitename)) {
						$sitename = '';
					}
				}
				
				$hubHomeDir = DS . 'home';

				if (!empty($sitename)) {
					$hubHomeDir .= DS . $sitename;
				}		

				if (!empty($hubHomeDir)) {
					$db = JFactory::getDBO();
					$component = new JTableComponent($db);
					$component->loadByOption('com_members');
					$params->set('homedir',$hubHomeDir);
					$component->params = $params->toString();
					$component->store();
				}
			}
			
			$xprofile = new Hubzero_User_Profile();
			
			$xprofile->set('gidNumber', $params->get('gidNumber', '100'));
			$xprofile->set('gid', $params->get('gid', 'users'));
			$xprofile->set('uidNumber', $user['id']);
			$xprofile->set('homeDirectory', $hubHomeDir . DS . $user['username']);
			$xprofile->set('loginShell', '/bin/bash');
			$xprofile->set('ftpShell', '/usr/lib/sftp-server');
			$xprofile->set('name', $user['name']);
			$xprofile->set('email', $user['email']);
			$xprofile->set('emailConfirmed', '3');
			$xprofile->set('username', $user['username']);
			$xprofile->set('jobsAllowed', 3);
			$xprofile->set('regIP', $_SERVER['REMOTE_ADDR']);
			$xprofile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
			$xprofile->set('public', $params->get('privacy', 0));
			
			if (isset($_SERVER['REMOTE_HOST'])) {
				$xprofile->set('regHost', $_SERVER['REMOTE_HOST']);
			}
			
			$xprofile->set('registerDate', date('Y-m-d H:i:s'));

			$result = $xprofile->create();

			if (!$result) {
				return JError::raiseError('500', 'xHUB Internal Error: Unable to create Hubzero_User_Profile record');
			}
		}
		else {
			$update = false;

			$params =& JComponentHelper::getParams('com_members');

			if ($xprofile->get('username') != $user['username']) {
				$xprofile->set('username', $user['username']);
				$update = true;
			}

			if ($xprofile->get('name') != $user['name']) {
				$xprofile->set('name', $user['name']);
				$update = true;
			}

			if ($xprofile->get('email') != $user['email']) {
				$xprofile->set('email', $user['email']);
				$xprofile->set('emailConfirmed', 0);
				$update = true;
			}

			if ($xprofile->get('emailConfirmed') == '')	{
				$xprofile->set('emailConfirmed', '3');
				$update = true;
			}

			if ($xprofile->get('gid') == '')
			{
				$xprofile->set('gid', $params->get('gid', 'users'));
				$update = true;
			}

			if ($xprofile->get('gidNumber') == '')
			{
				$xprofile->set('gidNumber', $params->get('gidNumber', '100'));
				$update = true;
			}

			if ($xprofile->get('loginShell') == '')
			{
				$xprofile->set('loginShell', '/bin/bash');
				$update = true;
			}

			if ($xprofile->get('ftpShell') == '')
			{
				$xprofile->set('ftpShell', '/usr/lib/sftp-server');

				// This isn't right, but we're using an empty shell as an indicator that we should also update jobs allowed and default privacy
				$xprofile->set('jobsAllowed', 3);
				$xprofile->set('public', $params->get('privacy', 0));

				$update = true;
			}

			if ($update) {
				$xprofile->update();
			}
		}
	}

	public function onUserAfterDelete($user, $succes, $msg)
	{
		return $this->onAfterDeleteUser($user, $succes, $msg);
	}

	/**
	 * Method is called after user data is deleted from the database
	 *
	 * @param array holds the user data
	 * @param boolean true if user was succesfully stored in the database
	 * @param string message
	 */
	public function onAfterDeleteUser($user, $succes, $msg)
	{
		ximport('Hubzero_User_Profile');
		ximport('Hubzero_Auth_Link');

		$xprofile = Hubzero_User_Profile::getInstance($user['id']);

		if (is_object($xprofile)) {
			$xprofile->delete();
		}

		Hubzero_Auth_Link::delete_by_user_id($user['id']);

		return true;
	}

	public function onUserLogout($user, $options = array())
	{
		return $this->onLogoutUser($user, $options);
	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @access public
	 * @param array holds the user data
	 * @return boolean True on success
	 */
	public function onLogoutUser($user, $options = array())
	{
		$authlog = Hubzero_Factory::getAuthLogger();
		
		$authlog->logAuth($user['username'] . ' ' . $_SERVER['REMOTE_ADDR'] . ' logout');
		
		apache_note('auth','logout');

		// If this is a temporary user created during the auth_link process (ex: username is a negative number)
		// and they're logging out (i.e. they didn't finish the process to create a full account),
		// then delete the temp account
		if(is_numeric($user['username']) && $user['username'] < 0)
		{
			$juser = &JFactory::getUser($user['id']);

			// Further check to make sure this was an abandoned auth_link account
			if(substr($juser->email, -8) == '@invalid')
			{
				// Delete the user
				$juser->delete();
			}
		}

		return true;
	}
}
