<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to create #__publication_versions_bundles table
 **/
class Migration20180413135700ComPublicationsBundle extends Base
{

	static $tableName = '#__publication_versions_bundles';

	/*
	 * Up
	 */
	public function up()
	{
		$tableName = self::$tableName;

		$createTable = "CREATE TABLE `$tableName` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`publication_id` int(11) unsigned NOT NULL,
			`publication_version_id` int(11) unsigned NOT NULL,
			`created` timestamp NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

		if (!$this->db->tableExists($tableName))
		{
			$this->db->setQuery($createTable);
			$this->db->query();
		}
	}

	/*
	 * Down
	 */
	public function down()
	{
		$tableName = self::$tableName;

		if ($this->db->tableExists($tableName))
		{
			$dropTable = "DROP TABLE $tableName;";

			$this->db->setQuery($dropTable);
			$this->db->query();
		}
	}

}
