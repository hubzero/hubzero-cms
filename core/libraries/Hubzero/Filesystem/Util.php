<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

use Hubzero\Filesystem\Util\MimeType;
use Hubzero\Filesystem\Exception\PathViolationException;

/**
 * Utility methods
 */
class Util
{
	/**
	 * Normalize a dirname return value.
	 *
	 * @param   string  $dirname
	 * @return  string  Normalized dirname
	 */
	public static function normalizeDirname($dirname)
	{
		if ($dirname === '.')
		{
			return '';
		}

		return $dirname;
	}

	/**
	 * Get a normalized dirname from a path.
	 *
	 * @param   string  $path
	 * @return  string  Dirname
	 */
	public static function dirname($path)
	{
		return static::normalizeDirname(dirname($path));
	}

	/**
	 * Checks for snooping outside of the file system root.
	 *
	 * @param   string  $path  A file system path to check.
	 * @param   string  $ds    Directory separator (optional).
	 * @return  string  A cleaned version of the path or exit on error.
	 */
	public static function checkPath($path, $ds = DIRECTORY_SEPARATOR)
	{
		if (strpos($path, '..') !== false)
		{
			throw new PathViolationException('Use of relative paths not permitted');
		}

		$path = self::normalizePath($path);

		if (PATH_ROOT != '' && strpos($path, self::normalizePath(PATH_ROOT)) !== 0)
		{
			// Don't translate
			throw new PathViolationException('Snooping out of bounds @ ' . $path);
		}

		return $path;
	}

	/**
	 * Normalize path.
	 *
	 * @param   string  $path
	 * @return  string
	 * @throws  InvalidArgumentException
	 */
	public static function normalizePath($path, $ds = DIRECTORY_SEPARATOR)
	{
		if (!is_string($path) && !empty($path))
		{
			var_dump($path); die();
			throw new \InvalidArgumentException('$path is not a string.');
		}

		$path = trim($path);

		// Remove any kind of funky unicode whitespace
		$path = preg_replace('#\p{C}+|^\./#u', '', $path);

		if (empty($path))
		{
			$path = PATH_ROOT;
		}
		// Remove double slashes and backslashes and convert all slashes
		// and backslashes to DIRECTORY_SEPARATOR. If dealing with a UNC
		// path don't forget to prepend the path with a backslash.
		else if ($ds == '\\' && $path[0] == '\\' && $path[1] == '\\')
		{
			$path = "\\" . preg_replace('#[/\\\\]+#', $ds, $path);
		}
		else
		{
			$path = preg_replace('#[/\\\\]+#', $ds, $path);
		}

		return $path;
	}

	/**
	 * Normalize relative directories in a path.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public static function normalizeRelativePath($path)
	{
		// Path remove self referring paths ("/./").
		$path = preg_replace('#/\.(?=/)|^\./|/\./?$#', '', $path);

		// Regex for resolving relative paths
		$regex = '#/*[^/\.]+/\.\.#Uu';

		while (preg_match($regex, $path))
		{
			$path = preg_replace($regex, '', $path);
		}

		return $path;
	}

	/**
	 * Makes path name safe to use.
	 *
	 * @param   string  $path  The full path to sanitise.
	 * @return  string  The sanitised string.
	 */
	public static function normalizeDirectory($path)
	{
		$regex = array('#[^A-Za-z0-9:_\\\/-]#');

		return preg_replace($regex, '', $path);
	}

	/**
	 * Normalize path.
	 *
	 * @param   string  $file
	 * @return  string
	 */
	public static function normalizeFile($file)
	{
		// Remove any trailing dots, as those aren't ever valid file names.
		$normalized = rtrim($file, '.');

		$regex = array(
			'#(\.){2,}#',
			'#[^A-Za-z0-9\.\_\- ]#',
			'#^\.#'
		);

		return preg_replace($regex, '', $normalized);
	}

	/**
	 * Get content size.
	 *
	 * @param   string   $contents
	 * @return  integer  Content size
	 */
	public static function contentSize($contents)
	{
		return mb_strlen($contents, '8bit');
	}

	/**
	 * Guess MIME Type based on the path of the file and it's content.
	 *
	 * @param   string  $path
	 * @param   string  $content
	 * @return  mixed   MIME Type or NULL if no extension detected
	 */
	public static function guessMimeType($path, $content)
	{
		$mimeType = MimeType::detectByContent($content);

		if (empty($mimeType) || $mimeType === 'text/plain')
		{
			$extension = pathinfo($path, PATHINFO_EXTENSION);

			if ($extension)
			{
				$mimeType = MimeType::detectByFileExtension($extension) ?: 'text/plain';
			}
		}

		return $mimeType;
	}
}
