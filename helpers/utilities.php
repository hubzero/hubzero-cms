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
 * @package		hubzero-cms
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Helpers;

/**
 * Utility methods
 */
class Utilities
{
	/**
	 * Returns mkAIP script path
	 *
	 * @return		 string
	 */
	public static function getMkAipBase()
	{
		return PATH_APP . DS . 'mkAIP/cli/mkaip/bin/mkaip';
	}

	/**
	 * Checks if mkAIP is used
	 *
	 * @return	 boolean
	 */
	public static function archiveOn()
	{
		$mkaip = self::getMkAipBase();
		if (file_exists($mkaip))
		{
			return true;
		}

		return false;
	}

	/**
	 * Run mkAIP
	 *
	 * @param			 object $row			Publication version object
	 * @return		 void
	 */
	public static function mkAip($row)
	{
		$mkaip = self::getMkAipBase();

		// Create OAIS Archival Information Package
		if (file_exists($mkaip))
		{
			$mkaipOutput = 'mkaip-' . str_replace( '/', '__', $row->doi) . '.out';

			/**
			 * Changes for receving error notification from mkAIP script
			 * Exit status code 0 represents mkAIP script completes its execution and without any exception.
			 * Exit status code 1 represents some exception is thrown out.
			 **/
			$cmd = '/usr/bin/php ' . $mkaip . ' ' .$row->doi . ' ' . '2>&1 > ' . "/www/tmp/$mkaipOutput";
			exec($cmd, $output, $exitCode);

			if ($exitCode == 0)
			{
				 return true;
			}
		}
		return false;
	}
}
