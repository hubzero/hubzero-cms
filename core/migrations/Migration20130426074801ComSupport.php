<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for add watching table
 **/
class Migration20130426074801ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableExists('#__support_watching'))
		{
			$query .= "CREATE TABLE `#__support_watching` (
							`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
							`ticket_id` int(11) NOT NULL DEFAULT '0',
							`user_id` int(11) NOT NULL DEFAULT '0',
							PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "";

		if (!$this->db->tableHasKey('#__support_watching', 'idx_ticket_id'))
		{
			$query .= "ALTER TABLE `#__support_watching` ADD INDEX `idx_ticket_id` (`ticket_id`);";
		}

		if (!$this->db->tableHasKey('#__support_watching', 'idx_user_id'))
		{
			$query .= "ALTER TABLE `#__support_watching` ADD INDEX `idx_user_id` (`user_id`);";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "";

		if ($this->db->tableExists('#__support_watching'))
		{
			$query .= "DROP TABLE `#__support_watching`";
		}

		if ($this->db->tableHasKey('#__support_watching', 'idx_ticket_id'))
		{
			$query .= "ALTER TABLE DROP INDEX `idx_ticket_id`;";
		}

		if ($this->db->tableHasKey('#__support_watching', 'idx_user_id'))
		{
			$query .= "ALTER TABLE DROP INDEX `idx_user_id`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}