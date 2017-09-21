<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding 'status_code' field to redirect_links table
 **/
class Migration20170921161800ComRedirect extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__redirect_links'))
		{
			if (!$this->db->tableHasField('#__redirect_links', 'status_code'))
			{
				$query = "ALTER TABLE `#__redirect_links` ADD `status_code` INT(3) NOT NULL DEFAULT '404'";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__redirect_links` SET `status_code`=301 WHERE `new_url`!=''";
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
		if ($this->db->tableExists('#__redirect_links'))
		{
			if ($this->db->tableHasField('#__redirect_links', 'status_code'))
			{
				$query = "ALTER TABLE `#__redirect_links` DROP COLUMN `status_code`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
