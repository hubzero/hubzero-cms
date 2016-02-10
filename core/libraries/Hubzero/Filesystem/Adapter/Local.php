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
	 * {@inheritdoc}
	 */
	public function exists($path)
	{
		return file_exists($path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function read($path)
	{
		if ($this->isFile($path)) return file_get_contents($path);

		throw new FileNotFoundException(\Lang::txt('File does not exist at path %s', $path));
	}

	/**
	 * {@inheritdoc}
	 */
	public function write($path, $contents)
	{
		return file_put_contents($path, $contents);
	}

	/**
	 * {@inheritdoc}
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
	 * {@inheritdoc}
	 */
	public function append($path, $contents)
	{
		return file_put_contents($path, $contents, FILE_APPEND);
	}

	/**
	 * {@inheritdoc}
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
	 * {@inheritdoc}
	 */
	public function move($path, $target)
	{
		return $this->rename($path, $target);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rename($path, $target)
	{
		return rename($path, $target);
	}

	/**
	 * {@inheritdoc}
	 */
	public function copy($path, $target)
	{
		return copy($path, $target);
	}

	/**
	 * {@inheritdoc}
	 */
	public function find($paths, $file)
	{
		$paths = is_array($paths) ? $paths : array($paths);

		foreach ($paths as $path)
		{
			$fullname = $path . DS . $file;

			// Is the path based on a stream?
			if (strpos($path, '://') === false)
			{
				// Not a stream, so do a realpath() to avoid directory
				// traversal attempts on the local file system.
				$path     = realpath($path);
				$fullname = realpath($fullname);
			}

			// The substr() check added to make sure that the realpath()
			// results in a directory registered so that
			// non-registered directories are not accessible via directory
			// traversal attempts.
			if (file_exists($fullname) && substr($fullname, 0, strlen($path)) == $path)
			{
				return $fullname;
			}
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function name($path)
	{
		return preg_replace('#\.[^.]*$#', '', $path);
		//return pathinfo($path, PATHINFO_FILENAME);
	}

	/**
	 * {@inheritdoc}
	 */
	public function extension($path)
	{
		$dot = strrpos($path, '.') + 1;

		return substr($path, $dot);
		//return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * {@inheritdoc}
	 */
	public function type($path)
	{
		return filetype($path);
	}

	/**
	 * {@inheritdoc}
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
	 * {@inheritdoc}
	 */
	public function lastModified($path)
	{
		return filemtime($path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function mimetype($path)
	{
		$mimeType = null;

		if (class_exists('Finfo') && $this->exists($path))
		{
			$finfo = new Finfo(FILEINFO_MIME_TYPE);
			try
			{
				$mimeType = $finfo->file($path);
			}
			catch (\Exception $e)
			{
				// Gracefully ignore the filetype
			}
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
	 * {@inheritdoc}
	 */
	public function isDirectory($directory)
	{
		return is_dir($directory);
	}

	/**
	 * {@inheritdoc}
	 */
	public function isWritable($path)
	{
		return is_writable($path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function isFile($file)
	{
		return is_file($file);
	}

	/**
	 * {@inheritdoc}
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
	 * {@inheritdoc}
	 */
	public function makeDirectory($path, $mode = 0755, $recursive = true, $force = false)
	{
		if ($force)
		{
			return @mkdir($path, $mode, $recursive);
		}

		return mkdir($path, $mode, $recursive);
	}

	/**
	 * {@inheritdoc}
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
	 * {@inheritdoc}
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

			$items = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);

			foreach ($items as $item)
			{
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
