<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to create jos_supportstats_hubs table
 **/
class Migration20171218135400ComSupportstatsHubs extends Base
{
	static $table = '#__supportstats_hubs';

	public function up()
	{
		$table = self::$table;

		$query = "CREATE TABLE `{$table}` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(20) NOT NULL DEFAULT 'Title',
			`base_url` varchar(255) NOT NULL,
			`api_url` varchar(255) NOT NULL,
			`created` timestamp NULL DEFAULT NULL,
			`modified` timestamp NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		if (!$this->db->tableExists($table))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		$table = self::$table;

		if ($this->db->tableExists($table))
		{
			$query = "DROP TABLE {$table};";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

}
