<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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