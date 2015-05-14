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
use Hubzero\Filesystem\Util\MimeType;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;
use DirectoryIterator;
use SplFileInfo;
use Finfo;

/**
 * Hubzero class for manipulating and reading the filesystem.
 */
class Local implements AdapterInterface
{
	/**
	 * File scanning command.
	 *
	 * @var  string
	 */
	protected $command = null;

	/**
	 * Constructor.
	 *
	 * @param   string  $command
	 * @return  void
	 */
	public function __construct($command = null)
	{
		$this->command = $command;
	}

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
	public function read($path)
	{
		if ($this->isFile($path)) return file_get_contents($path);

		throw new FileNotFoundException(\Lang::txt('File does not exist at path %s', $path));
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
		return file_put_contents($path, $contents);
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
		if ($this->exists($path))
		{
			return $this->write($path, $contents . $this->read($path));
		}

		return $this->write($path, $contents);
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
		return file_put_contents($path, $contents, FILE_APPEND);
	}

	/**
	 * Delete the file at a given path.
	 *
	 * @param   mixed  $paths  string|array
	 * @return  bool
	 */
	public function delete($path)
	{
		$paths = is_array($path) ? $path : array($path);

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

		if (!is_dir($dir))
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
		return $this->rename($path, $target);
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
		foreach (glob($path . DIRECTORY_SEPARATOR . "*") as $fn)
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
	 * Get the file mime type.
	 *
	 * @param   string  $path
	 * @return  int
	 */
	public function mimetype($path)
	{
		$mimeType = null;

		if (class_exists('Finfo') && $this->exists($path))
		{
			$finfo = new Finfo(FILEINFO_MIME_TYPE);
			$mimeType = $finfo->file($path);
		}

		if (empty($mimeType) || $mimeType === 'text/plain')
		{
			$extension = $this->extension($path);

			if ($extension)
			{
				$mimeType = MimeType::detectByFileExtension($extension) ?: 'text/plain';
			}
		}

		return $mimeType;
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
		if ($this->command)
		{
			$command = trim($this->command);
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
	 * Get an array of all files in a directory.
	 *
	 * @param   string  $directory
	 * @return  array
	 */
	public function files($path, $filter = '.', $recursive = false, $full = false, $exclude = array('.svn', '.git', 'CVS', '.DS_Store', '__MACOSX'))
	{
		$items = array();

		if (is_dir($path))
		{
			foreach ($this->listContents($path, $filter, $recursive, $full, $exclude) as $file)
			{
				if ($file['type'] == 'file')
				{
					$items[] = $file['path'];
				}
			}
		}

		return $items;
	}

	/**
	 * Get all of the directories within a given directory.
	 *
	 * @param   string  $path
	 * @return  array
	 */
	public function directories($path, $filter = '.', $recursive = false, $full = false, $exclude = array('.svn', '.git', 'CVS', '.DS_Store', '__MACOSX'))
	{
		/*$items = $this->listContents($path, $filter, $recursive, $full, $exclude);

		return array_filter($items, function($file)
		{
			return $file['type'] == 'path';
		});*/
		$items = array();

		if (is_dir($path))
		{
			foreach ($this->listContents($path, $filter, $recursive, $full, $exclude) as $file)
			{
				if ($file['type'] == 'path')
				{
					$items[] = $file['path'];
				}
			}
		}

		return $items;
	}

	/**
	 * {@inheritdoc}
	 */
	public function listContents($path, $filter = '.', $recursive = false, $full = false, $exclude = array('.svn', '.git', 'CVS', '.DS_Store', '__MACOSX'))
	{
		$result = array();

		if (!is_dir($path))
		{
			return $result;
		}

		$iterator = $recursive ? $this->getRecursiveDirectoryIterator($path) : $this->getDirectoryIterator($path);

		foreach ($iterator as $file)
		{
			if ($file->isLink())
			{
				continue;
			}

			if (preg_match('#(^|/|\\\\)\.{1,2}$#', $file->getPathname()))
			{
				continue;
			}

			$name = $file->getFilename();

			if (preg_match("/$filter/", $name) && !in_array($name, $exclude))
			{
				$result[] = $this->normalizeFileInfo($file, ($full ? null : $path));
			}
		}

		return $result;
	}

	/**
	 * Normalize the file info.
	 *
	 * @param   object  $file  SplFileInfo
	 * @return  array
	 */
	protected function normalizeFileInfo(SplFileInfo $file, $base = null)
	{
		$normalized = array(
			'type'      => $file->getType(),
			'path'      => ($base ? substr($file->getPathname(), strlen($base)) : $file->getPathname()),
			'timestamp' => $file->getMTime()
		);

		if ($normalized['type'] === 'file')
		{
			$normalized['size'] = $file->getSize();
		}

		return $normalized;
	}

	/**
	 * @param   string  $path
	 * @return  object  RecursiveIteratorIterator
	 */
	protected function getRecursiveDirectoryIterator($path)
	{
		$directory = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
		$iterator  = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);

		return $iterator;
	}

	/**
	 * @param   string  $path
	 * @return  object  DirectoryIterator
	 */
	protected function getDirectoryIterator($path)
	{
		return new DirectoryIterator($path);
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
}
