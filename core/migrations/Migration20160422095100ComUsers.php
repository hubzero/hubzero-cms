<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding 'access' and 'registerIP' columns to users table
 **/
class Migration20160422095100ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__users', 'access'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `access` INT(10) NOT NULL DEFAULT 0;";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableExists('#__xprofiles'))
			{
				$public  = 1;
				$private = 5;

				if ($this->db->tableExists('#__viewlevels'))
				{
					$query = "SELECT * FROM `#__viewlevels` ORDER BY `ordering` ASC";
					$this->db->setQuery($query);
					$levels = $this->db->loadObjectList();
					if ($levels)
					{
						foreach ($levels as $level)
						{
							if ($level->title == 'Public')
							{
								$public = $level->id;
							}

							if ($level->title == 'Private')
							{
								$private = $level->id;
							}
						}
					}
				}

				$query = "UPDATE `#__users` AS u LEFT JOIN `#__xprofiles` AS x ON u.`id`=x.`uidNumber` SET u.`access`=" . $this->db->quote($public) . " WHERE x.`public`=1";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__users` AS u LEFT JOIN `#__xprofiles` AS x ON u.`id`=x.`uidNumber` SET u.`access`=" . $this->db->quote($private) . " WHERE x.`public`=0";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if (!$this->db->tableHasField('#__users', 'registerIP'))
		{
			$query = "ALTER TABLE `#__users` ADD COLUMN `registerIP` VARCHAR(40) NOT NULL DEFAULT '' AFTER `registerDate`;";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableExists('#__xprofiles') && $this->db->tableHasField('#__xprofiles', 'regIP'))
			{
				$query = "UPDATE `#__users` AS u LEFT JOIN `#__xprofiles` AS x ON u.`id`=x.`uidNumber` SET u.`registerIP`=x.`regIP`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableHasField('#__users', 'sendEmail') && $this->db->tableHasField('#__xprofiles', 'mailPreferenceOption'))
		{

			$query = "UPDATE `#__users` AS u LEFT JOIN `#__xprofiles` AS x ON u.`id`=x.`uidNumber` SET u.`sendEmail`=x.`mailPreferenceOption`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__users', 'access'))
		{
			$query = "ALTER TABLE `#__users` DROP COLUMN `access`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__users', 'registerIP'))
		{
			$query = "ALTER TABLE `#__users` DROP COLUMN `registerIP`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}