<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding 'created', 'created_by' columns to wish_attachments table
 **/
class Migration20171214183501ComWishlist extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wish_attachments'))
		{
			if (!$this->db->tableHasField('#__wish_attachments', 'created'))
			{
				$query = "ALTER TABLE `#__wish_attachments` ADD `created` datetime NULL  DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__wish_attachments', 'created_by'))
			{
				$query = "ALTER TABLE `#__wish_attachments` ADD `created_by` INT(11)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__wish_attachments'))
		{
			if ($this->db->tableHasField('#__wish_attachments', 'created'))
			{
				$query = "ALTER TABLE `#__wish_attachments` DROP `created`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__wish_attachments', 'created_by'))
			{
				$query = "ALTER TABLE `#__wish_attachments` DROP `created_by`';";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
