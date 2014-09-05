<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for courses badges cleanup
 **/
class Migration20131111200831ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__courses_offering_badges') && !$this->db->tableExists('#__courses_offering_section_badges'))
		{
			$query = "RENAME TABLE `#__courses_offering_badges` TO `#__courses_offering_section_badges`";
			$this->db->setQuery($query);
			$this->db->query();
		}
		else if ($this->db->tableExists('#__courses_offering_badges') && $this->db->tableExists('#__courses_offering_section_badges'))
		{
			$query = "DROP TABLE `#__courses_offering_badges`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__courses_offerings', 'badge_id'))
		{
			$query = "ALTER TABLE `#__courses_offerings` DROP `badge_id`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__courses_offering_section_badges', 'offering_id') && !$this->db->tableHasField('#__courses_offering_section_badges', 'section_id'))
		{
			$query = "ALTER TABLE `#__courses_offering_section_badges` CHANGE `offering_id` `section_id` INT(11) NOT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__courses_offering_section_badges', 'provider_name') && $this->db->tableHasField('#__courses_offering_section_badges', 'section_id'))
		{
			$query = "ALTER TABLE `#__courses_offering_section_badges` ADD `provider_name` VARCHAR(255) NOT NULL DEFAULT 'passport' AFTER `section_id`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__courses_offering_section_badges', 'provider_badge_id') && $this->db->tableHasField('#__courses_offering_section_badges', 'badge_id'))
		{
			$query = "ALTER TABLE `#__courses_offering_section_badges` CHANGE `badge_id` `provider_badge_id` INT(11) NOT NULL";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__courses_offering_section_badges', 'criteria_id') && $this->db->tableHasField('#__courses_offering_section_badges', 'img_url'))
		{
			$query = "ALTER TABLE `#__courses_offering_section_badges` ADD `criteria_id` INT(11) NOT NULL AFTER `img_url`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__courses_offering_section_badges', 'published') && $this->db->tableHasField('#__courses_offering_section_badges', 'section_id'))
		{
			$query = "ALTER TABLE `#__courses_offering_section_badges` ADD `published` INT(1) NOT NULL DEFAULT '0' AFTER `section_id`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__courses_member_badges', 'section_badge_id') && $this->db->tableHasField('#__courses_member_badges', 'member_id'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` ADD `section_badge_id` INT(11) NOT NULL AFTER `member_id`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__courses_member_badges', 'claim_url'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` DROP `claim_url`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__courses_member_badges', 'validation_token') && $this->db->tableHasField('#__courses_member_badges', 'action_on'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` ADD `validation_token` VARCHAR(20) NULL DEFAULT NULL AFTER `action_on`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__courses_member_badges', 'criteria_id') && $this->db->tableHasField('#__courses_member_badges', 'validation_token'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` ADD `criteria_id` INT(11)  NULL  DEFAULT NULL  AFTER `validation_token`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_offering_section_badge_criteria'))
		{
			$query = "CREATE TABLE `#__courses_offering_section_badge_criteria` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`text` text NOT NULL,
						`section_badge_id` int(11) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}