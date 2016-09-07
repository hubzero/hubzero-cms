<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add default value to field for consistency between upgrade/new installs
 **/
class Migration2016090710280000ComCategories extends Base
{	
	public function up()
	{
		if ($this->db->tableHasField('#__categories', 'title'))
		{
			$query = "ALTER TABLE `#__categories` CHANGE COLUMN `title` `title` varchar(255) NOT NULL DEFAULT '';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
	}
}
