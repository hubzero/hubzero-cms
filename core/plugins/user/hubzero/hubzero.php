<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Hubzero User plugin
 */
class plgUserHubzero extends \Hubzero\Plugin\Plugin
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
		$query = $db->getQuery()
			->delete('#__session')
			->whereEquals('userid', (int) $user['id']);
		$db->setQuery($query->toString());
		$db->query();

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

				$eview = new Hubzero\Mail\View(array(
					'base_path'     => __DIR__,
					'name'          => 'emails',
					'layout'        => 'admincreate_plain',
					'override_path' => ''
				));
				$eview->addTemplatePath(App::get('template')->path . '/html/plg_' . $this->_type . '_' . $this->_name);

				$eview->set('user', $user);
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
						Lang::txt('PLG_USER_HUBZERO_EMAIL_ADMIN', $config->get('sitename'))
					)
					->addTo($emailAddress)
					->addHeader('X-Component', Request::getCmd('option', 'com_members'))
					->addHeader('X-Component-Object', 'user_creation_admin_notification')
					->setSubject(Lang::txt('PLG_USER_HUBZERO_EMAIL_ACCOUNT_CREATION', $config->get('sitename')))
					->addPart($plain, 'text/plain')
					->addPart($html, 'text/html');

				if (!$mail->send())
				{
					// TODO: Probably should raise a plugin error but this event is not error checked.
					Log::error(Lang::txt('PLG_USER_HUBZERO_EMAIL_ERROR', $emailAddress));
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
					'PLG_USER_HUBZERO_NEW_USER_EMAIL_SUBJECT',
					$user['name'],
					$config->get('sitename')
				);

				// Compute the mail body.
				$emailBody = Lang::txt(
					'PLG_USER_HUBZERO_NEW_USER_EMAIL_BODY',
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
					throw new Exception(Lang::txt('PLG_USER_HUBZERO_EMAIL_ERROR'), 500);
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

				$query = $db->getQuery()
					->select('session_id')
					->from('#__session')
					->whereEquals('session_id', $session->getId())
					->limit(1)
					->start(0);

				$db->setQuery($query->toString());
				$exists = $db->loadResult();

				// If the session record doesn't exist initialise it.
				if (!$exists)
				{
					$query->clear();

					$ip = Request::ip();

					if ($session->isNew())
					{
						$query = $db->getQuery()
							->insert('#__session')
							->values(array(
								'session_id' => $session->getId(),
								'client_id'  => (int) App::get('client')->id,
								'time'       => (int) time(),
								'ip'         => $ip
							));

						$db->setQuery($query->toString());
					}
					else
					{
						$query = $db->getQuery()
							->insert('#__session')
							->values(array(
								'session_id' => $session->getId(),
								'client_id'  => (int) App::get('client')->id,
								'guest'      => (int) $instance->get('guest'),
								'time'       => (int) $session->get('session.timer.start'),
								'userid'     => (int) $instance->get('id'),
								'username'   => $instance->get('username'),
								'ip'         => $ip
							));

						$db->setQuery($query->toString());
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
			// Update the user related fields for the sessions table.
			$db = App::get('db');
			$query = $db->getQuery()
				->update('#__session')
				->set(array(
					'guest'    => $instance->get('guest'),
					'username' => $instance->get('username'),
					'userid'   => (int) $instance->get('id')
				))
				->whereEquals('session_id', $session->getId());
			$db->setQuery($query->toString());
			$db->query();
		}

		// Determine whether user has an existing Secret, and create if needed.
		$userid = $instance->get('id');
		$this->verifyUserSecret($userid);

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
		$query = $db->getQuery()
			->delete('#__session')
			->whereEquals('userid', (int) $user['id'])
			->whereEquals('client_id', (int) $options['clientid']);
		$db->setQuery($query->toString());
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

		$instance->set('id', 0);
		$instance->set('name', $user['fullname']);
		$instance->set('username', $user['username']);
		//$instance->set('password_clear', ((isset($user['password_clear'])) ? $user['password_clear'] : ''));
		$instance->set('email', $user['email']);  // Result should contain an email (check)
		$instance->set('usertype', 'deprecated');
		$instance->set('accessgroups', array($defaultUserGroup));
		$instance->set('activation', 1);
		$instance->set('loginShell', '/bin/bash');
		$instance->set('ftpShell', '/usr/lib/sftp-server');
		$instance->set('sendEmail', -1);

		// Check user activation setting
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


	/**
	 * Event Trigger and different functions used to deidentify users
	 */
	public function runSelectQuery($query) {
        $db = \App::get('db');
        $db->setQuery($query);
        $objRows = $db->loadObjectList();

        // json_encode: returns a string containing the JSON representation from the mySQL -> json_decode: Returns the value encoded in json in appropriate PHP type
        $objString = json_encode($objRows, true);
        return json_decode($objString, true);
    }

    public function runInsertQuery($query, $vars) {
        $db = \App::get('db');
        $db->prepare($query);
        $db->bind($vars);
        return $db->execute();
    }

    public function runUpdateOrDeleteQuery($query) {
        $db = \App::get('db');
        $db->setQuery($query);
        return $db->query();
    }

	// Main function to deidentify users
	public function onUserDeidentify($user_id) {
        $db = \App::get('db');

        // PURPOSE: Find username, id, email from jos_users table
        $select_UsersById_Query = "SELECT id, username, email, password FROM `#__users` WHERE id='" . $user_id . "';";
        $userJsonObj = $this->runSelectQuery($select_UsersById_Query);

        $userId = $user_id;
        $userEmail = "";
        $userName = "";

        // There could be multiple auth links to a specific user. Will need to loop through this array to delete. 
        $userLinkIdArray = array();

        if ($userJsonObj) {
            $userId = $userJsonObj[0]['id'];
            $userEmail = $userJsonObj[0]['email'];
            $userName = $userJsonObj[0]['username'];
        }

        // Creating New Credentials
        $anonPassword = "anonPassword_" . $userId;
        $anonUserName = "anonUsername_" . $userId;
		$anonUserNameSpace = "AnonFirst Middle Last" . $userId;

        // PURPOSE: Find auth link id from jos_auth_link table
        $select_AuthLink_Query = "SELECT id, user_id FROM `#__auth_link` WHERE user_id='" . $user_id . "';";
        $authLinkJsonObj = $this->runSelectQuery($select_AuthLink_Query);
        
        // Create an array of link ids
        if ($authLinkJsonObj) {
            $userLinkIdArray = array_map(function ($el) {
                return $el['id'];
            }, $authLinkJsonObj);
        }

        // ======= Sanitation Queries // deletes, updates, inserts =======
        // NOTE: moved the insert (pre deletes) and updated (post deletes) profile key SQL statement to controller com_members/admin/controllers/members.php
		$delete_UserProfile_Query = "DELETE from `#__user_profiles` where user_id =" . $db->quote($userId) . " AND 'profile_key' !='edulevel' AND profile_key !='gender' AND profile_key !='hispanic' AND profile_key !='organization' AND profile_key !='orgtype' AND profile_key !='race' AND profile_key !='reason'";
		$this->runUpdateOrDeleteQuery($delete_UserProfile_Query);

        $update_SupportTicketsByEmail_Query = "UPDATE `#__support_tickets` set login='',ip='', email='', hostname='', name='' where email=" . $db->quote($userEmail);
        $this->runUpdateOrDeleteQuery($update_SupportTicketsByEmail_Query);

        $update_SupportTicketsByLogin_Query = "UPDATE `#__support_tickets` set login='',ip='', email='', hostname='', name='' where login=" . $db->quote($userName);;
        $this->runUpdateOrDeleteQuery($update_SupportTicketsByLogin_Query);

        $delete_SessionGeo_Query = "DELETE from `#__session_geo` where username=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($delete_SessionGeo_Query);

        $delete_Session_Query = "DELETE from `#__session` where username=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($delete_Session_Query);

        $delete_SessionById_Query = "DELETE from `#__session` where userid=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_SessionById_Query);

        $delete_ProfileCompletionAward_Query = "DELETE from `#__profile_completion_awards` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_ProfileCompletionAward_Query);

        $update_NewsletterMailingRecipientActions_Query = "UPDATE `#__newsletter_mailing_recipient_actions` set email='',ip='' where email=" . $db->quote($userEmail);
        $this->runUpdateOrDeleteQuery($update_NewsletterMailingRecipientActions_Query);

        $update_NewsletterMailingRecipients_Query = "UPDATE `#__newsletter_mailing_recipients` set email='' where email=" . $db->quote($userEmail);
        $this->runUpdateOrDeleteQuery($update_NewsletterMailingRecipients_Query);

        $update_NewsletterMailingListEmails_Query = "UPDATE `#__newsletter_mailinglist_emails` set email='' where email=" . $db->quote($userEmail);
        $this->runUpdateOrDeleteQuery($update_NewsletterMailingListEmails_Query);

        $update_NewsletterMailingListUnsubscribes_Query = "UPDATE `#__newsletter_mailinglist_unsubscribes` set email='' where email=" . $db->quote($userEmail);
        $this->runUpdateOrDeleteQuery($update_NewsletterMailingListUnsubscribes_Query);

        $delete_Messages_Query = "DELETE from `#__messages` where user_id_from=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_Messages_Query);

        $update_MediaTrackingDetailed_Query = "UPDATE `#__media_tracking_detailed` set ip_address='' where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($update_MediaTrackingDetailed_Query);

        $update_MediaTracking_Query = "UPDATE `#__media_tracking` set ip_address='' where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($update_MediaTracking_Query);

        $delete_JobsSeeker_Query = "DELETE from `#__jobs_seekers` where uid=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_JobsSeeker_Query);

        $delete_JobsResume_Query = "DELETE from `#__jobs_resumes` where uid=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_JobsResume_Query);

        $delete_JobsApplications_Query = "DELETE from `#__jobs_applications` where uid=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_JobsApplications_Query);

        $delete_Feedback_Query = "DELETE from `#__feedback` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_Feedback_Query);

        $update_EventRegistration_Query = "UPDATE `#__event_registration` set name='', email='', phone='', address='', city='', zip='', username=" . $db->quote($anonUserName) . " where username=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($update_EventRegistration_Query);

        $delete_CartSavedAddresses_Query = "DELETE from `#__cart_saved_addresses` where uidNumber=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_CartSavedAddresses_Query);

        $delete_BlogEntries_Query = "DELETE from `#__blog_entries` where created_by=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_BlogEntries_Query);

        $delete_BlogComments_Query = "DELETE from `#__blog_comments` where created_by=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_BlogComments_Query);

        // Loop through user link id array to delete
        if(!empty($userLinkIdArray)) {
            foreach ($userLinkIdArray as $userLinkId) {
                $delete_AuthLinkData_Query = "DELETE from `#__auth_link_data` where link_id=" . $db->quote($userLinkId);
                $this->runUpdateOrDeleteQuery($delete_AuthLinkData_Query);
            }
        }

        $delete_AuthLink_Query = "DELETE from `#__auth_link` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_AuthLink_Query);

        $delete_XGroupMember_Query = "DELETE from `#__xgroups_members` where uidNumber=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XGroupMember_Query);

        $delete_XProfilesBio_Query = "DELETE from `#__xprofiles_bio` where uidNumber=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XProfilesBio_Query);

        $delete_XProfilesAddress_Query = "DELETE from `#__xprofiles_address` where uidNumber=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XProfilesAddress_Query);

        $delete_XMessage_Query = "DELETE from `#__xmessage` where created_by=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XMessage_Query);

        $delete_XProfilesAdmin_Query = "DELETE from `#__xprofiles_admin` where uidNumber=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XProfilesAdmin_Query);

        $delete_XProfilesDisability_Query = "DELETE from `#__xprofiles_disability` where uidNumber=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XProfilesDisability_Query);

        $delete_XProfilesTokens_Query = "DELETE from `#__xprofiles_tokens` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XProfilesTokens_Query);

        $delete_WishAttachment_Query = "DELETE from `#__wish_attachments` where created_by=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_WishAttachment_Query);

        $delete_WikiAttachment_Query = "DELETE from `#__wiki_attachments` where created_by=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_WikiAttachment_Query);

        $delete_UsersQuotasLogByUserName_Query = "DELETE from `#__users_quotas_log` where name=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($delete_UsersQuotasLogByUserName_Query);

        $delete_UsersQuotasLogByActorId_Query = "DELETE from `#__users_quotas_log` where actor_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_UsersQuotasLogByActorId_Query);

        $delete_UsersLogAuth_Query = "DELETE from `#__users_log_auth` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_UsersLogAuth_Query);

        $delete_UsersPassword_Query = "DELETE from `#__users_password` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_UsersPassword_Query);

        $delete_UsersPasswordHistory_Query = "DELETE from `#__users_password_history` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_UsersPasswordHistory_Query);

        $update_UsersPointsSubscription_Query = "UPDATE `#__users_points_subscriptions` set contact=" . $db->quote($anonUserName) . " where uid=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($update_UsersPointsSubscription_Query);

		// replace user secret with null
		$this->nullifyUserSecret($userId);

        // ----------- DELETE the user's home directory, with the path being /webdav/home/$userName. ----------
        if ($userName) {
            // as user Apache, we can't delete the directory so we need to delete all files.
            $cmd1 = "/bin/rm -rf /webdav/home/" . escapeshellarg($userName) . "/*";
            system($cmd1, $retval);
            
            // delete the files starting with a dot; ignore messages about '.' and '..'
            $cmd2 = "/bin/rm -rf /webdav/home/" . escapeshellarg($userName) . "/.*";
            system($cmd2, $retval);
        }

		$update_XProfilesById_Query = "UPDATE `#__xprofiles` set name=" . $db->quote($anonUserNameSpace) . ", username=" . $db->quote($anonUserName) . ", userPassword=" . $db->quote($anonPassword) . ", url='', phone='', regHost='', regIP='', givenName=" . $db->quote($anonUserName) . ", middleName='', surname='anonSurName', picture='', public=0, params='', note='', orcid='', homeDirectory='/home/anonymous', email=" . $db->quote($anonUserName . "@example.com") . " where uidNumber =" . $db->quote($userId);
		$this->runUpdateOrDeleteQuery($update_XProfilesById_Query);

		return true;
    }

	/**
	 * This utility method checks whether current user has a secret set
	 * in the database, and if not, it generates and saves one.
	 *
	 * @param   integer	$userId  Primary key identifying user in jos_users table
	 * @return  boolean	True if user has secret column populated in database
	 */
	protected function verifyUserSecret($userId)
	{
		// create a new user secret if none exists in database:
		if (!$this->checkForUserSecret($userId))
		{
			$newSecret = $this->createUserSecret();

			// Attempt to save the new user secret to the database:
			if (!$this->saveUserSecret($userId, $newSecret))
			{
				return false;
			}
		}
		// User secret exists in database:
		return true;
	}

	/**
	 * This utility method will return true if current user has a secret set, false otherwise.
	 *
	 * @param   integer	$userId    Primary key identifying user in jos_users table
	 * @return  boolean	$hasSecret True if user has secret column populated in database
	 */
	protected function checkForUserSecret($userId)
	{
		$query = new \Hubzero\Database\Query;

		// Determine whether user's secret is different from null
		$foundSecret = $query->select('*')
               		->from('#__users')
               		->whereEquals('id', $userId)
               		->whereIsNotNull('secret')
               		->fetch();

		// User has secret set if one row was returned:
       	if (is_array($foundSecret) && count($foundSecret) == 1)
       	{
       		return true;
       	}
       	return false;
	}

	/**
	 * This utility method generates a new user secret.
	 *
	 * @return String user secret
	 */
	protected function createUserSecret()
	{
		// create 32-character secret:
		$secretLength = 32;
		$newSecret = \Hubzero\User\Password::genRandomPassword($secretLength);

		if (!is_null($newSecret)) {
			return $newSecret;
		}
		return false;
	}

	/**
	 * This utility method saves a new user secret.
	 *
	 * @param   integer	$userId  Primary key identifying user in jos_users table
	 * @param   String $secret   User secret
	 * @return  boolean	True if secret column successfully populated in database
	 */
	protected function saveUserSecret($userId, $secret)
	{
		$query = new \Hubzero\Database\Query;

		// Set the secret generated for this user:
		$query->update('#__users')
				->set(['secret' => $secret])
				->whereEquals('id', $userId)
				->execute();

		return true;
	}

	/**
	 * Replaces user secret with null.
	 *
	 * @param   integer	$userId  Primary key identifying user in jos_users table
	 * @return  boolean	True if secret value was overwritten successfully.
	 */
	protected function nullifyUserSecret($userId)
	{
		$query = new \Hubzero\Database\Query;

		// If user exists:
		$user = User::oneOrFail($userId);
		if (isset($user))
		{
			// NULL out any existing secret for this user:
			$query->update('#__users')
				->set(['secret' => NULL])
				->whereEquals('id', $userId)
				->execute();
			return true;
		}
		return false;
	}
}
