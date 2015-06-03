<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
