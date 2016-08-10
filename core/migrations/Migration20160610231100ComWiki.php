<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming the column to Relational standards
 **/
class Migration20160610231100ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__wiki_versions', 'pageid'))
		{
			$query = "ALTER TABLE `#__wiki_versions` CHANGE `pageid` `page_id` INT(11)  NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__wiki_versions', 'page_id'))
		{
			$query = "ALTER TABLE `#__wiki_versions` CHANGE `page_id` `pageid` INT(11)  NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}

