<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;
use Hubzero\Facades\Date;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Hook to update timezone database
 **/
class UpdateTimezoneDatabase extends Base
{
	/**
	 * Hook options
	 *
	 * @var array
	 **/
	protected $options = array('timing' => 'onBeforeMigrate');

	/**
	 * Execute hook
	 *
	 * @return bool
	 **/
	public function fire()
	{
		$my_mtime    = 0;
		$my_filename = null;
		$zoneinfo    = '/usr/share/zoneinfo';
		$tz_dat_file = PATH_APP . DS . 'site' . DS . 'protected' . DS . 'timezone.dat';

		$files = array_diff(scandir($zoneinfo), array('.', '..'));

		if (count($files) > 0)
		{
			foreach ($files as $file)
			{
				$filename = $zoneinfo . DS . $file;

				if (is_file($filename))
				{
					$file_mtime = filemtime($filename);
					if ($file_mtime > $my_mtime)
					{
						$my_mtime    = $file_mtime;
						$my_filename = $filename;
					}
				}
			}
		}

		if (is_file($tz_dat_file))
		{
			$tz_dat = file_get_contents($tz_dat_file);
			$lines  = explode("\n", $tz_dat);
		}

		$last_mtime = 0;

		if (isset($lines[0]) && is_numeric($lines[0]))
		{
			$last_mtime = $lines[0];
		}

		// Only run if files have been updated since last run
		if ($last_mtime >= $my_mtime)
		{
			return false;
		}

		// Connecting to mysql database, need elevated permissions
		if (!$this->runAsRoot())
		{
			// The tables may have been loaded another way, by hand or by
			// MariaDB's own container image; only their absence matters
			if ($this->timezoneTablesLoaded())
			{
				return false;
			}

			$return = array(
				'success' => false,
				'message' => "Insufficient privileges to update timezone tables."
			);
			return $return;
		}

		// MariaDB 10.5 renamed the converter; older servers have only the old name
		$converter = null;
		foreach (array('/usr/bin/mariadb-tzinfo-to-sql', '/usr/bin/mysql_tzinfo_to_sql') as $path)
		{
			if (is_executable($path))
			{
				$converter = $path;
				break;
			}
		}

		if (!$converter)
		{
			return array(
				'success' => false,
				'message' => "No mariadb-tzinfo-to-sql or mysql_tzinfo_to_sql to load the timezone tables with."
			);
		}

		$this->db->select('mysql');

		// Run command to generate SQL
		$sql = shell_exec($converter . " {$zoneinfo} 2>/dev/null");
		$sql = explode(";", $sql);

		if (count($sql) > 0)
		{
			foreach ($sql as $s)
			{
				$s = trim($s);

				if ($s == ''
					|| strpos($s, 'Riyadh') !== false
					|| strpos($s, 'zone.tab') !== false
					|| strpos($s, 'iso3166.tab') !== false
					|| strpos($s, 'Local time zone must be set') !== false)
				{
					continue;
				}

				$this->db->setQuery($s);

				try
				{
					$this->db->query();
				}
				catch (Exception $e)
				{
					$return = array(
						'success' => false,
						'message' => $e->getMessage()
					);

					return $return;
				}
			}
		}

		$content  = '';
		$content .= $my_mtime . "\n";
		$content .= Date::of($my_mtime)->format('D M d H:i:s Y') . "\n";
		$content .= $my_filename . "\n";

		if (is_dir(PATH_APP . DS . 'site' . DS . 'protected'))
		{
			file_put_contents($tz_dat_file, $content);
		}
	}

	/**
	 * Whether the database server can already convert between named zones
	 *
	 * CONVERT_TZ() returns NULL for a named zone when the server's time zone
	 * tables are empty, and needs no privileges to ask.
	 *
	 * @return  bool
	 **/
	private function timezoneTablesLoaded()
	{
		try
		{
			$this->db->setQuery("SELECT CONVERT_TZ('2000-01-01 00:00:00', 'UTC', 'America/New_York')");
			return $this->db->loadResult() !== null;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
}
