<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding more details to asset views table
 **/
class Migration20130725184342ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__courses_asset_views', 'course_id') && $this->db->tableHasField('#__courses_asset_views', 'asset_id'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` ADD `course_id` INT(11)  NULL  DEFAULT NULL  AFTER `asset_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__courses_asset_views', 'ip') && $this->db->tableHasField('#__courses_asset_views', 'viewed_by'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` ADD `ip` VARCHAR(15)  CHARACTER SET utf8  NULL  DEFAULT NULL  AFTER `viewed_by`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__courses_asset_views', 'url') && $this->db->tableHasField('#__courses_asset_views', 'ip'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` ADD `url` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT NULL  AFTER `ip`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__courses_asset_views', 'referrer') && $this->db->tableHasField('#__courses_asset_views', 'url'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` ADD `referrer` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT NULL  AFTER `url`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__courses_asset_views', 'user_agent_string') && $this->db->tableHasField('#__courses_asset_views', 'referrer'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` ADD `user_agent_string` VARCHAR(255)  CHARACTER SET utf8  NULL  DEFAULT NULL  AFTER `referrer`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__courses_asset_views', 'session_id') && $this->db->tableHasField('#__courses_asset_views', 'user_agent_string'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` ADD `session_id` VARCHAR(200)  CHARACTER SET utf8  NULL  DEFAULT NULL  AFTER `user_agent_string`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__courses_asset_views', 'course_id'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` DROP `course_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__courses_asset_views', 'ip'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` DROP `ip`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__courses_asset_views', 'url'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` DROP `url`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__courses_asset_views', 'referrer'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` DROP `referrer`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__courses_asset_views', 'user_agent_string'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` DROP `user_agent_string`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__courses_asset_views', 'session_id'))
		{
			$query = "ALTER TABLE `#__courses_asset_views` DROP `session_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}