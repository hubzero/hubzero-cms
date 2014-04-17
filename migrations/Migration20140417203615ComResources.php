<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140417203615ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__media_tracking'))
		{
			if (!$this->db->tableHasKey('#__media_tracking', 'idx_user_id'))
			{
				$query  = "ALTER TABLE `#__media_tracking` ADD INDEX `idx_user_id` (`user_id`);";
				$query .= "ALTER TABLE `#__media_tracking` ADD INDEX `idx_session_id` (`session_id`);";
				$query .= "ALTER TABLE `#__media_tracking` ADD INDEX `idx_object_id` (`object_id`);";
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
		if ($this->db->tableExists('#__media_tracking'))
		{
			if ($this->db->tableHasKey('#__media_tracking', 'idx_user_id'))
			{
				$query  = "ALTER TABLE `#__media_tracking` DROP INDEX `idx_user_id`;";
				$query .= "ALTER TABLE `#__media_tracking` DROP INDEX `idx_session_id`;";
				$query .= "ALTER TABLE `#__media_tracking` DROP INDEX `idx_object_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}