<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for changing ticket owner field 
 * to use user ID instead of username
 **/
class Migration20140627062357ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Support tickets
		$query = "DESCRIBE #__support_tickets owner";
		$this->db->setQuery($query);
		$uidField = $this->db->loadObject();

		if (strtolower($uidField->Type) != 'int(11)')
		{
			$query = "SELECT DISTINCT t.owner AS username, u.id FROM `#__support_tickets` AS t LEFT JOIN `#__users` AS u ON u.username=t.owner WHERE t.owner != '' AND t.owner IS NOT NULL";
			$this->db->setQuery($query);
			if ($owners = $this->db->loadObjectList())
			{
				foreach ($owners as $owner)
				{
					$query = "UPDATE `#__support_tickets` SET owner=" . $this->db->quote($owner->id) . " WHERE owner=" . $this->db->quote($owner->username);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			$query = "ALTER TABLE `#__support_tickets` CHANGE owner owner INT(11) NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Support ticket comments
		$query = "DESCRIBE #__support_comments created_by";
		$this->db->setQuery($query);
		$uidField = $this->db->loadObject();

		if (strtolower($uidField->Type) != 'int(11)')
		{
			$query = "SELECT DISTINCT t.created_by AS username, u.id FROM `#__support_comments` AS t LEFT JOIN `#__users` AS u ON u.username=t.created_by WHERE t.created_by != '' AND t.created_by IS NOT NULL";
			$this->db->setQuery($query);
			if ($creators = $this->db->loadObjectList())
			{
				foreach ($creators as $creator)
				{
					$query = "UPDATE `#__support_comments` SET created_by=" . $this->db->quote($creator->id) . " WHERE created_by=" . $this->db->quote($creator->username);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			$query = "ALTER TABLE `#__support_comments` CHANGE created_by created_by INT(11) NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Support tickets
		$query = "DESCRIBE #__support_tickets owner";
		$this->db->setQuery($query);
		$uidField = $this->db->loadObject();

		if (strtolower($uidField->Type) == 'int(11)')
		{
			$query = "SELECT DISTINCT u.username, t.owner AS id FROM `#__support_tickets` AS t LEFT JOIN `#__users` AS u ON u.id=t.owner WHERE t.owner > 0";
			$this->db->setQuery($query);
			if ($owners = $this->db->loadObjectList())
			{
				foreach ($owners as $owner)
				{
					$query = "UPDATE `#__support_tickets` SET owner=" . $this->db->quote($owner->username) . " WHERE owner=" . $this->db->quote($owner->id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			$query = "ALTER TABLE `#__support_tickets` CHANGE owner owner VARCHAR(50) DEFAULT '' NOT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Support ticket comments
		$query = "DESCRIBE #__support_comments created_by";
		$this->db->setQuery($query);
		$uidField = $this->db->loadObject();

		if (strtolower($uidField->Type) == 'int(11)')
		{
			$query = "SELECT DISTINCT t.created_by AS id, u.username FROM `#__support_comments` AS t LEFT JOIN `#__users` AS u ON u.id=t.created_by WHERE t.created_by > 0";
			$this->db->setQuery($query);
			if ($creators = $this->db->loadObjectList())
			{
				foreach ($creators as $creator)
				{
					$query = "UPDATE `#__support_comments` SET created_by=" . $this->db->quote($creator->username) . " WHERE created_by=" . $this->db->quote($creator->id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			$query = "ALTER TABLE `#__support_comments` CHANGE created_by created_by VARCHAR(50) DEFAULT '' NOT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}