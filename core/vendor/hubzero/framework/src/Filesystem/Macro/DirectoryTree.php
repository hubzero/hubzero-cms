<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
