<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding target_date field to support tickets
 **/
class Migration20160830165511ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__support_tickets', 'target_date'))
		{
			$query = "ALTER TABLE `#__support_tickets` ADD COLUMN `target_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__support_tickets', 'target_date'))
		{
			$query = "ALTER TABLE `#__support_tickets` DROP COLUMN `target_date`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}