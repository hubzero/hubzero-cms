<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Filesystem\Macro;

/**
 * Filesystem macro for listing directories in a tree.
 */
class DirectoryTree extends Base
{
	/**
	 * Tree index.
	 *
	 * @var  int
	 */
	private $index = 0;

	/**
	 * Get the method name.
	 *
	 * @return  string
	 */
	public function getMethod()
	{
		return 'directoryTree';
	}

	/**
	 * Lists folder in format suitable for tree display.
	 *
	 * @param   string   $path      The path of the folder to read.
	 * @param   string   $filter    A filter for folder names.
	 * @param   integer  $maxLevel  The maximum number of levels to recursively read, defaults to three.
	 * @param   integer  $level     The current level, optional.
	 * @param   integer  $parent    Unique identifier of the parent folder, if any.
	 * @return  array
	 */
	public function handle($path, $filter = '.', $maxLevel = 3, $level = 0, $parent = 0)
	{
		$dirs = array();

		if ($level == 0)
		{
			$this->index = 0;
		}

		if ($level < $maxLevel)
		{
			$folders = $this->filesystem->listContents($path, $filter);

			// First path, index foldernames
			foreach ($folders as $name)
			{
				if ($name['type'] != 'dir')
				{
					continue;
				}

				$this->index++;

				$fullName = $this->filesystem->cleanPath($path . DS . $name['path']);

				$dirs[] = array(
					'id'       => $this->index,
					'parent'   => $parent,
					'name'     => ltrim($name['path'], '\\/'),
					'fullname' => $fullName,
					'relname'  => str_replace(PATH_ROOT, '', $fullName)
				);

				$dirs2 = $this->handle($fullName, $filter, $maxLevel, $level + 1, $this->index);

				$dirs = array_merge($dirs, $dirs2);
			}
		}

		return $dirs;
	}
}
