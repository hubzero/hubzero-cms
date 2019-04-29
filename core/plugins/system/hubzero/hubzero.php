<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin for hubzero
 */
class plgSystemHubzero extends \Hubzero\Plugin\Plugin
{
	/**
	 * Hook for after app initialization
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		// Get the session object
		$session = App::get('session');

		if ($session->isNew())
		{
			$tracker = array();

			// Transfer tracking cookie data to session
			$hash = App::hash(App::get('client')->name . ':tracker');

			$key = App::hash('');
			$crypt = new Hubzero\Encryption\Encrypter(
				new Hubzero\Encryption\Cipher\Simple,
				new Hubzero\Encryption\Key('simple', $key, $key)
			);

			if ($str = Request::getString($hash, '', 'cookie', 1 | 2))
			{
				$sstr = $crypt->decrypt($str);
				$tracker = @unserialize($sstr);

				if ($tracker === false) // old tracking cookies encrypted with UA which is too short term for a tracking cookie
				{
					//Create the encryption key, apply extra hardening using the user agent string
					$key = App::hash(@$_SERVER['HTTP_USER_AGENT']);
					$crypt = new Hubzero\Encryption\Encrypter(
						new Hubzero\Encryption\Cipher\Simple,
						new Hubzero\Encryption\Key('simple', $key, $key)
					);
					$sstr = $crypt->decrypt($str);
					$tracker = @unserialize($sstr);
				}
			}

			if (!is_array($tracker))
			{
				$tracker = array();
			}

			if (empty($tracker['user_id']))
			{
				$session->clear('tracker.user_id');
			}
			else
			{
				$session->set('tracker.user_id', $tracker['user_id']);
			}

			if (empty($tracker['username']))
			{
				$session->clear('tracker.username');
			}
			else
			{
				$session->set('tracker.username', $tracker['username']);
			}

			if (empty($tracker['sid']))
			{
				$session->clear('tracker.psid');
			}
			else
			{
				$session->set('tracker.psid', $tracker['sid']);
			}

			$session->set('tracker.sid', $session->getId());

			if (empty($tracker['ssid']))
			{
				$session->set('tracker.ssid', $session->getId());
			}
			else
			{
				$session->set('tracker.ssid', $tracker['ssid']);
			}

			if (empty($tracker['rsid']))
			{
				$session->set('tracker.rsid', $session->getId());
			}
			else
			{
				$session->set('tracker.rsid', $tracker['rsid']);
			}

			// Log tracking cookie detection to auth log
			$username = (empty($tracker['username'])) ? '-' : $tracker['username'];
			$user_id  = (empty($tracker['user_id']))  ? 0   : $tracker['user_id'];

			App::get('log')
				->logger('auth')
				->info($username . ' ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '') . ' detect');

			// set new tracking cookie with current data
			$tracker = array();
			$tracker['user_id']  = $session->get('tracker.user_id');
			$tracker['username'] = $session->get('tracker.username');
			$tracker['sid']      = $session->get('tracker.sid');
			$tracker['rsid']     = $session->get('tracker.rsid');
			$tracker['ssid']     = $session->get('tracker.ssid');

			$cookie = $crypt->encrypt(serialize($tracker));
			$lifetime = time() + 365*24*60*60*10;

			// Determine whether cookie should be 'secure' or not
			$secure   = false;
			$forceSsl = Config::get('force_ssl', false);

			if (App::isAdmin() && $forceSsl >= 1)
			{
				$secure = true;
			}
			else if (App::isSite() && $forceSsl == 2)
			{
				$secure = true;
			}

			setcookie($hash, $cookie, $lifetime, '/', '', $secure, true);
		}

		// All page loads set apache log data
		if (strpos(php_sapi_name(), 'apache') !== false && function_exists('apache_note'))
		{
			apache_note('session', $session->getId());

			if (User::get('id') != 0)
			{
				apache_note('auth', 'session');
				apache_note('userid', User::get('id'));
			}
			else if (!empty($tracker['user_id']))
			{
				apache_note('auth', 'cookie');
				apache_note('userid', $tracker['user_id']);
				apache_note('tracker', $tracker['rsid']);
			}
		}
	}

	/**
	 * Hook for login failure
	 *
	 * @param   array  $response
	 * @return  void
	 */
	public function onUserLoginFailure($response)
	{
		App::get('log')
			->logger('auth')
			->info((isset($_POST['username']) ? $_POST['username'] : '[unknown]') . ' ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '') . ' invalid');

		if (strpos(php_sapi_name(), 'apache') !== false && function_exists('apache_note'))
		{
			apache_note('auth', 'invalid');
		}
	}
}
