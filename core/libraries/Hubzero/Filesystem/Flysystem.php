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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Filesystem;

/**
 * Hubzero class for manipulating and reading the filesystem
 */
class Flysystem extends \League\Flysystem\Filesystem
{
	/**
	 * Gets the directory contents
	 *
	 * @param   string  $directory  The subdirectory to access within the filesystem root
	 * @param   bool    $recursive  Whether or not to iterate down recursively
	 * @return  \Hubzero\Filesystem\Collection
	 **/
	public function listContents($directory = '', $recursive = false)
	{
		$contents = parent::listContents($directory, $recursive);

		return $this->encapsulate($contents);
	}

	/**
	 * Encapsulates the entities list in their appropriate classes and returns as part of a collection
	 *
	 * @param   array  $entities  The filesystem contents
	 * @return  \Hubzero\Filesystem\Collection
	 */
	private function encapsulate($entities)
	{
		$items = [];

		foreach ($entities as $entity)
		{
			$items[] = Entity::fromMetadata($entity, $this);
		}

		return new Collection($items);
	}
}