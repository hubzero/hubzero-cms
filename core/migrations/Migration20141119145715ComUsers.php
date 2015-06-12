<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for extending password field length in jos_users table
 **/
class Migration20141119145715ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users') && $this->db->tableHasField('#__users', 'password'))
		{
			$info = $this->db->getTableColumns('#__users', false);

			if ($info['password']->Type != "varchar(127)")
			{
				$query = "ALTER TABLE `#__users` CHANGE `password` `password` VARCHAR(127) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}