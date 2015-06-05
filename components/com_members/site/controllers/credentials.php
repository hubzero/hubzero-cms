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

namespace Components\Members\Site\Controllers;

use Hubzero\Component\SiteController;
use Exception;
use Request;
use Session;
use Config;
use Route;
use Lang;
use User;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'token.php';

/**
 * Members controller class for profiles
 */
class Credentials extends SiteController
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
		Session::checkToken('post') or exit(Lang::txt('JINVALID_TOKEN'));

		// Get the email address
		if (!$email = trim(Request::getVar('email', false)))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=remind', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_MISSING_EMAIL'),
				'warning'
			);
			return;
		}

		// Make sure it looks like a valid email address
		if (!\Hubzero\Utility\Validate::email($email))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=remind', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_INVALID_EMAIL'),
				'warning'
			);
			return;
		}

		// Find the user(s) for the given email address
		$users = \Hubzero\User\User::whereEquals('email', $email)->whereEquals('block', 0)->rows();

		// Make sure we have at least one
		if ($users->count() < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=remind', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		$eview  = new \Hubzero\Mail\View(array(
			'name'   => 'emails',
			'layout' => 'remind_plain'
		));

		$eview->config  = Config::getRoot();
		$eview->baseUrl = rtrim(Request::base(), DS);
		$eview->users   = $users;

		$plain = $eview->loadTemplate(false);
		$plain = str_replace("\n", "\r\n", $plain);

		// HTML
		$eview->setLayout('remind_html');

		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// Build message
		$message = new \Hubzero\Mail\Message();
		$message->setSubject(Lang::txt('COM_MEMBERS_CREDENTIALS_EMAIL_REMIND_SUBJECT', Config::get('sitename')))
		        ->addFrom(Config::get('mailfrom'), Config::get('fromname'))
		        ->addTo($email, $users->first()->name)
		        ->addHeader('X-Component', $this->_option)
		        ->addHeader('X-Component-Object', 'username_reminder')
		        ->addPart($plain, 'text/plain')
		        ->addPart($html, 'text/html');

		// Send mail
		if (!$message->send())
		{
			Log::error('Members username reminder email failed: ' . Lang::txt('Failed to mail %s', $email));

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=remind', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_FIAILED_TO_SEND_MAIL'),
				'warning'
			);
			return;
		}

		// Everything went well...go to the login page
		App::redirect(
			Route::url('index.php?option=com_users&view=login', false),
			Lang::txt('COM_MEMBERS_CREDENTIALS_EMAIL_SENT'),
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
		Session::checkToken('post') or exit(Lang::txt('JINVALID_TOKEN'));

		// Grab the incoming username
		if (!$username = trim(Request::getVar('username', false)))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=reset', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_MISSING_USERNAME'),
				'warning'
			);
			return;
		}

		// Make sure it looks like a valid username
		require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'utility.php';

		if (!\\Components\Members\Helpers\Utility::validlogin($username))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=reset', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_INVALID_USERNAME'),
				'warning'
			);
			return;
		}

		// Find the user for the given username
		$user = \Hubzero\User\User::whereEquals('username', $username)->rows();

		// Make sure we have at least one (although there's really no way to have more than 1)
		if ($user->count() < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=reset', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// Get the user object
		$user = $user->first();
		$user = User::getInstance($user->id);

		// Make sure the user isn't blocked
		if ($user->block)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=reset', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// Make sure the user isn't a super admin
		if ($user->authorise('core.admin'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=reset', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_USER_IS_SUPER'),
				'warning'
			);
			return;
		}

		// Make sure the user has not exceeded the reset limit
		if ($user->hasExceededResetLimit())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=reset', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_EXCEEDED_LIMIT'),
				'warning'
			);
			return;
		}

		// Set the confirmation token
		$token       = App::hash(\JUserHelper::genRandomPassword());
		$salt        = \JUserHelper::getSalt('crypt-md5');
		$hashedToken = md5($token.$salt).':'.$salt;

		// Save the token
		$user->tokens()->save(['token' => $hashedToken]);

		// Send an email
		$eview  = new \Hubzero\Mail\View(array(
			'name'   => 'emails',
			'layout' => 'reset_plain'
		));

		$eview->config  = Config::getRoot();
		$eview->baseUrl = rtrim(Request::base(), '/');
		$eview->user    = $user;
		$eview->token   = $token;
		$eview->return  = Route::url('index.php?option=' . $this->_option . '&task=verify');

		$plain = $eview->loadTemplate(false);
		$plain = str_replace("\n", "\r\n", $plain);

		$eview->setLayout('reset_html');

		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// Build message
		$message = new \Hubzero\Mail\Message();
		$message->setSubject(Lang::txt('COM_MEMBERS_CREDENTIALS_EMAIL_RESET_SUBJECT', Config::get('sitename')))
		        ->addFrom(Config::get('mailfrom'), Config::get('fromname'))
		        ->addTo($user->email, $user->name)
		        ->addHeader('X-Component', $this->_option)
		        ->addHeader('X-Component-Object', 'password_reset')
		        ->addPart($plain, 'text/plain')
		        ->addPart($html, 'text/html');

		// Send mail
		if (!$message->send())
		{
			Log::error('Members password reset email failed: ' . Lang::txt('Failed to mail %s', $user->email));

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=remind', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_FIAILED_TO_SEND_MAIL'),
				'warning'
			);
			return;
		}

		// Push the user data into the session
		User::setState('com_users.reset.user', $user->id);

		// Everything went well...go to the token verification page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=verify', false),
			Lang::txt('COM_MEMBERS_CREDENTIALS_EMAIL_SENT'),
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
		Session::checkToken('request') or exit(Lang::txt('JINVALID_TOKEN'));

		// Grab the token (not to be confused with the CSRF token above!)
		if (!$token = trim(Request::getVar('token', false)))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=verify', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_MISSING_TOKEN'),
				'warning'
			);
			return;
		}

		// Get the token and user id from the confirmation process
		$id  = User::getState('com_users.reset.user', null);

		// Get the user object
		$user  = \Hubzero\User\User::oneOrFail($id);
		$parts = explode(':', $user->tokens()->latest()->token);
		$crypt = $parts[0];

		if (!isset($parts[1]))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=verify', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		$salt      = $parts[1];
		$testcrypt = \JUserHelper::getCryptedPassword($token, $salt);

		// Verify the token
		if (!($crypt == $testcrypt))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=verify', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// Make sure the user isn't blocked
		if ($user->block)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=verify', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// Push the user data into the session
		User::setState('com_users.reset.token', $crypt . ':' . $salt);

		// Everything went well...go to the actual change password page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=setpassword', false),
			Lang::txt('COM_MEMBERS_CREDENTIALS_TOKEN_CONFIRMED'),
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

		foreach ($password_rules as $rule)
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
		Session::checkToken('post') or exit(Lang::txt('JINVALID_TOKEN'));

		// Get the token and user id from the verification process
		$token   = User::getState('com_users.reset.token', null);
		$id      = User::getState('com_users.reset.user',  null);
		$no_html = Request::getInt('no_html', 0);

		// Check the token and user id
		if (empty($token) || empty($id))
		{
			throw new Exception(Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_TOKENS_MISSING'), 403);
		}

		// Get the user object
		$user = \Hubzero\User\User::oneOrFail($id);

		// Check for a user and that the tokens match
		if ($user->tokens()->latest()->token !== $token)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=setpassword', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// Make sure the user isn't blocked
		if ($user->block)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=setpassword', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_USER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// Instantiate profile classs
		$profile = new \Hubzero\User\Profile();
		$profile->load($id);

		if (\Hubzero\User\Helper::isXDomainUser($user->id))
		{
			throw new Exception(Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_LINKED_ACCOUNT'), 403);
		}

		$password_rules = \Hubzero\Password\Rule::getRules();

		$password1 = trim(Request::getVar('password1', null));
		$password2 = trim(Request::getVar('password2', null));

		if (!empty($password1))
		{
			$msg = \Hubzero\Password\Rule::validate($password1, $password_rules, $profile->get('username'));
		}
		else
		{
			$msg = array();
		}

		require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'utility.php';

		$error    = false;
		$changing = true;

		if (!$password1 || !$password2)
		{
			$error = Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_PASSWORD_TWICE');
		}
		elseif ($password1 != $password2)
		{
			$error = Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_PASSWORD_DONT_MATCH');
		}
		elseif (!\Components\Members\Helpers\Utility::validpassword($password1))
		{
			$error = Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_PASSWORD_INVALID');
		}
		elseif (!empty($msg))
		{
			$error = Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_PASSWORD_FAILS_REQUIREMENTS');
		}

		// If we're resetting password to the current password, just return true
		// That way you can't reset the counter on your current password, or invalidate it by putting it into history
		if (\Hubzero\User\Password::passwordMatches($profile->get('uidNumber'), $password1))
		{
			$error    = false;
			$changing = false;
			$result   = true;
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
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&task=setpassword', false),
					$error,
					'warning'
				);
				return;
			}
		}

		if ($changing)
		{
			// Encrypt the password and update the profile
			$result = \Hubzero\User\Password::changePassword($profile->get('username'), $password1);
		}

		// Save the changes
		if (!$result)
		{
			if ($no_html)
			{
				$response = array(
					'success' => false,
					'message' => Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_GENERIC')
				);

				echo json_encode($response);
				die();
			}
			else
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&task=setpassword', false),
					Lang::txt('COM_MEMBERS_CREDENTIALS_ERROR_GENERIC'),
					'warning'
				);
				return;
			}
		}

		// Flush the user data from the session
		User::setState('com_users.reset.token', null);
		User::setState('com_users.reset.user', null);

		if ($no_html)
		{
			$response = array(
				'success'  => true,
				'redirect' => Route::url('index.php?option=com_users&view=login', false)
			);

			echo json_encode($response);
			die();
		}
		else
		{
			// Everything went well...go to the login page
			App::redirect(
				Route::url('index.php?option=com_users&view=login', false),
				Lang::txt('COM_MEMBERS_CREDENTIALS_PASSWORD_RESET_COMPLETE'),
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
		\Document::setTitle(
			Lang::txt('COM_MEMBERS_CREDENTIALS_' . ucfirst($this->_task))
		);

		\Pathway::append(
			Lang::txt('COM_MEMBERS_CREDENTIALS_' . ucfirst($this->_task)),
			'index.php?option=' . $this->_option . '&task=' . $this->_task
		);
	}
}