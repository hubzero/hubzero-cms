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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

			$local = Manager::getTempPath($tarname);

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