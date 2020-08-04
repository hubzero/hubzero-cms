<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Utilities;

/**
 * GIT class
 **/
class Git
{
	/**
	 * Root path of git repository
	 *
	 * @var  string
	 **/
	private $dir;

	/**
	 * Git working tree directory
	 *
	 * @var  string
	 **/
	private $workTree;

	/**
	 * Base command for all git calls
	 *
	 * @var  string
	 **/
	private $baseCmd;

	/**
	 * Get upstream branch name (when needing to compare master with upstream branch)
	 *
	 * @var  string
	 **/
	private $upstream = null;

	/**
	 * Constructor
	 *
	 * @param   string  $root    The git root directory (not including .git suffix)
	 * @param   string  $source  The upstream repository
	 * @return  void
	 **/
	public function __construct($root, $source = null)
	{
		$this->dir      = $root . DS . '.git';
		$this->workTree = $root;
		$this->baseCmd  = "git --git-dir={$this->dir} --work-tree={$this->workTree}";

		// Save upstream branch name
		if (!isset($source))
		{
			$this->upstream = $this->call('rev-parse', array('--abbrev-ref', '--symbolic-full-name', '@{u}'));
			$this->upstream = trim($this->upstream);
		}
		else
		{
			$this->upstream = trim($source);
		}
	}

	/**
	 * Just return the name of this utility
	 *
	 * @return  string
	 **/
	public function getName()
	{
		return 'GIT';
	}

	/**
	 * Return the base path of the repository
	 *
	 * @return  string
	 **/
	public function getBasePath()
	{
		return $this->workTree;
	}

	/**
	 * Get the mechanisms version identifier
	 *
	 * @return  string
	 **/
	public function getMechanismVersionName()
	{
		$name = $this->call('rev-parse', array('--abbrev-ref', 'HEAD'));
		$name = trim($name);

		return $name;
	}

	/**
	 * Get the status of the repository
	 *
	 * @return  string
	 **/
	public function status()
	{
		// Use 'porcelain' argument for consistent formatting of output
		$arguments = array('--porcelain');
		$status    = $this->call('status', $arguments);
		$response  = '';

		if (!empty($status))
		{
			$status = trim($status);
			$lines  = explode("\n", $status);

			$response = array(
				'added'     => array(),
				'modified'  => array(),
				'deleted'   => array(),
				'renamed'   => array(),
				'copied'    => array(),
				'untracked' => array(),
				'unmerged'  => array(),
				'merged'    => array()
			);
			foreach ($lines as $line)
			{
				$line  = trim($line);
				preg_match('/([A|D|M|U|R|C|?]{1,2})[ ]{1,2}([[:alnum:]_\-\.\/]*)/', $line, $parts);

				if (strlen($parts[1]) == 2 && $parts[1] != '??' && $parts[1] != 'UU')
				{
					$parts[1] = 'merged';
				}

				switch ($parts[1])
				{
					case 'A':
						$response['added'][] = $parts[2];
						break;
					case 'D':
						$response['deleted'][] = $parts[2];
						break;
					case 'M':
						$response['modified'][] = $parts[2];
						break;
					case 'R':
						$response['renamed'][] = $parts[2];
						break;
					case 'C':
						$response['copied'][] = $parts[2];
						break;
					case 'UU':
						$response['unmerged'][] = $parts[2];
						break;
					case '??':
						$response['untracked'][] = $parts[2];
						break;
					case 'merged':
						$response['merged'][] = $parts[2];
						break;
				}
			}
		}

		return $response;
	}

	/**
	 * Get the log
	 *
	 * @param   int     $length     Number of entires to return
	 * @param   int     $start      Commit number to start at
	 * @param   bool    $upcoming   Whether or not to include upcoming commits in response
	 * @param   bool    $installed  Whether or not to include installed commits in response
	 * @param   string  $search     Filter by search string
	 * @param   string  $format     Format of response
	 * @param   bool    $count      Return count of entires
	 * @return  array
	 **/
	public function log($length = null, $start = null, $upcoming = false, $installed = true, $search = null, $format = '%an: %s', $count = false)
	{
		$args = array();

		// Count trumps all, just compute and return
		if ($count)
		{
			return $this->countLogs($installed, $upcoming, $search);
		}

		if ($upcoming)
		{
			$args['upcoming'] = "HEAD..{$this->upstream}";
		}
		if (isset($length))
		{
			$args['length'] = '-' . (int)$length;
		}
		if (isset($start))
		{
			$args['skip'] = '--skip=' . (int)$start;
		}
		if (isset($format))
		{
			$args['format'] = '--pretty=format:' . escapeshellarg($format);
		}
		if (isset($search))
		{
			$args['case-insensitive'] = '-i';
			$args['search'] = '--grep=' . escapeshellarg($search);
		}

		// If upcoming is set, we have to pull those commits first
		$upcomingCount = 0;
		$upcomingTotal = 0;
		if ($upcoming)
		{
			$upcomingLog = $this->call('log', $args);

			if (isset($upcomingLog))
			{
				$upcomingLogs  = explode("\n", $upcomingLog);
				$upcomingCount = count($upcomingLogs);
			}

			$upcomingTotal = $this->countLogs(false, true, $search);
		}

		if ($upcomingCount < $length)
		{
			if (isset($args['upcoming']))
			{
				unset($args['upcoming']);
			}
			$args['length'] = '-' . ($length - $upcomingCount);
			$args['skip']   = '--skip=' . ((($start - $upcomingTotal) >= 0) ? ($start - $upcomingTotal) : 0);
			$currentLog     = $this->call('log', $args);
			$currentLogs    = (!empty($currentLog)) ? explode("\n", $currentLog) : array();
		}

		$response = array();

		if ($upcomingCount > 0)
		{
			foreach ($upcomingLogs as $entry)
			{
				$response[] = '* ' . $entry;
			}
		}
		if (isset($currentLogs) && count($currentLogs) > 0 && $installed)
		{
			foreach ($currentLogs as $entry)
			{
				$response[] = $entry;
			}
		}

		return $response;
	}

	/**
	 * Count log entries
	 *
	 * @param   bool    $installed  Whether or not to include installed entries
	 * @param   bool    $upcoming   Whether or not to include upcoming entries
	 * @param   string  $search     A search filter to apply
	 * @return  int
	 **/
	private function countLogs($installed = true, $upcoming = false, $search = null)
	{
		$total     = 0;
		$countArgs = array('--count');

		if (isset($search))
		{
			$countArgs[] = '-i';
			$countArgs[] = '--grep=' . escapeshellarg($search);
		}

		if ($installed)
		{
			$installedArgs = $countArgs;
			array_unshift($installedArgs, 'HEAD');
			$total = $this->call('rev-list', $installedArgs);
			$total = trim($total);
		}

		if ($upcoming)
		{
			$upcomingArgs = $countArgs;
			array_unshift($upcomingArgs, "HEAD..{$this->upstream}");
			$upcomingTotal = $this->call('rev-list', $upcomingArgs);
			$upcomingTotal = trim($upcomingTotal);
			$total += $upcomingTotal;
		}

		return trim($total);
	}

	/**
	 * Pull the latest updates
	 *
	 * @param   bool    $dryRun      Whether or not to do the run, or just check what's incoming
	 * @param   bool    $allowNonFf  Whether or not to allow non fast forward pulls (i.e. merges)
	 * @param   string  $source      Where this repository should pull from (this should be a valid git remote/branch)
	 * @return  string
	 **/
	public function update($dryRun = true, $allowNonFf = false, $source = null)
	{
		if (!$dryRun)
		{
			// Move to the working tree dir (git 1.8.5 has a built in option for this...but we're still generally running 1.7.x)
			chdir($this->workTree);

			$arguments = array();

			if (isset($source))
			{
				$arguments[] = $source;
			}
			else
			{
				$arguments[] = $this->upstream;
			}

			// Add fast forward only arg if applicable
			if (!$allowNonFf)
			{
				$arguments[] = '--ff-only';
			}

			// Doing some trickery here.
			// Newer versions of git prefer you to edit the auto generated message after every merge...we don't want to do this
			// There is an option to prevent that (--no-edit), but it doesn't apply to older versions of git
			// Thus we're going to use an env variable to prevent the behavior on newer versions, while not effecting older
			// versions that are currently unaware of the --no-edit option

			// Start off by seeing if there is an autoedit env var already set
			$autoedit = getenv("GIT_MERGE_AUTOEDIT");
			// Now set it to no
			putenv("GIT_MERGE_AUTOEDIT=no");

			// Now do the actual pull
			$response = $this->call('merge', $arguments);

			// Now clear the var or reset it if it had been previously set
			if ($autoedit)
			{
				putenv("GIT_MERGE_AUTOEDIT={$autoedit}");
			}
			else
			{
				putenv("GIT_MERGE_AUTOEDIT");
			}

			$response  = trim($response);
			$return    = array();

			if (substr($response, 0, 5) == 'fatal')
			{
				$return['status']  = 'fatal';
				$return['message'] = trim(substr($response, stripos($response, 'fatal') + 6));
			}
			else if (substr($response, 0, 5) == 'error')
			{
				$return['status']  = 'fatal';
				$return['message'] = trim(substr($response, stripos($response, 'error') + 6));
			}
			else if (stripos($response, 'automatic merge failed') !== false)
			{
				$return['status']  = 'fatal';
				$return['message'] = $response;
			}
			else
			{
				$return['status'] = 'success';
			}

			// Include the raw return for all calls
			$return['raw'] = $response;
		}
		else
		{
			// Be sure to fetch so we known we're up-to-date
			$this->call('fetch');
			$this->call('remote update');

			// Build arguments
			$arguments = array(
				'--pretty=format:"%an: \"%s\" (%ar)"',
				"HEAD..{$this->upstream}"
			);

			// Make call to get log differences between us and origin
			$log    = $this->call('log', $arguments);
			$log    = trim($log);
			$return = array();
			$logs   = explode("\n", $log);

			if (!empty($log) && count($logs) > 0)
			{
				foreach ($logs as $log)
				{
					$return[] = trim($log);
				}
			}
		}

		return $return;
	}

	/**
	 * Pushes the local repository to the remote destination
	 *
	 * @param   string  $ref        The ref to push from/to (the same name unless $remoteRef is provided)
	 * @param   string  $remote     The remote name to push to
	 * @param   string  $remoteRef  The remote ref if it differs in name from the local one
	 * @return  array
	 **/
	public function push($ref = 'master', $remote = 'origin', $remoteRef = null)
	{
		$ref      = (isset($remoteRef)) ? $ref . ':' . $remoteRef : $ref;
		$response = $this->call('push', array($remote, $ref));

		$response = trim($response);
		$return   = array();

		if (stripos($response, 'error: failed to push some refs') !== false)
		{
			$return['status']  = 'fatal';
			$return['message'] = $response;
		}
		else if (stripos($response, 'everything up-to-date') !== false)
		{
			$return['status']  = 'success';
			$return['message'] = $response;
		}
		else
		{
			$return['status'] = 'success';
		}

		return $return;
	}

	/**
	 * Create a rollback point for restoration in the event of a problem during the update
	 *
	 * @return  string
	 **/
	public function createRollbackPoint()
	{
		$tagname = 'cmsrollbackpoint-' . date('U');
		return $this->tag($tagname);
	}

	/**
	 * Get the latest rollback point
	 *
	 * @return  string|bool
	 **/
	public function getRollbackPoint()
	{
		$tagList = $this->call('tag');
		$tags    = explode("\n", $tagList);
		$rbp     = 0;

		if (count($tags) > 0)
		{
			foreach ($tags as $tag)
			{
				if (strstr($tag, 'cmsrollbackpoint-') !== false)
				{
					$tmp_tag = substr($tag, 17);
					if ($tmp_tag > $rbp)
					{
						$rbp = $tmp_tag;
					}
				}
			}

			if ($rbp === 0)
			{
				return false;
			}

			return $rbp;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Purge rollback points except for the latest
	 *
	 * @return  void
	 **/
	public function purgeRollbackPoints()
	{
		$tagList        = $this->call('tag');
		$tags           = explode("\n", $tagList);
		$rollbackPoints = array();

		foreach ($tags as $tag)
		{
			if (strstr($tag, 'cmsrollbackpoint-') !== false)
			{
				$rollbackPoints[] = $tag;
			}
		}

		if (count($rollbackPoints) > 1)
		{
			for ($i=0; $i < (count($rollbackPoints) - 1); $i++)
			{
				$this->call('tag', array('-d', $rollbackPoints[$i]));
			}
		}
	}

	/**
	 * Purge stashed changes
	 *
	 * @return  void
	 **/
	public function purgeStash()
	{
		$this->call('stash', array('clear'));
	}

	/**
	 * Perform rollback
	 *
	 * @param   string  $rollbackPoint  Tagname of rollback point
	 * @return  bool
	 **/
	public function rollback($rollbackPoint)
	{
		$tagname = 'cmsrollbackpoint-'.$rollbackPoint;

		// Make sure the tag exists first
		if (substr($this->call('show', array($tagname)), 0, 5 == 'fatal'))
		{
			return false;
		}

		// Do a hard reset - this is destructive!
		$this->call('reset', array('--hard', $tagname));

		return true;
	}

	/**
	 * Create a tag
	 *
	 * @param   string  $tagname  The tag name to create
	 * @return  string
	 **/
	public function tag($tagname)
	{
		$arguments = array($tagname);

		return $this->call('tag', $arguments);
	}

	/**
	 * Check to see if the repo is clean, at least as far as this mechanism is concerned
	 *
	 * @return  bool
	 **/
	public function isClean()
	{
		$status   = $this->status();
		$eligible = true;

		if (!empty($status) && is_array($status))
		{
			foreach ($status as $type => $files)
			{
				if ($type != 'untracked' && !empty($files))
				{
					$eligible = false;
					break;
				}
			}
		}

		return $eligible;
	}

	/**
	 * Stash local changes
	 *
	 * @return
	 **/
	public function stash()
	{
		$response = $this->call('stash');

		return $response;
	}

	/**
	 * Clone Repo
	 *
	 * @return
	 **/
	public function cloneRepo($source)
	{
		$response = $this->call('clone', array($source));
		ddie($response);
		return $response;
	}

	/**
	 * Call a git command
	 *
	 * @param   string  $cmd   Git command being called
	 * @param   array   $args  Arguments for the specific command
	 * @return  string
	 **/
	private function call($cmd, $arguments = array())
	{
		$command = "{$this->baseCmd} {$cmd}" . ((!empty($arguments)) ? ' ' . implode(' ', $arguments) : '') . ' 2>&1';
		$response = shell_exec($command);

		return $response;
	}
}
