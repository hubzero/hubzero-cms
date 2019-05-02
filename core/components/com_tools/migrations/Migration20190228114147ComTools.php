<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to #__recent_tools table
 **/
class Migration20190228114147ComTools extends Base
{
	/**
	 * List of tables
	 *
	 * @var  array
	 **/
	public static $table = '#__recent_tools';

	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists(self::$table))
		{
			if (!$this->db->tableHasKey(self::$table, 'idx_uid'))
			{
				$query = "ALTER TABLE `" . self::$table . "` ADD INDEX `idx_uid` (`uid`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey(self::$table, 'idx_tool'))
			{
				$query = "ALTER TABLE `" . self::$table . "` ADD INDEX `idx_tool` (`tool`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists(self::$table))
		{
			if ($this->db->tableHasKey(self::$table, 'idx_uid'))
			{
				$query = "ALTER TABLE `" . self::$table . "` DROP KEY `idx_uid`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey(self::$table, 'idx_tool'))
			{
				$query = "ALTER TABLE `" . self::$table . "` DROP KEY `idx_tool`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
