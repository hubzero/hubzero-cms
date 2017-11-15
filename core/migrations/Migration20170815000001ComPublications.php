<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding 'featured' column to publications table
 **/
class Migration20170815000001ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__publications', 'featured'))
		{
			$query = "ALTER TABLE `#__publications` ADD `featured` TINYINT(1)  NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__publications', 'featured'))
		{
			$query = "ALTER TABLE `#__publications` DROP `featured`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
