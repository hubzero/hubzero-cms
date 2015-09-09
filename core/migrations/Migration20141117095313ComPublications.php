<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding comment field to curation history table
 **/
class Migration20141117095313ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_curation_history'))
		{
			if (!$this->db->tableHasField('#__publication_curation_history', 'comment'))
			{
				$query = "ALTER TABLE `#__publication_curation_history` ADD COLUMN `comment` TEXT AFTER newstatus;";
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
		if ($this->db->tableExists('#__publication_curation_history'))
		{
			if ($this->db->tableHasField('#__publication_curation_history', 'comment'))
			{
				$query = "ALTER TABLE `#__publication_curation_history` DROP `comment`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}