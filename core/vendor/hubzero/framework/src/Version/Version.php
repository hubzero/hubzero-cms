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

namespace Hubzero\Version;

/**
 * Class to store and retrieve the version of HUBzero CMS.
 *
 * Based off of Zend Framework's \Zend\Version\Version class
 * (http://framework.zend.com/)
 */
final class Version
{
	/**
	 * HUBzero CMS version identification - see compareVersion()
	 */
	const VERSION = '2.0.0';

	/**
	 * Github Service Identifier for version information is retreived from
	 */
	const VERSION_SERVICE_GITHUB = 'GITHUB';

	/**
	 * HUBzero (hubzero.org) Service Identifier for version information is retreived from
	 */
	const VERSION_SERVICE_HUBZERO = 'HUBZERO';

	/**
	 * The latest stable version HUBzero CMS available
	 *
	 * @var  string
	 */
	protected static $latestVersion;

	/**
	 * Compare the specified version string $version
	 * with the current Hubzero\Version\Version::VERSION
	 *
	 * @param   string  $version  A version string (e.g. "0.7.1").
	 * @return  int               -1 if the $version is older,
	 *                            0 if they are the same,
	 *                            and +1 if $version is newer.
	 */
	public static function compareVersion($version)
	{
		$version = strtolower($version);
		$version = preg_replace('/(\d)pr(\d?)/', '$1a$2', $version);
		return version_compare($version, strtolower(self::VERSION));
	}

	/**
	 * Fetches the version of the latest stable release.
	 *
	 * By default, this uses the API provided by hubzero.org for version
	 * retrieval.
	 *
	 * If $service is set to VERSION_SERVICE_GITHUB, this will use the GitHub
	 * API (v3) and only returns refs that begin with * 'tags/release-'.
	 * Because GitHub returns the refs in alphabetical order, we need to reduce
	 * the array to a single value, comparing the version numbers with
	 * version_compare().
	 *
	 * @see     http://developer.github.com/v3/git/refs/#get-all-references
	 * @link    https://api.github.com/repos/hubzero/hzcms1/git/refs/tags/release-
	 * @link    http://hubzero.org/api/hz-version?v=1
	 * @param   string  $service  Version Service with which to retrieve the version
	 * @return  string
	 */
	public static function getLatest($service = self::VERSION_SERVICE_HUBZERO)
	{
		if (null === static::$latestVersion)
		{
			static::$latestVersion = 'not available';
			if ($service == self::VERSION_SERVICE_GITHUB)
			{
				$url = 'https://api.github.com/repos/hubzero/hzcms1/git/refs/tags/release-';

				$apiResponse = json_decode(file_get_contents($url), true);

				// Simplify the API response into a simple array of version numbers
				$tags = array_map(function ($tag) {
					return substr($tag['ref'], 18); // Reliable because we're filtering on 'refs/tags/release-'
				}, $apiResponse);

				// Fetch the latest version number from the array
				static::$latestVersion = array_reduce($tags, function ($a, $b) {
					return version_compare($a, $b, '>') ? $a : $b;
				});
			}
			elseif ($service == self::VERSION_SERVICE_HUBZERO)
			{
				$handle = fopen('http://hubzero.org/api/hz-version?v=1', 'r');
				if (false !== $handle)
				{
					static::$latestVersion = stream_get_contents($handle);
					fclose($handle);
				}
			}
		}

		return static::$latestVersion;
	}

	/**
	 * Returns true if the running version of HUBzero CMS is
	 * the latest (or newer??) than the latest tag on GitHub,
	 * which is returned by static::getLatest().
	 *
	 * @return  bool
	 */
	public static function isLatest()
	{
		return static::compareVersion(static::getLatest()) < 1;
	}
}
