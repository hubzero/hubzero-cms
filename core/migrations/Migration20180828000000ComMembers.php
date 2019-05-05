<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing duplicate extended profile entries
 **/
class Migration20180828000000ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__user_profiles'))
		{
			$query = "SELECT user_id, profile_key, profile_value, count(*) AS no_of_records, group_concat(id) AS duplicates
				FROM `#__user_profiles`
				GROUP BY user_id, profile_key, profile_value
				HAVING count(*) > 1
				ORDER BY user_id ASC, profile_key ASC;";
			$this->db->setQuery($query);
			$rows = $this->db->loadObjectList();

			$delete = array();

			foreach ($rows as $i => $row)
			{
				$dupes = explode(',', $row->duplicates);

				if (empty($dupes) || count($dupes) < 2)
				{
					unset($rows[$i]);
					continue;
				}

				$dupes = array_map('intval', $dupes);

				// Sort lowest to highest
				sort($dupes);

				// Discard the first (original/oldest record)
				$first = array_shift($dupes);

				// Add the other entries to the list ot delete
				foreach ($dupes as $dupe)
				{
					$delete[] = $dupe;
				}

				unset($rows[$i]);
			}

			if (!empty($delete))
			{
				$query = "DELETE FROM `#__user_profiles` WHERE `id` IN (" . implode(',', $delete) . ");";
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
		// Nothing here
	}
}
