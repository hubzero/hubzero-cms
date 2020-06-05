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
