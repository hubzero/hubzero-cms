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
 * Migration script for adding indexes to #__media_tracking_detailed table
 **/
class Migration20190228114147MediaTracking extends Base
{
	/**
	 * List of tables
	 *
	 * @var  array
	 **/
	public static $table = '#__media_tracking_detailed';

	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists(self::$table))
		{
			if (!$this->db->tableHasKey(self::$table, 'idx_user_id'))
			{
				$query = "ALTER TABLE `" . self::$table . "` ADD INDEX `idx_user_id` (`user_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey(self::$table, 'idx_session_id'))
			{
				$query = "ALTER TABLE `" . self::$table . "` ADD INDEX `idx_session_id` (`session_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey(self::$table, 'idx_object_id'))
			{
				$query = "ALTER TABLE `" . self::$table . "` ADD INDEX `idx_object_id` (`object_id`)";
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
			if ($this->db->tableHasKey(self::$table, 'idx_user_id'))
			{
				$query = "ALTER TABLE `" . self::$table . "` DROP KEY `idx_user_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey(self::$table, 'idx_session_id'))
			{
				$query = "ALTER TABLE `" . self::$table . "` DROP KEY `idx_session_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey(self::$table, 'idx_object_id'))
			{
				$query = "ALTER TABLE `" . self::$table . "` DROP KEY `idx_object_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
