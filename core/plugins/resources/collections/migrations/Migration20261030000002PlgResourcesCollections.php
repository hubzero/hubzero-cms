<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing the nameless resource type an earlier
 * migration added to hubs that had no series type
 **/
class Migration20261030000002PlgResourcesCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__resource_types') || !$this->db->tableHasField('#__resource_types', 'collection'))
		{
			return;
		}

		// Only a type with neither a name nor an alias, marked as a collection,
		// that no resource uses: what Migration20180418161953 left behind
		$query = "DELETE t FROM `#__resource_types` AS t
			WHERE t.`type` = ''
			AND (t.`alias` IS NULL OR t.`alias` = '')
			AND t.`collection` = 1
			AND NOT EXISTS (
				SELECT 1 FROM `#__resources` AS r WHERE r.`type` = t.`id` OR r.`logical_type` = t.`id`
			)";
		$this->db->setQuery($query);
		$this->db->query();
	}
}
