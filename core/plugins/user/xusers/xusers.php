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

// No direct access
defined('_HZEXEC_') or die();

/**
 * User plugin for hub users
 */
class plgUserXusers extends \Hubzero\Plugin\Plugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  Event observer
	 * @param   array   $config    Optional config values
	 * @return  void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->database = App::get('db');
	}

	/**
	* This method is an alias for onLoginUser
	*
	* @param   array    $user     holds the user data
	* @param   array    $options  array holding options (remember, autoregister, group)
	* @return  boolean  True on success
	*/
	public function onUserLogin($user, $options = array())
	{
		return $this->onLoginUser($user, $options);
	}

	/**
	* This method should handle any login logic and report back to the subject
	*
	* @param   array    $user     holds the user data
	* @param   array    $options  array holding options (remember, autoregister, group)
	* @return  boolean  True on success
	*/
	public function onLoginUser($user, $options = array())
	{
		jimport('joomla.user.helper');

		$xuser = User::getRoot(); // get user from session (might be tmp_user, can't fetch from db)

		if ($xuser->get('guest'))
		{
			// joomla user plugin hasn't run or something went very badly

			$plugins = Plugin::byType('user');
			$xuser_order = false;
			$joomla_order = false;
			$i = 0;

			foreach ($plugins as $plugin)
			{
				if ($plugin->name == 'xusers')
				{
					$xuser_order = $i;
				}

				if ($plugin->name == 'joomla')
				{
					$joomla_order = $i;
				}

				$i++;
			}

			if ($joomla_order === false)
			{
				return new Exception(Lang::txt('E_JOOMLA_USER_PLUGIN_MISCONFIGURED'), 500);
			}

			if ($xuser_order <= $joomla_order)
			{
				return new Exception(Lang::txt('E_HUBZERO_USER_PLUGIN_MISCONFIGURED'), 500);
			}

			return new Exception(Lang::txt('E_JOOMLA_USER_PLUGIN_FAILED'), 500);
		}

		// log login to auth log
		Log::auth($xuser->get('id') . ' [' . $xuser->get('username') . '] ' . $_SERVER['REMOTE_ADDR'] . ' login');

		// correct apache log data
		apache_note('auth','login');

		// Log attempt to the database
		Hubzero\User\User::oneOrFail($xuser->get('id'))->logger()->auth()->save(
		[
			'username' => $xuser->get('username'),
			'status'   => 'success'
		]);

		// update session tracking with new data
		$session = App::get('session');

		$session->set('tracker.user_id', $xuser->get('id'));
		$session->set('tracker.username', $xuser->get('username'));

		if ($session->get('tracker.sid') == '')
		{
			$session->set('tracker.sid', $session->getId());
		}

		$session->set('tracker.psid', $session->get('tracker.sid'));

		if ($session->get('tracker.rsid') == '')
		{
			$session->set('tracker.rsid', $session->getId());
		}

		if (($session->get('tracker.user_id') != $xuser->get('id')) || ($session->get('tracker.ssid') == ''))
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
		jimport('joomla.utilities.utility');

		$hash = App::hash(App::get('client')->name . ':tracker');

		$key = \App::hash('');
		$crypt = new \Hubzero\Encryption\Encrypter(
			new \Hubzero\Encryption\Cipher\Simple,
			new \Hubzero\Encryption\Key('simple', $key, $key)
		);

		$tracker = array();
		$tracker['user_id'] = $session->get('tracker.user_id');
		$tracker['username'] = $session->get('tracker.username');
		$tracker['sid']  = $session->getId();
		$tracker['rsid'] = $session->get('tracker.rsid', $tracker['sid']);
		$tracker['ssid'] = $session->get('tracker.ssid', $tracker['sid']);
		$cookie = $crypt->encrypt(serialize($tracker));
		$lifetime = time() + 365*24*60*60;

		// Determine whether cookie should be 'secure' or not
		$secure   = false;
		$forceSsl = \Config::get('force_ssl', false);

		if (\App::isAdmin() && $forceSsl >= 1)
		{
			$secure = true;
		}
		else if (\App::isSite() && $forceSsl == 2)
		{
			$secure = true;
		}

		setcookie($hash, $cookie, $lifetime, '/', '', $secure, true);

		/* Mark registration as incomplete so it gets checked on next page load */

		$username = $xuser->get('username');

		if (isset($user['auth_link']) && is_object($user['auth_link']))
		{
			$hzal = $user['auth_link'];
		}
		else
		{
			$hzal = null;
		}

		if ($xuser->get('tmp_user'))
		{
			$email = $xuser->get('email');

			if ($username[0] == '-')
			{
				$username = trim($username,'-');

				if ($hzal)
				{
					$xuser->set('username','guest;' . $username);
					$xuser->set('email', $hzal->email);
				}
			}
		}
		else {

			if ($username[0] == '-')
			{
				$username = trim($username, '-');

				if ($hzal)
				{
					$hzal->user_id = $xuser->get('id');
					$hzal->update();
				}
			}
		}

		if ($hzal)
		{
			$xuser->set('auth_link_id',$hzal->id);
			$session->set('linkaccount', true);
		}

		$session->set('registration.incomplete', true);

		// Check if quota exists for the user
		$params = Component::params('com_members');

		if ($params->get('manage_quotas', false))
		{
			require_once PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'users_quotas.php';
			require_once PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'quotas_classes.php';

			$quota = new \Components\Members\Tables\UsersQuotas($this->database);
			$quota->load(array('user_id' => $xuser->get('id')));

			if (!$quota->id)
			{
				$class = new \Components\Members\Tables\QuotasClasses($this->database);
				$class->load(array('alias'=>'default'));

				if ($class->id)
				{
					$quota->set('user_id'    , $xuser->get('id'));
					$quota->set('class_id'   , $class->id);
					$quota->set('soft_blocks', $class->soft_blocks);
					$quota->set('hard_blocks', $class->hard_blocks);
					$quota->set('soft_files' , $class->soft_files);
					$quota->set('hard_files' , $class->hard_files);
					$quota->store();
				}
			}
			else if ($quota->class_id)
			{
				// Here, we're checking to make sure their class matches their actual quota values
				$class = new \Components\Members\Tables\QuotasClasses($this->database);
				$class->load($quota->class_id);

				if ($quota->get('soft_blocks') != $class->get('soft_blocks')
				 || $quota->get('hard_blocks') != $class->get('hard_blocks')
				 || $quota->get('soft_files')  != $class->get('soft_files')
				 || $quota->get('hard_files')  != $class->get('hard_files'))
				{
					$quota->set('user_id'    , $xuser->get('id'));
					$quota->set('class_id'   , $class->id);
					$quota->set('soft_blocks', $class->soft_blocks);
					$quota->set('hard_blocks', $class->hard_blocks);
					$quota->set('soft_files' , $class->soft_files);
					$quota->set('hard_files' , $class->hard_files);
					$quota->store();
				}
			}
		}

		return true;
	}

	/**
	 * This method is an alias for onAfterStoreUser
	 *
	 * @param   array    $user     holds the new user data
	 * @param   boolean  $isnew    true if a new user is stored
	 * @param   boolean  $success  true if user was succesfully stored in the database
	 * @param   string   $msg      message
	 * @return  void
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		return $this->onAfterStoreUser($user, $isnew, $success, $msg);
	}

	/**
	 * Method is called after user data is stored in the database
	 *
	 * @param   array    $user     holds the new user data
	 * @param   boolean  $isnew    true if a new user is stored
	 * @param   boolean  $success  true if user was succesfully stored in the database
	 * @param   string   $msg      message
	 * @return  void
	 */
	public function onAfterStoreUser($user, $isnew, $succes, $msg)
	{
		$xprofile = \Hubzero\User\Profile::getInstance($user['id']);

		if (!is_object($xprofile))
		{
			$params = Component::params('com_members');

			$hubHomeDir = rtrim($params->get('homedir'),'/');

			if (empty($hubHomeDir))
			{
				// try to deduce a viable home directory based on sitename or live_site
				$sitename = strtolower(Config::get('sitename'));
				$sitename = preg_replace('/^http[s]{0,1}:\/\//','',$sitename,1);
				$sitename = trim($sitename,'/ ');
				$sitename_e = explode('.', $sitename, 2);
				if (isset($sitename_e[1]))
				{
					$sitename = $sitename_e[0];
				}
				if (!preg_match("/^[a-zA-Z]+[\-_0-9a-zA-Z\.]+$/i", $sitename))
				{
					$sitename = '';
				}
				if (empty($sitename))
				{
					$sitename = strtolower(Request::base());
					$sitename = preg_replace('/^http[s]{0,1}:\/\//','',$sitename,1);
					$sitename = trim($sitename,'/ ');
					$sitename_e = explode('.', $sitename, 2);
					if (isset($sitename_e[1]))
					{
						$sitename = $sitename_e[0];
					}
					if (!preg_match("/^[a-zA-Z]+[\-_0-9a-zA-Z\.]+$/i", $sitename))
					{
						$sitename = '';
					}
				}

				$hubHomeDir = DS . 'home';

				if (!empty($sitename))
				{
					$hubHomeDir .= DS . $sitename;
				}

				if (!empty($hubHomeDir))
				{
					$db = App::get('db');

					$component = new JTableExtension($this->database);
					$component->load($component->find(array('element' => 'com_members', 'type' => 'component')));
					$params = new \Hubzero\Config\Registry($component->params);
					$params->set('homedir',$hubHomeDir);
					$component->params = $params->toString();
					$component->store();
				}
			}

			$xprofile = new \Hubzero\User\Profile();

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
			$xprofile->set('regIP', $_SERVER['REMOTE_ADDR']);
			$xprofile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
			$xprofile->set('public', $params->get('privacy', 0));

			if (isset($_SERVER['REMOTE_HOST']))
			{
				$xprofile->set('regHost', $_SERVER['REMOTE_HOST']);
			}

			$xprofile->set('registerDate', Date::toSql());

			$result = $xprofile->create();

			if (!$result)
			{
				return new Exception('Unable to create \Hubzero\User\Profile record', 500);
			}
		}
		else
		{
			$update = false;

			$params = Component::params('com_members');

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

			// Fix missing surname/given name as well
			if ($xprofile->get('name') && (!$xprofile->get('surname') || !$xprofile->get('givenName')))
			{
				$firstname  = $xprofile->get('givenName');
				$middlename = $xprofile->get('middleName');
				$lastname   = $xprofile->get('surname');

				$words = array_map('trim', explode(' ', $xprofile->get('name')));
				$count = count($words);

				if ($count == 1)
				{
					$firstname = $words[0];
				}
				else if ($count == 2)
				{
					$firstname = $words[0];
					$lastname  = $words[1];
				}
				else if ($count == 3)
				{
					$firstname  = $words[0];
					$middlename = $words[1];
					$lastname   = $words[2];
				}
				else
				{
					$firstname  = $words[0];
					$lastname   = $words[$count-1];
					$middlename = $words[1];

					for ($i = 2; $i < $count-1; $i++)
					{
						$middlename .= ' ' . $words[$i];
					}
				}

				$firstname = trim($firstname);
				if ($firstname)
				{
					$xprofile->set('givenName', $firstname);
				}
				$middlename = trim($middlename);
				if ($middlename)
				{
					$xprofile->set('middleName', $middlename);
				}
				$lastname = trim($lastname);
				if ($lastname)
				{
					$xprofile->set('surname', $lastname);
				}
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

				// This isn't right, but we're using an empty shell as an indicator that we should also update default privacy
				$xprofile->set('public', $params->get('privacy', 0));

				$update = true;
			}

			if ($update)
			{
				$xprofile->update();
			}
		}

		// Check if quota exists for the user
		$params = Component::params('com_members');

		if ($params->get('manage_quotas', false))
		{
			require_once PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'users_quotas.php';
			require_once PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'quotas_classes.php';

			$quota = new \Components\Members\Tables\UsersQuotas($this->database);
			$quota->load(array('user_id'=>$user['id']));

			if (!$quota->id)
			{
				$class = new \Components\Members\Tables\QuotasClasses($this->database);
				$class->load(array('alias'=>'default'));

				if ($class->id)
				{
					$quota->set('user_id'    , $user['id']);
					$quota->set('class_id'   , $class->id);
					$quota->set('soft_blocks', $class->soft_blocks);
					$quota->set('hard_blocks', $class->hard_blocks);
					$quota->set('soft_files' , $class->soft_files);
					$quota->set('hard_files' , $class->hard_files);
					$quota->store();
				}
			}
		}
	}

	/**
	 * This method is an alias for onAfterDeleteUser
	 *
	 * @param   array    $user     holds the user data
	 * @param   boolean  $success  true if user was succesfully stored in the database
	 * @param   string   $msg      message
	 * @return  boolean  True on success
	 */
	public function onUserAfterDelete($user, $succes, $msg)
	{
		return $this->onAfterDeleteUser($user, $succes, $msg);
	}

	/**
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user     holds the user data
	 * @param   boolean  $success  true if user was succesfully stored in the database
	 * @param   string   $msg      message
	 * @return  boolean  True on success
	 */
	public function onAfterDeleteUser($user, $succes, $msg)
	{
		$xprofile = \Hubzero\User\Profile::getInstance($user['id']);

		// remove user from groups
		\Hubzero\User\Helper::removeUserFromGroups($user['id']);

		if (is_object($xprofile))
		{
			$xprofile->delete();
		}

		\Hubzero\Auth\Link::delete_by_user_id($user['id']);

		// Check if quota exists for the user
		require_once PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'users_quotas.php';

		$quota = new \Components\Members\Tables\UsersQuotas($this->database);
		$quota->load(array('user_id'=>$user['id']));
		if ($quota->id)
		{
			$quota->delete();
		}

		return true;
	}

	/**
	 * This method is an alias for onLogoutUser
	 *
	 * @param   array    $user     holds the user data
	 * @param   array    $options  array holding options (remember, autoregister, group)
	 * @return  boolean  True on success
	 */
	public function onUserLogout($user, $options = array())
	{
		return $this->onLogoutUser($user, $options);
	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @param   array    $user     holds the user data
	 * @param   array    $options  array holding options (remember, autoregister, group)
	 * @return  boolean  True on success
	 */
	public function onLogoutUser($user, $options = array())
	{
		Log::auth($user['username'] . ' ' . $_SERVER['REMOTE_ADDR'] . ' logout');

		apache_note('auth','logout');

		// If this is a temporary user created during the auth_link process (ex: username is a negative number)
		// and they're logging out (i.e. they didn't finish the process to create a full account),
		// then delete the temp account
		if (is_numeric($user['username']) && $user['username'] < 0)
		{
			$user = User::getInstance($user['id']);

			// Further check to make sure this was an abandoned auth_link account
			if (substr($user->get('email'), -8) == '@invalid')
			{
				// Delete the user
				$user->delete();
			}
		}

		return true;
	}

	/**
	 * Hook for login failure
	 *
	 * @param   unknown  $response
	 * @return  boolean
	 */
	public function onUserLoginFailure($response)
	{
		// Log attempt to the database
		Hubzero\User\User::blank()->logger()->auth()->set(
		[
			'user_id'  => 0,
			'username' => isset($response['username']) ? $response['username'] : '[unknown]',
			'status'   => 'failure'
		])->save();

		return true;
	}
}