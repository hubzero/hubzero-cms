<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding 'delivered' and 'digest' columns to  table #__item_watch
 **/
class Migration20150722080000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__item_watch') && !$this->db->tableHasField('#__item_watch', 'digest'))
		{
			$query = "ALTER TABLE `#__item_watch` ADD COLUMN `digest` INT(11) NOT NULL DEFAULT 0 AFTER `state`;";
			/* 0 = on-action delivery, 1 = daily digest, 2 = weekly digest, 3 = monthly digest */
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__item_watch') && !$this->db->tableHasField('#__item_watch', 'delivered'))
		{
			$query = "ALTER TABLE `#__item_watch` ADD COLUMN `delivered` DATETIME NULL AFTER `digest`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__item_watch') && $this->db->tableHasField('#__item_watch', 'digest'))
		{
			$query = "ALTER TABLE `#__item_watch` DROP COLUMN `digest`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__item_watch') && $this->db->tableHasField('#__item_watch', 'delivered'))
		{
			$query = "ALTER TABLE `#__item_watch` DROP COLUMN `delivered`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}