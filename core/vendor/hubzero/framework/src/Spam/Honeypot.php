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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam;

use Hubzero\Html\Builder\Input;
use Hubzero\Encryption\Encrypter;
use Hubzero\Encryption\Cipher\Simple;
use Hubzero\Encryption\Key;

/**
 * This technique is based on creating an input field that should be left
 * empty by the real users of the application but will most likely be
 * filled out by spam bots.
 */
class Honeypot
{
	/**
	 * Displays a hidden token field to reduce the risk of CSRF exploits
	 *
	 * @param   string   $name
	 * @param   integer  $delay
	 * @return  string
	 */
	public static function generate($name = null)
	{
		$name = $name ?: self::getName();

		return '<label id="hypt_' . $name . '_wrap" style="display:none;">' . "\n" .
					'Leave this field empty:' . "\n" .
					Input::input('text', $name . '[p]') . "\n" .
					Input::input('text', $name . '[t]', self::getEncrypter()->encrypt(time())) . "\n" .
				'</label>' . "\n";
	}

	/**
	 * Validate honeypot
	 * 
	 * @param   mixed    $value
	 * @param   mixed    $tme
	 * @param   integer  $delay
	 * @return  boolean
	 */
	public static function isValid($value, $tme, $delay = 3)
	{
		return (self::validatePot($value) && self::validateTime($tme, $delay));
	}

	/**
	 * Validate pot is empty
	 * 
	 * @param   mixed  $value
	 * @return  boolean
	 */
	public static function validatePot($value)
	{
		return $value == '';
	}

	/**
	 * Validate time was within the time limit
	 * 
	 * @param   mixed    $value
	 * @param   integer  $delay
	 * @return  boolean
	 */
	public static function validateTime($value, $delay)
	{
		// Get the decrypted time
		$value = self::getEncrypter()->decrypt($value);

		// The current time should be greater than the time the form was built + the speed option
		return (is_numeric($value) && time() > ($value + $delay));
	}

	/**
	 * Get a unique form name
	 * 
	 * @return  string
	 */
	public static function getName()
	{
		return 'hypt' . substr(\App::get('session')->getFormToken(), 0, 7);
	}

	/**
	 * Get the encrypter
	 * 
	 * @return  object
	 */
	protected static function getEncrypter()
	{
		static $crypt;

		if (!$crypt)
		{
			$key = \App::get('session')->getFormToken();

			$crypt = new Encrypter(
				new Simple,
				new Key('simple', $key, $key)
			);
		}

		return $crypt;
	}
}
