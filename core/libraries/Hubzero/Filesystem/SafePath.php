<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

/**
 * Resolves paths inside a directory whose contents someone else controls
 *
 * The web server writes into member home directories through the webdav mount,
 * but the member owns everything under their home. A symlink anywhere along a
 * path is therefore enough to redirect a read, a write or a delete into another
 * account, and the ordinary filesystem calls all follow links: file_exists(),
 * mkdir(), file_put_contents() and chmod() every one of them.
 *
 * These helpers never follow a link. A path is built one component at a time
 * from the canonical base, every component has to be a real directory, and the
 * result has to still live under the base. Traversal is refused outright rather
 * than normalized away, so a caller cannot smuggle '..' in through a request
 * parameter.
 *
 * Note that links are only distrusted *below* the base: the base itself is
 * configured by an administrator, so it is canonicalized with realpath() and
 * may perfectly well be a link or a mount point.
 */
class SafePath
{
	/**
	 * The base directory is missing or is not a directory
	 *
	 * @var  string
	 */
	const REASON_BASE = 'base';

	/**
	 * A path component is a link, is missing, or leads out of the base
	 *
	 * @var  string
	 */
	const REASON_UNSAFE = 'unsafe';

	/**
	 * A path component does not exist and was not to be created
	 *
	 * @var  string
	 */
	const REASON_MISSING = 'missing';

	/**
	 * A missing directory could not be created
	 *
	 * @var  string
	 */
	const REASON_CREATE = 'create';

	/**
	 * Resolve a directory below a base directory, refusing anything unsafe
	 *
	 * @param   string   $base      Trusted base directory
	 * @param   string   $relative  Path below the base, may be empty
	 * @param   boolean  $create    Create missing directories?
	 * @param   integer  $mode      Mode for directories that get created
	 * @param   string   &$reason   One of the REASON_* constants on failure
	 * @return  string|boolean      Canonical path, or false
	 */
	public static function directory($base, $relative = '', $create = false, $mode = 0700, &$reason = null)
	{
		$reason = self::REASON_BASE;

		if (!is_string($base) || $base === '')
		{
			return false;
		}

		// The base is ours, not the member's, so a link or a mount point here is
		// fine - it just has to resolve to a directory
		$path = realpath($base);

		if ($path === false || !is_dir($path))
		{
			return false;
		}

		$reason = self::REASON_UNSAFE;

		$components = self::components($relative);

		if ($components === false)
		{
			return false;
		}

		foreach ($components as $component)
		{
			$path .= DS . $component;

			// is_link() is tested before file_exists() because a dangling symlink
			// is invisible to the latter
			if (!is_link($path) && !file_exists($path))
			{
				if (!$create)
				{
					$reason = self::REASON_MISSING;
					return false;
				}

				if (!@mkdir($path, $mode))
				{
					$reason = self::REASON_CREATE;
					return false;
				}
			}

			// Never follow a link, and never accept anything but a directory
			if (is_link($path) || !is_dir($path))
			{
				$reason = self::REASON_UNSAFE;
				return false;
			}
		}

		// Every component was a real directory, so the path we assembled has to
		// be canonical already. Anything else means something moved underneath us
		if (realpath($path) !== $path)
		{
			return false;
		}

		$reason = null;

		return $path;
	}

	/**
	 * Resolve a file inside an already resolved directory
	 *
	 * @param   string  $directory  Directory returned by directory()
	 * @param   string  $name       File name, a single path component
	 * @param   string  &$reason    One of the REASON_* constants on failure
	 * @return  string|boolean      Path of the file, or false
	 */
	public static function file($directory, $name, &$reason = null)
	{
		$reason = self::REASON_UNSAFE;

		if (!is_string($directory) || $directory === '' || !self::isComponent($name))
		{
			return false;
		}

		$path = $directory . DS . $name;

		// A link, a directory, a device, ... is not ours to touch
		if (is_link($path) || (file_exists($path) && !is_file($path)))
		{
			return false;
		}

		// A hard link can reach a file living outside of this directory
		if (is_file($path))
		{
			$stat = @stat($path);

			if (!$stat || $stat['nlink'] > 1)
			{
				return false;
			}
		}

		$reason = null;

		return $path;
	}

	/**
	 * Write a file without ever following a link
	 *
	 * The content goes to a temporary file created with O_EXCL, which fails if
	 * the path exists at all - including as a link - and is then moved into
	 * place, since rename() replaces the destination rather than following it.
	 * The write therefore stays inside the directory even if the path was turned
	 * into a link after it was resolved.
	 *
	 * The directory itself could still be swapped between the check and the
	 * write. PHP exposes no openat()/O_NOFOLLOW, so closing that last window
	 * would mean handing the write to a privileged helper.
	 *
	 * @param   string   $path     Path returned by file()
	 * @param   string   $content  File content
	 * @param   integer  $mode     Mode for the resulting file, null to leave it to the umask
	 * @return  boolean
	 */
	public static function write($path, $content, $mode = 0600)
	{
		$content = (string) $content;

		$tmp    = dirname($path) . DS . '.' . basename($path) . '.' . bin2hex(random_bytes(8));
		$handle = @fopen($tmp, 'xb');

		if ($handle === false)
		{
			return false;
		}

		$written = fwrite($handle, $content);
		fclose($handle);

		if ($written === false || $written !== strlen($content))
		{
			@unlink($tmp);
			return false;
		}

		// Set the mode before the file appears under its final name
		if ($mode !== null)
		{
			@chmod($tmp, $mode);
		}

		if (!@rename($tmp, $path))
		{
			@unlink($tmp);
			return false;
		}

		return true;
	}

	/**
	 * Normalize a relative path, refusing traversal
	 *
	 * Useful when a path has to be handed on before the directory it points at
	 * exists - the components are known to be safe names, but nothing about them
	 * is resolved on disk.
	 *
	 * @param   string  $relative
	 * @return  string|boolean  Normalized relative path, or false if it isn't safe
	 */
	public static function relative($relative)
	{
		$components = self::components($relative);

		if ($components === false)
		{
			return false;
		}

		return implode(DS, $components);
	}

	/**
	 * Split a relative path into components, refusing traversal
	 *
	 * @param   string  $relative
	 * @return  array|boolean  Components, or false if the path isn't safe
	 */
	protected static function components($relative)
	{
		if (!is_string($relative))
		{
			return false;
		}

		$components = array();

		// A backslash separates paths on Windows but is a perfectly ordinary
		// character in a POSIX file name, so only treat it as a separator where it
		// actually is one
		$separators = (DS == '\\' ? '#[\\\\/]+#' : '#/+#');

		foreach (preg_split($separators, $relative) as $component)
		{
			// Empty components come from leading, trailing or doubled separators
			if ($component === '')
			{
				continue;
			}

			if (!self::isComponent($component))
			{
				return false;
			}

			$components[] = $component;
		}

		return $components;
	}

	/**
	 * Is this a single, non-traversing path component?
	 *
	 * @param   string  $component
	 * @return  boolean
	 */
	protected static function isComponent($component)
	{
		if (!is_string($component) || $component === '' || $component === '.' || $component === '..')
		{
			return false;
		}

		// Separators, null bytes and control characters have no business in a name.
		// A backslash is only a separator on Windows (see components()).
		$pattern = (DS == '\\' ? '#[\\\\/\x00-\x1f]#' : '#[/\x00-\x1f]#');

		return !preg_match($pattern, $component);
	}
}
