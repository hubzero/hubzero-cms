<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for properly associating gradebook items with an offering
 **/
class Migration20140529192810ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query  = "SELECT ca.* FROM `#__courses_assets` ca";
		$query .= " LEFT JOIN `#__courses_asset_associations` caa ON ca.id = caa.asset_id";
		$query .= " WHERE `type` = 'gradebook'";
		$query .= " AND `subtype` = 'auxiliary'";
		$query .= " AND caa.id IS NULL";

		$this->db->setQuery($query);
		$results  = $this->db->loadObjectList();
		$ordering = array();

		if ($results && count($results) > 0)
		{
			foreach ($results as $result)
			{
				$query  = "SELECT `id`, `title` FROM `#__courses_offerings`";
				$query .= " WHERE `course_id` = '{$result->course_id}' AND `created` < '{$result->created}' AND `state` = 1";
				$query .= " ORDER BY `id` ASC LIMIT 1";
				$this->db->setQuery($query);
				$offering = $this->db->loadObject();
				$ordering[$offering->id] = (!isset($ordering[$offering->id])) ? 0 : $ordering[$offering->id] + 1;

				$query  = "INSERT INTO `#__courses_asset_associations` (`asset_id`, `scope_id`, `scope`, `ordering`) VALUES";
				$query .= " ('{$result->id}', '{$offering->id}', 'offering', '{$ordering[$offering->id]}')";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}