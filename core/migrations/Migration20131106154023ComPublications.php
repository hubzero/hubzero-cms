<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding publication version fulltext key
 **/
class Migration20131106154023ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasKey('#__publication_versions', 'idx_fulltxt_title_description_abstract'))
		{
			$query = "ALTER TABLE `#__publication_versions` ADD FULLTEXT KEY `idx_fulltxt_title_description_abstract` (`title`, `description`, `abstract`);";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasKey('#__publication_versions', 'idx_fulltxt_title_description_abstract'))
		{
			$query = "ALTER TABLE `#__publication_versions` DROP INDEX `idx_fulltxt_title_description_abstract`;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}