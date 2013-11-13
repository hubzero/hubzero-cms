<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for replacing odd characters in resource license text
 **/
class Migration20131113193815ComResources extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__resource_licenses'))
		{
			$query = "UPDATE `#__resource_licenses` SET `text` = REPLACE(`text`, 'â€”', '—')";
			$db->setQuery($query);
			$db->query();
		}
	}
}