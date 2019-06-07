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
 * Migration script for tracking section enrollment
 **/
class Migration20130401000000ComCourses extends Base
{
	public function up()
	{
		$query = '';

		if (!$this->db->tableHasField('#__courses_offering_sections', 'enrollment'))
		{
			$query .= "ALTER TABLE `#__courses_offering_sections` ADD `enrollment` TINYINT(2)  NOT NULL  DEFAULT '0'  AFTER `created_by`;";
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

		if ($this->db->tableHasField('#__courses_offering_sections', 'enrollment'))
		{
			$query .= "ALTER TABLE `#__courses_offering_sections` DROP `enrollment`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
