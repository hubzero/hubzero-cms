<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Helpers;

/**
 * Utility methods
 */
class Utilities
{
	/**
	 * Returns mkAIP script path
	 *
	 * @return		 string
	 */
	public static function getMkAipBase()
	{
		return PATH_APP . DS . 'mkAIP/cli/mkaip/bin/mkaip';
	}

	/**
	 * Checks if mkAIP is used
	 *
	 * @return	 boolean
	 */
	public static function archiveOn()
	{
		$mkaip = self::getMkAipBase();
		if (file_exists($mkaip))
		{
			return true;
		}

		return false;
	}

	/**
	 * Run mkAIP
	 *
	 * @param			 object $row			Publication version object
	 * @return		 void
	 */
	public static function mkAip($row)
	{
		$mkaip = self::getMkAipBase();

		// Create OAIS Archival Information Package
		if (file_exists($mkaip))
		{
			$mkaipOutput = 'mkaip-' . str_replace( '/', '__', $row->doi) . '.out';

			/**
			 * Changes for receiving error notification from mkAIP script
			 * Exit status code 0 represents mkAIP script completes its execution and without any exception.
			 * Exit status code 1 represents some exception is thrown out.
			 **/
			$cmd = '/usr/bin/php ' . $mkaip . ' ' .$row->doi . ' ' . '2>&1 > ' . "/www/tmp/$mkaipOutput";
			exec($cmd, $output, $exitCode);

			if ($exitCode == 0)
			{
				 return true;
			}
		}
		return false;
	}
}
