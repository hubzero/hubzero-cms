<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Api\Controllers;

use Hubzero\Component\ApiController;
use Exception;
use Component;
use Request;
use Lang;
use User;

$componentPath = Component::path('com_tools');

include_once "$componentPath/helpers/utils.php";

/**
 * API controller for a member's tool-storage quota.
 */
class Storagev1_0 extends ApiController
{
	/**
	 * Current disk-usage / quota for the authenticated member (read-only).
	 *
	 * Reads the storage host's `getquota` response (via the same helper the
	 * site page uses) and returns normalized byte figures plus a clamped
	 * percentage, so a client can render and live-refresh a usage gauge.
	 *
	 * @apiMethod GET
	 * @apiUri    /tools/storage/usage
	 * @return    void
	 */
	public function usageTask()
	{
		$this->requiresAuthentication();

		$profile = User::getInstance(\App::get('authn')['user_id']);
		if (!$profile->get('id'))
		{
			throw new Exception(Lang::txt('Unable to find user.'), 404);
		}
		$username = $profile->get('username');

		$du = \Components\Tools\Helpers\Utils::getDiskUsage($username);

		// A healthy response carries the quota keys; anything less is an error
		// talking to the storage host.
		if (!is_array($du) || !isset($du['space']))
		{
			throw new Exception(Lang::txt('Unable to retrieve storage usage.'), 503);
		}

		$used = (float) $du['space'];
		$soft = isset($du['softspace']) ? (float) $du['softspace'] : 0;
		$hard = isset($du['hardspace']) ? (float) $du['hardspace'] : 0;

		$percent = ($soft > 0) ? round(($used / $soft) * 100) : 0;
		$percent = max(0, min(100, $percent));

		$free = ($soft > $used) ? ($soft - $used) : 0;

		$this->send(array(
			'used'      => $used,
			'soft'      => $soft,
			'hard'      => $hard,
			'free'      => $free,
			'percent'   => $percent,
			'used_gb'   => round($used / 1024000000, 2),
			'soft_gb'   => round($soft / 1024000000, 2),
			'free_gb'   => round($free / 1024000000, 2)
		));
	}

	/**
	 * List the folders and files in one directory of the member's storage.
	 *
	 * @apiMethod GET
	 * @apiUri    /tools/storage/files
	 * @apiParameter {
	 * 		"name":        "dir",
	 * 		"description": "Directory relative to the member's storage root",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @return    void
	 */
	public function filesTask()
	{
		$this->requiresAuthentication();

		$username = $this->member();
		$dir      = trim((string) Request::getString('dir', ''));

		$path = $this->resolveDir($dir, $username);
		if ($path === false)
		{
			throw new Exception(Lang::txt('Invalid path.'), 400);
		}

		$folders = array();
		$files   = array();

		if (is_dir($path))
		{
			$it = new \DirectoryIterator($path);
			foreach ($it as $f)
			{
				if ($f->isDot())
				{
					continue;
				}
				$name = $f->getFilename();

				if ($f->isDir())
				{
					$rel = ($dir !== '') ? trim($dir, '/') . '/' . $name : $name;
					$folders[$name] = array(
						'name'      => $name,
						'dir'       => $rel,
						// Mirrors the site view: the storage roots are protected
						'deletable' => !in_array(strtolower($name), array('data', 'sessions'))
					);
				}
				else if ($f->isFile())
				{
					$ln = strtolower($name);
					if ($ln === 'cvs' || $ln === '.svn')
					{
						continue;
					}
					$size = @filesize($f->getPathname());
					$files[$name] = array(
						'name'   => $name,
						'size'   => (int) $size,
						'size_h' => \Hubzero\Utility\Number::formatBytes((float) $size)
					);
				}
			}
			ksort($folders);
			ksort($files);
		}

		// Breadcrumb of the current directory
		$crumbs = array();
		$acc    = '';
		if ($dir !== '')
		{
			foreach (explode('/', trim($dir, '/')) as $seg)
			{
				if ($seg === '')
				{
					continue;
				}
				$acc = ($acc === '') ? $seg : $acc . '/' . $seg;
				$crumbs[] = array('name' => $seg, 'dir' => $acc);
			}
		}

		$this->send(array(
			'dir'        => trim($dir, '/'),
			'breadcrumb' => $crumbs,
			'folders'    => array_values($folders),
			'files'      => array_values($files)
		));
	}

	/**
	 * Delete one file or folder from the member's storage.
	 *
	 * @apiMethod DELETE
	 * @apiUri    /tools/storage/delete
	 * @apiParameter {
	 * 		"name":        "dir",
	 * 		"description": "Directory the item lives in, relative to the storage root",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @apiParameter {
	 * 		"name":        "name",
	 * 		"description": "File or folder name to delete",
	 * 		"type":        "string",
	 * 		"required":    true
	 * }
	 * @apiParameter {
	 * 		"name":        "type",
	 * 		"description": "file or folder",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "file"
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		$method = strtoupper(\App::get('request')->method());
		if ($method !== 'DELETE' && $method !== 'POST')
		{
			throw new Exception(Lang::txt('Method not allowed.'), 405);
		}

		$username = $this->member();

		$dir  = trim((string) Request::getString('dir', ''));
		$name = (string) Request::getString('name', '');
		$type = strtolower(Request::getString('type', 'file'));

		if ($name === '')
		{
			throw new Exception(Lang::txt('Nothing to delete.'), 400);
		}

		$path = $this->resolveDir($dir, $username);
		if ($path === false)
		{
			throw new Exception(Lang::txt('Invalid path.'), 400);
		}

		// Resolve the target below the directory without following it if it is a
		// link, so a delete can never escape the member's own storage.
		$target = $this->resolveTarget($path, $name);
		if ($target === false)
		{
			throw new Exception(Lang::txt('Invalid path.'), 400);
		}

		if ($type === 'folder')
		{
			if (in_array(strtolower(basename($name)), array('data', 'sessions')))
			{
				throw new Exception(Lang::txt('This folder cannot be deleted.'), 403);
			}
			if (!is_dir($target) && !is_link($target))
			{
				throw new Exception(Lang::txt('Folder not found.'), 404);
			}
			if (!\Filesystem::deleteDirectory($target))
			{
				throw new Exception(Lang::txt('Unable to delete the folder.'), 500);
			}
		}
		else
		{
			if (!file_exists($target) && !is_link($target))
			{
				throw new Exception(Lang::txt('File not found.'), 404);
			}
			if (!\Filesystem::delete($target))
			{
				throw new Exception(Lang::txt('Unable to delete the file.'), 500);
			}
		}

		$this->send(array('ok' => true, 'deleted' => $name, 'type' => $type));
	}

	/**
	 * Create a new folder in the member's storage.
	 *
	 * @apiMethod POST
	 * @apiUri    /tools/storage/mkdir
	 * @return    void
	 */
	public function mkdirTask()
	{
		$this->requiresAuthentication();
		$this->requirePost();

		$username = $this->member();
		$dir      = trim((string) Request::getString('dir', ''));
		$name     = $this->safeName(Request::getString('name', ''));

		if ($name === false)
		{
			throw new Exception(Lang::txt('A valid folder name is required.'), 400);
		}

		$path = $this->resolveDir($dir, $username);
		if ($path === false)
		{
			throw new Exception(Lang::txt('Invalid path.'), 400);
		}

		$target = $this->resolveTarget($path, $name);
		if ($target === false)
		{
			throw new Exception(Lang::txt('Invalid path.'), 400);
		}
		if (file_exists($target) || is_link($target))
		{
			throw new Exception(Lang::txt('That name is already in use.'), 409);
		}
		if (!@mkdir($target, 0700))
		{
			throw new Exception(Lang::txt('Unable to create the folder.'), 500);
		}

		$this->send(array('ok' => true, 'name' => $name));
	}

	/**
	 * Rename a file or folder in the member's storage.
	 *
	 * @apiMethod POST
	 * @apiUri    /tools/storage/rename
	 * @return    void
	 */
	public function renameTask()
	{
		$this->requiresAuthentication();
		$this->requirePost();

		$username = $this->member();
		$dir      = trim((string) Request::getString('dir', ''));
		$name     = (string) Request::getString('name', '');
		$newname  = $this->safeName(Request::getString('newname', ''));

		if ($name === '')
		{
			throw new Exception(Lang::txt('Nothing to rename.'), 400);
		}
		if ($newname === false)
		{
			throw new Exception(Lang::txt('A valid name is required.'), 400);
		}
		// The storage roots are not renamable, same as they are not deletable.
		if (in_array(strtolower(basename($name)), array('data', 'sessions')))
		{
			throw new Exception(Lang::txt('This item cannot be renamed.'), 403);
		}

		$path = $this->resolveDir($dir, $username);
		if ($path === false)
		{
			throw new Exception(Lang::txt('Invalid path.'), 400);
		}

		$src = $this->resolveTarget($path, $name);
		$dst = $this->resolveTarget($path, $newname);
		if ($src === false || $dst === false)
		{
			throw new Exception(Lang::txt('Invalid path.'), 400);
		}
		if (!file_exists($src) && !is_link($src))
		{
			throw new Exception(Lang::txt('The item was not found.'), 404);
		}
		if (file_exists($dst) || is_link($dst))
		{
			throw new Exception(Lang::txt('That name is already in use.'), 409);
		}
		if (!@rename($src, $dst))
		{
			throw new Exception(Lang::txt('Unable to rename the item.'), 500);
		}

		$this->send(array('ok' => true, 'name' => $newname));
	}

	/**
	 * Upload one or more files into a directory of the member's storage.
	 *
	 * Files arrive as multipart form-data under `files[]`; `dir` is the
	 * destination directory relative to the storage root. Rejected as a whole
	 * if the combined size would push the member over their soft quota.
	 *
	 * @apiMethod POST
	 * @apiUri    /tools/storage/upload
	 * @return    void
	 */
	public function uploadTask()
	{
		$this->requiresAuthentication();
		$this->requirePost();

		$username = $this->member();
		$dir      = trim((string) Request::getString('dir', ''));

		$path = $this->resolveDir($dir, $username);
		if ($path === false || !is_dir($path))
		{
			throw new Exception(Lang::txt('Invalid path.'), 400);
		}

		if (empty($_FILES) || !isset($_FILES['files']) || !isset($_FILES['files']['name']))
		{
			throw new Exception(Lang::txt('No files were received.'), 400);
		}

		$f     = $_FILES['files'];
		$names = (array) $f['name'];
		$tmps  = (array) $f['tmp_name'];
		$sizes = (array) $f['size'];
		$errs  = (array) $f['error'];

		// Quota gate: reject the whole batch if it would exceed the soft space.
		$du   = \Components\Tools\Helpers\Utils::getDiskUsage($username);
		$used = (isset($du['space'])) ? (float) $du['space'] : 0;
		$soft = (isset($du['softspace'])) ? (float) $du['softspace'] : 0;
		$incoming = 0;
		foreach ($sizes as $s) { $incoming += (int) $s; }

		if ($soft > 0 && ($used + $incoming) > $soft)
		{
			throw new Exception(Lang::txt('This upload would exceed your storage quota.'), 413);
		}

		$saved  = array();
		$failed = array();

		for ($i = 0; $i < count($names); $i++)
		{
			if (!isset($errs[$i]) || $errs[$i] !== UPLOAD_ERR_OK)
			{
				$failed[] = $names[$i];
				continue;
			}
			$safe = $this->safeName(basename($names[$i]));
			if ($safe === false)
			{
				$failed[] = $names[$i];
				continue;
			}
			$target = $this->resolveTarget($path, $safe);
			if ($target === false || !is_uploaded_file($tmps[$i]) || !@move_uploaded_file($tmps[$i], $target))
			{
				$failed[] = $names[$i];
				continue;
			}
			@chmod($target, 0600);
			$saved[] = $safe;
		}

		if (empty($saved))
		{
			throw new Exception(Lang::txt('The upload failed.'), 500);
		}

		$this->send(array('ok' => true, 'saved' => $saved, 'failed' => $failed));
	}

	/**
	 * Require a POST request, or fail with 405.
	 *
	 * @return  void
	 */
	protected function requirePost()
	{
		if (strtoupper(\App::get('request')->method()) !== 'POST')
		{
			throw new Exception(Lang::txt('Method not allowed.'), 405);
		}
	}

	/**
	 * Validate a single path segment (a file/folder name) -- no separators,
	 * no traversal, no null bytes.
	 *
	 * @param   string          $name  Incoming name
	 * @return  string|boolean
	 */
	protected function safeName($name)
	{
		$name = trim((string) $name);
		if ($name === '' || $name === '.' || $name === '..')
		{
			return false;
		}
		if (strpos($name, '/') !== false || strpos($name, '\\') !== false || strpos($name, "\0") !== false)
		{
			return false;
		}
		return $name;
	}

	/**
	 * Resolve the authenticated member's username, or fail.
	 *
	 * @return  string
	 */
	protected function member()
	{
		$profile = User::getInstance(\App::get('authn')['user_id']);
		if (!$profile->get('id'))
		{
			throw new Exception(Lang::txt('Unable to find user.'), 404);
		}
		$username = (string) $profile->get('username');
		if ($username === '')
		{
			throw new Exception(Lang::txt('Unable to find user.'), 404);
		}
		return $username;
	}

	/**
	 * Resolve a listing directory below the member's storage root (no traversal,
	 * no following links out of the member's own directory). Mirrors the site
	 * controller's _buildUploadPath().
	 *
	 * @param   string  $listdir   Directory relative to the member's root
	 * @param   string  $username  Member username
	 * @return  string|boolean
	 */
	protected function resolveDir($listdir, $username)
	{
		$config = Component::params('com_tools');
		$base   = DS . trim($config->get('storagepath', 'webdav' . DS . 'home'), DS);

		$relative = \Hubzero\Filesystem\SafePath::relative($username . DS . $listdir);
		if ($relative === false)
		{
			return false;
		}

		$reason = null;
		$path   = \Hubzero\Filesystem\SafePath::directory($base, $relative, false, 0700, $reason);

		if ($path === false)
		{
			// A not-yet-existing directory is not an error; whatever existed along
			// the way was still checked for links.
			if ($reason != \Hubzero\Filesystem\SafePath::REASON_MISSING)
			{
				return false;
			}
			$path = realpath($base) . DS . $relative;
		}

		return $path;
	}

	/**
	 * Resolve a file/folder below an already-resolved directory. Mirrors the
	 * site controller's _resolveTarget().
	 *
	 * @param   string  $path  Directory from resolveDir()
	 * @param   string  $name  Incoming file or folder name
	 * @return  string|boolean
	 */
	protected function resolveTarget($path, $name)
	{
		$name = \Hubzero\Filesystem\SafePath::relative($name);
		if ($name === false || $name === '')
		{
			return false;
		}

		$parent = dirname($name);
		if ($parent != '.')
		{
			$path = \Hubzero\Filesystem\SafePath::directory($path, $parent);
			if ($path === false)
			{
				return false;
			}
		}

		return $path . DS . basename($name);
	}
}
