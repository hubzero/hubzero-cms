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
 * Migration script for updating form info to correspond to code changes
 **/
class Migration20130830175756ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$queries   = array();
		$queries[] = "UPDATE `#__courses_assets` SET url = SUBSTRING(url, 30, 50) WHERE `type` = 'form' AND `url` LIKE '/courses/form/complete?crumb=%'";
		$queries[] = "UPDATE `#__courses_assets` SET url = SUBSTRING(url, 22) WHERE `type` = 'form' AND `url` LIKE '/courses/form/layout/%'";

		foreach ($queries as $query)
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
