<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for extending password field length in #__users table
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