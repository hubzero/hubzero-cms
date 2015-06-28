<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing wrong datatype on column
 **/
class Migration20130617171001ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasField('#__forum_posts', 'closed'))
		{
			$query = "ALTER TABLE `#__forum_posts` ADD `closed` TINYINT(2)  NOT NULL  DEFAULT '0';";
		}

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
		$query = "";

		if ($this->db->tableHasField('#__forum_posts', 'closed'))
		{
			$query .= "ALTER TABLE `#__forum_posts` DROP `closed`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}