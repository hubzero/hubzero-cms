<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2017 HUBzero Foundation, LLC.
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
 * @author    Zach Weidner <zweidner@purdue.edu>
 * @copyright Copyright 2005-2017 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Adapters;

use Hubzero\Base\Object;
use Components\Projects\Models;
use Components\Projects\Helpers;

require_once PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'nogithelper.php';


/**
 * Projects Git adapter class
 */
class Nogit extends Models\Adapter
{
	/**
	 * Adapter name
	 *
	 * @var  string
	 */
	private $_name = 'nogit';

	/**
	 * Full path to repository
	 *
	 * @var  string
	 */
	private $_path = null;

	/**
	 * Constructor
	 *
	 * @param   string  $path
	 * @param   bool    $remote
	 * @return  void
	 */
	public function __construct($path = null, $remote = false)
	{
		// Get component configs
		$configs = Component::params('com_projects');

		// Set repo path
		$this->_path = $path;
		$this->_nogit = new Helpers\Nogit($this->_path);

		$this->set('remote', $remote);
	}

	/**
	 * Get file count
	 *
	 * @param   array  $params
	 * @return  array
	 */
	public function count($params = array())
	{
		$cmd  = 'cd ' . escapeshellarg($this->_path) . ' && ';
		$cmd .='find . -prune -o -type f -print | wc -l';
		return shell_exec($cmd);
	}

	/**
	 * Get file list (retrieve and sort)
	 *
	 * @param   array  $params
	 * @return  array
	 */
	public function filelist($params = [])
	{
		// Parse incoming params and establish defaults
		$filter        = isset($params['filter']) ? $params['filter'] : null;
		$dirPath       = isset($params['subdir']) ? $params['subdir'] : null;
		$sortdir       = isset($params['sortdir']) && $params['sortdir'] == 'DESC' ? SORT_DESC : SORT_ASC;
		$sortby        = isset($params['sortby']) ? $params['sortby'] : 'name';
		$files         = isset($params['files']) && is_array($params['files']) ? $params['files'] : [];
		$dirsOnly      = isset($params['dirsOnly']) ? $params['dirsOnly'] : false;
		$showAll       = isset($params['showAll']) ? $params['showAll'] : false;
		$recursive     = isset($params['recursive']) ? $params['recursive'] : false;

		if (!$dirsOnly)
		{
			// Get a list of files from the git repository
			$files = empty($files) ? $this->_nogit->getFiles($dirPath, $recursive) : $files;

		}
		else
		{
			// This is recursive by default
			$files = empty($files) ? $this->_nogit->getDirectories($dirPath) : $files;

		}

		// Output containers
		$items   = [];
		$sorting = [];

		// Apply the filter early, reduces iterations through foreach()
		if (isset($filter) && $filter != '')
		{
			$files = preg_grep("(" . $filter . ")", $files);
		}

		// Go through items and get what we need
		foreach ($files as $item)
		{
			$item = rawurldecode($dirPath . DS . $item);
			if (trim($item) == '')
			{
				continue;
			}
			// Load basic file metadata
			$file = new Models\File($item, $this->_path);
			if ($this->_shouldSkipFile($file))
			{
				continue;
			}
			// Add to list
			if (empty($items[$file->get('name')]))
			{
				$items[$dirPath . DS . $file->get('localPath')] = $file;

				// Collect info for sorting
				switch ($sortby)
				{
					case 'size':
						$sorting[] = $file->getSize();
					break;

					case 'modified':
						$sorting[] = $file->get('date');
					break;

					case 'localpath':
						$sorting[] = strtolower($file->get('localPath'));
					break;

					case 'name':
					default:
						$sorting[] = strtolower($file->get('name'));
					break;
				}
			}
		}

		// Sort
		array_multisort($sorting, $sortdir, $items);

		// Apply start and limit, get complete metadata and return
		return $this->_list($items, $params);

	}

	protected function _shouldSkipFile($file)
	{
		$fileName = $file->get('name');
		$filesToSkip = array('.git', '.gitignore');

		return in_array($fileName, $filesToSkip);
	}

	/**
	 * Get file content and write to specified location
	 *
	 * @param   array  $params
	 * @return  array
	 */
	public function content($params = array())
	{
		$fileName = isset($params['fileName']) ? $params['fileName'] : null;
		$target   = isset($params['target']) ? $params['target'] : null;

		return $this->_nogit->getContent($fileName, $target);
	}

	/**
	 * Get last file revision
	 *
	 * @param   array   $params
	 * @return  boolean
	 */
	public function getLastRevision($params = array())
	{
		$file = isset($params['file']) ? $params['file'] : null;

		if (!($file instanceof Models\File))
		{
			return false;
		}

		return $file->getMd5Hash();
	}

	/**
	 * Get final list
	 *
	 * @param   array  $items
	 * @param   array  $params
	 * @param   array  $folders
	 * @return  array
	 */
	protected function _list($items, $params, $folders = array())
	{
		$dirPath  = isset($params['subdir']) ? $params['subdir'] : null;
		$limit    = isset($params['limit']) ? $params['limit'] : 0;
		$start    = isset($params['start']) ? $params['start'] : 0;
		$pubLinks = isset($params['getPubConnections']) ? $params['getPubConnections'] : false;
		$extended = isset($params['showFullMetadata']) ? $params['showFullMetadata'] : true;
		$folders  = count($folders) > 0 ? $folders : false;

		// Skip forward?
		if ($start)
		{
			$items = array_slice($items, ($start - 1));
		}
		// No results, return early
		if (empty($items))
		{
			return array();
		}

		// Go through sorted list, get extended metadata and limit results
		$i = 1;
		$results = array();
		foreach ($items as $file)
		{
			// First cut off at limit if set
			if ($limit && $i > $limit)
			{
				break;
			}

			// Now get more metadata
			$file->setSize();
			$file->setMimeType();
			$file->setIcon();

			$results[] = $file;
			$i++;
		}

		// Return top level folders or not.
		if (!$folders)
		{
			return $results;
		}
		else
		{
			return array($results, $folders);
		}
	}

	/**
	 * Get changes for sync
	 *
	 * @param   array  $params
	 * @return  array
	 */
	public function getChanges($params = array())
	{
		$localPath    = isset($params['localPath']) ? $params['localPath'] : null;
		$fromLocal    = isset($params['fromLocal']) ? $params['fromLocal'] : null;
		$localDir     = isset($params['localDir']) ? $params['localDir'] : null;
		$localRenames = isset($params['localRenames']) ? $params['localRenames'] : null;
		$connections  = isset($params['connections']) ? $params['connections'] : null;

		return $this->_nogit->getChanges($localPath, $fromLocal, $localDir, $localRenames, $connections);
	}

	/**
	 * Move item
	 *
	 * @param   array  $params
	 * @return  bool
	 */
	public function move($params = array())
	{
		$fromFile = isset($params['fromFile']) ? $params['fromFile'] : null;
		$toFile   = isset($params['toFile']) ? $params['toFile'] : null;
		$type     = isset($params['type']) ? $params['type'] : 'file';
		$author   = isset($params['author']) ? $params['author'] : null;
		$date     = isset($params['date']) ? $params['date'] : null;

		if (!($fromFile instanceof Models\File) || !($toFile instanceof Models\File))
		{
			return false;
		}

		$this->_nogit->call("mv " . escapeshellarg($fromFile->get('localPath')) . " " . escapeshellarg($toFile->get('localPath')));
		return true;
	}

	/**
	 * Make dir
	 *
	 * @param   array  $params
	 * @return  bool
	 */
	public function makeDirectory($params = array())
	{
		$file = isset($params['file']) ? $params['file'] : null;

		if (!($file instanceof Models\File) || $file->get('type') != 'folder')
		{
			return false;
		}

		if (!$this->get('remote'))
		{
			if (Filesystem::makeDirectory($file->get('fullPath'), 0755, true, true))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Delete dir
	 *
	 * @param   array  $params
	 * @return  bool
	 */
	public function deleteDirectory($params = array())
	{
		$file   = isset($params['file']) ? $params['file'] : null;
		$author = isset($params['author']) ? $params['author'] : null;
		$date   = isset($params['date']) ? $params['date'] : null;

		if (!($file instanceof Models\File) || $file->get('type') != 'folder')
		{
			return false;
		}

		if (file_exists($file->get('fullPath')))
		{
			// Remove directory that is not in Git
			if (!Filesystem::deleteDirectory($file->get('fullPath')))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Delete file
	 *
	 * @param   array  $params
	 * @return  bool
	 */
	public function deleteFile($params = array())
	{
		$file     = isset($params['file']) ? $params['file'] : null;
		$author   = isset($params['author']) ? $params['author'] : null;
		$date     = isset($params['date']) ? $params['date'] : null;

		if (!($file instanceof Models\File) || $file->get('type') != 'file' || !$file->exists())
		{
			return false;
		}

		$this->_nogit->call("rm " . escapeshellarg($file->get('localPath')));

		return true;
	}

	/**
	 * Get file data and map to file object
	 *
	 * @param   object  $file     Models\File
	 * @param   string  $dirPath
	 * @param   string  $property
	 * @return  array
	 */
	protected function _file($file, $dirPath, $property = null)
	{
		if (isset($this->_files[$file->get('localPath')]))
		{
			return $property
				? $this->_files[$file->get('localPath')]->get($property)
				: $this->_files[$file->get('localPath')];
		}

		if (!isset($this->_profileAssoc))
		{
			$this->_profileAssoc = array();
		}

		// Entry is in the master log?
		if (isset($this->_fileLog[$file->get('localPath')]))
		{
			$log = $this->_fileLog[$file->get('localPath')];
		}
		else
		{
			$log = $this->_nogit->gitLog($file->get('localPath'), 'combined');
		}

		// Map data we got
		if (!empty($log))
		{
			foreach ($log as $name => $value)
			{
				$file->set($name, $value);
			}
		}

		// SFTP?  Need some parsing
		if ($file->get('message') && substr($file->get('message'), 0, 5) == 'SFTP-')
		{
			$file->set('origin', 'SFTP');

			if (isset($this->_profileAssoc[trim($file->get('author'))]))
			{
				$profile = $this->_profileAssoc[trim($file->get('author'))];
				$file->set('author', $profile->get('name'));
				$file->set('email', $profile->get('email'));
			}
			else
			{
				$profile = \User::getInstance(trim($file->get('author')));
				if ($profile->get('id'))
				{
					$this->_profileAssoc[trim($file->get('author'))] = $profile;
					$file->set('author', $profile->get('name'));
					$file->set('email', $profile->get('email'));
				}
			}
		}

		// Store
		$this->_files[$file->get('localPath')] = $file;

		return $property
			? $this->_files[$file->get('localPath')]->get($property)
			: $this->_files[$file->get('localPath')];
	}

	/**
	 * Get used disk space in repo
	 *
	 * @param   array    $params
	 * @return  integer
	 */
	public function getDiskUsage($params = array())
	{
		$path    = isset($params['path']) ? $params['path'] : $this->_path;

		$used = 0;
		if ($path && is_dir($path))
		{
			chdir($path);

			exec('du -sk', $out);

			if ($out && isset($out[0]))
			{
				$kb = str_replace('.', '', trim($out[0]));
				$used = $kb * 1024;
			}
		}

		return $used;
	}

	/**
	 * Initialize repository
	 *
	 * @return  void
	 */
	public function ini()
	{
		// Initialize
		$this->_nogit->iniGit();
	}

	/**
	 * Stub for checking in required for compatibility
	 *
	 * @return
	 */
	public function checkin($params = array())
	{
		return true;
	}

	/**
	 * Stub for optimizing, required for compatibility
	 *
	 * @return
	 */
	public function optimize($params = array())
	{
		return true;
	}

	/**
	 * Stub for erasing, required for compatibility
	 *
	 * @return
	 */
	public function erase($params = array())
	{
		return true;
	}

	/**
	 * Stub for checking if it's git, required for compatibility
	 *
	 * @return
	 */
	public function isGit()
	{
		return false;
	}
	/**
	 * Stub for diffing, required for compatibility
	 *
	 * @return
	 */
	public function diff()
	{
		return false;
	}
	/**
	 * Stub for restoring a file, required for compatibility
	 *
	 * @return
	 */
	public function restore()
	{
		return false;
	}
	/**
	 * Stub for discarding, required for compatibility
	 *
	 * @return
	 */
	public function discard()
	{
		return true;
	}
	/**
	 * Stub for getting trash, required for compatibility
	 *
	 * @return
	 */
	public function getTrash()
	{
		return false;
	}
	/**
	 * Stub for getting history, required for compatibility
	 *
	 * @return
	 */
	public function history()
	{
		return false;
	}
}
