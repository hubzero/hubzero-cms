<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indices and setting default field value
 **/
class Migration20131113143500ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_acl_acos'))
		{
			$query = "ALTER TABLE `#__support_acl_acos`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `model` `model` VARCHAR(100)  NOT NULL  DEFAULT '',
					CHANGE `foreign_key` `foreign_key` INT(11)  NOT NULL  DEFAULT '0'
			;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_acl_aros'))
		{
			$query = "ALTER TABLE `#__support_acl_aros`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `foreign_key` `foreign_key` INT(11)  NOT NULL  DEFAULT '0',
					CHANGE `alias` `alias` VARCHAR(255)  NOT NULL  DEFAULT '',
					CHANGE `model` `model` VARCHAR(100)  NOT NULL  DEFAULT ''
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__support_acl_aros', 'idx_model_foreign_key'))
			{
				$query = "ALTER TABLE `#__support_acl_aros` ADD INDEX `idx_model_foreign_key` (`model`, `foreign_key`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__support_acl_aros_acos'))
		{
			$query = "ALTER TABLE `#__support_acl_aros_acos`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `aro_id` `aro_id` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `aco_id` `aco_id` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `action_read` `action_read` TINYINT(3)  NOT NULL  DEFAULT '0',
					CHANGE `action_create` `action_create` TINYINT(3)  NOT NULL  DEFAULT '0',
					CHANGE `action_update` `action_update` TINYINT(3)  NOT NULL  DEFAULT '0',
					CHANGE `action_delete` `action_delete` TINYINT(3)  NOT NULL  DEFAULT '0';
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__support_acl_aros_acos', 'idx_aco_id'))
			{
				$query = "ALTER TABLE `#__support_acl_aros_acos` ADD INDEX `idx_aco_id` (`aco_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__support_acl_aros_acos', 'idx_aro_id'))
			{
				$query = "ALTER TABLE `#__support_acl_aros_acos` ADD INDEX `idx_aro_id` (`aro_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__support_attachments'))
		{
			$query = "ALTER TABLE `#__support_attachments`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `ticket` `ticket` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `filename` `filename` VARCHAR(255)  DEFAULT '',
					CHANGE `description` `description` VARCHAR(255)  NOT NULL  DEFAULT ''
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__support_attachments', 'idx_ticket'))
			{
				$query = "ALTER TABLE `#__support_attachments` ADD INDEX `idx_ticket` (`ticket`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__support_comments'))
		{
			$query = "ALTER TABLE `#__support_comments`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `ticket` `ticket` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `created_by` `created_by` VARCHAR(50)  NOT NULL  DEFAULT '',
					CHANGE `comment` `comment` TEXT  NOT NULL,
					CHANGE `changelog` `changelog` TEXT  NOT NULL
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__support_comments', 'idx_ticket'))
			{
				$query = "ALTER TABLE `#__support_comments` ADD INDEX `idx_ticket` (`ticket`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__support_comments', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__support_comments` ADD INDEX `idx_created_by` (`created_by`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__support_messages'))
		{
			$query = "ALTER TABLE `#__support_messages`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `title` `title` VARCHAR(250)  NOT NULL  DEFAULT '',
					CHANGE `message` `message` TEXT  NOT NULL
			;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_queries'))
		{
			$query = "ALTER TABLE `#__support_queries`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `title` `title` VARCHAR(250)  NOT NULL  DEFAULT '',
					CHANGE `conditions` `conditions` TEXT  NOT NULL,
					CHANGE `query` `query` TEXT  NOT NULL,
					CHANGE `user_id` `user_id` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `sort` `sort` VARCHAR(100)  NOT NULL  DEFAULT '',
					CHANGE `sort_dir` `sort_dir` VARCHAR(100)  NOT NULL  DEFAULT ''
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__support_queries', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__support_queries` ADD INDEX `idx_user_id` (`user_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__support_queries', 'idx_iscore'))
			{
				$query = "ALTER TABLE `#__support_queries` ADD INDEX `idx_iscore` (`iscore`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__support_resolutions'))
		{
			$query = "ALTER TABLE `#__support_resolutions`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `title` `title` VARCHAR(100)  NOT NULL  DEFAULT '',
					CHANGE `alias` `alias` VARCHAR(100)  NOT NULL  DEFAULT ''
			;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__support_tickets'))
		{
			$query = "ALTER TABLE `#__support_tickets`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__support_tickets', 'idx_owner'))
			{
				$query = "ALTER TABLE `#__support_tickets` ADD INDEX `idx_owner` (`owner`);";
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
		if ($this->db->tableExists('#__support_acl_acos'))
		{
			if ($this->db->tableHasKey('#__support_acl_acos', 'idx_model_foreign_key'))
			{
				$query = "ALTER TABLE `#__support_acl_acos` DROP INDEX `idx_model_foreign_key`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__support_acl_aros'))
		{
			if ($this->db->tableHasKey('#__support_acl_aros', 'idx_model_foreign_key'))
			{
				$query = "ALTER TABLE `#__support_acl_aros` DROP INDEX `idx_model_foreign_key`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__support_acl_aros_acos'))
		{
			if ($this->db->tableHasKey('#__support_acl_aros_acos', 'idx_aco_id'))
			{
				$query = "ALTER TABLE `#__support_acl_aros_acos` DROP INDEX `idx_aco_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__support_acl_aros_acos', 'idx_aro_id'))
			{
				$query = "ALTER TABLE `#__support_acl_aros_acos` DROP INDEX `idx_aro_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__support_attachments'))
		{
			if ($this->db->tableHasKey('#__support_attachments', 'idx_ticket'))
			{
				$query = "ALTER TABLE `#__support_attachments` DROP INDEX `idx_ticket`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__support_comments'))
		{
			if ($this->db->tableHasKey('#__support_comments', 'idx_ticket'))
			{
				$query = "ALTER TABLE `#__support_comments` DROP INDEX `idx_ticket`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__support_comments', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__support_comments` DROP INDEX `idx_created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__support_queries'))
		{
			if ($this->db->tableHasKey('#__support_queries', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__support_queries` DROP INDEX `idx_user_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__support_queries', 'idx_iscore'))
			{
				$query = "ALTER TABLE `#__support_queries` DROP INDEX `idx_iscore`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__support_tickets'))
		{
			if ($this->db->tableHasKey('#__support_tickets', 'idx_owner'))
			{
				$query = "ALTER TABLE `#__support_tickets` DROP INDEX `idx_owner`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}