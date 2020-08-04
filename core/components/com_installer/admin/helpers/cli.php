<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Installer\Admin\Helpers;

/**
 * CLI helper class
 */
class Cli
{
	/**
	 * Get the version of the code in the repository
	 *
	 * @return  string
	 **/
	public static function version()
	{
		static $version = null;

		if (!isset($version))
		{
			$version = self::call('', 'repository', array('--version'));
		}

	}

	/**
	 * Get the repository management mechanism
	 *
	 * @return  string
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
	 * @return  string
	 **/
	public static function status()
	{
		return self::call('status');
	}

	/**
	 * Get repository log
	 *
	 * @param   int     $length     the number of messages to include in log
	 * @param   int     $start      the commit number to start at
	 * @param   string  $search     the search query
	 * @param   bool    $upcoming   whether or not to include upcoming commits
	 * @param   bool    $installed  whether or not to include installed commits
	 * @param   bool    $count      whether to return count of entries
	 * @param   string  $source     the repo source to get logs from
	 * @return  string
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
	 * @param   bool    $dryRun       whether or not to do a dry run
	 * @param   bool    $allowNonFf   whether or not to allow non fast forward pulls
	 * @param   string  $source       the repo source to update from
	 * @param   string  $autoPushRef  the ref to auto push to after an update
	 * @return  string
	 **/
	public static function update($dryRun=true, $allowNonFf=false, $source=null, $autoPushRef=null)
	{
		$args = array();
		if (!$dryRun)
		{
			$args[] = '-f';
			$args[] = '--install-packages';
		}

		if ($allowNonFf)
		{
			$args[] = '--allow-non-ff';
		}

		if (isset($source))
		{
			$args[] = '--source=' . escapeshellarg($source);
		}

		if (isset($autoPushRef))
		{
			$args[] = '--git-auto-push-ref=' . escapeshellarg($autoPushRef);
		}

		return self::call('update', 'repository', $args);
	}

	/**
	 * Rollback to last checkpoint
	 *
	 * @return  string
	 **/
	public static function rollback()
	{
		return self::call('rollback', 'repository', array('-f'));
	}

	/**
	 * Migrate database
	 *
	 * @param   bool    $dryRun       dry run - basically like returning a status
	 * @param   bool    $ignoreDates  ignore dates
	 * @param   string  $file         specific file to run
	 * @param   string  $dir          direction to run (up or down)
	 * @param   string  $folder       restrict migrations to a specific directory
	 * @return  string
	 **/
	public static function migration($dryRun=true, $ignoreDates=false, $file=null, $dir='up', $folder=null)
	{
		$args = array();

		if (in_array($dir, array('up', 'down')))
		{
			$args[] = '-d=' . $dir;
		}
		if (!$dryRun)
		{
			$args[] = '-f';
		}
		if ($ignoreDates)
		{
			$args[] = '-i';
		}
		if ($folder)
		{
			$args[] = '-r=' . $folder;
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
	 * @param   string  $cmd   the command to run
	 * @param   string  $task  the muse task to run (only repository and migration will work)
	 * @param   array   $args  the command arguments
	 * @return  string
	 **/
	private static function call($cmd, $task='repository', $args=array())
	{
		static $user = null;
		static $processUser = null;

		if (!isset($user))
		{
			$user = \Component::params('com_installer')->get('system_user', 'hubadmin');
		}

		if (!isset($processUser))
		{
			$processUser = posix_geteuid();
			$processUser = posix_getpwuid($processUser);
			$processUser = $processUser['name'];
		}

		$sudo = ($processUser != $user) ? '/usr/bin/sudo -u ' . $user . ' ' : '';

		$cmd = $sudo . PATH_ROOT . DS . 'muse' . ' ' . $task . ' ' . $cmd . ' ' . ((!empty($args)) ? implode(' ', $args) : '') . ' --format=json';

		return shell_exec($cmd);
	}
}
