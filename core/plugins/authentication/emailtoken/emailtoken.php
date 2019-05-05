<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Authentication plugin for HUBzero
 */
class plgAuthenticationEmailtoken extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior.
	 * If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * This method returns the HTML for display on the login page
	 *
	 * @param	string	$returnQueryString	String containing return query hash
	 * @return	string
	 */
	public static function onRenderOption($returnQueryString = '', $title = '')
	{
		// Immediately return, rendering nothing
		return "";
	}
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

	public function login(&$credentials, &$options)
	{
		$return = Request::getString('return', '');
		$options['return'] = base64_decode($return);
		return;
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
		// For JLog
		$response->type = 'emailtoken';

		$email = Request::getString('email', "no email given");

		// Some versions sent 'code', some sent 'confirm'
		$code = Request::getString('confirm', '');
		if (!$code)
		{
			$code = Request::getString('code', '');
		}

		// Get the user profile requested
		$user = User::oneByEmail($email);
		$activation = $user->get('activation', "no activation code");

		// User was already activated have them log in
		if ($activation == 1)
		{
			App::redirect(
				Route::url('index.php?option=com_users&view=login', false),
				Lang::txt('This account is already activated, please login'),
				'warning');
		}

		// Check if they gave the correct confimation token
		if ($code != -$activation)
		{
			// Don't need to do anything if it failed
			return;
		}

		// Success, build a response
		$response->username      = $user->get('username');
		$response->email         = $user->get('email');
		$response->fullname      = $user->get('name');
		$response->status        = \Hubzero\Auth\Status::SUCCESS;
		$response->error_message = '';

		// Set cookie with login preference info
		$prefs = array(
			'user_id'       => $user->get('id'),
			'user_img'      => $user->picture(0, false),
			'authenticator' => 'emailtoken'
		);

		$namespace = 'authenticator';
		$lifetime  = time() + 365*24*60*60;

		\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
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
	 * @param   object  $response
	 * @return  bool
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
					Log::error('fail2ban-client not found.');

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
