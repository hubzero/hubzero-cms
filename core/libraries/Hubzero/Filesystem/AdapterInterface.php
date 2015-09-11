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
