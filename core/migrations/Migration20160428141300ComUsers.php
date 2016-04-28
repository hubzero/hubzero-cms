<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding name parts and usageAgreement columns to users table
 **/
class Migration20160428141300ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__users', 'activation') && $this->db->tableHasField('#__xprofiles', 'emailConfirmed'))
		{
			$query = "ALTER TABLE `#__users` CHANGE `activation` `activation` INT(11)  NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__users` AS u LEFT JOIN `#__xprofiles` AS x ON u.`id`=x.`uidNumber` SET u.`activation`=x.`emailConfirmed`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__users', 'givenName') && $this->db->tableHasField('#__xprofiles', 'givenName'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `givenName` VARCHAR(255) NOT NULL AFTER `name`;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__users` AS u LEFT JOIN `#__xprofiles` AS x ON u.`id`=x.`uidNumber` SET u.`givenName`=x.`givenName`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__users', 'middleName') && $this->db->tableHasField('#__xprofiles', 'middleName'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `middleName` VARCHAR(255) NOT NULL AFTER `givenName`;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__users` AS u LEFT JOIN `#__xprofiles` AS x ON u.`id`=x.`uidNumber` SET u.`middleName`=x.`middleName`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__users', 'surname') && $this->db->tableHasField('#__xprofiles', 'surname'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `surname` VARCHAR(255) NOT NULL AFTER `middleName`;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__users` AS u LEFT JOIN `#__xprofiles` AS x ON u.`id`=x.`uidNumber` SET u.`surname`=x.`surname`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__users', 'usageAgreement') && $this->db->tableHasField('#__xprofiles', 'usageAgreement'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `usageAgreement` TINYINT(2) NOT NULL DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__users` AS u LEFT JOIN `#__xprofiles` AS x ON u.`id`=x.`uidNumber` SET u.`usageAgreement`=x.`usageAgreement`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__users', 'activation'))
		{
			$query = "ALTER TABLE `#__users` CHANGE `activation` `activation` VARCHAR(100) NOT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__users', 'givenName'))
		{
			$query = "ALTER TABLE `#__users` DROP COLUMN `givenName`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__users', 'middleName'))
		{
			$query = "ALTER TABLE `#__users` DROP COLUMN `middleName`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__users', 'givenName'))
		{
			$query = "ALTER TABLE `#__users` DROP COLUMN `surname`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__users', 'usageAgreement'))
		{
			$query = "ALTER TABLE `#__users` DROP COLUMN `usageAgreement`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}