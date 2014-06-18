<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for new group calendar plugin
 **/
class Migration20130429103200PlgGroupsCalendar extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = '';

		// create event calendars table
		if (!$this->db->tableExists('#__events_calendars'))
		{
			$query .= "CREATE TABLE `#__events_calendars` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`scope` varchar(100) DEFAULT NULL,
							`scope_id` int(11) DEFAULT NULL,
							`title` varchar(100) DEFAULT NULL,
							`color` varchar(100) DEFAULT NULL,
							`published` int(11) DEFAULT 1,
							PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		}

		// add calendar_id, scope, and scope id to events so we can have them belong to other sections
		if (!$this->db->tableHasField('#__events', 'calendar_id'))
		{
			$query .= "ALTER TABLE `#__events` ADD COLUMN calendar_id int(11) AFTER catid;\n";
		}
		if (!$this->db->tableHasField('#__events', 'scope'))
		{
			$query .= "ALTER TABLE `#__events` ADD COLUMN scope VARCHAR(100) AFTER calendar_id;\n";

			// set scope on all current site events
			$query .= "UPDATE `#__events` SET scope='event' WHERE (scope IS NULL OR scope='');";
		}
		if (!$this->db->tableHasField('#__events', 'scope_id'))
		{
			$query .= "ALTER TABLE `#__events` ADD COLUMN scope_id INT(11)  AFTER scope;\n";
		}

		if ($this->db->tableExists('#__xgroups_events'))
		{
			// move group events to events table
			$query .= "INSERT INTO `#__events`(scope, scope_id, title, content, state, created, created_by, publish_up, publish_down)
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
							`#__xgroups_events`;";

			// drop group events table
			$query .= "DROP TABLE `#__xgroups_events`";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}