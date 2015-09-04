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

namespace Hubzero\Filesystem\Type;

use Hubzero\Filesystem\File;

/**
 * Expandable model
 */
class Expandable extends File
{
	/**
	 * Expand archive
	 *
	 * @param   bool  $cleanup  Whether or not to clean up after expansion (i.e. removing known OS files, etc...)
	 * @return  bool
	 */
	public function expand($cleanup = true)
	{
		if ($cleanup)
		{
			return $this->cleanup();
		}

		return true;
	}

	/**
	 * Cleans the archive of OS-specific files
	 *
	 * @return  bool
	 **/
	protected function cleanup()
	{
		$items = $this->getParent(false)->listContents();

		foreach ($items as $item)
		{
			if (in_array($item->getName(), ['.svn', 'CVS', '.DS_Store', '__MACOSX']))
			{
				if (!$item->delete())
				{
					return false;
				}
			}
		}

		return true;
	}
}