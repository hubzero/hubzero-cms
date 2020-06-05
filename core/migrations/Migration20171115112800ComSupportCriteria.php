<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to create the `#__support_criteria` table
 **/
class Migration20171115112800ComSupportCriteria extends Base
{
	static $tableName = '#__support_criteria';

	public function up()
	{
		$tableName = self::$tableName;

		if (!$this->db->tableExists($tableName))
		{
			$createTable = "CREATE TABLE `{$tableName}` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`description` varchar(255) DEFAULT NULL,
				`query` varchar(255) DEFAULT NULL,
				`created` timestamp NULL DEFAULT NULL,
				`modified` timestamp NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($createTable);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$tableName = self::$tableName;

		if ($this->db->tableExists($tableName))
		{
			$dropTable = "DROP TABLE {$tableName};";
			$this->db->setQuery($dropTable);
			$this->db->query();
		}
	}
}
