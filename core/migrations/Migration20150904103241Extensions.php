<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating wrong client_id on entries
 **/
class Migration20150904103241Extensions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET `client_id` = " . $this->db->quote('0') . " WHERE `type`=" . $this->db->quote('component') . " AND `element`=" . $this->db->quote('com_feedaggregator');
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__extensions` SET `client_id` = " . $this->db->quote('1') . " WHERE `type`=" . $this->db->quote('module') . " AND `element`=" . $this->db->quote('mod_grouppages');
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET `client_id` = " . $this->db->quote('1') . " WHERE `type`=" . $this->db->quote('component') . " AND `element`=" . $this->db->quote('com_feedaggregator');
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__extensions` SET `client_id` = " . $this->db->quote('0') . " WHERE `type`=" . $this->db->quote('module') . " AND `element`=" . $this->db->quote('mod_grouppages');
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}