<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding column doi to #__citations_assoc
 **/
class Migration20190403124646ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__citations_assoc') && !$this->db->tableHasField('#__citations_assoc', 'doiRef'))
		{
			$query = "ALTER TABLE `#__citations_assoc` ADD COLUMN `doiRef` VARCHAR(255) AFTER `oid`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__citations_assoc') && $this->db->tableHasField('#__citations_assoc', 'doiRef'))
		{
			$query = "ALTER TABLE `#__citations_assoc` DROP COLUMN `doiRef`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
