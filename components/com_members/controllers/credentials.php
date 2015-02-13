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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . DS . 'models' . DS . 'token.php';

/**
 * Members controller class for profiles
 */
class MembersControllerCredentials extends \Hubzero\Component\SiteController
{
	/**
	 * Displays the username recovery form
	 *
	 * @return void
	 */
	public function remindTask()
	{
		$this->setTitle();
		$this->view->display();
	}

	/**
	 * Processes the username recovery request
	 *
	 * @return void
	 */
	public function remindingTask()
	{
		// Check the request token
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		// Get the email address
		if (!$email = trim(JRequest::getVar('email', false)))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=remind', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_MISSING_EMAIL'),
				'warning'
			);
			return;
		}

		// Make sure it looks like a valid email address
		if (!\Hubzero\Utility\Validate::email($email))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=remind', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_INVALID_EMAIL'),
				'warning'
			);
			return;
		}

		// Find the user(s) for the given email address
		$users = \Hubzero\User\User::whereEquals('email', $email)->whereEquals('block', 0)->rows();

		// Make sure we have at least one
		if ($users->count() < 1)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=remind', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		$config	= JFactory::getConfig();
		$eview  = new \Hubzero\Mail\View(array(
			'name'   => 'emails',
			'layout' => 'remind_plain'
		));

		$eview->config  = $config;
		$eview->baseUrl = rtrim(JURI::getInstance()->base(), DS);
		$eview->users   = $users;

		$plain = $eview->loadTemplate(false);
		$plain = str_replace("\n", "\r\n", $plain);

		// HTML
		$eview->setLayout('remind_html');

		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// Build message
		$message = new \Hubzero\Mail\Message();
		$message->setSubject(JText::sprintf('COM_MEMBERS_CREDENTIALS_EMAIL_REMIND_SUBJECT', $config->get('sitename')))
		        ->addFrom($config->get('mailfrom'), $config->get('fromname'))
		        ->addTo($email, $users->first()->name)
		        ->addHeader('X-Component', $this->_option)
		        ->addHeader('X-Component-Object', 'username_reminder')
		        ->addPart($plain, 'text/plain')
		        ->addPart($html, 'text/html');

		// Send mail
		if (!$message->send())
		{
			\JFactory::getLogger()->error('Members username reminder email failed: ' . JText::sprintf('Failed to mail %s', $email));
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=remind', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_FIAILED_TO_SEND_MAIL'),
				'warning'
			);
			return;
		}

		// Everything went well...go to the login page
		$this->setRedirect(
			JRoute::_('index.php?option=com_users&view=login', false),
			JText::_('COM_MEMBERS_CREDENTIALS_EMAIL_SENT'),
			'passed'
		);
	}

	/**
	 * Displays the password reset form
	 *
	 * @return void
	 */
	public function resetTask()
	{
		$this->setTitle();
		$this->view->display();
	}

	/**
	 * Processes intial reset password request
	 *
	 * @return void
	 **/
	public function resettingTask()
	{
		// Check the request token
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		// Grab the incoming username
		if (!$username = trim(JRequest::getVar('username', false)))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=reset', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_MISSING_USERNAME'),
				'warning'
			);
			return;
		}

		// Make sure it looks like a valid username
		require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'utility.php';
		if (!MembersHelperUtility::validlogin($username))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=reset', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_INVALID_USERNAME'),
				'warning'
			);
			return;
		}

		// Find the user for the given username
		$user = \Hubzero\User\User::whereEquals('username', $username)->rows();

		// Make sure we have at least one (although there's really no way to have more than 1)
		if ($user->count() < 1)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=reset', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// Get the user object
		$user  = $user->first();
		$juser = JUser::getInstance($user->id);

		// Make sure the user isn't blocked
		if ($user->block)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=reset', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// Make sure the user isn't a super admin
		if ($juser->authorise('core.admin'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=reset', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_USER_IS_SUPER'),
				'warning'
			);
			return;
		}

		// Make sure the user has not exceeded the reset limit
		if ($user->hasExceededResetLimit())
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=reset', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_EXCEEDED_LIMIT'),
				'warning'
			);
			return;
		}

		// Set the confirmation token
		$token       = JApplication::getHash(JUserHelper::genRandomPassword());
		$salt        = JUserHelper::getSalt('crypt-md5');
		$hashedToken = md5($token.$salt).':'.$salt;

		// Save the token
		$user->tokens()->save(['token' => $hashedToken]);

		// Send an email
		$config	= JFactory::getConfig();
		$eview  = new \Hubzero\Mail\View(array(
			'name'   => 'emails',
			'layout' => 'reset_plain'
		));

		$eview->config  = $config;
		$eview->baseUrl = rtrim(JURI::getInstance()->base(), DS);
		$eview->user    = $user;
		$eview->token   = $token;
		$eview->return  = JRoute::_('index.php?option=' . $this->_option . '&task=verify');

		$plain = $eview->loadTemplate(false);
		$plain = str_replace("\n", "\r\n", $plain);

		$eview->setLayout('reset_html');

		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// Build message
		$message = new \Hubzero\Mail\Message();
		$message->setSubject(JText::sprintf('COM_MEMBERS_CREDENTIALS_EMAIL_RESET_SUBJECT', $config->get('sitename')))
		        ->addFrom($config->get('mailfrom'), $config->get('fromname'))
		        ->addTo($user->email, $user->name)
		        ->addHeader('X-Component', $this->_option)
		        ->addHeader('X-Component-Object', 'password_reset')
		        ->addPart($plain, 'text/plain')
		        ->addPart($html, 'text/html');

		// Send mail
		if (!$message->send())
		{
			\JFactory::getLogger()->error('Members password reset email failed: ' . JText::sprintf('Failed to mail %s', $user->email));
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=remind', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_FIAILED_TO_SEND_MAIL'),
				'warning'
			);
			return;
		}

		// Push the user data into the session
		JFactory::getApplication()->setUserState('com_users.reset.user', $user->id);

		// Everything went well...go to the token verification page
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&task=verify', false),
			JText::_('COM_MEMBERS_CREDENTIALS_EMAIL_SENT'),
			'passed'
		);
	}

	/**
	 * Displays the password reset token verification form
	 *
	 * @return void
	 */
	public function verifyTask()
	{
		$this->setTitle();
		$this->view->display();
	}

	/**
	 * Processes the password reset token verification request
	 *
	 * @return void
	 **/
	public function verifyingTask()
	{
		// Check the request token
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Grab the token (not to be confused with the CSRF token above!)
		if (!$token = trim(JRequest::getVar('token', false)))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=verify', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_MISSING_TOKEN'),
				'warning'
			);
			return;
		}

		// Get the token and user id from the confirmation process
		$app = JFactory::getApplication();
		$id  = $app->getUserState('com_users.reset.user', null);

		// Get the user object
		$user  = \Hubzero\User\User::oneOrFail($id);
		$parts = explode(':', $user->tokens()->latest()->token);
		$crypt = $parts[0];

		if (!isset($parts[1]))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=verify', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		$salt      = $parts[1];
		$testcrypt = JUserHelper::getCryptedPassword($token, $salt);

		// Verify the token
		if (!($crypt == $testcrypt))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=verify', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// Make sure the user isn't blocked
		if ($user->block)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=verify', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// Push the user data into the session
		$app->setUserState('com_users.reset.token', $crypt . ':' . $salt);

		// Everything went well...go to the actual change password page
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&task=setpassword', false),
			JText::_('COM_MEMBERS_CREDENTIALS_TOKEN_CONFIRMED'),
			'passed'
		);
	}

	/**
	 * Displays the password set form
	 *
	 * @return void
	 */
	public function setpasswordTask()
	{
		$password_rules = \Hubzero\Password\Rule::getRules();
		$this->view->password_rules = array();

		foreach($password_rules as $rule)
		{
			if (!empty($rule['description']))
			{
				$this->view->password_rules[] = $rule['description'];
			}
		}

		$this->setTitle();
		$this->view->display();
	}

	/**
	 * Processes the password set form
	 *
	 * @return void
	 */
	public function settingpasswordTask()
	{
		// Check for request forgeries
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		// Get the token and user id from the verification process
		$app     = JFactory::getApplication();
		$token   = $app->getUserState('com_users.reset.token', null);
		$id      = $app->getUserState('com_users.reset.user',  null);
		$no_html = JRequest::getInt('no_html', 0);

		// Check the token and user id
		if (empty($token) || empty($id))
		{
			throw new JException(JText::_('COM_MEMBERS_CREDENTIALS_ERROR_TOKENS_MISSING'), 403);
		}

		// Get the user object
		$user = \Hubzero\User\User::oneOrFail($id);

		// Check for a user and that the tokens match
		if ($user->tokens()->latest()->token !== $token)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=setpassword', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// Make sure the user isn't blocked
		if ($user->block)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=setpassword', false),
				JText::_('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// Instantiate profile classs
		$profile = new \Hubzero\User\Profile();
		$profile->load($id);

		if (\Hubzero\User\Helper::isXDomainUser($user->id))
		{
			throw new JException(JText::_('COM_MEMBERS_CREDENTIALS_ERROR_LINKED_ACCOUNT'), 403);
		}

		$password_rules = \Hubzero\Password\Rule::getRules();

		$password1 = trim(JRequest::getVar('password1', null));
		$password2 = trim(JRequest::getVar('password2', null));

		if (!empty($password1))
		{
			$msg = \Hubzero\Password\Rule::validate($password1, $password_rules, $profile->get('username'));
		}
		else
		{
			$msg = array();
		}

		require_once JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'utility.php';

		$error = false;

		if (!$password1 || !$password2)
		{
			$error = JText::_('COM_MEMBERS_CREDENTIALS_ERROR_PASSWORD_TWICE');
		}
		elseif ($password1 != $password2)
		{
			$error = JText::_('COM_MEMBERS_CREDENTIALS_ERROR_PASSWORD_DONT_MATCH');
		}
		elseif (!MembersHelperUtility::validpassword($password1))
		{
			$error = JText::_('COM_MEMBERS_CREDENTIALS_ERROR_PASSWORD_INVALID');
		}
		elseif (!empty($msg))
		{
			$error = JText::_('COM_MEMBERS_CREDENTIALS_ERROR_PASSWORD_FAILS_REQUIREMENTS');
		}

		if ($error)
		{
			if ($no_html)
			{
				$response = array(
					'success' => false,
					'message' => $error
				);

				echo json_encode($response);
				die();
			}
			else
			{
				$this->setRedirect(
					JRoute::_('index.php?option=' . $this->_option . '&task=setpassword', false),
					$error,
					'warning'
				);
				return;
			}
		}

		// Encrypt the password and update the profile
		$result = \Hubzero\User\Password::changePassword($profile->get('username'), $password1);

		// Save the changes
		if (!$result)
		{
			if ($no_html)
			{
				$response = array(
					'success' => false,
					'message' => JText::_('COM_MEMBERS_CREDENTIALS_ERROR_GENERIC')
				);

				echo json_encode($response);
				die();
			}
			else
			{
				$this->setRedirect(
					JRoute::_('index.php?option=' . $this->_option . '&task=setpassword', false),
					JText::_('COM_MEMBERS_CREDENTIALS_ERROR_GENERIC'),
					'warning'
				);
				return;
			}
		}

		// Flush the user data from the session
		$app->setUserState('com_users.reset.token', null);
		$app->setUserState('com_users.reset.user', null);

		if ($no_html)
		{
			$response = array(
				'success'  => true,
				'redirect' => JRoute::_('index.php?option=com_users&view=login', false)
			);

			echo json_encode($response);
			die();
		}
		else
		{
			// Everything went well...go to the login page
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login', false),
				JText::_('COM_MEMBERS_CREDENTIALS_PASSWORD_RESET_COMPLETE'),
				'passed'
			);
		}
	}

	/**
	 * Sets the title and pathway based on the current task
	 *
	 * @return void
	 **/
	private function setTitle()
	{
		JFactory::getDocument()->setTitle(
			JText::_('COM_MEMBERS_CREDENTIALS_' . ucfirst($this->_task))
		);

		JFactory::getApplication()->getPathway()->addItem(
			JText::_('COM_MEMBERS_CREDENTIALS_' . ucfirst($this->_task)),
			'index.php?option=' . $this->_option . '&task=' . $this->_task
		);
	}
}