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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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