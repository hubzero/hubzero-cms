<?php
/**
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Joomla User plugin
 */
class plgUserJoomla extends \Hubzero\Plugin\Plugin
{
	/**
	 * Remove all sessions for the user name
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user    Holds the user data
	 * @param   boolean  $succes  True if user was succesfully stored in the database
	 * @param   string   $msg     Message
	 * @return  boolean
	 */
	public function onUserAfterDelete($user, $succes, $msg)
	{
		if (!$succes)
		{
			return false;
		}

		$db = App::get('db');
		$db->setQuery(
			'DELETE FROM ' . $db->quoteName('#__session') .
			' WHERE ' . $db->quoteName('userid') . ' = ' . (int) $user['id']
		);
		$db->Query();

		return true;
	}

	/**
	 * Utility method to act on a user after it has been saved.
	 *
	 * This method sends a registration email to new users created in the backend.
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isnew    True if a new user is stored.
	 * @param   boolean  $success  True if user was succesfully stored in the database.
	 * @param   string   $msg      Message.
	 * @return  void
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		// Existing user - nothing to do...yet.
		if (!$isnew)
		{
			return;
		}

		// Initialise variables.
		$config = App::get('config');

		if (App::isSite())
		{
			if ($this->params->get('mail_to_admin', 1))
			{
				$lang = App::get('language');
				$lang->load('plg_user_' . $this->_name, PATH_APP . DS . 'bootstrap' . DS . 'site') ||
				$lang->load('plg_user_' . $this->_name, PATH_APP . DS . 'bootstrap' . DS . 'administrator') ||
				$lang->load('plg_user_' . $this->_name, __DIR__);

				$emailAddress = $config->get('mailfrom');

				$profile = User::getInstance($user['id']);

				$eview = new Hubzero\Mail\View(array(
					'base_path' => __DIR__,
					'name'      => 'emails',
					'layout'    => 'admincreate_plain'
				));
				$eview->set('user', $profile);
				$eview->set('sitename', $config->get('sitename'));

				$plain = $eview->loadTemplate(false);
				$plain = str_replace("\n", "\r\n", $plain);

				$eview->setLayout('admincreate_html');
				$html = $eview->loadTemplate();
				$html = str_replace("\n", "\r\n", $html);

				// Assemble the email data
				$mail = new Hubzero\Mail\Message();
				$mail
					->addFrom(
						$emailAddress,
						Lang::txt('PLG_USER_JOOMLA_EMAIL_ADMIN', $config->get('sitename'))
					)
					->addTo($emailAddress)
					->addHeader('X-Component', Request::getCmd('option', 'com_members'))
					->addHeader('X-Component-Object', 'user_creation_admin_notification')
					->setSubject(Lang::txt('PLG_USER_JOOMLA_EMAIL_ACCOUNT_CREATION', $config->get('sitename')))
					->addPart($plain, 'text/plain')
					->addPart($html, 'text/html');

				if (!$mail->send())
				{
					// TODO: Probably should raise a plugin error but this event is not error checked.
					Log::error(Lang::txt('PLG_USER_JOOMLA_EMAIL_ERROR', $emailAddress));
				}
			}
		}

		// TODO: Suck in the frontend registration emails here as well. Job for a rainy day.
		if (App::isAdmin())
		{
			if ($this->params->get('mail_to_user', 0))
			{
				$lang = App::get('language');
				$defaultLocale = $lang->getTag();

				// Look for user language. Priority:
				//  1. User frontend language
				//  2. User backend language
				$userParams = new Hubzero\Config\Registry($user['params']);
				$userLocale = $userParams->get('language', $userParams->get('admin_language', $defaultLocale));

				if ($userLocale != $defaultLocale)
				{
					$lang->setLanguage($userLocale);
				}

				$lang->load('plg_user_' . $this->_name, PATH_APP . DS . 'bootstrap' . DS . 'site') ||
				$lang->load('plg_user_' . $this->_name, PATH_APP . DS . 'bootstrap' . DS . 'administrator') ||
				$lang->load('plg_user_' . $this->_name, __DIR__);

				// Compute the mail subject.
				$emailSubject = Lang::txt(
					'PLG_USER_JOOMLA_NEW_USER_EMAIL_SUBJECT',
					$user['name'],
					$config->get('sitename')
				);

				// Compute the mail body.
				$emailBody = Lang::txt(
					'PLG_USER_JOOMLA_NEW_USER_EMAIL_BODY',
					$user['name'],
					$config->get('sitename'),
					Request::root(),
					$user['username'],
					$user['password_clear']
				);

				// Assemble the email data...the sexy way!
				$mail = new Hubzero\Mail\Message();
				$mail
					->addFrom(
						$config->get('mailfrom'),
						$config->get('fromname')
					)
					->addTo($user['email'])
					->setSubject($emailSubject)
					->setBody($emailBody);

				// Set application language back to default if we changed it
				if ($userLocale != $defaultLocale)
				{
					$lang->setLanguage($defaultLocale);
				}

				if (!$mail->send())
				{
					// TODO: Probably should raise a plugin error but this event is not error checked.
					throw new Exception(Lang::txt('PLG_USER_JOOMLA_EMAIL_ERROR'), 500);
				}
			}
		}
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array    $user     Holds the user data
	 * @param   array    $options  Array holding options (remember, autoregister, group)
	 * @return  boolean  True on success
	 */
	public function onUserLogin($user, $options = array())
	{
		$instance = $this->_getUser($user, $options);

		// If _getUser returned an error, then pass it back.
		if ($instance instanceof Exception)
		{
			return false;
		}

		// If the user is blocked, redirect with an error
		if ($instance->get('block') == 1)
		{
			Notify::warning(Lang::txt('JERROR_NOLOGIN_BLOCKED'));
			return false;
		}

		// Authorise the user based on the group information
		if (!isset($options['group']))
		{
			$options['group'] = 'USERS';
		}

		// Chek the user can login.
		$result	= $instance->authorise($options['action']);
		if (!$result)
		{
			Notify::warning(Lang::txt('JERROR_LOGIN_DENIED'));
			return false;
		}

		// Mark the user as logged in
		$instance->set('guest', 0);

		// Register the needed session variables
		$session = App::get('session');
		$session->set('user', $instance);

		// Check to see the the session already exists.
		if ((App::get('config')->get('session_handler') != 'database' && (time() % 2 || $session->isNew()))
		 || (App::get('config')->get('session_handler') == 'database' && $session->isNew()))
		{
			if (App::get('config')->get('session_handler') == 'database' && App::has('db'))
			{
				$db = App::get('db');

				$query = $db->getQuery(true);
				$query->select($query->qn('session_id'))
					->from($query->qn('#__session'))
					->where($query->qn('session_id') . ' = ' . $query->q($session->getId()));

				$db->setQuery($query, 0, 1);
				$exists = $db->loadResult();

				// If the session record doesn't exist initialise it.
				if (!$exists)
				{
					$query->clear();

					$ip = Request::ip();

					if ($session->isNew())
					{
						$query->insert($query->qn('#__session'))
							->columns($query->qn('session_id') . ', ' . $query->qn('client_id') . ', ' . $query->qn('time') .  ', ' . $query->qn('ip'))
							->values($query->q($session->getId()) . ', ' . (int) App::get('client')->id . ', ' . $query->q((int) time()) . ', ' . $query->q($ip));
						$db->setQuery($query);
					}
					else
					{
						$query->insert($query->qn('#__session'))
							->columns(
								$query->qn('session_id') . ', ' . $query->qn('client_id') . ', ' . $query->qn('guest') . ', ' .
								$query->qn('time') . ', ' . $query->qn('userid') . ', ' . $query->qn('username') .  ', ' . $query->q('ip')
							)
							->values(
								$query->q($session->getId()) . ', ' . (int) App::get('client')->id . ', ' . (int) $instance->get('guest') . ', ' .
								$query->q((int) $session->get('session.timer.start')) . ', ' . (int) $instance->get('id') . ', ' . $query->q($instance->get('username')) .  ', ' . $query->q($ip)
							);

						$db->setQuery($query);
					}

					// If the insert failed, exit the application.
					if (App::get('client')->id != 4 && !$db->execute())
					{
						exit($db->getErrorMsg());
					}
				}
			}

			// Session doesn't exist yet, so create session variables
			if ($session->isNew())
			{
				$session->set('registry', new Hubzero\Config\Registry('session'));
				$session->set('user', $instance);
			}
		}

		if (App::get('config')->get('session_handler') == 'database')
		{
			// Update the user related fields for the Joomla sessions table.
			$db = App::get('db');
			$db->setQuery(
				'UPDATE '.$db->quoteName('#__session') .
				' SET '.$db->quoteName('guest').' = '.$db->quote($instance->get('guest')).',' .
				'	'.$db->quoteName('username').' = '.$db->quote($instance->get('username')).',' .
				'	'.$db->quoteName('userid').' = '.(int) $instance->get('id') .
				' WHERE '.$db->quoteName('session_id').' = '.$db->quote($session->getId())
			);
			$db->query();
		}

		// Hit the user last visit field
		$instance->setLastVisit();

		return true;
	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @param   array    $user     Holds the user data.
	 * @param   array    $options  Array holding options (client, ...).
	 * @return  boolean  True on success
	 */
	public function onUserLogout($user, $options = array())
	{
		$my = User::getInstance();

		// Make sure we're a valid user first
		if ($user['id'] == 0 && !$my->get('tmp_user'))
		{
			return true;
		}

		// Check to see if we're deleting the current session
		if ($my->get('id') == $user['id'] && $options['clientid'] == App::get('client')->id)
		{
			// Hit the user last visit field
			$my->setLastVisit();

			// Destroy the php session for this user
			$session = App::get('session');
			$session->destroy();
		}

		// Force logout all users with that userid
		$db = App::get('db');
		$db->setQuery(
			'DELETE FROM ' . $db->quoteName('#__session') .
			' WHERE ' . $db->quoteName('userid') . ' = ' . (int) $user['id'] .
			' AND ' . $db->quoteName('client_id') . ' = ' . (int) $options['clientid']
		);
		$db->query();

		return true;
	}

	/**
	 * This method will return a user object
	 *
	 * If options['autoregister'] is true, if the user doesn't exist yet he will be created
	 *
	 * @param   array   $user     Holds the user data.
	 * @param   array   $options  Array holding options (remember, autoregister, group).
	 * @return  object  A User object
	 */
	protected function _getUser($user, $options = array())
	{
		$instance = Hubzero\User\User::oneByUsername($user['username']);

		if ($id = intval($instance->get('id')))
		{
			return $instance;
		}

		//TODO : move this out of the plugin
		$config = Component::params('com_members');

		// Default to Registered.
		$defaultUserGroup = $config->get('new_usertype', 2);

		$instance->set('id',             0);
		$instance->set('name',           $user['fullname']);
		$instance->set('username',       $user['username']);
		//$instance->set('password_clear', ((isset($user['password_clear'])) ? $user['password_clear'] : ''));
		$instance->set('email',          $user['email']);  // Result should contain an email (check)
		$instance->set('usertype',       'deprecated');
		$instance->set('accessgroups',   array($defaultUserGroup));
		$instance->set('activation',     1);
		$instance->set('loginShell',    '/bin/bash');
		$instance->set('ftpShell',      '/usr/lib/sftp-server');

		// Check joomla user activation setting
		// 0 = automatically confirmed
		// 1 = require email confirmation (the norm)
		// 2 = require admin confirmation
		$useractivation = $config->get('useractivation', 1);

		// If requiring admin approval, set user to not approved
		if ($useractivation == 2)
		{
			$instance->set('approved', 0);
		}
		else // Automatically approved
		{
			$instance->set('approved', 2);
		}

		// Now, also check to see if user came in via an auth plugin, as that may affect their approval status
		if (isset($user['auth_link']))
		{
			$domain = Hubzero\Auth\Domain::find_by_id($user['auth_link']->auth_domain_id);

			if ($domain && is_object($domain))
			{
				$params = Plugin::params('authentication', $domain->authenticator);

				if ($params && is_object($params) && $params->get('auto_approve', false))
				{
					$instance->set('approved', 2);
				}
			}
		}

		// If autoregister is set let's register the user
		$autoregister = isset($options['autoregister']) ? $options['autoregister'] : $this->params->get('autoregister', 1);

		if ($autoregister)
		{
			if (!$instance->save())
			{
				return new Exception($instance->getError());
			}
		}
		else
		{
			// No existing user and autoregister off, this is a temporary user.
			$instance->set('tmp_user', true);
		}

		$instance->set('password_clear', (isset($user['password_clear']) ? $user['password_clear'] : ''));

		return $instance;
	}
}
