<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die;

/**
 * Joomla! System Remember Me Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.remember
 */
class plgSystemRemember extends \Hubzero\Plugin\Plugin
{
	public function onAfterInitialise()
	{
		// No remember me for admin
		if (!App::isSite())
		{
			return;
		}

		if (User::isGuest())
		{
			$hash = App::hash('JLOGIN_REMEMBER');

			if ($str = Request::getString($hash, '', 'cookie', 1 | 2))
			{
				$credentials = array();
				$goodCookie = true;
				$filter = JFilterInput::getInstance();

				// Create the encryption key, apply extra hardening using the user agent string.
				// Since we're decoding, no UA validity check is required.
				$privateKey = App::hash(@$_SERVER['HTTP_USER_AGENT']);

				$crypt = new \Hubzero\Encryption\Encrypter(
					new \Hubzero\Encryption\Cipher\Simple,
					new \Hubzero\Encryption\Key('simple', $privateKey, $privateKey)
				);

				try
				{
					$str = $crypt->decrypt($str);
					if (!is_string($str))
					{
						throw new Exception('Decoded cookie is not a string.');
					}

					$cookieData = json_decode($str);
					if (null === $cookieData)
					{
						throw new Exception('JSON could not be docoded.');
					}
					if (!is_object($cookieData))
					{
						throw new Exception('Decoded JSON is not an object.');
					}

					// json_decoded cookie could be any object structure, so make sure the
					// credentials are well structured and only have user and password.
					if (isset($cookieData->username) && is_string($cookieData->username))
					{
						$credentials['username'] = $filter->clean($cookieData->username, 'username');
					}
					else
					{
						throw new Exception('Malformed username.');
					}
					if (isset($cookieData->password) && is_string($cookieData->password))
					{
						$credentials['password'] = $filter->clean($cookieData->password, 'string');
					}
					else
					{
						throw new Exception('Malformed password.');
					}

					// We're only doing this for the site app, so we explicitly set the action here
					$return = App::get('auth')->login($credentials, array('silent' => true, 'action' => 'core.login.site'));
					if (!$return)
					{
						throw new Exception('Log-in failed.');
					}

				}
				catch (Exception $e)
				{
					$cookie_domain = Config::get('cookie_domain', '');
					$cookie_path   = Config::get('cookie_path', '/');
					// Clear the remember me cookie
					setcookie(
						App::hash('JLOGIN_REMEMBER'), false, time() - 86400,
						$cookie_path, $cookie_domain
					);
					Log::warning('A remember me cookie was unset for the following reason: ' . $e->getMessage());
				}
			}
		}
	}
}
