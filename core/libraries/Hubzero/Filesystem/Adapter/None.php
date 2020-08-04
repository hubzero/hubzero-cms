<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Adapter;

use Hubzero\Filesystem\AdapterInterface;

/**
 * Null adapter for filesystem.
 */
class None implements AdapterInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function exists($path)
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function read($path)
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function write($path, $contents)
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function prepend($path, $contents)
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function append($path, $contents)
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function move($path, $target)
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function rename($path, $target)
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function copy($path, $target)
	{
		return false;
	}

	/**
	 * {@inheritdoc}
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
	 * {@inheritdoc}
	 */
	public function find($paths, $file)
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function name($path)
	{
		return preg_replace('#\.[^.]*$#', '', $path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function extension($path)
	{
		$dot = strrpos($path, '.') + 1;

		return substr($path, $dot);
	}

	/**
	 * {@inheritdoc}
	 */
	public function type($path)
	{
		return '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function size($path)
	{
		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function lastModified($path)
	{
		return 0;
	}

	/**
	 * {@inheritdoc}
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
	 * {@inheritdoc}
	 */
	public function isSafe($file)
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function listContents($path, $filter = '.', $recursive = false, $full = false, $exclude = array('.svn', '.git', 'CVS', '.DS_Store', '__MACOSX'))
	{
		return array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function makeDirectory($path, $mode = 0755, $recursive = true, $force = false)
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function copyDirectory($directory, $destination, $options = null)
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleteDirectory($directory, $preserve = false)
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
