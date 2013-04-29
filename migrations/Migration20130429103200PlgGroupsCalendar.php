<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130429103200PlgGroupsCalendar extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		// create event calendars table
		$query = "CREATE TABLE `jos_events_calendars` (
			         `id` int(11) NOT NULL AUTO_INCREMENT,
			         `scope` varchar(100) DEFAULT NULL,
			         `scope_id` int(11) DEFAULT NULL,
			         `title` varchar(100) DEFAULT NULL,
			         `color` varchar(100) DEFAULT NULL,
			         `published` int(11) DEFAULT 1,
			         PRIMARY KEY (`id`)
			     ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		
		// add calendar_id, scope, and scope id to events so we can have them belong to other sections
		$query .= "ALTER TABLE jos_events ADD COLUMN calendar_id int(11) AFTER catid;";
		$query .= "ALTER TABLE jos_events ADD COLUMN scope VARCHAR(100) AFTER calendar_id;"
		$query .= "ALTER TABLE jos_events ADD COLUMN scope_id INT(11)  AFTER scope;"
		
		// set scope on all current site events
		$query .= "UPDATE jos_events SET scope='event' WHERE (scope IS NULL OR scope='');"
		
		// move group events to events table
		$query .= "INSERT INTO jos_events(scope, scope_id, title, content, state, created, created_by, publish_up, publish_down)
		           SELECT
		               'group',
		               gidNumber AS scope_id,
		               title,
		               details as content,
		               active AS state,
		               created, 
		               actorid AS created,
		               start AS publish_up,
		               end AS publish_down
		           FROM
		               jos_xgroups_events;"
		
		// drop group events table
		$query .= "DROP TABLE jos_xgroups_events";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}