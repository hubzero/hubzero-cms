<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for setting the correct engine on the migrations table
 **/
class Migration20141009154241Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__migrations') && strtolower($this->db->getEngine('#__migrations')) != 'myisam')
		{
			$query = "ALTER TABLE `#__migrations` ENGINE = MyISAM";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}