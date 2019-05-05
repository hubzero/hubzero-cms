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
 * Migration script for adding is_default column
 **/
class Migration20140217151012ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__courses_offering_sections'))
		{
			if (!$this->db->tableHasField('#__courses_offering_sections', 'is_default'))
			{
				$query = "ALTER TABLE `#__courses_offering_sections` ADD `is_default` TINYINT(2)  NOT NULL  DEFAULT '0' AFTER `offering_id`";

				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__courses_offering_sections` SET `is_default`=1 WHERE `alias`='__default'";

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
		if ($this->db->tableExists('#__courses_offering_sections'))
		{
			if ($this->db->tableHasField('#__courses_offering_sections', 'is_default'))
			{
				$query = "ALTER TABLE `#__courses_offering_sections` DROP `is_default`;";

				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
