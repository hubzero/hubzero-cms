<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding status to file uploads
 **/
class Migration20150218105200PlgGroupsForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__forum_attachments') && !$this->db->tableHasField('#__forum_attachments', 'status'))
		{
			// adds column status to forum_attachments table
			$query = "ALTER TABLE `#__forum_attachments` ADD COLUMN `status` INT(11) NULL DEFAULT 1 AFTER `description`;";
			 /* 0 = unpublished, 1 = published, 2 = deleted */
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__forum_attachments') && $this->db->tableHasField('#__forum_attachments', 'status'))
		{
			// drops column status from forum_attachments table
			$query = "ALTER TABLE `#__forum_attachments` DROP COLUMN `status`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}