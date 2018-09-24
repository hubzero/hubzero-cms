<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration to add primary key to hosttype table
 **/

class Migration20180924153232Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('hosttype'))
		{
			if (!$this->db->tableHasKey('hosttype', 'name'))
			{
				$query = "ALTER TABLE `hosttype` ADD PRIMARY KEY (name)";
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
		if ($this->db->tableExists('hosttype'))
		{
			if ($this->db->getPrimaryKey('hosttype') == 'name')
			{
				$query = "ALTER TABLE `hosttype` DROP PRIMARY KEY";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
