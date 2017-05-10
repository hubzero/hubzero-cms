<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing #__support_tickets group (cn) field to group ID
 **/
class Migration20170209150806ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_tickets') && $this->db->tableHasField('#__support_tickets', 'group')
			&& $this->db->tableExists('#__xgroups') && $this->db->tableHasField('#__xgroups', 'cn'))
		{
			/*$query = "SELECT `id`, `group` FROM `#__support_tickets` WHERE `group`!='' AND `group` IS NOT NULL";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $result)
				{
					$query = "SELECT `gidNumber` FROM `#__xgroups` WHERE `cn`=" . $this->db->quote($result->group);
					$this->db->setQuery($query);

					$gidNumber = 0;
					if ($gid = $this->db->loadResult())
					{
						$gidNumber = $gid;
					}

					$query = "UPDATE `#__support_tickets` SET `group`=" . $this->db->quote($gidNumber) . " WHERE `id`=" . $this->db->quote($result->id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}*/
			// Update group aliases to IDs
			$query = "UPDATE `#__support_tickets` AS u
						LEFT JOIN `#__xgroups` AS x ON x.`cn`=u.`group`
						SET u.`group` = x.`gidNumber`;";
			$this->db->setQuery($query);
			$this->db->query();

			// Remove the old index
			if ($this->db->tableHasKey('#__support_tickets', 'idx_group'))
			{
				$query = "ALTER TABLE `#__support_tickets` DROP KEY `idx_group`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			// Change the column type
			$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `group` `group_id` int(11) NOT NULL DEFAULT '0'";
			$this->db->setQuery($query);
			$this->db->query();

			// Add the new index
			if (!$this->db->tableHasKey('#__support_tickets', 'idx_group_id'))
			{
				$query = "ALTER IGNORE TABLE `#__support_tickets` ADD INDEX `idx_group_id` (`group_id`)";
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
		if ($this->db->tableExists('#__support_tickets') && $this->db->tableHasField('#__support_tickets', 'group')
			&& $this->db->tableExists('#__xgroups') && $this->db->tableHasField('#__xgroups', 'cn'))
		{
			if ($this->db->tableHasKey('#__support_tickets', 'idx_group_id'))
			{
				$query = "ALTER TABLE `#__support_tickets` DROP KEY `idx_group_id`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `group_id` `group` varchar(250)";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__support_tickets` AS u
						LEFT JOIN `#__xgroups` AS x ON x.`gidNumber`=u.`group`
						SET u.`group` = x.`cn`;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__support_tickets', 'idx_group'))
			{
				$query = "ALTER IGNORE TABLE `#__support_tickets` ADD INDEX `idx_group` (`group`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
