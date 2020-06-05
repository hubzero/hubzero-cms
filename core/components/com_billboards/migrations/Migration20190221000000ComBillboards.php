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
 * Migration script for changing DATETIME fields default to NULL for com_billboards
 **/
class Migration20190221000000ComBillboards extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__billboards_billboards')
		 && $this->db->tableHasField('#__billboards_billboards', 'checked_out_time'))
		{
			$query = "ALTER TABLE `#__billboards_billboards` CHANGE `checked_out_time` `checked_out_time` DATETIME  NULL  DEFAULT NULL";

			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__billboards_billboards` SET `checked_out_time`=NULL WHERE `checked_out_time`='0000-00-00 00:00:00'";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__billboards_billboards')
		 && $this->db->tableHasField('#__billboards_billboards', 'checked_out_time'))
		{
			$query = "ALTER TABLE `#__billboards_billboards` CHANGE `checked_out_time` `checked_out_time` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00'";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
