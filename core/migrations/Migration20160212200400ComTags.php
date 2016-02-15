<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding objects and substitutes columns to tags table
 **/
class Migration20160212200400ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__tags'))
		{
			if (!$this->db->tableHasField('#__tags', 'objects'))
			{
				$query = "ALTER TABLE `#__tags` ADD COLUMN `objects` int(11) NOT NULL default 0;";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "ALTER TABLE `#__tags` ADD INDEX `idx_objects` (`objects`)";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__tags` AS t SET t.`objects`=(SELECT COUNT(*) FROM `#__tags_object` AS o WHERE o.tagid=t.id)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__tags', 'substitutes'))
			{
				$query = "ALTER TABLE `#__tags` ADD COLUMN `substitutes` int(11) NOT NULL default 0;";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "ALTER TABLE `#__tags` ADD INDEX `idx_substitutes` (`substitutes`)";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__tags` AS t SET t.`substitutes`=(SELECT COUNT(*) FROM `#__tags_substitute` AS o WHERE o.tag_id=t.id)";
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
		if ($this->db->tableExists('#__tags'))
		{
			if ($this->db->tableHasField('#__tags', 'objects'))
			{
				$query = "ALTER TABLE `#__tags` DROP `objects`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__tags', 'substitutes'))
			{
				$query = "ALTER TABLE `#__tags` DROP `substitutes`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}