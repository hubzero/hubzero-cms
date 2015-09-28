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
 * Filesystem macro for listing directories.
 */
class Directories extends Base
{
	/**
	 * Get the method name.
	 *
	 * @return  string
	 */
	public function getMethod()
	{
		return 'directories';
	}

	/**
	 * List all directories.
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
			if ($object['type'] === 'dir')
			{
				$result[] = $object['path'];
			}
		}

		return $result;
	}
}
