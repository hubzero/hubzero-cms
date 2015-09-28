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
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Log;

/**
 * Post log class
 **/
class Post extends Base
{
	/**
	 * Fields available in this log and their default visibility
	 *
	 * @var  array
	 **/
	protected static $fields = array(
		'timestamp' => true,
		'uri'       => true,
		'referrer'  => true,
		'data'      => true
	);

	/**
	 * If dates/times are present, how are they formatted
	 *
	 * @var  string
	 **/
	protected static $dateFormat = "Y-m-d\TH:i:s.uP";

	/**
	 * Parses
	 *
	 * @return  void
	 **/
	public static function parseData($value)
	{
		$ciphertext = base64_decode($value);

		// Get the IV
		$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
		$iv     = substr($ciphertext, 0, $ivSize);

		// Get just the cipher without the IV
		$ciphertext = substr($ciphertext, $ivSize);

		// Generate key and decrypt
		$key       = md5(\App::get('config')->get('secret'));
		$plaintext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $ciphertext, MCRYPT_MODE_CBC, $iv);

		return $plaintext;
	}

	/**
	 * Get log path
	 *
	 * @return  string
	 **/
	public static function path()
	{
		$dir = \Config::get('log_path');

		if (is_dir('/var/log/hubzero-cms'))
		{
			$dir = '/var/log/hubzero-cms';
		}

		$path = $dir . '/cmspost.log';

		return $path;
	}
}