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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

/**
 * Cookie utility class
 *
 * Set and retrieve cookies in consistent manner
 */
class Cookie
{
	/**
	 * Drop a cookie
	 *
	 * @param  (string) $namespace - make sure the cookie name is unique
	 * @param  (time)   $lifetime  - how long the cookie should last
	 * @param  (array)  $data      - data to be saved in cookie
	 * @return void
	 **/
	public static function bake($namespace, $lifetime, $data=array())
	{
		$hash   = \App::hash(\App::get('client')->name . ':' . $namespace);

		$key = \App::hash('');
		$crypt = new \Hubzero\Encryption\Encrypter(
			new \Hubzero\Encryption\Cipher\Simple,
			new \Hubzero\Encryption\Key('simple', $key, $key)
		);
		$cookie = $crypt->encrypt(serialize($data));

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

		// Set the actual cookie
		setcookie($hash, $cookie, $lifetime, '/', '', $secure, true);
	}

	/**
	 * Retrieve a cookie
	 *
	 * @param  (string) $namespace - make sure the cookie name is unique
	 * @return (object) $cookie data
	 **/
	public static function eat($namespace)
	{
		$hash  = \App::hash(\App::get('client')->name . ':' . $namespace);

		$key = \App::hash('');
		$crypt = new \Hubzero\Encryption\Encrypter(
			new \Hubzero\Encryption\Cipher\Simple,
			new \Hubzero\Encryption\Key('simple', $key, $key)
		);

		if ($str = \App::get('request')->getString($hash, '', 'cookie', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM))
		{
			$sstr   = $crypt->decrypt($str);
			$cookie = @unserialize($sstr);

			return (object)$cookie;
		}

		return false;
	}
}