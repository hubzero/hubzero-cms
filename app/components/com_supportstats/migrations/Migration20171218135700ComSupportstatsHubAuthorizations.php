<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to create jos_supportstats_hub_authorizations table
 **/
class Migration20171218135700ComSupportstatsHubAuthorizations extends Base
{
	static $table = '#__supportstats_hub_authorizations';

	public function up()
	{
		$table = self::$table;

		$query = "CREATE TABLE `{$table}` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` int(11) unsigned NOT NULL,
			`hub_id` int(11) unsigned NOT NULL,
			`api_request_state` varchar(255),
			`escaped_api_request_state` varchar(255),
			`access_token` varchar(100),
			`access_token_expiration` timestamp NULL DEFAULT NULL,
			`token_type` varchar(100) NULL DEFAULT NULL,
			`refresh_token` varchar(100) NULL DEFAULT NULL,
			`refresh_token_expiration` timestamp NULL DEFAULT NULL,
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
