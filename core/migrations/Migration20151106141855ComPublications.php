<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding repository_contact field to authors table
 **/
class Migration20151106141855ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__publication_authors', 'repository_contact'))
		{
			$query = "ALTER TABLE `#__publication_authors` ADD COLUMN `repository_contact` TINYINT(2) NOT NULL DEFAULT 0;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__publication_authors', 'repository_contact'))
		{
			$query = "ALTER TABLE `#__publication_authors` DROP COLUMN `repository_contact`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}