<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding fulltext key to collections tables
 **/
class Migration20131106152023ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "ALTER TABLE `#__collections_items` ADD FULLTEXT KEY `idx_fulltxt_title_description` (`title`, `description`);";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "ALTER TABLE `#__collections_posts` ADD FULLTEXT KEY `idx_fulltxt_description` (`description`);";

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
		$query = "ALTER TABLE `#__collections_items` DROP INDEX `idx_fulltxt_title_description`;";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "ALTER TABLE `#__collections_posts` DROP INDEX `idx_fulltxt_description`;";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}