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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Projects Git helper class
 */
class ProjectsGitHelper extends JObject {
		
	/**
	 * Git path
	 * 
	 * @var syring
	 */
	private $_gitpath 		= NULL;
	
	/**
	 * User ID
	 * 
	 * @var integer
	 */
	private $_uid 			= NULL;
	
	/**
	 * Prefix to project Git repo paths
	 * 
	 * @var syring
	 */
	private $_prefix 		= NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      string 	$gitpath 	Path to git
	 * @param      integer 	$userid 	User ID
	 * @param      string 	$prefix 	Repo path prefix
	 * @return     void
	 */	
	public function __construct( $gitpath = NULL, $userid = 0, $prefix = NULL)
	{
		$this->_gitpath = $gitpath;
		$this->_uid 	= $userid;
		$this->_prefix 	= $prefix;
		
		if (!$userid)
		{
			$juser =& JFactory::getUser();
			$this->_uid = $juser->get('id');
		}		
	}
		
	/**
	 * Get author for Git commits
	 * 
	 * @param      string 	$name	Author name
	 * @param      string 	$email 	Author email
	 *
	 * @return     string
	 */
	public function getGitAuthor($name = '', $email = '')
	{
		if (!$name || !$email)
		{
			if (!$this->_uid)
			{
				return false;
			}

			// Get author profile
			$profile =& Hubzero_Factory::getProfile();
			$profile->load( $this->_uid );

			$name    = $profile->get('name');
			$email   = $profile->get('email');
		}
		
		$author  = escapeshellarg($name . ' <' . $email . '> ');
		
		return $author;
	}
	
	/**
	 * Get date of last commit
	 * 
	 * @param      string	$path
	 * @param      array  	$out
	 *
	 * @return     string
	 */
	function getLastCommit( $path = '', $out = array() )    
	{
		chdir($this->_prefix . $path);
        $date = exec($this->_gitpath 
			. " rev-list  --header --max-count=1 HEAD | grep -a committer | cut -f5-6 -d' '");
			
        return date("D n/j/y G:i", (int)$date);
	}
	
	/**
	 * Init Git repository
	 * 
	 * @param      string	$path	Repo path
	 *
	 * @return     string
	 */
	public function iniGit( $path = '') 
	{							
		if (!$path)
		{
			return false;
		}
		
		// Build .git repo
		$gitRepoBase = $this->_prefix . $path . DS . '.git';
			
		// Need to create .git repository if not yet there
		if (!is_dir($gitRepoBase)) 
		{	
			if (!is_dir($this->_prefix . $path))
			{
				return false;
			}
			chdir($this->_prefix . $path);
			exec($this->_gitpath . ' init 2>&1', $out);
		}
							
		return true;			
	}
	
	/**
	 * Show text content
	 * 
	 * @param      string  	$fpath		file name or commit hash
	 *
	 * @return     string
	 */
	public function showTextContent($fpath = '', $max = 100)
	{		
		if (!$fpath)
		{
			return false;
		}
		
		$content = '';
		
		// Get non-binary object content			
		exec($this->_gitpath . ' show  HEAD:' . escapeshellarg($fpath) . ' 2>&1', $out);
				
		// Reformat text content
		if (count($out) > 0) 
		{
			// Cut number of lines
			if (count($out) > $max)
			{
				$out = array_slice($out, 0, $max);
			}
			
			$content = ProjectsGitHelper::filterASCII($out, false, false, $max);			
		}
		
		return $content;
	}
	
	/**
	 * Diff revisions
	 * 
	 * @param      string  	$path
	 * @param      array  	$old
	 * @param      array  	$new
	 *
	 * @return     string
	 */
	public function gitDiff ($path = '', $old = array(), $new = array())
	{		
		if (!$path || !isset($old['hash']) || !isset($new['hash']) || !isset($new['fpath']))
		{
			return false;
		}
		
		$file = $new['fpath'] == $old['fpath'] ? ' -- ' . escapeshellarg($new['fpath']) : '';
		
		exec($this->_gitpath . ' diff --name-status ' . $old['hash'] . '^ ' .  ' 2>&1 ', $oCount  );
		exec($this->_gitpath . ' diff --name-status ' . $new['hash'] . '^ ' .  ' 2>&1 ', $nCount  );
		
		// Get file content
		if (count($oCount) <= 2 && count($nCount) <= 2)
		{
			exec($this->_gitpath . ' diff -M -C ' . $old['hash'] . ' ' 
			. $new['hash'] . ' 2>&1 ', $out);
		}
		/*
		if ($file)
		{
			exec($this->_gitpath . ' diff -M -C ' . $old['hash'] . ' ' 
			. $new['hash'] . $file . ' 2>&1 ', $out);
		}
		*/
		else
		{
			exec($this->_gitpath . ' diff -M -C ' . $old['hash'] . ':' . $old['fpath'] . ' '
			. $new['hash'] . ':' . $new['fpath'] . ' 2>&1 ', $out);
		}
				
		return $out;
	}
		
	/**
	 * Get file content
	 * 
	 * @param      string  	$file		file path
	 * @param      string  	$hash		Git hash
	 * @param      string  	$temppath	Output content to temp path
	 *
	 * @return     void
	 */
	public function getContent($file = '', $hash = '', $temppath = '')
	{		
		if (!$file || !$hash || !$temppath)
		{
			return false;
		}
		
		// Get file content
		exec($this->_gitpath . ' show  ' . $hash . ':' . escapeshellarg($file) 
			. ' > ' . escapeshellarg($temppath) . ' 2>&1 ', $out);
			
		return true;
	}
	
	/**
	 * Show commit log detail
	 * 
	 * @param      string	$path		repository path
	 * @param      string  	$file		file path
	 * @param      string  	$hash		Git hash
	 * @param      string  	$return
	 *
	 * @return     string
	 */
	public function gitLog ($path = '', $file = '', $hash = '', $return = 'date') 
	{
		chdir($this->_prefix . $path);
		$what = '';
		
		// Set exec command for retrieving different commit information
		switch ( $return ) 
		{
			case 'combined':
				$exec = ' log --diff-filter=AMR --pretty=format:"%ci||%an||%ae||%H"';
				break;
			
			case 'date':
			default:
				$exec = ' log --pretty=format:%ci ';
				break;
				
			case 'timestamp':
				$exec = ' log --pretty=format:%ct ';
				break;
				
			case 'num':
				$exec = ' log --diff-filter=AMR --pretty=format:%H ';
				break;
				
			case 'author':
				$exec = ' log --pretty=format:%an ';
				break;
			
			case 'email':
				$exec = ' log --pretty=format:%ae ';
				break;
				
			case 'hash':
				$exec = ' log --pretty=format:%H ';
				break;
				
			case 'message':
				$exec = ' log --pretty=format:%s ';
				break;
				
			case 'size':
				$exec = ' cat-file -s ';
				$what = $hash . ':' . escapeshellarg($file);
				break;
				
			case 'diff':
				$exec = ' diff -M -C  ';
				$what = $hash . '^ ' . $hash . ' -- '. escapeshellarg($file);
				break;
			
			case 'content':
				$exec = ' show  ';
				$what = $hash . ':'. escapeshellarg($file);
				break;
								
			case 'rename':
				$exec = ' log --oneline --name-only --follow -M  ';
				break;
							
			case 'namestatus':
				$exec = ' diff -M -C --name-status ';
				$what = $hash . '^ ' . $hash . ' -- '. escapeshellarg($file);
				break;
				
			case 'blob':
				$exec = ' show  ';
				$what = $hash . ':' . escapeshellarg($file);
				break;
		}
					
		if (!$what)
		{
			$what = $hash ? $hash : '';
			$what.= $hash && $file ? ' ' : '';
			$what.= $file ? ' -- ' .escapeshellarg($file) : '';
		}
		
		// Exec command
		exec($this->_gitpath . ' '. $exec . ' ' . $what . ' 2>&1', $out);
		
		// Parse returned array of data
		if (empty($out))
		{
			return NULL;
		}
		if ($return == 'combined')
		{
			$arr  = explode("\t", $out[0]);			
			$data = explode("||", $arr[0]);
			
			$entry = array();
			$entry['date']  	= $data[0];
			$entry['num'] 		= count($out);
			$entry['author'] 	= $data[1];
			$entry['email'] 	= $data[2];
			return $entry;
		}
				
		if ($return == 'content' || $return == 'blob')
		{
			return $out;
		}		
		if ($return == 'date')
		{
			$arr = explode("\t", $out[0]);
			$timestamp = strtotime($arr[0]);
			return date ('m/d/Y g:i A', $timestamp);
		}
		elseif ($return == 'num')
		{
			return count($out);
		}
		elseif ($return == 'namestatus')
		{
			$n = substr($out[0], 0, 1);
			return $n == 'f' ? 'A' : $n;
		}
		elseif ($return == 'rename')
		{
			if (count($out) > 0) 
			{
				$names = array();
				$hashes = array();
				$k = 0;
				
				foreach ($out as $o)
				{
					if ($k % 2 == 0)
					{
						$hashes[] = substr($o, 0, 7);
					}
					else
					{
						$names[] = $o;
					}
					$k++;			
				}
				
				return array_combine($hashes, $names);
			}
			else
			{
				return NULL;
			}
		}
		else
		{	
			$arr = explode("\t", $out[0]);
			return $arr[0];
		}
	}
	
	/**
	 * Make Git call
	 * 
	 * @param      string	$path
	 * @param      string	$call	
	 *
	 * @return     array to be parsed
	 */
	public function callGit ($path = '', $call = '') 
	{
		$out = array();
		
		if (!$call)
		{
			return false;
		}
		
		chdir($this->_prefix . $path);
		exec($this->_gitpath . ' ' . $call . '  2>&1', $out);
		
		return $out;
	}
	
	/**
	 * Git commit
	 * 
	 * @param      string	$path
	 * @param      string	$commitMsg
	 * @param      string	$author
	 * @param      string	$date
	 *
	 * @return     void
	 */
	public function gitCommit ($path = '', $commitMsg = '', $author = '', $date = '' ) 
	{
		// Get author profile
		$author  = $author ? $author : $this->getGitAuthor();
		
		chdir($this->_prefix . $path);
		$date = $date ? ' --date="' . $date . '"' : '';
		exec($this->_gitpath . ' commit -a -m "' . $commitMsg . '" --author="' . $author . '"' . $date . '  2>&1', $out);
		
		return true;		
	}
	
	/**
	 * Add/update local repo item
	 * 
	 * @param      string	$path
	 * @param      string	$item		file path
	 * @param      string	&$commitMsg
	 * @param      boolean	$new		
	 *
	 * @return     void
	 */
	public function gitAdd ($path = '', $item = '', &$commitMsg = '', $new = true ) 
	{
		if (!$path || !$item)
		{
			return false;
		}
				
		chdir($this->_prefix . $path);
		exec($this->_gitpath . ' add ' . escapeshellarg($item) . ' 2>&1', $out);
		
		$commitMsg .= $new == true ? 'Added' : 'Updated';
		$commitMsg .= ' file '.escapeshellarg($item) . "\n";
		
		return true;
	}
	
	/**
	 * Delete item from local repo
	 * 
	 * @param      string	$path
	 * @param      string	$item		file path
	 * @param      string	$type		'file' or 'folder'
	 * @param      string	&$commitMsg
	 *
	 * @return     array to be parsed
	 */
	public function gitDelete ($path = '', $item = '', $type = 'file', &$commitMsg = '' ) 
	{
		if (!$path || !$item)
		{
			return false;
		}
		
		$deleted = 0;
		
		chdir($this->_prefix . $path);
		
		if ($type == 'folder')
		{
			if ($item != '' && is_dir($this->_prefix . $path . DS . $item)) 
			{
				exec($this->_gitpath . ' rm -r ' . escapeshellarg($item) . ' 2>&1', $out);
				$deleted++;
				$commitMsg .= 'Deleted folder '.escapeshellarg($item) . "\n";
			}
		}
		elseif ($type == 'file')
		{
			if ($item != '' && file_exists($this->_prefix . $path . DS . $item)) 
			{
				exec($this->_gitpath . ' rm ' . escapeshellarg($item) . ' 2>&1', $out);
				$deleted++;
				$commitMsg .= 'Deleted file '.escapeshellarg($item) . "\n";
			}
		}
		
		return $deleted;
	}
	
	/**
	 * Move/rename item
	 * 
	 * @param      string	$path		Repo path
	 * @param      string	$from		From file path
	 * @param      string	$where		To file path
	 * @param      string	$type		'file' or 'folder'
	 * @param      string	&$commitMsg
	 *
	 * @return     array to be parsed
	 */
	public function gitMove ($path = '', $from = '', $where = '', $type = 'file', &$commitMsg = '' ) 
	{
		if (!$path || !$from || !$where)
		{
			return false;
		}
		
		$moved = 0;
		
		chdir($this->_prefix . $path);
		
		if ($type == 'folder' && $from != '' && $from != $where  && is_dir($this->_prefix . $path . DS . $from))
		{
			exec($this->_gitpath . ' mv ' . escapeshellarg($from)
				. ' ' . escapeshellarg($where) . ' -f 2>&1', $out);
			$commitMsg .= 'Moved folder '.escapeshellarg($from) .' to ' . escapeshellarg($where) . "\n";
			$moved++;	
		}
		elseif ($type == 'file' && $from != '' && $from != $where  && file_exists($this->_prefix . $path . DS . $from))
		{
			exec($this->_gitpath . ' mv ' . escapeshellarg($from)
				. ' ' . escapeshellarg($where) . ' -f 2>&1', $out);
			$commitMsg .= 'Moved file '.escapeshellarg($from) .' to ' . escapeshellarg($where) . "\n";
			$moved++;
		}
		
		return $moved;
	}
	
	/**
	 * Ls files
	 * 
	 * @param      string	$path		Repo path
	 * @param      string	$subdir		Local directory path
	 *
	 * @return     array
	 */
	public function getFiles ($path = '', $subdir = '') 
	{
		// Get Git status
		$out = $this->callGit($path, ' ls-files --exclude-standard ' . escapeshellarg($subdir) );
				
		return $out && substr($out[0], 0, 5) == 'fatal' ? array() : $out;
	}
		
	/**
	 * Get changes for sync
	 * 
	 * @param      string	$path		Repo path
	 *
	 * @return     array
	 */
	public function getChanges ($path = '', $localPath = '', $synced = '', $localDir = '', &$localRenames, $connections) 
	{
		// Collector array
		$locals = array();
		
		// MIME types		
		ximport('Hubzero_Content_Mimetypes');
		$mt = new Hubzero_Content_Mimetypes();
		
		// Initial sync
		if ($synced == 1)
		{
			$files = $this->callGit( $path, 'ls-files --full-name ' . escapeshellarg($localDir));
			$files = $files && substr($files[0], 0, 5) == 'fatal' ? array() : $files;
			
			if (empty($files))
			{
				return $locals;
			}
			
			foreach ($files as $filename)
			{
				$type = 'file';
								
				// We are only interested in last local change on the file
				if (!isset($locals[$filename]))
				{	
					$time = strtotime(date('Y-m-d H:i:s', time() ));
					
					$mTypeParts = explode(';', $mt->getMimeType($localPath . DS . $filename));	
					$mimeType = $mTypeParts[0];
					
					$locals[$filename] = array(
						'status' 		=> 'A', 
						'time' 			=> $time, 
						'type' 			=> $type,
						'remoteid' 		=> 0,
						'converted' 	=> 0,
						'rParent'		=> NULL,
						'local_path'	=> $filename,
						'title'			=> basename($filename),
						'author'		=> NULL,
						'modified' 		=> gmdate('Y-m-d H:i:s', $time), 
						'synced'		=> NULL,
						'fullPath' 		=> $localPath . DS . $filename,
						'mimeType'		=> $mimeType,
						'md5' 			=> NULL,
						'rename'		=> NULL
					);
				}
			}			
		}
		// Repeat sync
		else
		{
			// Collect 
			$since 			= $synced != 1 ? ' --since="'. $synced . '"' : '';
			$where 			= $localDir ? '  --all -- ' . escapeshellarg($localDir) . ' ' : ' --all ';
			$changes 		= $this->callGit( $path, 'rev-list ' . $where . $since);
			
			// Empty repo or no changes?
			if (empty($changes) || trim(substr($changes[0], 0, 5)) == 'usage')
			{
				$changes = array();
			}
			
			// Parse Git file list to find which items changed since last sync
			if (count($changes) > 0) 
			{
				$timestamps = array();

				// Get files involved in each commit
				foreach ($changes as $hash) 
				{															
					// Get time and author of commit
					$time   = $this->gitLog($path, '', $hash, 'timestamp');			
					$author = $this->gitLog($path, '', $hash, 'author');

					// Get filename and change
					$fileinfo = $this->callGit( $path, 'diff --name-status ' . $hash . '^ ' . $hash );

					// First commit
					if ($fileinfo[0] && substr($fileinfo[0], 0, 5) == 'fatal')
					{
						$fileinfo = $this->callGit( $path, 'log --pretty=oneline --name-status --root' );

						if (!empty($fileinfo))
						{
							// Remove first line
							array_shift($fileinfo);
						}
					}

					// Go through files
					foreach ($fileinfo as $line) 
					{
						$n = substr($line, 0, 1);

						if ($n == 'f')
						{
							// First file in repository
							$finfo = $this->callGit( $path, 'log --pretty=oneline --name-status ' . $hash );
							$status = 'A';
							$filename = trim(substr($finfo[1], 1));	
							break;
						}
						else
						{
							$status = $n;
							$filename = trim(substr($line, 1));	
						}

						$type = 'file';
						$rename = '';

						// Detect a rename
						if (isset($localRenames[$filename]))
						{
							$rename = $localRenames[$filename];
						}
						else
						{
							$rename = $this->getRename($path, $filename, $hash, $since);

							if ($rename && $status == 'A')
							{			
								// Rename or move?
								if (basename($rename) == basename($filename))
								{
									$status = 'W'; // this means 'move'
								}
								else
								{
									$status = 'R';
								}

								$localRenames[$filename] = $rename;						
							}
						}

						// Hidden file in local directory - treat as directory
						if (preg_match("/.gitignore/", $filename))
						{ 
							$filename = dirname($filename);

							// Skip home directory
							if ($filename == '.')
							{
								continue;
							}

							$type = 'folder';
						}

						// Specific local directory is synced?
						$lFilename = $localDir ? preg_replace( "/^" . $localDir. "\//", "", $filename) : $filename;

						$conn 		= $connections['paths'];
						$search 	= $status == 'R' || $status == 'W' ? $rename : $filename;
						$found 		= isset($conn[$search]) && $conn[$search]['type'] == $type ? $conn[$search] : false;

						// Rename/move connection not found  - check against new name in case of repeat sync
						if (!$found && ($status == 'R' || $status == 'W'))
						{
							$found 	= isset($conn[$filename]) && $conn[$filename]['type'] == $type ? $conn[$filename] : false;
						}

						$remoteid 	= $found ? $found['remote_id'] : NULL;					
						$converted 	= $found ? $found['converted']: 0;
						$rParent	= $found ? $found['rParent'] : NULL;
						$syncT		= $found ? $found['synced'] : NULL;

						$md5Checksum = $type == 'file' && file_exists($localPath . DS . $filename) 
							? hash_file('md5', $localPath . DS . $filename) : NULL;

						$mimeType = NULL;
						if ($type == 'file')
						{
							$mTypeParts = explode(';', $mt->getMimeType($localPath . DS . $filename));	
							$mimeType = $mTypeParts[0];
						}

						// We are only interested in last local change on the file
						if (!isset($locals[$lFilename]))
						{	
							$locals[$lFilename] = array(
								'status' 		=> $status, 
								'time' 			=> $time, 
								'type' 			=> $type,
								'remoteid' 		=> $remoteid,
								'converted' 	=> $converted,
								'rParent'		=> $rParent,
								'local_path'	=> $filename,
								'title'			=> basename($filename),
								'author'		=> $author,
								'modified' 		=> gmdate('Y-m-d H:i:s', $time), 
								'synced'		=> $syncT,
								'fullPath' 		=> $localPath . DS . $filename,
								'mimeType'		=> $mimeType,
								'md5' 			=> $md5Checksum,
								'rename'		=> $rename
							);

							$timestamps[] = $time;
						}
					}			
				}

				// Sort by time, most recent first	
				array_multisort($timestamps, SORT_DESC, $locals);
			}
			
		}
								
		return $locals;		
	}
	
	/**
	 * Run Git status
	 * 
	 * @param      string	$path
	 * @param      string	$status	
	 *
	 * @return     string
	 */
	public function gitStatus ($path = '', $status = '') 
	{
		chdir($this->_prefix . $path);
		
		// Clean up 
		exec('rm .DS_Store 2>&1', $out9);
		
		// Get Git status
		$out = $this->callGit($path, 'status');
		
		if (count($out) > 0 && $out[0] != '') 
		{
			foreach ($out as $line) {
				$status.=  '<br />' . $line;
			}
		}
				
		return $status;
	}
	
	/**
	 * Make Git recognize empty folder
	 * 
	 * @param      string	$path
	 * @param      string	$dir		
	 *
	 * @return     array to be parsed
	 */
	public function makeEmptyFolder ($path = '', $dir = '' ) 
	{
		chdir($this->_prefix . $path);
		
		// Clean up 
		exec('rm .DS_Store 2>&1', $out9);
		
		// Create an empty file
		exec('touch ' . escapeshellarg($dir) . '/.gitignore ' . ' 2>&1', $out);

		// Git add
		exec($this->_gitpath . ' add ' . escapeshellarg($dir) . ' 2>&1', $out);
		
		return true;
	}
	
	/**
	 * Get local file history
	 * 
	 * @param      string	$path
	 * @param      string	$file		
	 * @param      string	$rev	
	 * @param      string	$since	
	 *
	 * @return     array of hashes
	 */
	public function getLocalFileHistory($path, $file = '', $rev = '', $since = '')
	{		
		chdir($this->_prefix . $path);
				
		// Get local file history
		exec($this->_gitpath . ' log --follow --pretty=format:%H ' . $since . ' ' . $rev . ' ' 
			. escapeshellarg($file) . '  2>&1', $out);
		
		$hashes = array();
		
		// Get all commit hashes
		if (count($out) > 0 && $out[0] != '') 
		{
			$r = 0;
			foreach ($out as $line) 
			{
				if (preg_match("/[a-zA-Z0-9]/", $line) && strlen($line) == 40) 
				{
					$hashes[]  = $line;
				}
				$r++;
			}
		}
		
		return $hashes;
	}
	
	/**
	 * Get details on file history
	 * 	
	 * @param      string	$local_path			file path
	 * @param      string	$path				Repo path
	 * @param      array 	&$versions			Versions collector array
	 * @param      array 	&$timestamps		Collector array
	 * @param      integer	$original			Source file?
	 *
	 * @return     array of version info
	 */
	public function sortLocalRevisions($local_path = '', $path, &$versions = array(), &$timestamps = array(), $original = 0 )
	{
		// Get local file history
		$hashes = $this->getLocalFileHistory($path, $local_path, '--');
		
		// Binary
		$binary = $this->isBinary($this->_prefix . $path . DS . $local_path);
																		
		// Get info for each commit
		if (!empty($hashes)) 
		{
			$h = 1;
			
			// Get all names for this file
			$renames 		= $this->gitLog($path, $local_path, '', 'rename');
			$currentName	= $local_path;
			$rename			= 0;
									
			foreach ($hashes as $hash) 
			{						
				$date 			= $this->gitLog($path, '', $hash, 'date');
				$timestamps[]  	= $this->gitLog($path, '', $hash, 'timestamp');
				
				$order 			= $h == 1 ? 'first' : '';
				$order 			= $h == count($hashes) ? 'last' : $order;
				
				// Dealing with renames
				$abbr = substr($hash, 0, 7);
				$name = isset($renames[$abbr]) ? $renames[$abbr] : $local_path;
				
				$parts = explode('/', $name);
				$serveas = trim(end($parts));
				
				if ($name != $currentName)
				{
					$rename = 1;
					$currentName = $name;
				}
				
				$content		= $binary ? NULL : $this->gitLog($path, $name, $hash, 'content');
				$message		= $this->gitLog($path, '', $hash, 'message');
																
				$revision = array(
					'date' 			=> $date,
					'author' 		=> $this->gitLog($path, '', $hash, 'author'),
					'email'			=> $this->gitLog($path, '', $hash, 'email'),
					'hash' 			=> $hash,
					'file' 			=> $serveas,
					'base' 			=> $local_path,
					'remote'		=> NULL,
					'local'			=> true,
					'content'		=> '',
					'preview'		=> NULL,
					'original'		=> $original,
					'hide'			=> 0,
					'message'		=> $message,
					'rename'		=> $rename,
					'name'			=> $name,
					'change'		=> '',
					'movedTo'		=> '',
					'size'			=> '',
					'order'			=> $order,
					'count'			=> count($hashes),
					'commitStatus'	=> $this->gitLog($path, $name, $hash, 'namestatus')
				);
				
				if (in_array($revision['commitStatus'], array('A', 'M')))
				{
					$revision['size'] = ProjectsHtml::formatSize($this->gitLog($path, $name, $hash, 'size'));
				}

				// Exctract file content for certain statuses
				if (in_array($revision['commitStatus'], array('A', 'M', 'R')) && $content)
				{
					$revision['content'] = $this->filterASCII($content);
				}				
										
				$versions[] = $revision;
				$h++;
			}
		}
	}
	
	/**
	 * Check if file is binary
	 * 
	 * @param      string	$file
	 *
	 * @return     integer
	 */
	public function IsBinary($file) 
	{ 
	  	if (file_exists($file)) 
		{ 
	    	if (!is_file($file)) 
			{
				return 0;
			} 

	    	$fh  = fopen($file, "r"); 
	    	$blk = fread($fh, 512); 
	    	fclose($fh); 
	    	clearstatcache(); 

	    	return ( 
		      0 or substr_count($blk, "^ -~")/512 > 0.3 
		        or substr_count($blk, "\x00") > 0 
		    ); 
	  	}
	 
	  	return 0; 
	}
	
	/**
	 * Filter ASCII
	 * 
	 * @param      array	$out
	 * @param      boolean	$diff	Format as diff?
	 * @param      boolean	$color	Color changes?
	 * @param      int		$max	Max number of lines
	 *
	 * @return     string
	 */
	public function filterASCII($out = array(), $diff = false, $color = false, $max = 200) 
	{		
		$text = '';
		$o = 1;
		$found = 0;
		
		// Cut number of lines
		if (count($out) > $max)
		{
			$out = array_slice($out, 0, $max);
		}
		
		foreach ($out as $line) 
		{			
			$encoding = mb_detect_encoding($line);
			
			if ($encoding != "ASCII") 
			{
				break;
			}
			else 
			{
				if ($diff)
				{
					if (substr($line, 0, 2) == '@@')
					{
						$found = 1;
						continue;
					}
					
					if ($found == 0)
					{
						continue;
					}
					
					if ($color)
					{
						if (substr($line, 0, 1) == '+')
						{
							$line = trim($line, '+');
							$line = '<span class="rev-added">' . htmlentities($line) . '</span>';
						}
						if (substr($line, 0, 1) == '-')
						{
							$line = trim($line, '-');
							$line = '<span class="rev-removed">' . htmlentities($line) . '</span>';
						}
					}
					else
					{
						$line = htmlentities($line);
					}
					
					$line = trim($line, '+');
					$line = trim($line, '-');
				}
				else
				{
					$line = htmlentities($line);
				}
				
				$text.=  $line != '' ? $line . "\n" : "\n";
			}
			
			$o++;
		}

		$text = preg_replace("/\\\ No newline at end of file/", "", $text);
				
		return trim($text);
	}
	
	/**
	 * Determine if last change was a rename
	 * 
	 * @param      string	$path		repo path
	 * @param      string	$file		file path
	 * @param      string	$hash		Git hash
	 * @param      string	$since		
	 *
	 * @return     array to be parsed
	 */
	public function getRename ($path = '', $file = '', $hash = '', $since = '' ) 
	{
		$renames = $this->gitLog($path, $file, '', 'rename');
		$rename = '';
		
		$hashes = $this->getLocalFileHistory($path, $file);
		$new	= $this->getLocalFileHistory($path, $file, '', $since);
		$fetch  = 1;
		
		if (count($renames) > 0)
		{
			foreach ($hashes as $h)
			{				
				// get commit message
				if ($since && in_array($h, $new))
				{
					$message = $this->gitLog($path, $file, $h, 'message');
					
					if (!preg_match("/Moved/", $message))
					{
						$fetch = 0;
					}
				}			
				
				$abbr = substr($h, 0, 7);
				
				if (isset($renames[$abbr]) && $renames[$abbr] != $file)
				{
					$rename = $renames[$abbr];
					
					// Hidden file in local directory - treat as directory
					if (preg_match("/.gitignore/", $rename))
					{ 
						$rename = dirname($rename);
						
						// Skip home directory
						if ($rename == '.')
						{
							continue;
						}
					}
					
					return $fetch == 1 ? $rename : NULL;
				}
			}
		}
		else
		{
			return NULL;
		}
	}
	
	/**
	 * Get status for each file revision
	 *
	 * @param      array	$versions	Array of file version data 
	 * @return     array
	 */	
	public function getVersionStatus( $versions = array())
	{
		if (count($versions) == 0)
		{
			return $versions;
		}
						
		// Go through versions in reverse (from oldest to newest)
		for ($k = (count($versions) - 1); $k >= 0; $k--) 
		{			
			$current 	= $versions[$k];
			$previous 	= ($k - 1) >= 0 ? $versions[$k - 1] : NULL;
			$next 		= ($k + 1) <= (count($versions) - 1) ? $versions[$k + 1] : NULL;
			
			// Deleted?
			if ($current['commitStatus'] == 'D')
			{
				$current['change'] = JText::_('COM_PROJECTS_FILE_STATUS_DELETED');
			}
			
			// First sdded?
			if ($current['commitStatus'] == 'A' && $k == (count($versions) - 1))
			{
				$current['change'] = JText::_('COM_PROJECTS_FILE_STATUS_ADDED');
			}
								
			// Modified?
			if ($current['commitStatus'] == 'M')
			{
				if (($next && $next['local'] && $current['local'])
					|| ($next && $next['remote'] && $next['remote']) || !$next
				)
				{
					$current['change'] = JText::_('COM_PROJECTS_FILE_STATUS_MODIFIED');
				}
			}
			
			// Check renames
			if ($versions[$k]['rename'] == 1 
				&& $previous && $previous['commitStatus'] == 'A'
			)
			{
				$versions[$k - 1]['change'] = JText::_('COM_PROJECTS_FILE_STATUS_RENAMED');
				$versions[$k - 1]['commitStatus'] = 'R';
			}
			
			if (preg_match("/\bRenamed\b/", $current['message']) && $current['commitStatus'] == 'A')
			{
				$current['change'] = JText::_('COM_PROJECTS_FILE_STATUS_RENAMED');
				$current['commitStatus'] = 'R';
			}
						
			// Check restored after deletion
			if ($versions[$k]['commitStatus'] == 'D' 
				&& ($k - 1) >= 0 && $versions[$k - 1]['commitStatus'] == 'A'
				&& $versions[$k]['local'] && $versions[$k - 1]['local']
			)
			{
				$versions[$k - 1]['change'] = JText::_('COM_PROJECTS_FILE_STATUS_RESTORED');
			}
						
			if (preg_match("/" . JText::_('COM_PROJECTS_FILES_SHARE_EXPORTED') . "/", $current['message']) && $next)
			{
				$versions[$k + 1]['change']  = JText::_('COM_PROJECTS_FILE_STATUS_SENT_REMOTE');
				$versions[$k + 1]['movedTo'] = 'remote';
				$versions[$k + 1]['author']	 = $current['author'];
				$current['hide'] = 1;			
			}
			if (preg_match("/" . JText::_('COM_PROJECTS_FILES_SHARE_IMPORTED') . "/", $current['message']))
			{
				$current['change'] = JText::_('COM_PROJECTS_FILE_STATUS_SENT_LOCAL');
				$current['movedTo'] = 'local';
			}
			if ($current['remote'] && $current['commitStatus'] == 'M')
			{
				$current['change'] = JText::_('COM_PROJECTS_FILE_STATUS_MODIFIED');
			}
			
			$versions[$k] = $current;			
		}
		
		return $versions;		
	}	
}
