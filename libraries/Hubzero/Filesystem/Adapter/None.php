<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2009-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Filesystem\Adapter;

use Hubzero\Filesystem\AdapterInterface;

/**
 * Null adapter for filesystem.
 */
class None implements AdapterInterface
{
	/**
	 * Determine if a file exists.
	 *
	 * @param   string  $path
	 * @return  bool
	 */
	public function exists($path)
	{
		return false;
	}

	/**
	 * Get the contents of a file.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function read($path)
	{
		return false;
	}

	/**
	 * Write the contents of a file.
	 *
	 * @param   string  $path
	 * @param   string  $contents
	 * @return  int
	 */
	public function write($path, $contents)
	{
		return false;
	}

	/**
	 * Prepend to a file.
	 *
	 * @param   string  $path
	 * @param   string  $contents
	 * @return  int
	 */
	public function prepend($path, $contents)
	{
		return false;
	}

	/**
	 * Append to a file.
	 *
	 * @param   string  $path
	 * @param   string  $contents
	 * @return  int
	 */
	public function append($path, $contents)
	{
		return false;
	}

	/**
	 * Delete the file at a given path.
	 *
	 * @param   mixed  $paths  string|array
	 * @return  bool
	 */
	public function delete($path)
	{
		return false;
	}

	/**
	 * Upload a file
	 *
	 * @param   string  $path
	 * @param   string  $target
	 * @return  bool
	 */
	public function upload($path, $target)
	{
		return false;
	}

	/**
	 * Move a file to a new location.
	 *
	 * @param   string  $path
	 * @param   string  $target
	 * @return  bool
	 */
	public function move($path, $target)
	{
		return false;
	}

	/**
	 * Rename a file.
	 *
	 * @param   string  $path
	 * @param   string  $target
	 * @return  bool
	 */
	public function rename($path, $target)
	{
		return false;
	}

	/**
	 * Copy a file to a new location.
	 *
	 * @param   string  $path
	 * @param   string  $target
	 * @return  bool
	 */
	public function copy($path, $target)
	{
		return false;
	}

	/**
	 * Extract the file name from a file path.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function name($path)
	{
		return preg_replace('#\.[^.]*$#', '', $path);
	}

	/**
	 * Extract the file extension from a file path.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function extension($path)
	{
		$dot = strrpos($path, '.') + 1;

		return substr($path, $dot);
	}

	/**
	 * Get the file type of a given file.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function type($path)
	{
		return '';
	}

	/**
	 * Get the file size of a given file.
	 *
	 * @param   string  $path
	 * @return  int
	 */
	public function size($path)
	{
		return 0;
	}

	/**
	 * Get the file's last modification time.
	 *
	 * @param   string  $path
	 * @return  int
	 */
	public function lastModified($path)
	{
		return 0;
	}

	/**
	 * Get the file's last modification time.
	 *
	 * @param   string  $path
	 * @return  int
	 */
	public function mimetype($path)
	{
		return null;
	}

	/**
	 * Determine if the given path is a directory.
	 *
	 * @param   string  $directory
	 * @return  bool
	 */
	public function isDirectory($directory)
	{
		return false;
	}

	/**
	 * Determine if the given path is writable.
	 *
	 * @param   string  $path
	 * @return  bool
	 */
	public function isWritable($path)
	{
		return false;
	}

	/**
	 * Determine if the given path is a file.
	 *
	 * @param   string  $file
	 * @return  bool
	 */
	public function isFile($file)
	{
		return false;
	}

	/**
	 * Run a virus scan against a file
	 *
	 * @param   string   $file  The name of the file [not full path]
	 * @return  boolean
	 */
	public function isSafe($file)
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function files($path, $filter = '.', $recursive = false, $full = false, $exclude = array('.svn', '.git', 'CVS', '.DS_Store', '__MACOSX'))
	{
		return $this->listContents($path, $filter, $recursive, $full, $exclude);
	}

	/**
	 * {@inheritdoc}
	 */
	public function directories($path, $filter = '.', $recursive = false, $full = false, $exclude = array('.svn', '.git', 'CVS', '.DS_Store', '__MACOSX'))
	{
		return $this->listContents($path, $filter, $recursive, $full, $exclude);
	}

	/**
	 * {@inheritdoc}
	 */
	public function listContents($path, $filter = '.', $recursive = false, $full = false, $exclude = array('.svn', '.git', 'CVS', '.DS_Store', '__MACOSX'))
	{
		return array();
	}

	/**
	 * Create a directory.
	 *
	 * @param   string  $path
	 * @param   int     $mode
	 * @param   bool    $recursive
	 * @param   bool    $force
	 * @return  bool
	 */
	public function makeDirectory($path, $mode = 0755, $recursive = false, $force = false)
	{
		return false;
	}

	/**
	 * Copy a directory from one location to another.
	 *
	 * @param   string  $directory
	 * @param   string  $destination
	 * @param   int     $options
	 * @return  bool
	 */
	public function copyDirectory($directory, $destination, $options = null)
	{
		return false;
	}

	/**
	 * Recursively delete a directory.
	 *
	 * The directory itself may be optionally preserved.
	 *
	 * @param   string  $directory
	 * @param   bool    $preserve
	 * @return  bool
	 */
	public function deleteDirectory($directory, $preserve = false)
	{
		return false;
	}

	/**
	 * Empty the specified directory of all files and folders.
	 *
	 * @param   string  $directory
	 * @return  bool
	 */
	public function emptyDirectory($directory)
	{
		return false;
	}

	/**
	 * Chmods files and directories recursively to given permissions.
	 *
	 * @param   string   $path        Root path to begin changing mode [without trailing slash].
	 * @param   string   $filemode    Octal representation of the value to change file mode to [null = no change].
	 * @param   string   $foldermode  Octal representation of the value to change folder mode to [null = no change].
	 * @return  boolean  True if successful [one fail means the whole operation failed].
	 */
	public function setPermissions($path, $filemode = '0644', $foldermode = '0755')
	{
		return false;
	}
}
