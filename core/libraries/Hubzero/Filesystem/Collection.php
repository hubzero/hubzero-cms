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

use Hubzero\Base\ItemList;

/**
 * Iterable class for filesystem objects
 *
 * This is not necessarily a directory.  It could simply be a list of files
 * from multiple locations...maybe even multiple filesystems?
 */
class Collection extends ItemList
{
	/**
	 * Sorts items by a given field and direction
	 *
	 * @param   string  $key  The key to sort by
	 * @param   string  $asc  Whether or not to sort asc or descending
	 * @return  static
	 **/
	public function sort($key, $asc = true)
	{
		return $this->sortByCallback(function($a, $b) use ($key, $asc)
		{
			if (!isset($a->$key) || !isset($b->$key))
			{
				if (!isset($a->$key)) return ($asc) ? -1 :  1;
				if (!isset($b->$key)) return ($asc) ?  1 : -1;
				return 0;
			}

			if ($asc)
			{
				return strnatcmp($a->$key, $b->$key);
			}
			else
			{
				return strnatcmp($b->$key, $a->$key);
			}
		});
	}

	/**
	 * Sorts items using the provided callback function
	 *
	 * @param   closure  $callback  The sorting function to use
	 * @return  static
	 **/
	public function sortByCallback($callback)
	{
		$cache = $this->_data;

		usort($cache, $callback);

		return new static($cache);
	}

	/**
	 * Compresses/archives entities in collection
	 *
	 * @param   bool  $structure  Whether or not to retain directory location of files being zipped
	 * @param   bool  $upload     Whether or not to reupload compressed to filesystem location
	 * @return  string|bool
	 */
	public function compress($structure = false, $upload = false)
	{
		if (!extension_loaded('zip'))
		{
			return false;
		}

		// Get temp directory
		$adapter = null;
		$temp    = sys_get_temp_dir();
		$tarname = uniqid() . '.zip';
		$zip     = new \ZipArchive;

		if ($zip->open($temp . DS . $tarname, \ZipArchive::OVERWRITE) === TRUE)
		{
			foreach ($this->_data as $entity)
			{
				if ($entity->isFile())
				{
					$zip->addFromString($structure ? $entity->getPath() : $entity->getName(), $entity->read());
				}
				else if ($entity->isDir() && $structure)
				{
					$zip->addEmptyDir($entity->getPath());
				}

				// Set some vars in case we need them later
				$adapter = $adapter ?: $entity->getAdapter();
			}

			$zip->close();

			$local = Manager::getTempPath();

			if ($upload)
			{
				// @FIXME: use manager copy?
				$entity = Entity::fromPath($tarname, $adapter);
				$entity->put($local->readAndDelete());

				return $entity;
			}
			else
			{
				return $local;
			}
		}
		else
		{
			return false;
		}
	}
}