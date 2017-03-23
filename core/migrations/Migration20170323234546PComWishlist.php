<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding comment_id to wish attachments table
 **/
class Migration20170323234546PComWishlist extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wish_attachments') && !$this->db->tableHasField('#__wish_attachments', 'comment_id'))
		{
			$query = "ALTER TABLE `#__wish_attachments` ADD `comment_id` INT(11) unsigned NOT NULL DEFAULT '0'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Up
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__wish_attachments') && $this->db->tableHasField('#__wish_attachments', 'comment_id'))
		{
			$query = "ALTER TABLE `#__wish_attachments` DROP COLUMN `comment_id`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
