<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
		$tz_dat_file = JPATH_ROOT . DS . 'site' . DS . 'protected' . DS . 'timezone.dat';

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
			$return = array(
				'success' => false,
				'message' => "Insufficient privileges to update timezone tables."
			);
			return $return;
		}

		$this->db->select('mysql');

		// Run command to generate SQL
		$sql = shell_exec("/usr/bin/mysql_tzinfo_to_sql {$zoneinfo} 2>/dev/null");
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
				$this->db->query();
			}
		}

		$content  = '';
		$content .= $my_mtime . "\n";
		$content .= \JFactory::getDate($my_mtime)->format('D M d H:i:s Y') . "\n";
		$content .= $my_filename . "\n";

		file_put_contents($tz_dat_file, $content);
	}
}
