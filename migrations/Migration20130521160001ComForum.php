<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for add watching table
 **/
class Migration20130521160001ComForum extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query .= "UPDATE #__forum_posts SET `thread`=id WHERE `scope` IN ('site', 'group') AND `parent`=0;";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		$query .= "UPDATE #__forum_posts SET `thread`=parent WHERE `scope` IN ('site', 'group') AND `parent`!=0;";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}