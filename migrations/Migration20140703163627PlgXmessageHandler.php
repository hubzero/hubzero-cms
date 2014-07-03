<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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