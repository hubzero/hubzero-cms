<?php
/**
 * HUBzero CMS
 *
 * Copyright 2012-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2012-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Authentication plugin for HUBzero
 */
class plgAuthenticationHubzero extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior.
	 * If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array    $credentials  Array holding the user credentials
	 * @param   array    $options      Array of extra options
	 * @param   object   $response     Authentication response object
	 * @return  boolean
	 */
	public function onAuthenticate($credentials, $options, &$response)
	{
		return $this->onUserAuthenticate($credentials, $options, $response);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array    $credentials  Array holding the user credentials
	 * @param   array    $options      Array of extra options
	 * @param   object   $response     Authentication response object
	 * @return  boolean
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{
		jimport('joomla.user.helper');

		// For JLog
		$response->type = 'hubzero';

		// HUBzero does not like blank passwords
		if (empty($credentials['password']))
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_HUBZERO_ERROR_EMPTY_PASS');
			return false;
		}

		// Initialize variables
		$conditions = '';

		// Get a database object
		$db = \App::get('db');

		// Determine if attempting to log in via username or email address
		if (strpos($credentials['username'], '@'))
		{
			$conditions = ' WHERE email=' . $db->Quote($credentials['username']);
		}
		else
		{
			$conditions = ' WHERE username=' . $db->Quote($credentials['username']);
		}

		$query = 'SELECT `id`, `username`, `password`'
				. ' FROM `#__users`'
				. $conditions
				. ' AND `block` != 1';

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (is_array($result) && count($result) > 1)
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_HUBZERO_UNKNOWN_USER');
			return false;
		}
		elseif (is_array($result) && isset($result[0]))
		{
			$result = $result[0];
		}
		else
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_HUBZERO_AUTHENTICATION_FAILED');
			return false;
		}

		// Check to see if there are many blocked accounts
		if ($this->hasExceededBlockLimit($result))
		{
			// Might be a moot point if Fail2Ban is triggered
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_HUBZERO_TOO_MANY_ATTEMPTS');
			return false;
		}

		// Now make sure they haven't made too many failed login attempts
		if ($this->hasExceededLoginLimit(\Hubzero\User\User::oneOrFail($result->id)))
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_HUBZERO_TOO_MANY_ATTEMPTS');
			return false;
		}

		if ($result)
		{
			if (\Hubzero\User\Password::passwordMatches($result->username, $credentials['password'], true))
			{
				$user = User::getInstance($result->id);

				$response->username      = $user->get('username');
				$response->email         = $user->get('email');
				$response->fullname      = $user->get('name');
				$response->status        = \Hubzero\Auth\Status::SUCCESS;
				$response->error_message = '';

				// Check validity and age of password
				$password_rules = \Hubzero\Password\Rule::all()
					->whereEquals('enabled', 1)
					->rows();
				$msg = \Hubzero\Password\Rule::verify($credentials['password'], $password_rules, $result->username);
				if (is_array($msg) && !empty($msg[0]))
				{
					App::get('session')->set('badpassword', '1');
				}
				if (\Hubzero\User\Password::isPasswordExpired($result->username))
				{
					App::get('session')->set('expiredpassword', '1');
				}

				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => $user->picture(0, false),
					'authenticator' => 'hubzero'
				);

				$namespace = 'authenticator';
				$lifetime  = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
			}
			else
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_HUBZERO_AUTHENTICATION_FAILED');
			}
		}
		else
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_HUBZERO_AUTHENTICATION_FAILED');
		}
	}

	/**
	 * Checks to see if the current user has exceeded the site
	 * login attempt limit for a given time period
	 *
	 * @param		$user \Hubzero\User\User 
	 *
	 * @return  bool
	 */
	private function hasExceededLoginLimit($user)
	{
		$params    = \Component::params('com_members');
		$limit     = (int)$params->get('login_attempts_limit', 10);
		$timeframe = (int)$params->get('login_attempts_timeframe', 1);
		$result    = true;

		// Get the user's tokens
		$threshold = date("Y-m-d H:i:s", strtotime(\Date::toSql() . " {$timeframe} hours ago"));
		$auths     = new \Hubzero\User\Log\Auth;

		$auths->whereEquals('username', $user->username)
		      ->whereEquals('status', 'failure')
		      ->where('logged', '>=', $threshold);

		if ($auths->count() < $limit - 1)
		{
			$result = false;
		}
		else
		{
			// Log attempt to the database
			Hubzero\User\User::oneOrFail($user->id)->logger()->auth()->save(
			[
				'username' => $user->username,
				'status'   => 'blocked'
			]);
		}

		return $result;
	}

	/**
	 * hasExceededLoginLimit 
	 * 
	 * @param $result 

	 * @return bool
	 */
	private function hasExceededBlockLimit($response)
	{
		$params    = \Component::params('com_members');
		$limit     = (int)$params->get('blocked_accounts_limit', 10);
		$timeframe = (int)$params->get('blocked_accounts_timeframe', 1);
		$ip = $_SERVER['REMOTE_ADDR'];
		$fail2ban  = $params->get('fail2ban', 0);
		$jailname  = $params->get('fail2ban-jail', 'hub-login');
		$username = $response->username;
		$result    = true;

		// Fail2ban Enabled?
		if ($fail2ban == 1)
		{
			// Determine what the threshold is
			$threshold = date("Y-m-d H:i:s", strtotime(\Date::toSql() . " {$timeframe} hours ago"));
			$auths     = new \Hubzero\User\Log\Auth;

			// Select all usernames which are blocked
			$auths = $auths->whereEquals('status', 'blocked')
						->select('username')
						->whereEquals('ip', $ip)
			      ->where('logged', '>=', $threshold)
						->rows()
						->fieldsByKey('username');

			// Only unique blocked entries
			$auths = array_unique($auths);

			if (count($auths) < $limit)
			{
				$result = false;
			}
			else
			{
				// Check to see if fail2ban-client is installed
				$output = array();
				exec('which fail2ban-client', $output);
				if (!empty($output))
				{
					$path = $output[0];
				}
				else
				{
					//@FIXME: Convert to HUBzero Error Logger
					error_log('fail2ban-client not found.');

					// Bail early
					return false;
				}

				$command = 'sudo ' . $path . ' set ' . $jailname . ' banip ' . $ip;
				exec($command);
			}
		}
		else
		{
			// Fail2Ban disabled
			$result = false;
		}
		return $result;
	}
}
