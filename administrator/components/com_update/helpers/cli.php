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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
* CLI helper class
*/
class cli
{
	/**
	 * Get the version of the code in the repository
	 *
	 * @return (string) - version identifier
	 **/
	public static function version()
	{
		static $version = null;

		if (!isset($version))
		{
			$version = self::call('', 'repository', array('--version'));
		}

		return $version;
	}

	/**
	 * Get the repository management mechanism
	 *
	 * @return (string) - mechanism name
	 **/
	public static function mechanism()
	{
		static $mechanism = null;

		if (!isset($mechanism))
		{
			$mechanism = self::call('', 'repository', array('--mechanism'));
		}

		return $mechanism;
	}

	/**
	 * Check repository status
	 *
	 * @return (string) - mechanism status
	 **/
	public static function status()
	{
		return self::call('status');
	}

	/**
	 * Get repository log
	 *
	 * @param  int    $length the number of messages to include in log
	 * @param  int    $start the commit number to start at
	 * @param  string $search the search query
	 * @param  bool   $upcoming whether or not to include upcoming commits
	 * @param  bool   $installed whether or not to include installed commits
	 * @param  bool   $count whether to return count of entries
	 * @param  string $source the repo source to get logs from
	 * @return string
	 **/
	public static function log($length=null, $start=null, $search=null, $upcoming=false, $installed=true, $count=false, $source=null)
	{
		$args = array();

		if (isset($length))
		{
			$args[] = '--length=' . (int)$length;
		}
		if (isset($start))
		{
			$args[] = '--start=' . (int)$start;
		}
		if (isset($search))
		{
			$args[] = '--search=' . escapeshellarg($search);
		}
		if (isset($upcoming) && $upcoming)
		{
			$args[] = '--include-upcoming';
		}
		if (isset($installed) && !$installed)
		{
			$args[] = '--exclude-installed';
		}
		if ($count)
		{
			$args[] = '--count';
		}
		if (isset($source))
		{
			$args[] = '--source=' . escapeshellarg($source);
		}

		return self::call('log', 'repository', $args);
	}

	/**
	 * Do update
	 *
	 * In dry run mode, only list changes that would come in
	 *
	 * @param  bool   $dryRun whether or not to do a dry run
	 * @param  bool   $allowNonFf whether or not to allow non fast forward pulls
	 * @param  string $source the repo source to update from
	 * @return string
	 **/
	public static function update($dryRun=true, $allowNonFf=false, $source=null)
	{
		$args = array();
		if (!$dryRun)
		{
			$args[] = '-f';
		}

		if ($allowNonFf)
		{
			$args[] = '--allow-non-ff';
		}

		if (isset($source))
		{
			$args[] = '--source=' . escapeshellarg($source);
		}

		return self::call('update', 'repository', $args);
	}

	/**
	 * Rollback to last checkpoint
	 *
	 * @return (string) - response
	 **/
	public static function rollback()
	{
		return self::call('rollback', 'repository', array('-f'));
	}

	/**
	 * Migrate db
	 *
	 * @param  (bool)   - dry run
	 * @param  (bool)   - ignore dates
	 * @param  (string) - specific file to run
	 * @return (string) - response
	 **/
	public static function migration($dryRun=true, $ignoreDates=false, $file=null)
	{
		$args = array();

		if (!$dryRun)
		{
			$args[] = '-f';
		}
		if ($ignoreDates)
		{
			$args[] = '-i';
		}
		if (isset($file))
		{
			$args[] = '--file=' . escapeshellarg($file);
		}

		return self::call('run', 'migration', $args);
	}

	/**
	 * Make actual muse calls
	 *
	 * @param  (string) - command to run
	 * @param  (array)  - command arguments
	 * @return (string) - command output
	 **/
	private static function call($cmd, $task='repository', $args=array())
	{
		static $user = null;
		static $processUser = null;

		if (!isset($user))
		{
			$user = \JComponentHelper::getParams('com_update')->get('system_user', 'hubadmin');
		}

		if (!isset($processUser))
		{
			$processUser = posix_geteuid();
			$processUser = posix_getpwuid($processUser);
			$processUser = $processUser['name'];
		}

		$sudo = ($processUser != $user) ? '/usr/bin/sudo -u ' . $user . ' ' : '';

		$cmd = $sudo . JPATH_ROOT . DS . 'cli' . DS . 'muse.php' . ' ' . $task . ' ' . $cmd . ' ' . ((!empty($args)) ? implode(' ', $args) : '') . ' --format=json';

		return shell_exec($cmd);
	}
}
