<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming and changing value format of the column to hub standards
 **/
class Migration20160809152100ComRedirect extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__redirect_links', 'updated_date'))
		{
			if ($this->db->tableHasKey('#__redirect_links', 'idx_link_updated'))
			{
				$query = "ALTER TABLE `#__redirect_links` DROP KEY `idx_link_updated`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "ALTER TABLE `#__redirect_links` DROP COLUMN `updated_date`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__redirect_links', 'modified_date'))
		{
			$query = "ALTER TABLE `#__redirect_links` ADD `modified_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__redirect_links', 'idx_modified_date'))
			{
				$query = "ALTER TABLE `#__redirect_links` ADD INDEX `idx_modified_date` (`modified_date`)";
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
		if ($this->db->tableHasField('#__redirect_links', 'modified_date'))
		{
			if ($this->db->tableHasKey('#__redirect_links', 'idx_modified_date'))
			{
				$query = "ALTER TABLE `#__redirect_links` DROP KEY `idx_modified_date`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "ALTER TABLE `#__redirect_links` DROP COLUMN `modified_date`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__redirect_links', 'updated_date'))
		{
			$query = "ALTER TABLE `#__redirect_links` ADD `updated_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__redirect_links', 'idx_link_updated'))
			{
				$query = "ALTER TABLE `#__redirect_links` ADD INDEX `idx_link_updated` (`updated_date`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}

