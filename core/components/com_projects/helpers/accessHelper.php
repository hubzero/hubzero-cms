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
		$isPublicDirectory = preg_match('/^\/?public.*/', $subdir); //!= 'public' && $subdir != '/public')
		$allowPublicAccess = !User::isGuest() && $isPublicDirectory;

		return $allowPublicAccess;
	}
}
