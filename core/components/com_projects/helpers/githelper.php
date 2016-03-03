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

namespace Components\Projects\Helpers;

use Hubzero\Base\Object;

/**
 * Projects Git helper class
 */
class Git extends Object
{
	/**
	 * Git path
	 *
	 * @var string
	 */
	private $_gitpath 		= NULL;

	/**
	 * User ID
	 *
	 * @var integer
	 */
	private $_uid 			= NULL;

	/**
	 * Full path to Git repo
	 *
	 * @var string
	 */
	private $_path 		    = NULL;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct($path = NULL)
	{
		// Get component configs
		$configs = Component::params('com_projects');

		// Set path to git
		$this->_gitpath = $configs->get('gitpath', '/opt/local/bin/git');

		// Set repo path
		$this->_path = $path;

		// Set acting user
		$this->_uid = User::get('id');
	}

	/**
	 * Init Git repository
	 *
	 * @param      string	$path	Repo path
	 *
	 * @return     string
	 */
	public function iniGit( $path = NULL)
	{
		if (!$path)
		{
			$path = $this->_path;
		}
		if (!$path || !is_dir($path))
		{
			return false;
		}
		if (!$this->_path)
		{
			$this->_path = $path;
		}

		// Build .git repo
		$gitRepoBase = $path . DS . '.git';

		// Need to create .git repository if not yet there
		if (!is_dir($gitRepoBase))
		{
			$this->callGit('init');
		}

		return true;
	}

	/**
	 * Make Git call
	 *
	 * @param      string	$call
	 *
	 * @return     array to be parsed
	 */
	public function callGit ($call = '')
	{
		if (!$this->_path || !is_dir($this->_path) || !$call)
		{
			return false;
		}

		// cd into repo
		chdir($this->_path);

		// exec call
		return $this->_exec($this->_gitpath . ' ' . $call);
	}

	/**
	 * Exec call
	 *
	 * @param      string	$call
	 *
	 * @return     array to be parsed
	 */
	protected function _exec($call = '')
	{
		if (!$call)
		{
			return false;
		}

		$result = array();

		// exec call
		exec($call, $result);
		return $result;
	}

	/**
	 * Run Git status
	 *
	 * @param      string	$path
	 * @param      string	$status
	 *
	 * @return     string
	 */
	public function gitStatus ($status = NULL)
	{
		// Clean up
		$this->cleanup();

		// Get Git status
		$out = $this->callGit('status');

		if (!empty($out) && count($out) > 0 && $out[0] != '')
		{
			foreach ($out as $line)
			{
				$status.=  '<br />' . $line;
			}
		}

		return $status;
	}

	/**
	 * Clean up junk system files
	 *
	 * @param      string	$path
	 * @param      string	$status
	 *
	 * @return     string
	 */
	public function cleanup()
	{
		$this->callGit('rm .DS_Store');
		return true;
	}

	/**
	 * Git commit
	 *
	 * @param      string	$commitMsg
	 * @param      string	$author
	 * @param      string	$date
	 *
	 * @return     void
	 */
	public function gitCommit ($commitMsg = '', $author = '', $date = '' )
	{
		// Check if there is anything to commit
		$changes = $this->callGit('diff --cached --name-only');
		if (empty($changes))
		{
			return false;
		}

		$date = $date ? ' --date="' . $date . '"' : '';
		$author = $author ? $author : $this->getGitAuthor();

		$this->callGit('commit -a -m "' . $commitMsg . '" --author="' . $author . '"' . $date);

		return true;
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
			$profile = \Hubzero\User\Profile::getInstance($this->_uid);

			$name    = $profile->get('name');
			$email   = $profile->get('email');
		}

		$author  = escapeshellarg($name . ' <' . $email . '> ');

		return $author;
	}

	/**
	 * Add/update local repo item
	 *
	 * @param      string	$item		file path
	 * @param      string	&$commitMsg
	 * @param      boolean	$new
	 *
	 * @return     void
	 */
	public function gitAdd ($item = '', &$commitMsg = '', $new = true )
	{
		// Check for repo
		if (!$this->_path || !is_dir($this->_path))
		{
			return false;
		}
		if (!$item)
		{
			return false;
		}

		// Make Git call
		$out = $this->callGit(' add ' . escapeshellarg($item));

		$commitMsg .= $new == true ? 'Added' : 'Updated';
		$commitMsg .= ' file '.escapeshellarg($item) . "\n";

		return true;
	}

	/**
	 * Delete item from local repo
	 *
	 * @param      string	$item		file path
	 * @param      string	$type		'file' or 'folder'
	 * @param      string	&$commitMsg
	 *
	 * @return     array to be parsed
	 */
	public function gitDelete ($item = '', $type = 'file', &$commitMsg = '' )
	{
		// Check for repo
		if (!$this->_path || !is_dir($this->_path))
		{
			return false;
		}
		if (!$item)
		{
			return false;
		}

		$deleted = 0;

		if ($type == 'folder')
		{
			if ($item != '' && is_dir($this->_path . DS . $item))
			{
				// Make Git call
				$out = $this->callGit('rm -r ' . escapeshellarg($item));

				$deleted++;
				$commitMsg .= 'Deleted folder '.escapeshellarg($item) . "\n";
			}
		}
		elseif ($type == 'file')
		{
			if ($item != '' && file_exists($this->_path . DS . $item))
			{
				// Make Git call
				$out = $this->callGit('rm ' . escapeshellarg($item));

				$deleted++;
				$commitMsg .= 'Deleted file '.escapeshellarg($item) . "\n";
			}
		}

		return $deleted;
	}

	/**
	 * Move/rename item
	 *
	 * @param      string	$from		From file path
	 * @param      string	$where		To file path
	 * @param      string	$type		'file' or 'folder'
	 * @param      string	&$commitMsg
	 *
	 * @return     integer
	 */
	public function gitMove ($from = '', $where = '', $type = 'file', &$commitMsg = '' )
	{
		// Check for repo
		if (!$this->_path || !is_dir($this->_path))
		{
			return false;
		}
		if (!$from || !$where)
		{
			return false;
		}

		$moved = 0;

		if ($type == 'folder' && $from != '' && $from != $where  && is_dir($this->_path . DS . $from))
		{
			// Make Git call
			$out = $this->callGit(' mv ' . escapeshellarg($from)
				. ' ' . escapeshellarg($where) . ' -f');

			$commitMsg .= 'Moved folder ' . escapeshellarg($from) . ' to ' . escapeshellarg($where) . "\n";
			$moved++;
		}
		elseif ($type == 'file' && $from != '' && $from != $where  && file_exists($this->_path . DS . $from))
		{
			// Make Git call
			$out = $this->callGit(' mv ' . escapeshellarg($from)
				. ' ' . escapeshellarg($where) . ' -f');

			$commitMsg .= 'Moved file ' . escapeshellarg($from) . ' to ' . escapeshellarg($where) . "\n";
			$moved++;
		}

		return $moved;
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
	public function gitDiff ($old = array(), $new = array())
	{
		// Check for repo
		if (!$this->_path || !is_dir($this->_path))
		{
			return false;
		}
		if (!isset($old['hash']) || !isset($new['hash']) || !isset($new['fpath']))
		{
			return false;
		}

		$file = $new['fpath'] == $old['fpath'] ? ' -- ' . escapeshellarg($new['fpath']) : '';

		$oCount = $this->callGit('diff --name-status ' . $old['hash'] . '^ ');
		$nCount = $this->callGit('diff --name-status ' . $new['hash'] . '^ ');

		// Get file content
		if (count($oCount) <= 2 && count($nCount) <= 2)
		{
			$out = $this->callGit(' diff -M -C ' . $old['hash'] . ' ' . $new['hash']);
		}
		else
		{
			$out = $this->callGit(' diff -M -C ' . $old['hash'] . ':' . $old['fpath'] . ' '
			. $new['hash'] . ':' . $new['fpath']);
		}

		return $out;
	}

	/**
	 * Get file content
	 *
	 * @param      string  	$file		file path
	 * @param      string  	$hash		Git hash
	 * @param      string  	$target		Output content to path
	 *
	 * @return     void
	 */
	public function getContent($file = '', $hash = '', $target = '')
	{
		if (!$file || !$hash)
		{
			return false;
		}
		$call = 'show  ' . $hash . ':' . escapeshellarg($file);
		$call.= $target ? ' > ' . escapeshellarg($target) : '';

		// Make Git call
		$this->callGit($call);

		return true;
	}

	/**
	 * Show commit log detail
	 *
	 * @param      string  	$file		file path
	 * @param      string  	$hash		Git hash
	 * @param      string  	$return
	 *
	 * @return     string
	 */
	public function gitLog ($file = '', $hash = '', $return = 'date')
	{
		if (!$this->_path || !is_dir($this->_path))
		{
			return false;
		}

		$what = '';

		// Set exec command for retrieving different commit information
		switch ( $return )
		{
			case 'combined':
				$exec = ' log --diff-filter=AMR --pretty=format:"%ci||%an||%ae||%H||%f" --name-only --max-count=1 ';
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

		// Make Git call
		$out = $this->callGit($exec . ' ' . $what);

		// Parse returned array of data
		return $this->parseLog($out, $return);
	}

	/**
	 * Show logs in subdir
	 *
	 * @param      string  	$file		file path
	 * @param      integer  $limit		limit results
	 *
	 * @return     string
	 */
	public function gitLogAll ($subdir = '', $limit = 500)
	{
		if (!$this->_path || !is_dir($this->_path))
		{
			return false;
		}

		$exec = ' log --diff-filter=AMR --pretty=format:">>>%ci||%an||%ae||%H||%f" --name-only';
		$exec .= $limit ? ' --max-count=' . $limit : '';
		$exec .= $subdir ? ' ' . escapeshellarg($subdir) : '';

		// Make Git call
		$out = $this->callGit($exec);

		$collector = array();
		$entry 	   = array();
		$i = 0;

		foreach ($out as $line)
		{
			if (substr($line, 0, 3) == '>>>')
			{
				$line = str_replace('>>>', '', $line);
				$data = explode("||", $line);

				$entry = array();
				$entry['date']  	= $data[0];
				$entry['author'] 	= $data[1];
				$entry['email'] 	= $data[2];
				$entry['hash'] 		= $data[3];
				$entry['message'] 	= substr($data[4], 0, 100);
			}
			elseif ($line != '' && !isset($collector[$line]))
			{
				$collector[$line] = $entry;
			}
		}

		return $collector;
	}

	/**
	 * Parse response
	 *
	 * @param      array  	$out		Array of data to parse
	 * @param      string  	$return
	 *
	 * @return     mixed
	 */
	public function parseLog ($out = array(), $return = 'date')
	{
		if (empty($out))
		{
			return NULL;
		}

		$response = NULL;
		switch ($return)
		{
			case 'combined':
				$arr  = explode("\t", $out[0]);
				$data = explode("||", $arr[0]);

				$entry = array();
				$entry['date']  	= $data[0];
				$entry['author'] 	= $data[1];
				$entry['email'] 	= $data[2];
				$entry['hash'] 		= $data[3];
				$entry['message'] 	= substr($data[4], 0, 100);
				$response = $entry;
				break;

			case 'content':
			case 'blob':
				$response = $out;
				break;

			case 'date':
				$arr = explode("\t", $out[0]);
				$timestamp = strtotime($arr[0]);
				$response = date ('m/d/Y g:i A', $timestamp);
				break;

			case 'num':
				$response = count($out);
				break;

			case 'namestatus':
				$n = substr($out[0], 0, 1);
				$response = $n == 'f' ? 'A' : $n;
				break;

			case 'rename':
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

					$response = array_combine($hashes, $names);
				}
				break;

			default:
				$arr = explode("\t", $out[0]);
				$response = $arr[0];
				break;
		}

		return $response;
	}

	/**
	 * Git checkout
	 *
	 * @param      string	$item
	 * @param      string	$hash
	 *
	 * @return     boolean
	 */
	public function gitCheckout ($item = '' , $hash = '')
	{
		// Check for repo
		if (!$this->_path || !is_dir($this->_path))
		{
			return false;
		}
		if (!$item)
		{
			return false;
		}

		// Make Git call
		$this->callGit(' checkout ' . $hash . ' -- ' . escapeshellarg($item));

		return true;
	}

	/**
	 * Lists files in the repository
	 *
	 * @param   string  $subdir  Local directory path
	 * @return  array
	 */
	public function getFiles($subdir = '')
	{
		// Make sure subdir has a trailing slash
		$subdir = (!empty($subdir)) ? trim($subdir, DS) . DS : '';

		// Get Git status
		$out = $this->callGit('ls-tree --name-only master ' . escapeshellarg($subdir));

		return (empty($out) || substr($out[0], 0, 5) == 'fatal') ? array() : $out;
	}

	/**
	 * Lists the directories in the repository
	 *
	 * @param   string  $subdir  Local directory path
	 * @return  array
	 */
	public function getDirectories($subdir = '')
	{
		// Make sure subdir has a trailing slash
		$subdir = (!empty($subdir)) ? trim($subdir, DS) . DS : '';

		// Get Git status
		$out = $this->callGit('ls-tree --name-only -dr master ' . escapeshellarg($subdir));

		return (empty($out) || substr($out[0], 0, 5) == 'fatal') ? array() : $out;
	}

	/**
	 * Lists the untracked files from the repository
	 *
	 * @param   string  $subdir  The local/relative directory path to explore
	 * @return  array
	 **/
	public function getUntrackedFiles($subdir = '')
	{
		$cmd  = 'cd ' . DS . trim($this->_path, DS) . DS . trim($subdir, DS) . ' && ';
		$cmd .= $this->_gitpath . ' clean -nd | grep -v ".' . DS . '." | cut -c 14-';

		$results = $this->_exec($cmd);
		$return  = [];

		foreach ($results as $result)
		{
			$return[] = trim($subdir, DS) . DS . $result;
		}

		return $return;
	}

	/**
	 * Lists the untracked directories from the repository (which really isn't a thing in git)
	 *
	 * @param   string  $subdir  The local/relative directory path to explore
	 * @return  array
	 **/
	public function getUntrackedDirectories($subdir = '')
	{
		$cmd  = 'cd ' . DS . trim($this->_path, DS) . DS . trim($subdir, DS) . ' && ';
		$cmd .= $this->_gitpath . ' clean -nd | grep ".' . DS . '$" | cut -c 14-';

		$results = $this->_exec($cmd);
		$return  = [];

		foreach ($results as $result)
		{
			$return[] = trim($subdir, DS) . DS . $result;
		}

		return $return;
	}

	/**
	 * List deleted files
	 *
	 * @return     array to be parsed
	 */
	public function listDeleted()
	{
		// Call Git
		$out = $this->callGit('log --diff-filter=D --pretty=format:">>>%ct||%an||%ae||%H||%f" --name-only ');

		$files = array();
		if (empty($out) || count($out) == 0)
		{
			return $files;
		}

		$collector = array();
		foreach ($out as $line)
		{
			if (substr($line, 0, 3) == '>>>')
			{
				$line = str_replace('>>>', '', $line);
				$data = explode("||", $line);

				$entry = array();
				$entry['date']  	= $data[0];
				$entry['author'] 	= $data[1];
				$entry['email'] 	= $data[2];
				$entry['hash'] 		= $data[3];
				$entry['message']	= $data[4];
			}
			elseif (isset($entry) && $line != '' && !isset($collector[$line]))
			{
				$collector[$line] = $entry;
			}
		}

		if (empty($collector))
		{
			return false;
		}

		// Go through hashes and get file names
		foreach ($collector as $filename => $gitData)
		{
			// File is still there - skip
			if (is_file( $this->_path . DS . $filename))
			{
				continue;
			}

			if (basename($filename) == '.gitignore')
			{
				continue;
			}

			// File renamed/moved - skip
			if (strstr(strtolower($gitData['message']), 'moved-file ')
				|| strstr(strtolower($gitData['message']), 'moved-folder '))
			{
				continue;
			}

			$files[$filename] = array(
				'hash'			=> $gitData['hash'],
				'author'		=> $gitData['author'],
				'date'			=> date('c', $gitData['date']),
				'size'			=> NULL,
				'message'		=> NULL
			);
		}

		return $files;
	}

	/**
	 * Get changes for sync
	 *
	 * @return     array
	 */
	public function getChanges ($localPath = '', $synced = '',
		$localDir = '', &$localRenames, $connections)
	{
		// Collector array
		$locals = array();

		// Initial sync
		if ($synced == 1)
		{
			$files = $this->callGit('ls-files --full-name ' . escapeshellarg($localDir));
			$files = (empty($files) || substr($files[0], 0, 5) == 'fatal') ? array() : $files;

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
					$time = strtotime(date('c', time() )); // Important! needs to be local time, NOT UTC

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
						'modified'		=> gmdate('Y-m-d H:i:s', $time),
						'synced'		=> NULL,
						'fullPath' 		=> $localPath . DS . $filename,
						'mimeType'		=> Filesystem::mimetype($localPath . DS . $filename),
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
			$since 			= $synced != 1 ? ' --since="' . $synced . '"' : '';
			$where 			= $localDir ? '  --all -- ' . escapeshellarg($localDir) . ' ' : ' --all ';
			$changes 		= $this->callGit( 'rev-list ' . $where . $since);

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
					$time   = $this->gitLog('', $hash, 'timestamp');
					$author = $this->gitLog('', $hash, 'author');

					// Get filename and change
					$fileinfo = $this->callGit('diff --name-status ' . $hash . '^ ' . $hash );

					// First commit
					if (!empty($fileinfo) && !empty($fileinfo[0]) && substr($fileinfo[0], 0, 5) == 'fatal')
					{
						$fileinfo = $this->callGit('log --pretty=oneline --name-status --root' );

						if (!empty($fileinfo))
						{
							// Remove first line
							array_shift($fileinfo);
						}
					}
					if (empty($fileinfo))
					{
						continue;
					}

					// Go through files
					foreach ($fileinfo as $line)
					{
						$n = substr($line, 0, 1);

						if ($n == 'f')
						{
							// First file in repository
							$finfo = $this->callGit('log --pretty=oneline --name-status ' . $hash );
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
							$rename = $this->getRename($filename, $hash, $since);

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

						$conn 		= isset($connections['paths']) ? $connections['paths'] : NULL;
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

						$mimeType = $type == 'file' ? Filesystem::mimetype($localPath . DS . $filename) : NULL;

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
				//array_multisort($timestamps, SORT_DESC, $locals, SORT_STRING);
			}

		}

		return $locals;
	}

	/**
	 * Make Git recognize empty folder
	 *
	 * @param      string	$path
	 * @param      string	$dir
	 *
	 * @return     array to be parsed
	 */
	public function makeEmptyFolder ( $dir = '', $commit = true, $commitMsg = '' )
	{
		// Check for repo
		if (!$this->_path || !is_dir($this->_path))
		{
			return false;
		}

		// cd into repo
		chdir($this->_path);

		// Create an empty file
		$this->_exec('touch ' . escapeshellarg($dir) . '/.gitignore ');

		// Git add
		$this->gitAdd($dir, $commitMsg );

		if ($commit == false)
		{
			return true;
		}

		// Commit change
		if ($this->gitCommit(Lang::txt('PLG_PROJECTS_FILES_CREATED_DIRECTORY') . '  ' . escapeshellarg($dir)))
		{
			return true;
		}

		return false;
	}

	/**
	 * Get local file history
	 *
	 * @param      string	$file
	 * @param      string	$rev
	 * @param      string	$since
	 *
	 * @return     array of hashes
	 */
	public function getLocalFileHistory($file = '', $rev = '', $since = '')
	{
		$hashes = array();

		// Get local file history
		$out = $this->callGit('log --follow --pretty=format:%H ' . $since . ' ' . $rev . ' '
			. escapeshellarg($file));

		if (empty($out) || count($out) == 0)
		{
			return $hashes;
		}

		// Get hashes
		foreach ($out as $line)
		{
			if (preg_match("/[a-zA-Z0-9]/", $line) && strlen($line) == 40)
			{
				$hashes[]  = $line;
			}
		}

		return $hashes;
	}

	/**
	 * Get details on file history
	 *
	 * @param      string	$local_path			file path
	 * @param      array 	&$versions			Versions collector array
	 * @param      array 	&$timestamps		Collector array
	 * @param      integer	$original			Source file?
	 *
	 * @return     array of version info
	 */
	public function sortLocalRevisions($local_path = '',
		&$versions = array(), &$timestamps = array(), $original = 0 )
	{
		// Get local file history
		$hashes = $this->getLocalFileHistory($local_path, '--');

		// Binary
		$binary = \Components\Projects\Helpers\Html::isBinary($this->_path . DS . $local_path);

		// Get info for each commit
		if (!empty($hashes))
		{
			$h = 1;

			// Get all names for this file
			$renames 		= $this->gitLog($local_path, '', 'rename');
			$currentName	= $local_path;
			$rename			= 0;

			foreach ($hashes as $hash)
			{
				$order 	= $h == 1 ? 'first' : '';
				$order 	= $h == count($hashes) ? 'last' : $order;

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

				$gitData 	= $this->gitLog($name, $hash, 'combined');
				$date		= isset($gitData['date']) ? $gitData['date'] : NULL;
				$author 	= isset($gitData['author']) ? $gitData['author'] : NULL;
				$email 		= isset($gitData['email']) ? $gitData['email'] : NULL;
				$message 	= $this->gitLog($name, $hash, 'message');
				$content	= $binary ? NULL : $this->gitLog($name, $hash, 'content');

				// SFTP?
				if (strpos($message, '[SFTP]') !== false)
				{
					$profile = \Hubzero\User\Profile::getInstance( trim($author) );
					if ($profile)
					{
						$author = $profile->get('name');
						$email = $profile->get('email');
					}
				}

				$revision = array(
					'date' 			=> $date,
					'author' 		=> $author,
					'email'			=> $email,
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
					'commitStatus'	=> $this->gitLog($name, $hash, 'namestatus')
				);

				if (in_array($revision['commitStatus'], array('A', 'M')))
				{
					$revision['size'] = \Hubzero\Utility\Number::formatBytes($this->gitLog($name, $hash, 'size'));
				}

				// Exctract file content for certain statuses
				if (in_array($revision['commitStatus'], array('A', 'M', 'R')) && $content)
				{
					$revision['content'] = self::filterASCII($content, false, false, 10000);
				}

				$versions[] 	= $revision;
				$timestamps[]	= strtotime($date);
				$h++;
			}
		}
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
	public static function filterASCII($out = array(), $diff = false, $color = false, $max = 200)
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
	 * @param      string	$file		file path
	 * @param      string	$hash		Git hash
	 * @param      string	$since
	 *
	 * @return     array to be parsed
	 */
	public function getRename ($file = '', $hash = '', $since = '' )
	{
		$renames = $this->gitLog($file, '', 'rename');
		$rename = '';

		$hashes = $this->getLocalFileHistory($file);
		$new	= $this->getLocalFileHistory($file, '', $since);
		$fetch  = 1;

		if (count($renames) > 0)
		{
			foreach ($hashes as $h)
			{
				// get commit message
				if ($since && in_array($h, $new))
				{
					$message = $this->gitLog($file, $h, 'message');

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
	 * Parse status for each file revision
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
				$current['change'] = Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_DELETED');
			}

			// First sdded?
			if ($current['commitStatus'] == 'A' && $k == (count($versions) - 1))
			{
				$current['change'] = Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_ADDED');
			}

			// Modified?
			if ($current['commitStatus'] == 'M')
			{
				if (($next && $next['local'] && $current['local'])
					|| ($next && $next['remote'] && $next['remote']) || !$next
				)
				{
					$current['change'] = Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_MODIFIED');
				}
			}

			// Check renames
			if ($versions[$k]['rename'] == 1
				&& $previous && $previous['commitStatus'] == 'A'
			)
			{
				if ($versions[$k - 1]['size'] != $versions[$k]['size'])
				{
					$versions[$k - 1]['change'] = Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_RENAMED_AND_MODIFIED');
				}
				else
				{
					$versions[$k - 1]['change'] = Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_RENAMED');
				}
				$versions[$k - 1]['commitStatus'] = 'R';
			}

			if (preg_match("/\bRenamed\b/i", $current['message']) && $current['commitStatus'] == 'A')
			{
				$current['change'] = Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_RENAMED');
				$current['commitStatus'] = 'R';
			}

			// Check restored after deletion
			if ($versions[$k]['commitStatus'] == 'D'
				&& ($k - 1) >= 0 && $versions[$k - 1]['commitStatus'] == 'A'
				&& $versions[$k]['local'] && $versions[$k - 1]['local']
			)
			{
				$versions[$k - 1]['change'] = Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_RESTORED');
			}

			if (preg_match("/" . Lang::txt('PLG_PROJECTS_FILES_FILES_SHARE_EXPORTED') . "/", $current['message']) && $next)
			{
				$versions[$k + 1]['change']  = Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_SENT_REMOTE');
				$versions[$k + 1]['movedTo'] = 'remote';
				$versions[$k + 1]['author']	 = $current['author'];
				$current['hide'] = 1;
			}
			if (preg_match("/" . Lang::txt('PLG_PROJECTS_FILES_FILES_SHARE_IMPORTED') . "/", $current['message']))
			{
				$current['change'] = Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_SENT_LOCAL');
				$current['movedTo'] = 'local';
			}
			if ($current['remote'] && $current['commitStatus'] == 'M')
			{
				$current['change'] = Lang::txt('PLG_PROJECTS_FILES_FILE_STATUS_MODIFIED');
			}

			$versions[$k] = $current;
		}

		return $versions;
	}

	/**
	 * Show text content
	 *
	 * @param      string  	$fpath		file name or commit hash
	 *
	 * @return     string
	 */
	public function showTextContent($fpath = '', $max = 10000)
	{
		if (!$fpath)
		{
			return false;
		}

		$content = '';

		// Get non-binary object content
		$out = $this->callGit(' show  HEAD:' . escapeshellarg($fpath));

		// Reformat text content
		if (count($out) > 0)
		{
			// Cut number of lines
			if (count($out) > $max)
			{
				$out = array_slice($out, 0, $max);
			}

			$content = self::filterASCII($out, false, false, $max);
		}

		return $content;
	}
}
