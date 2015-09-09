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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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