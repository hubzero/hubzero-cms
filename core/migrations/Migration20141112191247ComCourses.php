<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for moving course form dates to section dates table
 **/
class Migration20141112191247ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Get all of the course form deployments
		$query  = "SELECT cfd.*, cf.asset_id, cu.offering_id";
		$query .= " FROM `#__courses_form_deployments` AS cfd";
		$query .= " LEFT JOIN `#__courses_forms` AS cf ON cfd.form_id = cf.id";
		$query .= " LEFT JOIN `#__courses_assets` AS ca ON cf.asset_id = ca.id";
		$query .= " LEFT JOIN `#__courses_asset_associations` AS caa ON ca.id = caa.asset_id AND scope = 'asset_group'";
		$query .= " LEFT JOIN `#__courses_asset_groups` AS cag ON caa.scope_id = cag.id";
		$query .= " LEFT JOIN `#__courses_units` AS cu ON cag.unit_id = cu.id";
		$this->db->setQuery($query);
		$deployments = $this->db->loadObjectList();

		if ($deployments && count($deployments) > 0)
		{
			$this->callback('progress', 'init', array('Running ' . __CLASS__ . '.php:'));
			$total = count($deployments);
			$i     = 1;

			foreach ($deployments as $deployment)
			{
				// Get all of the sections that this deployment is in (based on offering_id from deployment query)
				$query  = "SELECT `id` FROM `#__courses_offering_sections`";
				$query .= " WHERE offering_id = " . $this->db->quote($deployment->offering_id);
				$this->db->setQuery($query);
				$sections = $this->db->loadObjectList();

				// Now, each section must have a section date entry
				if ($sections && count($sections) > 0)
				{
					foreach ($sections as $section)
					{
						$query  = "SELECT * FROM `#__courses_offering_section_dates`";
						$query .= " WHERE `section_id` = " . $this->db->quote($section->id);
						$query .= " AND `scope` = 'asset' AND `scope_id` = " . $this->db->quote($deployment->asset_id);
						$this->db->setQuery($query);
						$found = $this->db->loadObject();

						if (!$found)
						{
							// No date exists...so add it
							$query  = "INSERT INTO `#__courses_offering_section_dates` (section_id, scope, scope_id, publish_up, publish_down, created) VALUES ";
							$query .= "(" . $this->db->quote($section->id) . ",";
							$query .= "'asset',";
							$query .= $this->db->quote($deployment->asset_id) . ",";
							$query .= $this->db->quote($deployment->start_time) . ",";
							$query .= $this->db->quote($deployment->end_time) . ",";
							$query .= $this->db->quote(\JFactory::getDate()->toSql()) . ")";

							$this->db->setQuery($query);
							$this->db->query();
						}
						else
						{
							$start = (isset($found->publish_up) && $found->publish_up != '0000-00-00 00:00:00') ? $found->publish_up : $deployment->start_time;
							$end   = (isset($found->publish_down) && $found->publish_down != '0000-00-00 00:00:00') ? $found->publish_down : $deployment->end_time;
							$query  = "UPDATE `#__courses_offering_section_dates` SET ";
							$query .= "publish_up = " . $this->db->quote($start);
							$query .= ", ";
							$query .= "publish_down = " . $this->db->quote($end);
							$query .= " WHERE `id` = " . $this->db->quote($found->id);

							$this->db->setQuery($query);
							$this->db->query();
						}
					}
				}

				$progress = round($i/$total*100);
				$this->callback('progress', 'setProgress', array($progress));
				$i++;
			}

			$this->callback('progress', 'done');
		}
	}
}