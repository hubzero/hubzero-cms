<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for dropping enused courses tables
 **/
class Migration20131021090512ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__courses_inviteemails'))
		{
			$query = "DROP TABLE `#__courses_inviteemails`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_events'))
		{
			$query = "DROP TABLE `#__courses_events`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_enrollments') && $this->db->getDatabase() != 'nanohub')
		{
			$query = "DROP TABLE `#__courses_enrollments`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_email'))
		{
			$query = "DROP TABLE `#__courses_email`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_email_log'))
		{
			$query = "DROP TABLE `#__courses_email_log`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_email_version'))
		{
			$query = "DROP TABLE `#__courses_email_version`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__courses_email'))
		{
			$query = "CREATE TABLE `#__courses_email` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `offering_id` int(11) NOT NULL DEFAULT '0',
					  `name` varchar(255) NOT NULL DEFAULT '',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_email_log'))
		{
			$query = "CREATE TABLE `#__courses_email_log` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `email_id` int(11) NOT NULL DEFAULT '0',
					  `version_id` int(11) NOT NULL DEFAULT '0',
					  `to` varchar(100) NOT NULL DEFAULT '',
					  `sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `sent_by` int(11) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_email_version'))
		{
			$query = "CREATE TABLE `#__courses_email_version` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `email_id` int(11) NOT NULL DEFAULT '0',
					  `subject` varchar(255) NOT NULL DEFAULT '',
					  `body` text NOT NULL,
					  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `created_by` int(11) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_enrollments'))
		{
			$query = "CREATE TABLE `#__courses_enrollments` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `offering_id` int(11) NOT NULL DEFAULT '0',
					  `user_id` int(11) NOT NULL DEFAULT '0',
					  `enrollment_id` int(11) NOT NULL DEFAULT '0',
					  `status` varchar(100) NOT NULL DEFAULT '',
					  `fname` varchar(200) NOT NULL DEFAULT '',
					  `lname` varchar(200) NOT NULL DEFAULT '',
					  `email1` varchar(100) NOT NULL DEFAULT '',
					  `email2` varchar(100) NOT NULL DEFAULT '',
					  `hubaccount` varchar(100) NOT NULL DEFAULT '',
					  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_events'))
		{
			$query = "CREATE TABLE `#__courses_events` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `gidNumber` int(11) NOT NULL,
					  `actorid` int(11) NOT NULL,
					  `title` varchar(255) NOT NULL,
					  `details` text NOT NULL,
					  `type` varchar(50) NOT NULL,
					  `start` datetime NOT NULL,
					  `end` datetime NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  `created` datetime NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__courses_inviteemails'))
		{
			$query = "CREATE TABLE `#__courses_inviteemails` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `email` varchar(150) NOT NULL,
					  `gidNumber` int(11) NOT NULL,
					  `token` varchar(255) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
