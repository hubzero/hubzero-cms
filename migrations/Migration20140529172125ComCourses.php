<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for making sure manual grade entries are of the proper type and subtype
 **/
class Migration20140529172125ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Get the problem assets
		$query =  "SELECT ca.id FROM `#__courses_assets` ca ";
		$query .= "LEFT JOIN `#__courses_asset_associations` caa ON ca.id = caa.asset_id ";
		$query .= "LEFT JOIN `#__courses_forms` cf ON ca.id = cf.asset_id ";
		$query .= "WHERE caa.id IS NULL ";
		$query .= "AND cf.id IS NULL ";
		$query .= "AND `type`='form'";

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		if ($results && count($results) > 0)
		{
			foreach ($results as $result)
			{
				$query = "UPDATE `#__courses_assets` SET `type` = 'gradebook', `subtype` = 'auxiliary' WHERE `id` = '{$result->id}'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}