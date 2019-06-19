<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding com_fmns tables
 **/
class Migration20190619000000ComFmns extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
    if ($this->db->tableExists('#__fmn_fmns'))
		{
			$query = "ALTER TABLE `#__fmn_fmns` DROP INDEX group_cn";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
    if ($this->db->tableExists('#__fmn_fmns'))
		{
			$query = "ALTER TABLE `#__fmn_fmns` ADD UNIQUE KEY `group_cn` (`group_cn`)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
