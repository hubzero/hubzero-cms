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
 * Filesystem macro for listing files.
 */
class Files extends Base
{
	/**
	 * Get the method name.
	 *
	 * @return  string
	 */
	public function getMethod()
	{
		return 'files';
	}

	/**
	 * List all files in the directory.
	 *
	 * @param   string   $path     The path of the folder to read.
	 * @param   string   $filter   A filter for file names.
	 * @param   mixed    $recurse  True to recursively search into sub-folders, or an integer to specify the maximum depth.
	 * @param   boolean  $full     True to return the full path to the file.
	 * @param   array    $exclude  Array with names of files which should not be shown in the result.
	 * @return  array
	 */
	public function handle($path, $filter = '.', $recursive = false, $full = false, $exclude = array('.svn', '.git', 'CVS', '.DS_Store', '__MACOSX'))
	{
		$result = array();

		$contents = $this->filesystem->listContents($path, $filter, $recursive, $full, $exclude);

		foreach ($contents as $object)
		{
			if ($object['type'] === 'file')
			{
				$result[] = $object['path'];
			}
		}

		return $result;
	}
}
