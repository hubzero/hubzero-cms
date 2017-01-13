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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

/**
 * System Remember Me Plugin
 */
class plgSystemRemember extends \Hubzero\Plugin\Plugin
{
	/**
	 * Hook for after app initialization
	 *
	 * @return  void
	 */
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
				$goodCookie  = true;

				$filter = JFilterInput::getInstance();

				// Create the encryption key, apply extra hardening using the user agent string.
				// Since we're decoding, no UA validity check is required.
				$privateKey = App::hash(@$_SERVER['HTTP_USER_AGENT']);

				$crypt = new Hubzero\Encryption\Encrypter(
					new Hubzero\Encryption\Cipher\Simple,
					new Hubzero\Encryption\Key('simple', $privateKey, $privateKey)
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
