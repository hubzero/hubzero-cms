<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding path field to course assets and subsequent updates
 **/
class Migration20141113222151ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Add folder/path field
		if ($this->db->tableExists('#__courses_assets') && !$this->db->tableHasField('#__courses_assets', 'path'))
		{
			$query = "ALTER TABLE `#__courses_assets` ADD `path` VARCHAR(255) NOT NULL DEFAULT ''";
			$this->db->setQuery($query);
			$this->db->query();

			// Set path based on asset id
			$query = "UPDATE `#__courses_assets` SET `path` = CONCAT(`course_id`, '/', `id`)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Find all assets with >1 associations
		$query = "SELECT `asset_id`, count(asset_id) AS count FROM `#__courses_asset_associations` GROUP BY `asset_id` HAVING count > 1";
		$this->db->setQuery($query);
		$assetIds = $this->db->loadObjectList();

		if ($assetIds && count($assetIds) > 0)
		{
			require_once JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php';

			foreach ($assetIds as $aa)
			{
				$query = "SELECT * FROM `#__courses_asset_associations` WHERE `asset_id` = " . (int)$aa->asset_id . " ORDER BY `id` DESC LIMIT " . (int)($aa->count-1);
				$this->db->setQuery($query);
				$toChange = $this->db->loadObjectList();

				foreach ($toChange as $a)
				{
					$oldAssetId = $a->asset_id;
					$asset = new CoursesModelAsset($oldAssetId);

					if ($asset->get('id'))
					{
						// Get the offering
						$offering = 0;
						if ($a->scope == 'asset_group')
						{
							$query  = "SELECT `offering_id` FROM `#__courses_asset_groups` AS cag";
							$query .= " LEFT JOIN `#__courses_units` AS cu ON cag.unit_id = cu.id";
							$query .= " WHERE cag.id = " . $this->db->quote($a->scope_id);
							$this->db->setQuery($query);
							$offering = $this->db->loadResult();
						}
						else if ($a->scope == 'offering')
						{
							$offering = $a->scope_id;
						}

						$asset->copy(false);

						$query = "UPDATE `#__courses_asset_associations` SET `asset_id` = " . $this->db->quote($asset->get('id')) . " WHERE `id` = " . $this->db->quote($a->id);
						$this->db->setQuery($query);
						$this->db->query();

						if ($offering)
						{
							// Update gradebook entries
							$query  = "UPDATE `#__courses_grade_book` AS g LEFT JOIN `#__courses_members` AS m ON g.member_id = m.id";
							$query .= " SET `scope_id` = " . (int)$asset->get('id');
							$query .= " WHERE `scope_id` = " . (int)$oldAssetId;
							$query .= " AND `scope` = 'asset'";
							$query .= " AND m.offering_id = " . (int)$offering;
							$this->db->setQuery($query);
							$this->db->query();

							// Update asset_unity
							$query  = "UPDATE `#__courses_asset_unity` AS u LEFT JOIN `#__courses_members` AS m ON u.member_id = m.id";
							$query .= " SET `asset_id` = " . (int)$asset->get('id');
							$query .= " WHERE `asset_id` = " . (int)$oldAssetId;
							$query .= " AND m.offering_id = " . (int)$offering;
							$this->db->setQuery($query);
							$this->db->query();

							// Update asset_views
							$query  = "UPDATE `#__courses_asset_views` AS v LEFT JOIN `#__courses_members` AS m ON v.viewed_by = m.id";
							$query .= " SET `asset_id` = " . (int)$asset->get('id');
							$query .= " WHERE `asset_id` = " . (int)$oldAssetId;
							$query .= " AND m.offering_id = " . (int)$offering;
							$this->db->setQuery($query);
							$this->db->query();
						}
					}
					else
					{
						$query = "DELETE FROM `#__courses_asset_associations` WHERE `id` = " . $this->db->quote($a->id);
						$this->db->setQuery($query);
						$this->db->query();
					}
				}
			}
		}
	}
}