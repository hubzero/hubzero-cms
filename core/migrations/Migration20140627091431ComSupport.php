<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for making ticket ID signed to allow
 * negative IDs for temp directories.
 **/
class Migration20140627091431ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__support_attachments', 'ticket'))
		{
			$query = "ALTER TABLE `#__support_attachments` CHANGE `ticket` `ticket` INT(11)  NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__support_attachments', 'ticket'))
		{
			$query = "ALTER TABLE `#__support_attachments` CHANGE `ticket` `ticket` INT(11) unsigned NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}