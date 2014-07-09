<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding created, creator, modified, 
 * and modifier info to tags table
 **/
class Migration20140709144527ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__tags', 'created'))
		{
			$query = "ALTER TABLE `#__tags` ADD `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__tags', 'created_by'))
		{
			$query = "ALTER TABLE `#__tags` ADD `created_by` int(11) NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "SELECT * FROM `#__tags_log` WHERE `action`='tag_created'";
			$this->db->setQuery($query);
			if ($rows = $this->db->loadObjectList())
			{
				foreach ($rows as $row)
				{
					$query = "UPDATE `#__tags` SET `created`=" . $this->db->quote($row->timestamp) . ", `created_by`=" . $this->db->quote($row->actorid) . " WHERE `id`=" . $this->db->quote($row->tag_id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}

		if (!$this->db->tableHasField('#__tags', 'modified'))
		{
			$query = "ALTER TABLE `#__tags` ADD `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__tags', 'modified_by'))
		{
			$query = "ALTER TABLE `#__tags` ADD `modified_by` int(11) NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__tags', 'created'))
		{
			$query = "ALTER TABLE `#__tags` DROP `created`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__tags', 'created_by'))
		{
			$query = "ALTER TABLE `#__tags` DROP `created_by`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__tags', 'modified'))
		{
			$query = "ALTER TABLE `#__tags` DROP `modified`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__tags', 'modified_by'))
		{
			$query = "ALTER TABLE `#__tags` DROP `modified_by`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}