<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;

/**
 * Table class for logging course asset views
 */
class AssetViews extends Table
{
	/**
	 * Constructor
	 *
	 * @param      object &$db Database
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_asset_views', 'id', $db);
		$this->_trackAssets = false;
	}

	/**
	 * Build a query based off of filters passed
	 *
	 * @param      array $filters Filters to construct query from
	 * @return     string SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$select = array();
		$from   = array();
		$where  = array();
		$group  = array();

		$select[] = "ca.id as asset_id";
		$select[] = "cm.id as member_id";
		$select[] = "cu.id as unit_id";

		$from = array();
		$from[] = "#__courses_assets AS ca";
		$from[] = "INNER JOIN #__courses_asset_associations AS caa ON ca.id = caa.asset_id";
		$from[] = "INNER JOIN #__courses_asset_groups AS cag ON caa.scope_id = cag.id AND caa.scope = 'asset_group'";
		$from[] = "INNER JOIN #__courses_asset_views AS cav ON ca.id = cav.asset_id";
		$from[] = "INNER JOIN #__courses_members AS cm ON cav.viewed_by = cm.id";
		$from[] = "INNER JOIN #__courses_units AS cu ON cag.unit_id = cu.id AND cm.offering_id = cu.offering_id";

		if (isset($filters['progress_calculation']) && $filters['progress_calculation'])
		{
			$from[] = "INNER JOIN #__courses_progress_factors AS cpf ON ca.id = cpf.asset_id"
					. ((isset($filters['section_id']) && $filters['section_id'])
						? " AND cpf.section_id = " . $this->_db->quote($filters['section_id'])
						: '');
		}

		$where[] = "cm.student = 1";
		$where[] = "ca.state = 1";

		$group[] = "ca.id";
		$group[] = "cm.id";

		if (isset($filters['member_id']) && $filters['member_id'])
		{
			if (!is_array($filters['member_id']))
			{
				$filters['member_id'] = (array) $filters['member_id'];
			}
			$where[] = "cm.id IN (" . implode(",", $filters['member_id']) . ")";
		}

		if (isset($filters['section_id']) && $filters['section_id'])
		{
			$where[] = "cm.section_id = " . $this->_db->quote($filters['section_id']);
		}

		if (isset($filters['asset_type']) && $filters['asset_type'])
		{
			$where[] = "ca.type = " . $this->_db->quote($filters['asset_type']);
		}

		$query = "SELECT ";

		if (count($select) > 0)
		{
			$query .= implode(", ", $select);
		}
		else
		{
			$query .= "*";
		}

		if (count($from) > 0)
		{
			$query .= "\nFROM ";
			$query .= implode("\n", $from);
		}
		else
		{
			$query .= "\nFROM $this->_tbl AS cav";
		}

		if (count($where) > 0)
		{
			$query .= "\nWHERE ";
			$query .= implode(" AND ", $where);
		}

		if (count($group) > 0)
		{
			$query .= "\nGROUP BY ";
			$query .= implode(", ", $group);
		}

		return $query;
	}

	/**
	 * Get asset view records
	 *
	 * @param      array $filters Filters to construct query from
	 * @return     array
	 */
	public function find($filters=array(), $key=null)
	{
		$query = $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList($key);
	}
}
