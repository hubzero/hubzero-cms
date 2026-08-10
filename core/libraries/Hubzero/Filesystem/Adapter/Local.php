<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		if ($this->isFile($path))
		{
			return file_get_contents($path);
		}

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
			// A link is removed as a link. unlink() doesn't follow it, but the chmod()
			// below would - loosening the permissions of whatever it points at, which
			// may be a file in another account entirely.
			if (is_link($path))
			{
				if (!@unlink($path))
				{
					$success = false;
				}

				continue;
			}

			if (!is_file($path))
			{
				continue;
			}

			// Try making the file writable first. If it's read-only, it can't be deleted
			// on Windows, even if the parent folder is writable
			@chmod($path, 0777);

			if (!@unlink($path))
			{
				$success = false;
			}
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
				// Gracefully handle non-standard filetypes
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
	 *
	 * Returns true only when the configured virus scanner exits 0
	 * (clean). Any non-zero exit — virus found (1), scanner error
	 * (2), command-not-found (127), signal kill — is treated as
	 * unsafe so a broken or unreachable scanner can't silently let
	 * uploads through.
	 *
	 * Previously this method only failed on exit code 1 (virus
	 * found per clamscan's convention), which meant an unreachable
	 * clamd (clamdscan exit 2) silently passed every upload.
	 */
	public function isSafe($file)
	{
		if (!$this->command)
		{
			return true;
		}

		$command = trim($this->command);
		if (strstr($command, '%s'))
		{
			$command = sprintf($command, escapeshellarg($file));
		}
		else
		{
			$command .= ' ' . escapeshellarg($file);
		}

		$output = [];
		$status = 0;
		exec($command . ' 2>&1', $output, $status);

		if ($status === 0)
		{
			return true;
		}

		// Anything other than 0 is "we can't confirm this is safe":
		// virus found, scanner error, daemon down, command missing,
		// killed by signal, etc. Log so admins can see the scanner
		// is broken rather than discover it from missed-virus reports.
		if ($status !== 1)
		{
			error_log(sprintf(
				'Hubzero\\Filesystem\\Adapter\\Local::isSafe: virus '
				. 'scanner "%s" exited with status %d (rejecting file). '
				. 'Output: %s',
				$this->command,
				$status,
				implode(' | ', $output)
			));
		}

		return false;
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
			if (!is_dir($path))
			{
				return mkdir($path, $mode, $recursive);
			}
		}

		return mkdir($path, $mode, $recursive);
	}

	/**
	 * {@inheritdoc}
	 */
	public function copyDirectory($directory, $destination, $options = null)
	{
		if (!$this->isDirectory($directory))
		{
			return false;
		}

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

				if (!$this->copyDirectory($path, $target, $options))
				{
					return false;
				}
			}

			// If the current items is just a regular file, we will just copy this to the new
			// location and keep looping. If for some reason the copy fails we'll bail out
			// and return false, so the developer is aware that the copy process failed.
			else
			{
				if (!$this->copy($item->getPathname(), $target))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleteDirectory($directory, $preserve = false)
	{
		// isDirectory() follows links, so a link to a directory would have us
		// empty out the target. Remove the link itself instead - that is what the
		// caller pointed at - and leave whatever it referenced alone.
		if (is_link($directory))
		{
			return @unlink($directory);
		}

		if (!$this->isDirectory($directory))
		{
			return false;
		}

		$items = new FilesystemIterator($directory);

		foreach ($items as $item)
		{
			// A symlink is removed, never followed. isDir() below reports true for a
			// link to a directory, so without this the recursion would delete the
			// contents of the target - which may well live in another account.
			if ($item->isLink())
			{
				@unlink($item->getPathname());
				continue;
			}

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

		if (!$preserve)
		{
			@rmdir($directory);
		}

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
