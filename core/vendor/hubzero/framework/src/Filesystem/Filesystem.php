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

namespace Hubzero\Filesystem;

use Hubzero\Filesystem\Exception\FileNotFoundException;
use Hubzero\Filesystem\Exception\FileExistsException;
use FilesystemIterator;
use DirectoryIterator;

/**
 * Hubzero class for manipulating and reading the filesystem.
 */
class Filesystem
{
	/**
	 * AdapterInterface
	 *
	 * @var  object
	 */
	protected $adapter;

	/**
	 * Macros list
	 *
	 * @var  array
	 */
	protected $macros = array();

	/**
	 * Constructor.
	 *
	 * @param   object  $adapter  AdapterInterface
	 * @return  void
	 */
	public function __construct(AdapterInterface $adapter)
	{
		$this->adapter = $adapter;
	}

	/**
	 * Get the Adapter.
	 *
	 * @return  object  AdapterInterface
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * Set the Adapter.
	 *
	 * @param   object  $adapter  AdapterInterface
	 * @return  object
	 */
	public function setAdapter(AdapterInterface $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	/**
	 * Determine if a file exists.
	 *
	 * @param   string  $path
	 * @return  bool
	 */
	public function exists($path)
	{
		$path = Util::normalizePath($path);

		return (bool) $this->adapter->exists($path);
	}

	/**
	 * Get the contents of a file.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function read($path)
	{
		$path = Util::normalizePath($path);

		$this->assertPresent($path);

		return (string) $this->adapter->read($path);
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
		$path = Util::normalizePath($path);

		//$this->assertAbsent($path);

		return (bool) $this->adapter->write($path, $contents);
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
			return $this->write($path, $data . $this->read($path));
		}

		return $this->write($path, $data);
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
		if ($this->exists($path))
		{
			return $this->write($path, $this->read($path) . $data);
		}

		return $this->write($path, $data);
	}

	/**
	 * Delete the file at a given path.
	 *
	 * @param   mixed  $path  string
	 * @return  bool
	 */
	public function delete($path)
	{
		$path = Util::normalizePath($path);

		$this->assertPresent($path);

		return $this->adapter->delete($path);
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
		$path   = Util::normalizePath($path);
		$target = Util::normalizePath($target);

		return $this->adapter->upload($path, $target);
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
		$path   = Util::normalizePath($path);
		$target = Util::normalizePath($target);

		$this->assertPresent($path);
		//$this->assertAbsent($target);

		return (bool) $this->adapter->rename($path, $target);
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
		$path   = Util::normalizePath($path);
		$target = Util::normalizePath($target);

		$this->assertPresent($path);
		//$this->assertAbsent($target);

		return (bool) $this->adapter->copy($path, $target);
	}

	/**
	 * Searches the directory paths for a given file.
	 *
	 * @param   mixed   $paths  An path string or array of path strings to search in
	 * @param   string  $file   The file name to look for.
	 * @return  mixed   Full path and name for the target file, or false if file not found.
	 */
	public function find($paths, $file)
	{
		if (!$file)
		{
			return false;
		}

		return $this->adapter->find((array) $paths, $file);
	}

	/**
	 * Extract the file name from a file path.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function name($path)
	{
		$path = Util::normalizePath($path);

		return $this->adapter->name($path);
	}

	/**
	 * Extract the file extension from a file path.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function extension($path)
	{
		$path = Util::normalizePath($path);

		return $this->adapter->extension($path);
	}

	/**
	 * Get the file type of a given file.
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function type($path)
	{
		$path = Util::normalizePath($path);

		return $this->adapter->type($path);
	}

	/**
	 * Get the file size of a given file.
	 *
	 * @param   string  $path
	 * @return  int
	 */
	public function size($path)
	{
		$path = Util::normalizePath($path);

		return $this->adapter->size($path);
	}

	/**
	 * Get a file's mime-type.
	 *
	 * @param   string  $path  path to file
	 * @return  string
	 * @throws  FileNotFoundException
	 */
	public function mimetype($path)
	{
		$path = Util::normalizePath($path);

		return $this->adapter->mimetype($path);
	}

	/**
	 * Get the file's last modification time.
	 *
	 * @param   string  $path
	 * @return  int
	 */
	public function lastModified($path)
	{
		$path = Util::normalizePath($path);

		return (int) $this->adapter->lastModified($path);
	}

	/**
	 * Determine if the given path is a directory.
	 *
	 * @param   string  $directory
	 * @return  bool
	 */
	public function isDirectory($directory)
	{
		$directory = Util::normalizePath($directory);

		return (bool) $this->adapter->isDirectory($directory);
	}

	/**
	 * Determine if the given path is writable.
	 *
	 * @param   string  $path
	 * @return  bool
	 */
	public function isWritable($path)
	{
		$path = Util::normalizePath($path);

		return (bool) $this->adapter->isWritable($path);
	}

	/**
	 * Determine if the given path is a file.
	 *
	 * @param   string  $file
	 * @return  bool
	 */
	public function isFile($file)
	{
		$file = Util::normalizePath($file);

		return (bool) $this->adapter->isFile($file);
	}

	/**
	 * Run a virus scan against a file
	 *
	 * @param   string   $file  The name of the file [not full path]
	 * @return  boolean
	 */
	public function isSafe($file)
	{
		$file = Util::normalizePath($file);

		return (bool) $this->adapter->isSafe($file);
	}

	/**
	 * Get all contents within a given directory.
	 *
	 * @param   string   $path     The path of the folder to read.
	 * @param   string   $filter   A filter for file names.
	 * @param   mixed    $recurse  True to recursively search into sub-folders, or an integer to specify the maximum depth.
	 * @param   boolean  $full     True to return the full path to the file.
	 * @param   array    $exclude  Array with names of files which should not be shown in the result.
	 * @return  array
	 */
	public function listContents($path, $filter = '.', $recursive = false, $full = false, $exclude = array('.svn', '.git', 'CVS', '.DS_Store', '__MACOSX'))
	{
		$path = Util::normalizePath($path);

		return (array) $this->adapter->listContents($path, $filter, $recursive, $full, $exclude);
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
	public function makeDirectory($path, $mode = 0755, $recursive = true, $force = false)
	{
		$path = Util::normalizePath($path);

		return (bool) $this->adapter->makeDirectory($path, $mode, $recursive, $force);
	}

	/**
	 * Copy a directory from one location to another.
	 *
	 * @param   string  $path
	 * @param   string  $target
	 * @param   int     $options
	 * @return  bool
	 */
	public function copyDirectory($path, $target, $options = null)
	{
		$path   = Util::normalizePath($path);
		$target = Util::normalizePath($target);

		$this->assertPresent($path);
		//$this->assertAbsent($target);

		return (bool) $this->adapter->copyDirectory($path, $target, $options);
	}

	/**
	 * Recursively delete a directory.
	 *
	 * The directory itself may be optionally preserved.
	 *
	 * @param   string  $path
	 * @param   bool    $preserve
	 * @return  bool
	 */
	public function deleteDirectory($path, $preserve = false)
	{
		$path = Util::normalizePath($path);

		$this->assertPresent($path);

		return (bool) $this->adapter->deleteDirectory($path, $preserve);
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
		$path = Util::normalizePath($path);

		return (bool) $this->adapter->setPermissions($path, $filemode, $foldermode);
	}

	/**
	 * Makes file name safe to use
	 *
	 * @param   string  $file  The name of the file [not full path]
	 * @return  string  The sanitised string
	 */
	public function clean($file)
	{
		return Util::normalizeFile($file);
	}

	/**
	 * Makes path safe to use
	 *
	 * @param   string  $path  The full path to sanitise.
	 * @return  string  The sanitised string
	 */
	public function cleanPath($path)
	{
		return Util::normalizePath($path);
	}

	/**
	 * Makes directory name safe to use.
	 *
	 * @param   string  $directory  The directory to sanitise.
	 * @return  string  The sanitised string.
	 */
	public function cleanDirectory($directory)
	{
		return Util::normalizeDirectory($directory);
	}

	/**
	 * Assert a file is present.
	 *
	 * @param   string  $path  Path to file
	 * @throws  FileNotFoundException
	 */
	public function assertPresent($path)
	{
		if (!$this->exists($path))
		{
			throw new FileNotFoundException($path);
		}
	}

	/**
	 * Assert a file is absent.
	 *
	 * @param   string  $path  Path to file
	 * @throws  FileExistsException
	 */
	public function assertAbsent($path)
	{
		if ($this->exists($path))
		{
			throw new FileExistsException($path);
		}
	}

	/**
	 * Register a macro.
	 *
	 * @param   object  $plugin  MacroInterface
	 * @return  $this
	 */
	public function addMacro(MacroInterface $macro)
	{
		if (!method_exists($macro, 'handle'))
		{
			throw new \LogicException(sprintf('%s does not have a handle method.', get_class($macro)));
		}

		$this->macros[$macro->getMethod()] = $macro;

		return $this;
	}

	/**
	 * Checks if macro is registered.
	 *
	 * @param   string  $name
	 * @return  bool
	 */
	public function hasMacro($method)
	{
		return isset($this->macros[$method]);
	}

	/**
	 * Call a macro.
	 *
	 * @param   string  $method
	 * @param   array   $arguments
	 * @return  mixed
	 * @throws  BadMethodCallException
	 */
	public function __call($method, array $arguments)
	{
		if ($this->hasMacro($method))
		{
			$macro = $this->macros[$method];
			$macro->setFilesystem($this);

			return call_user_func_array(array($macro, 'handle'), $arguments);
		}

		throw new \BadMethodCallException('Call to undefined method ' . __CLASS__ . '::' . $method);
	}
}
