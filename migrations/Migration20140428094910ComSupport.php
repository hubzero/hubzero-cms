<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140428094910ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// add comment ID
		if ($this->db->tableHasField('#__support_attachments', 'comment_id'))
		{
			$query = "ALTER TABLE `#__support_attachments` MODIFY COLUMN `comment_id` int(11) NOT NULL DEFAULT '0';";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// add comment ID
		if ($this->db->tableHasField('#__support_attachments', 'comment_id'))
		{
			$query = "ALTER TABLE `#__support_attachments` MODIFY COLUMN `comment_id` int(11) unsigned NOT NULL DEFAULT '0';";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}