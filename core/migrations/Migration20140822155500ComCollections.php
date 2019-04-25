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
 * Migration script for adding indexes on #__collections_following
 **/
class Migration20140822155500ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__collections_following'))
		{
			if (!$this->db->tableHasKey('#__collections_following', 'idx_follower_type_follower_id'))
			{
				$query = "ALTER TABLE `#__collections_following` ADD INDEX `idx_follower_type_follower_id` (`follower_type`, `follower_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__collections_following', 'idx_following_type_following_id'))
			{
				$query = "ALTER TABLE `#__collections_following` ADD INDEX `idx_following_type_following_id` (`following_type`, `following_id`);";
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
		if ($this->db->tableExists('#__collections_following'))
		{
			if ($this->db->tableHasKey('#__collections_following', 'idx_following_type_following_id'))
			{
				$query = "ALTER TABLE `#__collections_following` DROP INDEX `idx_following_type_following_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__collections_following', 'idx_follower_type_follower_id'))
			{
				$query = "ALTER TABLE `#__collections_following` DROP INDEX `idx_follower_type_follower_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
