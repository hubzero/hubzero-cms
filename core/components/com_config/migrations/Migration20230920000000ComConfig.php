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
 * Migration script for managing hub secret via com_config
 **/
class Migration20230920000000ComConfig extends Base
{
	// TODO: finalize this name
	static $tableName = "#__config";

	/**
	 * Up
	 **/
	public function up()
	{
		$tableName = self::$tableName;

		// Ensure the needed database table exists:
		if (!$this->db->tableExists($tableName))
		{
			$this->log("Table not found, creating...");

			$query = "CREATE TABLE IF NOT EXISTS $tableName (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`scope` VARCHAR(255) NOT NULL,
				`key` VARCHAR(255) NOT NULL,
				`value` CHAR(32) UNIQUE NULL,
				`created` DATETIME NOT NULL,
				`updated` DATETIME NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id_UNIQUE` (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		// Ensure a hub secret record is in the table:
		if ($this->db->tableExists($tableName))
		{
			$query = "SELECT count(*) FROM $tableName WHERE `scope`='hub' and `key`='secret';";
			$this->db->setQuery($query);
			$count = $this->db->loadResult();

			if ($count == 0)
			{
				$this->log("No Hub Secret record found, inserting...");

				// Create 32-character secret and insert in record:
				$secretLength = 32;
				$newSecret = \Hubzero\User\Password::genRandomPassword($secretLength);

				// Timestamp should be saved as UTC:
				$createDateUtc = Date::of('now')->toSql();

				$query = "INSERT INTO $tableName
					(`scope`, `key`, `value`, `created`) VALUES
					('hub', 'secret', '$newSecret', '$createDateUtc');";
				$this->db->setQuery($query);
				$this->db->query();
			}
			elseif ($count > 0)
			{
				// Table should contain only a single hub secret record.
				$this->log("Warning: more than one Hub Secret found");
			}
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
			$query = "DROP TABLE IF EXISTS $tableName;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
