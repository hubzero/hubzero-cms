<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.	 If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package	  hubzero-cms
 * @author	  Anthony Fuents <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	  http://opensource.org/licenses/MIT MIT
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
