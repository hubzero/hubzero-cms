<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating xmessage component entries
 **/
class Migration20140703163627PlgXmessageHandler extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xmessage_component'))
		{
			// Old flagged state was 1. Change it to 3.
			$query = "UPDATE `#__xmessage_component` SET `component`='com_tools' WHERE `component`='com_contribtool'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__xmessage_component'))
		{
			// Old flagged state was 1. Change it to 3.
			$query = "UPDATE `#__xmessage_component` SET `component`='com_contribtool' WHERE `component`='com_tools'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}