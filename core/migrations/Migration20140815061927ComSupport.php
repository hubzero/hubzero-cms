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
 * Migration script for adding 'color' column to support statuses and fix column type for alias
 **/
class Migration20140815061927ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_statuses'))
		{
			if ($this->db->tableHasField('#__support_statuses', 'alias'))
			{
				// Create the table
				$query = "ALTER TABLE `#__support_statuses` CHANGE `alias` `alias` VARCHAR(250)  NOT NULL  DEFAULT ''";

				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__support_statuses', 'color'))
			{
				// Create the table
				$query = "ALTER TABLE `#__support_statuses` ADD `color` VARCHAR(50)  NOT NULL  DEFAULT ''";

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
		if ($this->db->tableExists('#__support_statuses'))
		{
			if ($this->db->tableHasField('#__support_statuses', 'color'))
			{
				// Create the table
				$query = "ALTER TABLE `#__support_statuses` DROP `color`;";

				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_statuses', 'alias'))
			{
				// Create the table
				$query = "ALTER TABLE `#__support_statuses` CHANGE `alias` `alias` CHAR(250)  NOT NULL  DEFAULT ''";

				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
