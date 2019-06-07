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
 * Migration script for adding courses announcements publish up, down, and sticky
 **/
class Migration20130404000000ComCourses extends Base
{
	public function up()
	{
		$query = '';

		if (!$this->db->tableHasField('#__courses_announcements', 'publish_up'))
		{
			$query .= "ALTER TABLE `#__courses_announcements` ADD `publish_up` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00';\n";
		}
		if (!$this->db->tableHasField('#__courses_announcements', 'publish_down'))
		{
			$query .= "ALTER TABLE `#__courses_announcements` ADD `publish_down` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00';\n";
		}
		if (!$this->db->tableHasField('#__courses_announcements', 'sticky'))
		{
			$query .= "ALTER TABLE `#__courses_announcements` ADD `sticky` TINYINT(2)  NOT NULL  DEFAULT '0';\n";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		$query = '';

		if ($this->db->tableHasField('#__courses_announcements', 'publish_up'))
		{
			$query .= "ALTER TABLE `#__courses_announcements` DROP `publish_up`;\n";
		}
		if ($this->db->tableHasField('#__courses_announcements', 'publish_down'))
		{
			$query .= "ALTER TABLE `#__courses_announcements` DROP `publish_down`;\n";
		}
		if ($this->db->tableHasField('#__courses_announcements', 'sticky'))
		{
			$query .= "ALTER TABLE `#__courses_announcements` DROP `sticky`;\n";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
