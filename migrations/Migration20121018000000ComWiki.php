<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20121018000000ComWiki extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = '';

		if (!$db->tableHasField('#__wiki_page', 'modified'))
		{
			$query .= "ALTER TABLE `#__wiki_page` ADD `modified` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00'  AFTER `state`;\n";
		}
		if (!$db->tableHasField('#__wiki_page', 'version_id'))
		{
			$query .= "ALTER TABLE `#__wiki_page` ADD `version_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `modified`;\n";
		}
		if (!$db->tableHasField('#__wiki_version', 'length'))
		{
			$query .= "ALTER TABLE `#__wiki_version` ADD `length` INT(11)  NOT NULL  DEFAULT '0'  AFTER `summary`;\n";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}