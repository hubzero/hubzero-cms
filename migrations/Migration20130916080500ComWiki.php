<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding params field to asset groups
 **/
class Migration20130916080500ComWiki extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableHasField('#__wiki_page', 'created'))
		{
			$query = "ALTER TABLE `#__wiki_page` ADD `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_by`;";
			$db->setQuery($query);
			$db->query();

			$query = "UPDATE `#__wiki_page` AS p SET p.`created` = (SELECT v.created FROM `#__wiki_version` AS v WHERE v.pageid=p.id ORDER BY v.version ASC LIMIT 1);";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableHasField('#__wiki_page', 'created'))
		{
			$query = "ALTER TABLE `#__wiki_page` DROP `created`;";
			$db->setQuery($query);
			$db->query();
		}
	}
}