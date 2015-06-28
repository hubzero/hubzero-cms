<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating scope on existing migration entries
 **/
class Migration20150612203219Migrations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__migrations'))
		{
			$query = "UPDATE `#__migrations` SET `scope`=" . $this->db->quote('core/migrations') . " WHERE `scope`=" . $this->db->quote('migrations');
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__migrations'))
		{
			$query = "UPDATE `#__migrations` SET `scope`=" . $this->db->quote('migrations') . " WHERE `scope`=" . $this->db->quote('core/migrations');
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
