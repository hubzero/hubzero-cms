<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding an owner field to project connection entries
 **/
class Migration20160307191342PlgProjectsFiles extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__projects_connections')
			&& $this->db->tableHasField('#__projects_connections', 'provider_id')
			&& !$this->db->tableHasField('#__projects_connections', 'owner_id'))
		{
			$query = "ALTER TABLE `#__projects_connections` ADD `owner_id` INT(11) NULL DEFAULT NULL AFTER `provider_id`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__projects_connections') && $this->db->tableHasField('#__projects_connections', 'owner_id'))
		{
			$query = "ALTER TABLE `#__projects_connections` DROP `owner_id`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
