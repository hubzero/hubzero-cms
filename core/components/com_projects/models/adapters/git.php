<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		$cmd  = 'cd ' . $this->_path . ' && ';
		$cmd .='find . \( -path ./.git -o -name ".gitignore" \) -prune -o -type f -print | wc -l';

		return shell_exec($cmd);
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
		$files   		   = isset($params['files']) && is_array($params['files']) ? $params['files'] : array();
		$selector			 = isset($params['selector']) ? true : false;

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
		$topleveldirs = array();

		// Relates to gathering children
		if ($dirPath)
		{
			$folders[] = $dirPath;
		}

		// Apply the filter early, reduces iterations through foreach()
		if (isset($filter) && $filter != '')
		{
				$files = preg_grep("(".$filter.")", $files);
		}

		// Prevent too many files from being loaded in the file selector
		if (count($files > 500) && $filter == '' && $selector && !($dirPath))
		{
				// Get a list of top-level directories
				$rejects = preg_grep("(\\/)", $files);
				foreach ($rejects as $key => $rpath)
				{
					$pathString = explode("/", $rpath);
					if (count($pathString) > 1)
					{
						array_push($topleveldirs, $pathString[0]);
					}
				}

				// Cleanup the list
				$topleveldirs = array_unique($topleveldirs);

				// Get top-level files 
				$files = preg_grep("(\\/)", $files, PREG_GREP_INVERT);
		}

		// Go through items and get what we need
		foreach ($files as $item)
		{
			$item = urldecode($item);
			if (trim($item) == '')
			{
				continue;
			}

			// Load basic file metadata
			$file = new Models\File($item, $this->_path);

			// Search filter applied
			if ($filter)
			{
				$getParents = true;
				$getChildren = true;
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
						$folders[] =  $file->get('localPath');
						if (!$getParents)
						{
							continue;
						}

						$items[$file->get('name')] = $file;

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
				$file->set('converted', $syncRecord->remote_editing);
			}

			// Add to list
			if (empty($items[$file->get('name')]))
			{
				$items[$file->get('localPath')] = $file;

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
		return $this->_list($items, $params, $topleveldirs);

	}

	/**
	 * Get file list (retrieve and sort)
	 *
	 * @param   array  $params
	 * @return  array
	 */
	public function filelistnew($params = [])
	{
		// Parse incoming params and establish defaults
		$filter        = isset($params['filter']) ? $params['filter'] : NULL;
		$dirPath       = isset($params['subdir']) ? $params['subdir'] : NULL;
		$showUntracked = isset($params['showUntracked']) ? $params['showUntracked'] : false;
		$remotes       = isset($params['remoteConnections']) ? $params['remoteConnections'] : [];
		$sortdir       = isset($params['sortdir']) && $params['sortdir'] == 'DESC' ? SORT_DESC : SORT_ASC;
		$sortby        = isset($params['sortby']) ? $params['sortby'] : 'name';
		$files         = isset($params['files']) && is_array($params['files']) ? $params['files'] : [];
		$dirsOnly      = isset($params['dirsOnly']) ? $params['dirsOnly'] : false;

		if (!$dirsOnly)
		{
			// Get a list of files from the git repository
			$files = empty($files) ? $this->_git->getFilesNew($dirPath) : $files;

			// Add untracked?
			$untracked = $showUntracked ? $this->_git->getUntrackedFiles($dirPath) : [];
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
		}
		else
		{
			// This is recursive by default
			$files = empty($files) ? $this->_git->getDirectories($dirPath) : $files;

			// Add untracked?
			$untracked = $showUntracked ? $this->_git->getUntrackedDirectories($dirPath) : [];
			if (!empty($untracked))
			{
				$files = array_merge($files, $untracked);
			}
		}

		// Output containers
		$items 	 = [];
		$sorting = [];

		// Apply the filter early, reduces iterations through foreach()
		if (isset($filter) && $filter != '')
		{
			$files = preg_grep("(".$filter.")", $files);
		}

		// Go through items and get what we need
		foreach ($files as $item)
		{
			$item = urldecode($item);
			if (trim($item) == '')
			{
				continue;
			}

			// Load basic file metadata
			$file = new Models\File($item, $this->_path);

			// Untracked?
			if (in_array($file->get('localPath'), $untracked))
			{
				$file->set('untracked', true);
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
				$file->set('converted', $syncRecord->remote_editing);
			}

			// Add to list
			if (empty($items[$file->get('name')]))
			{
				$items[$file->get('localPath')] = $file;

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
		array_multisort($sorting, $sortdir, $items);

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
	protected function _list($items, $params, $folders = array())
	{
		$dirPath       = isset($params['subdir']) ? $params['subdir'] : NULL;
		$limit         = isset($params['limit']) ? $params['limit'] : 0;
		$start         = isset($params['start']) ? $params['start'] : 0;
		$pubLinks      = isset($params['getPubConnections']) ? $params['getPubConnections'] : false;
		$extended      = isset($params['showFullMetadata']) ? $params['showFullMetadata'] : true;
		$folders			 = count($folders) > 0 ? $folders : false;

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

			// Even more metadata
			if ($extended == true)
			{
				// Commenting out since it is a huge performance hit.
				// Pull from Git
				/*$this->_file($file, $dirPath);
				$file->setMd5Hash();*/
			}

			// Get size from Git?
			if (!$file->get('size') && $file->get('hash'))
			{
				$file->setSize($this->_git->gitLog($file->get('localPath'), $file->get('hash'), 'size'));
			}

			$results[] = $file;
			$i++;
		}

		// Return top level folders or not.
		if (!isset($folders) || !$folders)
		{
			return $results;
		}
		else
		{
			//ddie('there');
			return array($results, $folders);
		}
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
	 * Get changes for sync
	 *
	 * @return     array
	 */
	public function getChanges($params = array())
	{
		$localPath    = isset($params['localPath']) ? $params['localPath'] : NULL;
		$fromLocal    = isset($params['fromLocal']) ? $params['fromLocal'] : NULL;
		$localDir     = isset($params['localDir']) ? $params['localDir'] : NULL;
		$localRenames = isset($params['localRenames']) ? $params['localRenames'] : NULL;
		$connections  = isset($params['connections']) ? $params['connections'] : NULL;

		return $this->_git->getChanges($localPath, $fromLocal, $localDir, $localRenames, $connections);
	}

	/**
	 * Get last file revision
	 *
	 * @return  boolean
	 */
	public function getLastRevision($params = array())
	{
		$file = isset($params['file']) ? $params['file'] : NULL;
		if (!($file instanceof Models\File))
		{
			return false;
		}

		return $this->_git->gitLog($file->get('localPath'), '', 'hash');
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
		$author   = isset($params['author']) ? $params['author'] : NULL;
		$date     = isset($params['date']) ? $params['date'] : NULL;

		if (!($fromFile instanceof Models\File) || !($toFile instanceof Models\File))
		{
			return false;
		}

		$this->_git->gitMove($fromFile->get('localPath'), $toFile->get('localPath'), $type, $commitMsg);
		$this->_git->gitCommit($commitMsg, $author, $date);

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
			if (Filesystem::makeDirectory($file->get('fullPath'), 0755, true, true))
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
		$file     = isset($params['file']) ? $params['file'] : NULL;
		$author   = isset($params['author']) ? $params['author'] : NULL;
		$date     = isset($params['date']) ? $params['date'] : NULL;

		if (!($file instanceof Models\File) || $file->get('type') != 'folder')
		{
			return false;
		}

		// Delete from Git
		$this->_git->gitDelete($file->get('localPath'), 'folder', $commitMsg);
		$this->_git->gitCommit($commitMsg, $author, $date);

		if (!$this->get('remote') && file_exists($file->get('fullPath')))
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
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function deleteFile ($params = array())
	{
		$file     = isset($params['file']) ? $params['file'] : NULL;
		$author   = isset($params['author']) ? $params['author'] : NULL;
		$date     = isset($params['date']) ? $params['date'] : NULL;

		if (!($file instanceof Models\File) || $file->get('type') != 'file' || !$file->exists())
		{
			return false;
		}

		// Delete from Git
		$this->_git->gitDelete($file->get('localPath'), 'file', $commitMsg);
		$this->_git->gitCommit($commitMsg, $author, $date);

		// Untracked?
		if (!$this->get('remote') && file_exists($file->get('fullPath')))
		{
			// Remove file that is not in Git
			if (!Filesystem::delete($file->get('fullPath')))
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
		$author    = isset($params['author']) ? $params['author'] : NULL;
		$date      = isset($params['date']) ? $params['date'] : NULL;

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
		$this->_git->gitCommit($commitMsg, $author, $date);
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
		$file     = isset($params['file']) ? $params['file'] : NULL;
		$hash     = isset($params['version']) ? $params['version'] : NULL;
		$author   = isset($params['author']) ? $params['author'] : NULL;
		$date     = isset($params['date']) ? $params['date'] : NULL;

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
			$this->_git->gitCommit($commitMsg, $author, $date);
		}

		return true;
	}

	/**
	 * Diff revisions
	 *
	 * @param      object	$file		Models\File
	 * @param      array	$params
	 *
	 * @return     array
	 */
	public function diff ($params = array())
	{
		$file   = isset($params['file']) ? $params['file'] : NULL;
		$rev1   = isset($params['rev1']) ? $params['rev1'] : NULL;
		$rev2   = isset($params['rev2']) ? $params['rev2'] : NULL;
		$full   = isset($params['fullDiff']) ? $params['fullDiff'] : NULL;
		$mode   = isset($params['mode']) ? $params['mode'] : 'side-by-side';

		if (!$this->isGit())
		{
			return false;
		}
		if (!($file instanceof Models\File))
		{
			return false;
		}

		$rev1Parts = explode('@', $rev1);
		$rev2Parts = explode('@', $rev2);

		// Run some checks
		if (count($rev1Parts) <= 2 || count($rev2Parts) <= 2)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_DIFF_NO_CONTENT'));
			return false;
		}

		$rev1 = array(
			'rev'   => $rev1Parts[0],
			'hash'  => $rev1Parts[1],
			'fpath' => $rev1Parts[2],
			'val'   => urlencode($rev1)
		);

		$rev2 = array(
			'rev'   => $rev2Parts[0],
			'hash'  => $rev2Parts[1],
			'fpath' => $rev2Parts[2],
			'val'   => urlencode($rev2)
		);

		// Get text blobs
		$rev1['text'] = $this->_git->gitLog($rev1['fpath'], $rev1['hash'], 'blob');
		$rev2['text'] = $this->_git->gitLog($rev2['fpath'], $rev2['hash'], 'blob');

		// Diff class
		include_once( PATH_CORE . DS . 'plugins' . DS . 'projects' . DS
			. 'files' . DS . 'helpers' . DS . 'Diff.php' );

		$context = ($rev1['text'] == $rev2['text'] || $full) ? count($rev1['text']) : 10;
		$options = array('context' => $context);

		// Run diff
		$objDiff = new \Diff($rev1['text'], $rev2['text'], $options );

		if ($mode == 'side-by-side')
		{
			include_once( PATH_CORE . DS . 'plugins' . DS . 'projects' . DS . 'files'
				. DS . 'php-diff' . DS . 'Diff' . DS . 'Renderer' . DS . 'Html' . DS . 'hubSideBySide.php' );

			// Generate a side by side diff
			$renderer = new \Diff_Renderer_Html_SideBySide;
			$diff = $objDiff->Render($renderer);
		}
		elseif ($mode == 'inline')
		{
			include_once( PATH_CORE . DS . 'plugins' . DS . 'projects' . DS . 'files'
				. DS . 'php-diff' . DS . 'Diff' . DS . 'Renderer' . DS . 'Html' . DS . 'hubInline.php' );

			// Generate inline diff
			$renderer = new \Diff_Renderer_Html_Inline;
			$diff = $objDiff->Render($renderer);
		}
		else
		{
			// Print git diff
			$diff = $this->_git->gitDiff($rev1, $rev2);

			if (is_array($diff))
			{
				$diff = implode("\n", $diff);
			}
		}

		return $diff;
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
			$log = $this->_git->gitLog($file->get('localPath'), $file->get('hash'), 'combined');
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

	/**
	 * Initialize repository
	 *
	 * @param      array	$params
	 *
	 * @return     integer
	 */
	public function ini()
	{
		// Initialize
		$this->_git->iniGit();
	}
}
