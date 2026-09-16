<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Helpers;

use Hubzero\Base\Obj;
use User;

/**
 * Projects Access helper class
 */
class AccessHelper extends Obj
{
	/**
	 * Determines if a directory is open to public access
	 *
	 * @param   string  $subdir
	 * @return  bool
	 */
	public static function allowPublicAccess($subdir)
	{
		return !User::isGuest() && self::isPublicPath($subdir);
	}

	/**
	 * Is the path inside the project's top-level public directory?
	 *
	 * This used to accept anything starting with "public", which let in a
	 * folder named "publications" and a path such as "public/../private".
	 * Callers decode the path again before they use it, so an encoded ".."
	 * would get past a check on the raw value. Decode until nothing changes,
	 * then refuse any ".." segment.
	 *
	 * @param   string  $path  Path relative to the project repository
	 * @return  bool
	 */
	public static function isPublicPath($path)
	{
		$path = (string) $path;

		$stable = false;
		for ($i = 0; $i < 5 && !$stable; $i++)
		{
			$decoded = rawurldecode($path);
			$stable  = ($decoded === $path);
			$path    = $decoded;
		}

		// Still changing after five rounds is not a path anyone meant
		if (!$stable || strpos($path, "\0") !== false)
		{
			return false;
		}

		if (preg_match('#(^|/)\.\.(/|$)#', $path))
		{
			return false;
		}

		return preg_match('#^/?public(/|$)#', $path) === 1;
	}
}
