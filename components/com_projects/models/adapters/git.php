<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Projects\Models\Adapters;

use Hubzero\Base\Object;
use Components\Projects\Models;
use Components\Projects\Helpers;

// Get git helper
require_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
	. DS . 'helpers' . DS . 'githelper.php');

/**
 * Projects Git adapter class
 */
class Git extends Models\Adapter
{
	/**
	 * Adapter name
	 *
	 * @var string
	 */
	private $_name = 'git';

	/**
	 * Full path to repository
	 *
	 * @var string
	 */
	private $_path = NULL;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct($path = NULL, $remote = false)
	{
		// Get component configs
		$configs = Component::params('com_projects');

		// Set path to git engine
		$this->_gitpath = $configs->get('gitpath', '/opt/local/bin/git');

		// Set repo path
		$this->_path = $path;

		// Git helper
		$this->_git = new Helpers\Git($this->_path);

		// File manager
		$this->fileSystem = new \Hubzero\Filesystem\Filesystem();

		$this->set('remote', $remote);
	}

	/**
	 * Get file count
	 *
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function count ($params = array())
	{
		// Tracked + untracked
		return (count($this->_git->getFiles()) + count($this->_git->getFiles('', true)));
	}

	/**
	 * Get file list (retrieve and sort)
	 *
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function filelist ($params = array())
	{
		// Accept params
		$filter        = isset($params['filter']) ? $params['filter'] : NULL;
		$dirPath       = isset($params['subdir']) ? $params['subdir'] : NULL;
		$showUntracked = isset($params['showUntracked']) ? $params['showUntracked'] : false;
		$remotes       = isset($params['remoteConnections']) ? $params['remoteConnections'] : array();
		$sortdir       = isset($params['sortdir']) && $params['sortdir'] == 'DESC' ? SORT_DESC : SORT_ASC;
		$sortby        = isset($params['sortby']) ? $params['sortby'] : 'name';
		$getParents    = isset($params['getParents']) ? $params['getParents'] : false;
		$getChildren   = isset($params['getChildren']) ? $params['getChildren'] : false;
		$files   	   = isset($params['files']) && is_array($params['files']) ? $params['files'] : array();

		// Get a list of files Git repo
		$files = empty($files) ? $this->_git->getFiles($dirPath, false) : $files;

		// Add untracked?
		$untracked = $showUntracked ? $this->_git->getFiles($dirPath, true) : array();
		if (!empty($untracked))
		{
			$files = array_merge($files, $untracked);
		}

		// Include remote connections?
		if (!empty($remotes))
		{
			foreach ($remotes as $name => $item)
			{
				$files[] = $name;
			}
		}
		$remote = $this->get('remote');

		// Output containers
		$items 		= array();
		$sorting 	= array();
		$folders	= array();
		if ($dirPath)
		{
			$folders[] = $dirPath;
		}

		// Go through items and ween out
		foreach ($files as $item)
		{
			if (trim($item) == '')
			{
				continue;
			}

			// Load basic file metadata
			$file = new Models\File(trim($item), $this->_path);

			// Search filter applied
			if ($filter
				&& strpos(trim($file->get('localPath')), trim($filter)) === false
				&& strpos(trim($file->get('localPath')), trim($filter)) === false)
			{
				continue;
			}
			elseif ($filter)
			{
				$getParents = false;
			}

			// Untracked?
			if (in_array($file->get('localPath'), $untracked))
			{
				$file->set('untracked', true);
			}

			// Do we have a parent? Get folder information
			if ($file->get('dirname')
				&& !in_array($file->get('dirname'), $folders))
			{
				// Folder metadata
				$file->setFolder();

				// Add to list
				if (empty($items[$file->get('name')]))
				{
					// Recursive check
					if ($getChildren || (!$getChildren && ($file->getDirLevel($dirPath) >  $file->getDirLevel($file->get('dirname')))))
					{
						$items[$file->get('name')]   = $file;

						// Collect info for sorting
						switch ($sortby)
						{
							case 'size':
								$sorting[] = $file->getSize();
							break;

							case 'modified':
								// Need to get extra metadata (slower)
								$sorting[] = '';
							break;

							case 'localpath':
								$sorting[] = strtolower($file->get('localPath'));
							break;

							case 'name':
							default:
								$sorting[] = strtolower($file->get('name'));
							break;
						}

						$folders[] = $file->get('localPath');
					}
				}
			}

			// Getting parent information?
			if ($getParents)
			{
				$file->setParents();
			}

			// Do not recurse
			if (!$getChildren && $file->get('dirname'))
			{
				if (!$dirPath || $dirPath != $file->get('dirname'))
				{
					continue;
				}
			}

			// Skip this
			if ($file->get('name') == '.gitignore')
			{
				continue;
			}

			// Check for remote connections
			$syncRecord = NULL;
			if (isset($remotes[$file->get('localPath')]))
			{
				// Pick up data from sync record
				$syncRecord = $remotes[$file->get('localPath')];
				$file->set('remote', $syncRecord->service);
				$file->set('author', $syncRecord->remote_author);
				$file->set('date', date ('c', strtotime($syncRecord->remote_modified . ' UTC')));
				$file->set('mimeType', $syncRecord->remote_format);
			}

			// Add to list
			if (empty($items[$file->get('name')]))
			{
				$items[$file->get('name')]   = $file;

				// Collect info for sorting
				switch ($sortby)
				{
					case 'size':
						$sorting[] = $file->getSize();
					break;

					case 'modified':
						// Need to get extra metadata (slower)
						$sorting[] = $this->_file($file, $dirPath, 'date');
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
		array_multisort($sorting, $sortdir, $items );

		// Apply start and limit, get complete metadata and return
		return $this->_list($items, $params);
	}

	/**
	 * Get file content and write to specified location
	 *
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function content ($params = array())
	{
		$fileName      = isset($params['fileName']) ? $params['fileName'] : NULL;
		$hash          = isset($params['hash']) ? $params['hash'] : NULL;
		$target        = isset($params['target']) ? $params['target'] : NULL;

		return $this->_git->getContent($fileName, $hash, $target);
	}

	/**
	 * Get final list
	 *
	 * @param      object	$file		Models\File
	 * @param      array	$params
	 *
	 * @return     array
	 */
	protected function _list($items, $params)
	{
		$dirPath       = isset($params['subdir']) ? $params['subdir'] : NULL;
		$limit         = isset($params['limit']) ? $params['limit'] : 0;
		$start         = isset($params['start']) ? $params['start'] : 0;
		$pubLinks      = isset($params['getPubConnections']) ? $params['getPubConnections'] : false;
		$extended      = isset($params['showFullMetadata']) ? $params['showFullMetadata'] : true;

		// Skip forward?
		if ($start)
		{
			$items = array_slice($items, ($start - 1));
		}
		// No results
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

			// Even more metadata
			if ($extended == true)
			{
				// Pull from Git
				$this->_file($file, $dirPath);
				$file->setMd5Hash();
			}

			// Get size from Git?
			if (!$file->get('size') && $file->get('hash'))
			{
				$file->setSize($this->_git->gitLog($file->get('localPath'), $file->get('hash'), 'size'));
			}

			$results[] = $file;
			$i++;
		}

		return $results;
	}

	/**
	 * Get file history
	 *
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function history ($params = array(), &$versions, &$timestamps)
	{
		$file = isset($params['file']) ? $params['file'] : NULL;

		if (!($file instanceof Models\File))
		{
			return false;
		}

		// Local file present?
		if (file_exists($file->get('fullPath')))
		{
			$this->_git->sortLocalRevisions($file->get('localPath'), $versions, $timestamps);
		}
		if ($file->get('originalPath') && $file->get('originalPath') != $file->get('localPath'))
		{
			// Should history be paired with another file?
			$this->_git->sortLocalRevisions($file->get('originalPath'), $versions, $timestamps, 1);
		}
		return true;
	}

	/**
	 * Get trashed items
	 *
	 * @return  boolean
	 */
	public function getTrash()
	{
		return $this->_git->listDeleted();
	}

	/**
	 * Move item
	 *
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function move($params = array())
	{
		$fromFile = isset($params['fromFile']) ? $params['fromFile'] : NULL;
		$toFile   = isset($params['toFile']) ? $params['toFile'] : NULL;
		$type     = isset($params['type']) ? $params['type'] : 'file';

		if (!($fromFile instanceof Models\File) || !($toFile instanceof Models\File))
		{
			return false;
		}

		$this->_git->gitMove($fromFile->get('localPath'), $toFile->get('localPath'), $type, $commitMsg);
		$this->_git->gitCommit($commitMsg);

		return true;
	}

	/**
	 * Make dir
	 *
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function makeDirectory ($params = array())
	{
		$file = isset($params['file']) ? $params['file'] : NULL;

		if (!($file instanceof Models\File) || $file->get('type') != 'folder')
		{
			return false;
		}

		if (!$this->get('remote'))
		{
			if ($this->fileSystem->makeDirectory($file->get('fullPath'), 0755, true, true))
			{
				$this->checkin($params);
				return true;
			}
		}

		return false;
	}

	/**
	 * Delete dir
	 *
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function deleteDirectory ($params = array())
	{
		$file = isset($params['file']) ? $params['file'] : NULL;

		if (!($file instanceof Models\File) || $file->get('type') != 'folder')
		{
			return false;
		}

		// Delete from Git
		$this->_git->gitDelete($file->get('localPath'), 'folder', $commitMsg);
		$this->_git->gitCommit($commitMsg);

		if (!$this->get('remote') && file_exists($file->get('fullPath')))
		{
			// Remove directory that is not in Git
			if (!$this->fileSystem->deleteDirectory($file->get('fullPath')))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Delete file
	 *
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function deleteFile ($params = array())
	{
		$file = isset($params['file']) ? $params['file'] : NULL;

		if (!($file instanceof Models\File) || $file->get('type') != 'file')
		{
			return false;
		}

		// Delete from Git
		$this->_git->gitDelete($file->get('localPath'), 'file', $commitMsg);
		$this->_git->gitCommit($commitMsg);

		// Untracked?
		if (!$this->get('remote') && file_exists($file->get('fullPath')))
		{
			// Remove file that is not in Git
			if (!$this->fileSystem->delete($file->get('fullPath')))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Checkin file change
	 *
	 * @param      object	$file		Models\File
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function checkin ($params = array())
	{
		$file      = isset($params['file']) ? $params['file'] : NULL;
		$new       = isset($params['replace']) && !$params['replace'] ? true : false;
		$commitMsg = isset($params['message']) ? $params['message'] : NULL;

		if (!$this->isGit())
		{
			return false;
		}
		if (!($file instanceof Models\File))
		{
			return false;
		}
		if ($file->get('type') == 'folder')
		{
			if ($new)
			{
				// Provision new folder in Git
				$this->_git->makeEmptyFolder($file->get('localPath'));
			}
			return;
		}

		// Git add
		$this->_git->gitAdd($file->get('localPath'), $commitMsg, $new);
		$this->_git->gitCommit($commitMsg);
		return true;
	}

	/**
	 * Discard file change
	 *
	 * @param      object	$file		Models\File
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function discard ($params = array())
	{
		$file = isset($params['file']) ? $params['file'] : NULL;

		if (!$this->isGit())
		{
			return false;
		}
		if (!($file instanceof Models\File))
		{
			return false;
		}

		// Checkout
		$this->_git->gitCheckout($file->get('localPath'));
	}

	/**
	 * Restore file revision
	 *
	 * @param      object	$file		Models\File
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function restore ($params = array())
	{
		$file = isset($params['file']) ? $params['file'] : NULL;
		$hash = isset($params['version']) ? $params['version'] : NULL;

		if (!$this->isGit())
		{
			return false;
		}
		if (!($file instanceof Models\File) || !$hash)
		{
			return false;
		}

		// Checkout pre-delete revision
		$this->_git->gitCheckout( $file->get('localPath'), $hash . '^ ' );

		// If restored
		if (is_file( $file->get('fullPath')))
		{
			// Git add & commit
			$this->_git->gitAdd($file->get('localPath'), $commitMsg, $new = false);
			$this->_git->gitCommit($commitMsg);
		}

		return true;
	}

	/**
	 * Get file data from Git and map to file object
	 *
	 * @param      object	$file	Models\File
	 *
	 * @return     array
	 */
	protected function _file ($file, $dirPath, $property = NULL)
	{
		if (isset($this->_files[$file->get('localPath')]))
		{
			return $property
				? $this->_files[$file->get('localPath')]->get($property)
				: $this->_files[$file->get('localPath')];
		}

		// Master log exists?
		if (!isset($this->_fileLog))
		{
			$this->_fileLog = $this->_git->gitLogAll($dirPath);
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
			$log = $this->_git->gitLog($file->get('localPath'), $file->get('commitHash'), 'combined');
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
				$profile = \Hubzero\User\Profile::getInstance( trim($file->get('author')) );
				if ($profile)
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
	 * @param      array	$params
	 *
	 * @return     integer
	 */
	public function getDiskUsage($params = array())
	{
		$working     = isset($params['working']) ? $params['working'] : true;
		$git         = isset($params['history']) ? $params['history'] : true;
		$path        = isset($params['path']) ? $params['path'] : $this->_path;

		$used = 0;
		if ($path && is_dir($path))
		{
			chdir($path);
			$where = $git == true ? ' .[!.]*' : '';

			// Make sure there is .git directory
			if ($git == true && !is_dir($path . DS . '.git'))
			{
				return 0;
			}

			exec('du -sk ' . $where, $out);

			if ($out && isset($out[0]))
			{
				$dir = $git == true ? '.git' : '.';
				$kb = str_replace($dir, '', trim($out[0]));
				$used = $kb * 1024;
			}
		}

		if ($git == false && $working == true)
		{
			$gitUsage = $this->getDiskUsage();
			$used = $used - $gitUsage;
		}

		return $used;
	}

	/**
	 * Check if this is a git repo
	 *
	 * @return    boolean
	 */
	public function isGit()
	{
		if (!$this->_path || !is_dir($this->_path) || !is_dir( $this->_path . DS . '.git' ))
		{
			return false;
		}

		return true;
	}

	/**
	 * Erase repository
	 *
	 * @param      array	$params
	 *
	 * @return     integer
	 */
	public function erase($params = array())
	{
		$path = isset($params['path']) ? $params['path'] : $this->_path;
		if ($path && is_dir( $path . DS . '.git' ))
		{
			// cd
			chdir($path);

			// Wipe out .git directory
			exec('rm -rf .git', $out);

			return true;
		}

		return false;
	}

	/**
	 * Optimize repository
	 *
	 * @param      array	$params
	 *
	 * @return     integer
	 */
	public function optimize($params = array())
	{
		$path      = isset($params['path']) ? $params['path'] : $this->_path;
		$aggressive = isset($params['adv']) ? $params['adv'] : false;
		if ($path && is_dir( $path . DS . '.git' ))
		{
			$command = $aggressive ? 'gc --aggressive' : 'gc';
			$this->_git->callGit($command);
			return true;
		}

		return false;
	}
}
