<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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