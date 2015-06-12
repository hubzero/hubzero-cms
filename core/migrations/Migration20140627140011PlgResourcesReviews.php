<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding a state field to resource reviews
 **/
class Migration20140627140011PlgResourcesReviews extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__resource_ratings', 'state'))
		{
			$query = "ALTER TABLE `#__resource_ratings` ADD `state` TINYINT(2)  NOT NULL  DEFAULT '0';";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__resource_ratings` SET state=1 WHERE resource_id!=0";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__resource_ratings', 'state'))
		{
			$query = "ALTER TABLE `#__resource_ratings` DROP `state`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}