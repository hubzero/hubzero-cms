<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for making sure collection created_by is filled in
 **/
class Migration20140829142600ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__collections'))
		{
			$query = "UPDATE `#__collections` SET `created_by`=`object_id` WHERE `object_type`='member' AND `created_by`=0";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}