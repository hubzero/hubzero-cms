<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for setting proper scope on activity entries
 **/
class Migration20180601000000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__activity_logs'))
		{
			$query = "UPDATE `#__activity_logs` SET `scope`='publication' WHERE `scope`='project' AND (description LIKE 'started a new%' OR description LIKE 'posted version%');";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__activity_logs'))
		{
			$query = "UPDATE `#__activity_logs` SET `scope`='project' WHERE `scope`='publication' AND (description LIKE 'started a new%' OR description LIKE 'posted version%');";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
