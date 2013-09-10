<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding params field to asset groups
 **/
class Migration20130911070500ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableHasField('#__courses_asset_groups', 'params'))
		{
			$query = "ALTER TABLE `#__courses_asset_groups` ADD `params` TEXT  NOT NULL  AFTER `state`;";
			$db->setQuery($query);
			$db->query();

			$query = "SELECT id FROM `#__courses_asset_groups` WHERE `alias`='lectures'";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if ($results && count($results) > 0)
			{
				foreach ($results as $r)
				{
					$query = "UPDATE `#__courses_asset_groups` SET `params` = 'discussions_category=1' WHERE `parent` = '{$r->id}'";
					$db->setQuery($query);
					$db->query();
				}
			}
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableHasField('#__courses_asset_groups', 'params'))
		{
			$query = "ALTER TABLE `#__courses_asset_groups` DROP `params`;";
			$db->setQuery($query);
			$db->query();
		}
	}
}