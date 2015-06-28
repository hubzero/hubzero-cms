<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating support attachments column
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