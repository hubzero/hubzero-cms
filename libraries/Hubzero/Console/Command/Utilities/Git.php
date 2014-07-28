<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console\Command\Utilities;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * GIT class
 **/
class Git
{
	/**
	 * Root path of git repository
	 *
	 * @var string
	 **/
	private $dir;

	/**
	 * Git working tree directory
	 *
	 * @var string
	 **/
	private $workTree;

	/**
	 * Base command for all git calls
	 *
	 * @var string
	 **/
	private $baseCmd;

	/**
	 * Get upstream branch name (when needing to compare master with upstream branch)
	 *
	 * @var string
	 **/
	private $upstream = null;

	/**
	 * Constructor
	 *
	 * @param  (string) $root - git root directory (not including .git suffix)
	 * @return void
	 **/
	public function __construct($root)
	{
		$this->dir      = $root . DS . '.git';
		$this->workTree = $root;
		$this->baseCmd  = "git --git-dir={$this->dir} --work-tree={$this->workTree}";

		// Save upstream branch name
		$this->upstream = $this->call('rev-parse', array('--abbrev-ref', '--symbolic-full-name', '@{u}'));
		$this->upstream = trim($this->upstream);
	}

	/**
	 * Just return the name of this utility
	 *
	 * @return (string) - name of utility (as it would be displayed to the user)
	 **/
	public function getName()
	{
		return 'GIT';
	}

	/**
	 * Return the base path of the repository
	 *
	 * @return (string) - path
	 **/
	public function getBasePath()
	{
		return $this->workTree;
	}

	/**
	 * Get the mechanisms version identifier
	 *
	 * @return (string) - version name
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
	 * @return (string) $response
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
				'untracked' => array(),
				'merged'    => array()
			);
			foreach ($lines as $line)
			{
				$line  = trim($line);
				preg_match('/([A|D|M|?]{1,2})[ ]{1,2}([[:alnum:]_\.\/]*)/', $line, $parts);

				if (strlen($parts[1]) == 2 && $parts[1] != '??')
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
	 * @param  (int)    $length    - number of entires to return
	 * @param  (int)    $start     - commit number to start at
	 * @param  (bool)   $upcoming  - whether or not to include upcoming commits in response
	 * @param  (bool)   $installed - whether or not to include installed commits in response
	 * @param  (string) $search    - filter by search string
	 * @param  (string) $format    - format of response
	 * @param  (bool)   $count     - return count of entires
	 * @return (array)  $response
	 **/
	public function log($length=null, $start=null, $upcoming=false, $installed=true, $search=null, $format='%an: %s', $count=false)
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
			$args['length'] = '-'.(int)$length;
		}
		if (isset($start))
		{
			$args['skip'] = '--skip='.(int)$start;
		}
		if (isset($format))
		{
			$args['format'] = '--pretty=format:"'.$format.'"';
		}
		if (isset($search))
		{
			$args['case-insensitive'] = '-i';
			$args['search'] = '--grep="'.$search.'"';
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
	 * @return (int) count of logs
	 **/
	private function countLogs($installed=true, $upcoming=false, $search=null)
	{
		$total     = 0;
		$countArgs = array('--count');

		if (isset($search))
		{
			$countArgs[] = '-i';
			$countArgs[] = '--grep="'.$search.'"';
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
	 * You should probably call isEligibleForUpdate beforehand, althought it isn't required.
	 *
	 * @param  (bool)   $dryRun     - whether or not to do the run, or just check what's incoming
	 * @param  (bool)   $allowNonFf - whether or not to allow non fast forward pulls (i.e. merges)
	 * @return (string) $return     - response text to return
	 **/
	public function update($dryRun=true, $allowNonFf=false)
	{
		if (!$dryRun)
		{
			// Move to the working tree dir (git 1.8.5 has a built in option for this...but we're still generally running 1.7.x)
			chdir($this->workTree);

			// Add base arguments...shhh, quiet hours
			$arguments = array('-q');

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
			$response = $this->call('pull', $arguments);

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

			if (empty($response))
			{
				$return['status'] = 'success';
			}
			else if (stripos($response, 'fatal') !== false)
			{
				$return['status']  = 'fatal';
				$return['message'] = trim(substr($response, stripos($response, 'fatal') + 6));
			}
			else
			{
				$return['status'] = 'unknown';
			}

			// Include the raw return for all calls
			$return['raw'] = $response;
		}
		else
		{
			// Be sure to fetch so we known we're up-to-date
			$this->call('fetch');

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
	 * Create a rollback point for restoration in the event of a problem during the update
	 *
	 * @return (string) $response - response from tag method
	 **/
	public function createRollbackPoint()
	{
		$tagname = 'cmsrollbackpoint-' . date('U');
		return $this->tag($tagname);
	}

	/**
	 * Get the latest rollback point
	 *
	 * @return (string) $rollbackPoint
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
	 * @return void
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
	 * Perform rollback
	 *
	 * @param  (string) $rollbackPoint - tagname of rollback point
	 * @return void
	 **/
	public function rollback($rollbackPoint)
	{
		$tagname = 'cmsrollbackpoint-'.$rollbackPoint;

		// Make sure the tag exists first
		if (stripos($this->call('show', array($tagname)), 'fatal'))
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
	 * @return (string) $response
	 **/
	public function tag($tagname)
	{
		$arguments = array($tagname);

		return $this->call('tag', $arguments);
	}

	/**
	 * Check to see if the repo is clear and eligible for an update
	 *
	 * @return boolean
	 **/
	public function isEligibleForUpdate()
	{
		$status = $this->status();

		return (empty($status)) ? true : false;
	}

	/**
	 * Call a git command
	 *
	 * @param  (string) $cmd  - git command being called
	 * @param  (array)  $args - arguments for the specific command
	 * @return (string) $response - command response text
	 **/
	private function call($cmd, $arguments=array())
	{
		$command  = "{$this->baseCmd} {$cmd}" . ((!empty($arguments)) ? ' ' . implode(' ', $arguments) : '') . ' 2>&1';
		$response = shell_exec($command);

		return $response;
	}
}