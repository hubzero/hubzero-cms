<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indices and setting default field value for #__abuse_reports
 **/
class Migration20140818160800ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__abuse_reports'))
		{
			$query = "ALTER TABLE `#__abuse_reports`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `created_by` `created_by` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `reviewed_by` `reviewed_by` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `referenceid` `referenceid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `state` `state` TINYINT(2)  NOT NULL  DEFAULT '0'
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__abuse_reports', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__abuse_reports` ADD INDEX `idx_created_by` (`created_by`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__abuse_reports', 'idx_reviewed_by'))
			{
				$query = "ALTER TABLE `#__abuse_reports` ADD INDEX `idx_reviewed_by` (`reviewed_by`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__abuse_reports', 'idx_state'))
			{
				$query = "ALTER TABLE `#__abuse_reports` ADD INDEX `idx_state` (`state`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__abuse_reports', 'idx_category_referenceid'))
			{
				$query = "ALTER TABLE `#__abuse_reports` ADD INDEX `idx_category_referenceid` (`category`, `referenceid`);";
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
		if ($this->db->tableExists('#__abuse_reports'))
		{
			if ($this->db->tableHasKey('#__abuse_reports', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__abuse_reports` DROP INDEX `idx_created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__abuse_reports', 'idx_reviewed_by'))
			{
				$query = "ALTER TABLE `#__abuse_reports` DROP INDEX `idx_reviewed_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__abuse_reports', 'idx_state'))
			{
				$query = "ALTER TABLE `#__abuse_reports` DROP INDEX `idx_state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__abuse_reports', 'idx_reference'))
			{
				$query = "ALTER TABLE `#__abuse_reports` DROP INDEX `idx_reference`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}