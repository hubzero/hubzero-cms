<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding fields to support attachements
 **/
class Migration20140410085610ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// add comment ID
		if (!$this->db->tableHasField('#__support_attachments', 'comment_id'))
		{
			$query = "ALTER TABLE `#__support_attachments` ADD COLUMN `comment_id` int(11) NOT NULL DEFAULT '0';";

			$this->db->setQuery($query);
			$this->db->query();
		}

		// add created
		if (!$this->db->tableHasField('#__support_attachments', 'created'))
		{
			$query = "ALTER TABLE `#__support_attachments` ADD COLUMN `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";

			$this->db->setQuery($query);
			$this->db->query();
		}

		// add created by
		if (!$this->db->tableHasField('#__support_attachments', 'created_by'))
		{
			$query = "ALTER TABLE `#__support_attachments` ADD COLUMN `created_by` int(11) NOT NULL DEFAULT '0';";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// remove comment ID
		if ($this->db->tableHasField('#__support_attachments', 'comment_id'))
		{
			$query = "ALTER TABLE `#__support_attachments` DROP COLUMN `comment_id`;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		// remove created
		if ($this->db->tableHasField('#__support_attachments', 'created'))
		{
			$query = "ALTER TABLE `#__support_attachments` DROP COLUMN `created`;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		// remove created by
		if ($this->db->tableHasField('#__support_attachments', 'created_by'))
		{
			$query = "ALTER TABLE `#__support_attachments` DROP COLUMN `created_by`;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
