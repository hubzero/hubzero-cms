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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Helpers;

use Exception;
use Component;
use Lang;

/**
 * Utility methods
 */
class Utilities
{
	/**
	 * Cleans, normalizes, and constructs full path to media directory
	 *
	 * @param   string  $dir     Primary directory for media
	 * @param   string  $subdir  Sub-directory of primary (optional)
	 * @return  string  Return full system path
	 */
	public static function buildUploadPath($dir, $subdir='')
	{
		$config = Component::params('com_resources');

		if ($subdir)
		{
			// Normalize path
			$subdir = self::normalizePath($subdir);
		}

		// Get the configured upload path
		$base = $config->get('uploadpath', '/site/resources');
		$base = self::normalizePath($base);

		// Normalize path
		$dir = self::normalizePath($dir);

		// Does the beginning of the $dir match the config path?
		if (substr($dir, 0, strlen($base)) == $base)
		{
			// Yes - ... this really shouldn't happen
			throw new Exception(Lang::txt('Paths match.'), 500);
		}
		else
		{
			// No - append it
			$dir = $base . $dir;
		}

		// Build the path
		return PATH_APP . $dir . $subdir;
	}

	/**
	 * Strips trailing slashes and ensures path begins with a slash
	 *
	 * @param   string  $path  Path to normalize
	 * @return  string
	 */
	public static function normalizePath($path)
	{
		$path = \Filesystem::cleanPath($path);

		// Make sure the path doesn't end with a slash
		$path = rtrim($path, DS);

		// Ensure the path starts with a slash
		$path = ltrim($path, DS);
		$path = DS . $path;

		return $path;
	}
}

