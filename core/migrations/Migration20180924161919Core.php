<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration to add primary key to host table
 **/

class Migration20180924161919Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('host'))
		{
			if ($this->db->getPrimaryKey('host') != 'hostname')
			{
				$query = "ALTER TABLE `host` ADD PRIMARY KEY (hostname)";
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
		if ($this->db->tableExists('host'))
		{
			if ($this->db->getPrimaryKey('host') == 'hostname')
			{
				$query = "ALTER TABLE `host` DROP PRIMARY KEY";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
