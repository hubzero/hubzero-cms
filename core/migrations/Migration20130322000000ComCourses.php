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
 * Migration script for tracking when course form entries are submitted
 **/
class Migration20130322000000ComCourses extends Base
{
	public function up()
	{
		$query = '';

		if (!$this->db->tableHasField('#__courses_form_respondent_progress', 'submitted'))
		{
			$query .= "ALTER TABLE `#__courses_form_respondent_progress` ADD `submitted` DATETIME  NULL  AFTER `answer_id`;";
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

		if ($this->db->tableHasField('#__courses_form_respondent_progress', 'submitted'))
		{
			$query .= "ALTER TABLE `#__courses_form_respondent_progress` DROP `submitted`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
