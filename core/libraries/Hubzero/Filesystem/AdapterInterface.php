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
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

/**
 * Filesystem adapter interface.
 */
interface AdapterInterface
{
	/**
	 * Check whether a file exists.
	 *
	 * @param   string  $path
	 * @return  bool
	 */
	public function exists($path);

	/**
	 * Read a file.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function read($path);

	/**
	 * Write a new file.
	 *
	 * @param   string  $path
	 * @param   string  $contents
	 * @return  bool    False on failure, true on success
	 */
	public function write($path, $contents);

	/**
	 * Prepend to a file.
	 *
	 * @param   string  $path
	 * @param   string  $contents
	 * @return  bool    False on failure, true on success
	 */
	public function prepend($path, $contents);

	/**
	 * Append to a file.
	 *
	 * @param   string  $path
	 * @param   string  $contents
	 * @return  bool    False on failure, true on success
	 */
	public function append($path, $contents);

	/**
	 * Move a file to a new location.
	 *
	 * @param   string  $path
	 * @param   string  $target
	 * @return  bool
	 */
	public function move($path, $target);

	/**
	 * Rename a file.
	 *
	 * @param   string  $path
	 * @param   string  $target
	 * @return  bool
	 */
	public function rename($path, $target);

	/**
	 * Copy a file.
	 *
	 * @param   string  $path
	 * @param   string  $target
	 * @return  bool
	 */
	public function copy($path, $target);

	/**
	 * Delete a file.
	 *
	 * @param   string  $path
	 * @return  bool
	 */
	public function delete($path);

	/**
	 * Searches the directory paths for a given file.
	 *
	 * @param   mixed   $paths  An path string or array of path strings to search in
	 * @param   string  $file   The file name to look for.
	 * @return  mixed   Full path and name for the target file, or false if file not found.
	 */
	public function find($paths, $file);

	/**
	 * Get all of the directories within a given directory.
	 *
	 * @param   string   $path     The path of the folder to read.
	 * @param   string   $filter   A filter for file names.
	 * @param   mixed    $recurse  True to recursively search into sub-folders, or an integer to specify the maximum depth.
	 * @param   boolean  $full     True to return the full path to the file.
	 * @param   array    $exclude  Array with names of files which should not be shown in the result.
	 * @return  array
	 */
	public function listContents($path, $filter, $recursive, $full, $exclude);

	/**
	 * Create a directory.
	 *
	 * @param   string   $path       Directory name
	 * @param   integer  $mode       Permissions
	 * @param   boolean  $recursive  Recursively create?
	 * @param   boolean  $force      Forcefully create?
	 * @return  boolean
	 */
	public function makeDirectory($path, $mode, $recursive, $force);

	/**
	 * Create a directory.
	 *
	 * @param   string   $path     Source
	 * @param   string   $target   Destination
	 * @param   integer  $options  Options
	 * @return  boolean
	 */
	public function copyDirectory($path, $target, $options);

	/**
	 * Delete a directory.
	 *
	 * @param   string  $path
	 * @return  bool
	 */
	public function deleteDirectory($path, $preserve);

	/**
	 * Chmods files and directories recursively to given permissions.
	 *
	 * @param   string   $path        Root path to begin changing mode [without trailing slash].
	 * @param   string   $filemode    Octal representation of the value to change file mode to [null = no change].
	 * @param   string   $foldermode  Octal representation of the value to change folder mode to [null = no change].
	 * @return  boolean  True if successful [one fail means the whole operation failed].
	 */
	public function setPermissions($path, $filemode, $foldermode);
}
