<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing up some support ticket field data types
 **/
class Migration20141009204207ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_tickets'))
		{
			$info = $this->db->getTableColumns('#__support_tickets', false);

			if ($this->db->tableHasField('#__support_tickets', 'status') && $info['status']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `status` `status` TINYINT(3) NOT NULL DEFAULT '0'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'created') && $info['created']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'login') && $info['login']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `login` `login` VARCHAR(200) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'severity') && $info['severity']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `severity` `severity` VARCHAR(30) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'category') && $info['category']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `category` `category` VARCHAR(50) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'summary') && $info['summary']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `summary` `summary` VARCHAR(250) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'report') && $info['report']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `report` `report` TEXT NOT NULL";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'resolved') && $info['resolved']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `resolved` `resolved` VARCHAR(50) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'email') && $info['email']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `email` `email` VARCHAR(200) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'name') && $info['name']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `name` `name` VARCHAR(200) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'os') && $info['os']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `os` `os` VARCHAR(50) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'browser') && $info['browser']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `browser` `browser` VARCHAR(50) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'ip') && $info['ip']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `ip` `ip` VARCHAR(200) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'uas') && $info['uas']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `uas` `uas` VARCHAR(250) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'hostname') && $info['hostname']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `hostname` `hostname` VARCHAR(200) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'referrer') && $info['referrer']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `referrer` `referrer` VARCHAR(250) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__support_tickets', 'group') && $info['group']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `group` `group` VARCHAR(250) NOT NULL DEFAULT '' ";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}