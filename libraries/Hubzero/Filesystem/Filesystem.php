<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2009-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Filesystem;

use FilesystemIterator;
use DirectoryIterator;

/**
 * Hubzero class for manipulating and reading the filesystem.
 */
class Filesystem
{
	/**
	 * Determine if a file exists.
	 *
	 * @param   string  $path
	 * @return  bool
	 */
	public function exists($path)
	{
		return file_exists($path);
	}

	/**
	 * Get the contents of a file.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function get($path)
	{
		if ($this->isFile($path)) return file_get_contents($path);

		throw new FileNotFoundException(\Lang::txt('File does not exist at path %s', $path));
	}

	/**
	 * Get the contents of a remote file.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function getRemote($path)
	{
		return file_get_contents($path);
	}

	/**
	 * Write the contents of a file.
	 *
	 * @param   string  $path
	 * @param   string  $contents
	 * @return  int
	 */
	public function put($path, $contents)
	{
		return file_put_contents($path, $contents);
	}

	/**
	 * Prepend to a file.
	 *
	 * @param   string  $path
	 * @param   string  $data
	 * @return  int
	 */
	public function prepend($path, $data)
	{
		if ($this->exists($path))
		{
			return $this->put($path, $data . $this->get($path));
		}

		return $this->put($path, $data);
	}

	/**
	 * Append to a file.
	 *
	 * @param   string  $path
	 * @param   string  $data
	 * @return  int
	 */
	public function append($path, $data)
	{
		return file_put_contents($path, $data, FILE_APPEND);
	}

	/**
	 * Delete the file at a given path.
	 *
	 * @param   mixed  $paths  string|array
	 * @return  bool
	 */
	public function delete($paths)
	{
		$paths = is_array($paths) ? $paths : func_get_args();

		$success = true;

		foreach ($paths as $path)
		{
			if (!is_file($path))
			{
				continue;
			}

			// Try making the file writable first. If it's read-only, it can't be deleted
			// on Windows, even if the parent folder is writable
			@chmod($path, 0777);

			if (!@unlink($path)) $success = false;
		}

		return $success;
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
		$success = false;

		$dir = dirname($target);

		if (!file_exists($dir))
		{
			if (!$this->makeDirectory($dir))
			{
				return $success;
			}
		}

		if (is_writeable($dir) && move_uploaded_file($path, $target))
		{
			if ($this->setPermissions($target))
			{
				$success = true;
			}
		}

		return $success;
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
		return rename($path, $target);
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
		return copy($path, $target);
	}

	/**
	 * Extract the file name from a file path.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function name($path)
	{
		return pathinfo($path, PATHINFO_FILENAME);
	}

	/**
	 * Extract the file extension from a file path.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * Get the file type of a given file.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function type($path)
	{
		return filetype($path);
	}

	/**
	 * Get the file size of a given file.
	 *
	 * @param   string  $path
	 * @return  int
	 */
	public function size($path)
	{
		if ($this->isFile($path))
		{
			return filesize($path);
		}

		$ret = 0;
		foreach (glob($path . DS . "*") as $fn)
		{
			$ret += $this->size($fn);
		}
		return $ret;
	}

	/**
	 * Get the file's last modification time.
	 *
	 * @param   string  $path
	 * @return  int
	 */
	public function lastModified($path)
	{
		return filemtime($path);
	}

	/**
	 * Determine if the given path is a directory.
	 *
	 * @param   string  $directory
	 * @return  bool
	 */
	public function isDirectory($directory)
	{
		return is_dir($directory);
	}

	/**
	 * Determine if the given path is writable.
	 *
	 * @param   string  $path
	 * @return  bool
	 */
	public function isWritable($path)
	{
		return is_writable($path);
	}

	/**
	 * Determine if the given path is a file.
	 *
	 * @param   string  $file
	 * @return  bool
	 */
	public function isFile($file)
	{
		return is_file($file);
	}

	/**
	 * Run a virus scan against a file
	 *
	 * @param   string   $file  The name of the file [not full path]
	 * @return  boolean
	 */
	public function isSafe($file)
	{
		if ($command = \App::get('config')->get('virus_scanner', "clamscan -i --no-summary --block-encrypted"))
		{
			$command = trim($command);
			if (strstr($command, '%s'))
			{
				$command = sprintf($command, $file);
			}
			else
			{
				$command .= ' ' . str_replace(' ', '\ ', $file);
			}

			exec($command, $output, $status);

			if ($status == 1)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Find path names matching a given pattern.
	 *
	 * @param   string  $pattern
	 * @param   int     $flags
	 * @return  array
	 */
	public function glob($pattern, $flags = 0)
	{
		return glob($pattern, $flags);
	}

	/**
	 * Get an array of all files in a directory.
	 *
	 * @param   string  $directory
	 * @return  array
	 */
	public function files($directory)
	{
		$glob = glob($directory . DS . '*');

		if ($glob === false) return array();

		// To get the appropriate files, we'll simply glob the directory and filter
		// out any "files" that are not truly files so we do not end up with any
		// directories in our list, but only true files within the directory.
		return array_filter($glob, function($file)
		{
			return filetype($file) == 'file';
		});
	}

	/**
	 * Get all of the directories within a given directory.
	 *
	 * @param   string  $directory
	 * @return  array
	 */
	public function directories($directory)
	{
		$directories = array();

		if (is_dir($directory))
		{
			// Loop through all files and collect all the folders
			$dirIterator = new DirectoryIterator($directory);
			foreach ($dirIterator as $file)
			{
				if ($file->isDir())
				{
					$directories[] = $file->getPathname();
				}
			}
		}

		return $directories;
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
		if ($force)
		{
			return @mkdir($path, $mode, $recursive);
		}

		return mkdir($path, $mode, $recursive);
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
		if (!$this->isDirectory($directory)) return false;

		$options = $options ?: FilesystemIterator::SKIP_DOTS;

		// If the destination directory does not actually exist, we will go ahead and
		// create it recursively, which just gets the destination prepared to copy
		// the files over. Once we make the directory we'll proceed the copying.
		if (!$this->isDirectory($destination))
		{
			$this->makeDirectory($destination, 0777, true);
		}

		$items = new FilesystemIterator($directory, $options);

		foreach ($items as $item)
		{
			// As we spin through items, we will check to see if the current file is actually
			// a directory or a file. When it is actually a directory we will need to call
			// back into this function recursively to keep copying these nested folders.
			$target = $destination . DS . $item->getBasename();

			if ($item->isDir())
			{
				$path = $item->getPathname();

				if (!$this->copyDirectory($path, $target, $options)) return false;
			}

			// If the current items is just a regular file, we will just copy this to the new
			// location and keep looping. If for some reason the copy fails we'll bail out
			// and return false, so the developer is aware that the copy process failed.
			else
			{
				if (!$this->copy($item->getPathname(), $target)) return false;
			}
		}

		return true;
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
		if (!$this->isDirectory($directory)) return false;

		$items = new FilesystemIterator($directory);

		foreach ($items as $item)
		{
			// If the item is a directory, we can just recurse into the function and
			// delete that sub-director, otherwise we'll just delete the file and
			// keep iterating through each file until the directory is cleaned.
			if ($item->isDir())
			{
				$this->deleteDirectory($item->getPathname());
			}

			// If the item is just a file, we can go ahead and delete it since we're
			// just looping through and waxing all of the files in this directory
			// and calling directories recursively, so we delete the real path.
			else
			{
				$this->delete($item->getPathname());
			}
		}

		if (!$preserve) @rmdir($directory);

		return true;
	}

	/**
	 * Empty the specified directory of all files and folders.
	 *
	 * @param   string  $directory
	 * @return  bool
	 */
	public function emptyDirectory($directory)
	{
		return $this->deleteDirectory($directory, true);
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
		// Initialise return value
		$success = true;

		if (is_dir($path))
		{
			$dh = opendir($path);

			$items = new FilesystemIterator($path);

			foreach ($items as $item)
			{
				if ($item->isDot())
				{
					continue;
				}

				if ($item->isDir())
				{
					if ($this->setPermissions($item->getPathname(), $filemode, $foldermode))
					{
						$success = false;
					}

					continue;
				}

				if (isset($filemode))
				{
					if (!@chmod($item->getPathname(), octdec($filemode)))
					{
						$success = false;
					}
				}
			}

			if (isset($foldermode))
			{
				if (!@chmod($path, octdec($foldermode)))
				{
					$success = false;
				}
			}
		}
		else
		{
			if (isset($filemode))
			{
				$success = @chmod($path, octdec($filemode));
			}
		}

		return $success;
	}

	/**
	 * Makes path or file name safe to use
	 *
	 * @param   string  $file  The name of the file [not full path]
	 * @return  string  The sanitised string
	 */
	public function clean($file, $ds = DIRECTORY_SEPARATOR)
	{
		if ($this->isDirectory($file))
		{
			$path = trim($file);
			$path = preg_replace('#[^A-Za-z0-9:_\\\/-]#', '', $path);

			// Remove double slashes and backslashes and convert all slashes
			// and backslashes to DIRECTORY_SEPARATOR. If dealing with a UNC
			// path don't forget to prepend the path with a backslash.
			if ($ds == '\\' && $path[0] == '\\' && $path[1] == '\\')
			{
				$path = "\\" . preg_replace('#[/\\\\]+#', $ds, $path);
			}
			else
			{
				$path = preg_replace('#[/\\\\]+#', $ds, $path);
			}

			return $path;
		}

		// Remove any trailing dots, as those aren't ever valid file names.
		$file = rtrim($file, '.');

		$regex = array(
			'#(\.){2,}#',
			'#[^A-Za-z0-9\.\_\- ]#',
			'#^\.#'
		);

		return preg_replace($regex, '', $file);
	}
}
