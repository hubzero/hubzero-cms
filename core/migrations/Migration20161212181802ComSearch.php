<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
 **/
class Migration20161212181802ComSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__search_blacklist') && 
			$this->db->tableHasField('#__search_blacklist', 'scope_id'))
		{
			$sql = "ALTER TABLE `#__search_blacklist` CHANGE `scope` `doc_id` VARCHAR(255) NOT NULL  DEFAULT '';";
			$this->db->setQuery($sql);
			$this->db->query();

			$sql1 = "ALTER TABLE `#__search_blacklist` DROP `scope_id`;";
			$this->db->setQuery($sql1);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__search_blacklist') && 
			!$this->db->tableHasField('#__search_blacklist', 'scope_id'))
		{
			$sql = "ALTER TABLE `#__search_blacklist` CHANGE `doc_id` `scope` VARCHAR(255) NOT NULL  DEFAULT '';";
			$this->db->setQuery($sql);
			$this->db->query();

			$sql1 = "ALTER TABLE `#__search_blacklist` ADD `scope_id` INT NULL DEFAULT NULL after `scope`;";
			$this->db->setQuery($sql1);
			$this->db->query();
		}
	}
}
