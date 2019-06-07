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
 * Migration script for fixing wrong datatype on column
 **/
class Migration20130621115001ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasField('#__abuse_reports', 'reviewed'))
		{
			$query = "ALTER TABLE `#__abuse_reports` ADD `reviewed` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00';";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "";

		if (!$this->db->tableHasField('#__abuse_reports', 'reviewed_by'))
		{
			$query = "ALTER TABLE `#__abuse_reports` ADD `reviewed_by` INT(11)  NOT NULL  DEFAULT '0';";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "";

		if (!$this->db->tableHasField('#__abuse_reports', 'note'))
		{
			$query = "ALTER TABLE `#__abuse_reports` ADD `note` TEXT  NOT NULL;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "";

		if ($this->db->tableHasField('#__abuse_reports', 'reviewed'))
		{
			$query .= "ALTER TABLE `#__abuse_reports` DROP `reviewed`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "";

		if ($this->db->tableHasField('#__abuse_reports', 'reviewed_by'))
		{
			$query .= "ALTER TABLE `#__abuse_reports` DROP `reviewed_by`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "";

		if ($this->db->tableHasField('#__abuse_reports', 'note'))
		{
			$query .= "ALTER TABLE `#__abuse_reports` DROP `note`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
