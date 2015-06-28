<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add tables to associate sessions allowed with user groups
 **/
class Migration20150326183839ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__tool_session_classes'))
		{
			$query = "CREATE TABLE `#__tool_session_classes` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `alias` varchar(255) NOT NULL DEFAULT '',
				  `jobs` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `uidx_alias` (`alias`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();

			$this->db->setQuery("INSERT INTO `#__tool_session_classes` (`alias`, `jobs`) VALUES ('default', 3)");
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_session_class_groups'))
		{
			$query = "CREATE TABLE `#__tool_session_class_groups` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `class_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `idx_class_id` (`class_id`),
				  KEY `idx_group_id` (`group_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users_tool_preferences'))
		{
			if (!$this->db->tableHasField('#__users_tool_preferences', 'class_id'))
			{
				$query = "ALTER TABLE `#__users_tool_preferences` ADD COLUMN `class_id` int(11) NOT NULL DEFAULT 0";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "ALTER TABLE `#__users_tool_preferences` ADD INDEX `idx_class_id` (`class_id`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__users_tool_preferences', 'jobs'))
			{
				$query = "ALTER TABLE `#__users_tool_preferences` ADD COLUMN `jobs` int(11) NOT NULL DEFAULT 0";
				$this->db->setQuery($query);
				$this->db->query();
			}

			// Create a preferences entry for anyone who has a non-default value for jobs allowed
			$query = "SELECT `uidNumber`, `jobsAllowed` FROM `#__xprofiles` WHERE `jobsAllowed`!=3 AND `uidNumber` > 0";
			$this->db->setQuery($query);
			if ($rows = $this->db->loadObjectList())
			{
				include_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'preferences.php');

				foreach ($rows as $row)
				{
					$preferences = new \Components\Tools\Tables\Preferences($this->db);
					$preferences->loadByUser($row->uidNumber);
					$preferences->user_id  = $row->uidNumber;
					$preferences->class_id = 0;
					$preferences->jobs     = ($row->jobsAllowed ? $row->jobsAllowed : 10);
					$preferences->store();
				}
			}

			if ($this->db->tableHasField('#__xprofiles', 'jobsAllowed'))
			{
				$query = "ALTER TABLE `#__xprofiles` DROP COLUMN `jobsAllowed`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__tool_session_classes'))
		{
			$query = "DROP TABLE `#__tool_session_classes`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_session_class_groups'))
		{
			$query = "DROP TABLE `#__tool_session_class_groups`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users_tool_preferences'))
		{
			if (!$this->db->tableHasField('#__xprofiles', 'jobsAllowed'))
			{
				$query = "ALTER TABLE `#__xprofiles` ADD COLUMN `jobsAllowed` int(11) NOT NULL DEFAULT 0";
				$this->db->setQuery($query);
				$this->db->query();
			}

			// Create a preferences entry for anyone who has a non-default value for jobs allowed
			$query = "SELECT * FROM `#__users_tool_preferences` WHERE `jobs`!=3";
			$this->db->setQuery($query);

			if ($rows = $this->db->loadObjectList())
			{
				foreach ($rows as $row)
				{
					$query = "UPDATE `#__xprofiles` SET `jobsAllowed`=" . $this->db->quote($row->jobs) . " WHERE `uidNumber`=" . $this->db->quote($row->user_id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			$query = "UPDATE `#__xprofiles` SET `jobsAllowed`=3 WHERE `jobsAllowed`=0";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableHasField('#__users_tool_preferences', 'class_id'))
			{
				$query = "ALTER TABLE `#__users_tool_preferences` DROP COLUMN `class_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__users_tool_preferences', 'jobs'))
			{
				$query = "ALTER TABLE `#__users_tool_preferences` DROP COLUMN `jobs`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}