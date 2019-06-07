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
 * Migration script for adding params field to asset groups
 **/
class Migration20130911070500ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__courses_asset_groups', 'params'))
		{
			$query = "ALTER TABLE `#__courses_asset_groups` ADD `params` TEXT  NOT NULL  AFTER `state`;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "SELECT id FROM `#__courses_asset_groups` WHERE `alias`='lectures'";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if ($results && count($results) > 0)
			{
				foreach ($results as $r)
				{
					$query = "UPDATE `#__courses_asset_groups` SET `params` = 'discussions_category=1' WHERE `parent` = '{$r->id}'";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__courses_asset_groups', 'params'))
		{
			$query = "ALTER TABLE `#__courses_asset_groups` DROP `params`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
