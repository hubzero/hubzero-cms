<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for renaming 'template' column of newsletters table
 **/
class Migration20170314214609ComNewsletters extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__newsletters'))
		{
			if ($this->db->tableHasField('#__newsletters', 'template'))
			{
				$query = "ALTER TABLE `#__newsletters` CHANGE `template` `template_id` INT(11)  NOT NULL  DEFAULT '0';";
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
		if ($this->db->tableExists('#__newsletters'))
		{
			if ($this->db->tableHasField('#__newsletters', 'template_id'))
			{
				$query = "ALTER TABLE `#__newsletters` CHANGE `template_id` `template` INT(11)  NOT NULL  DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
