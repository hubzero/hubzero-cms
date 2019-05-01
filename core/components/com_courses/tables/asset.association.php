<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Course asset associations table class
 */
class AssetAssociation extends Table
{
	/**
	 * Contructor method for Table class
	 *
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_asset_associations', 'id', $db);
		$this->_trackAssets = false;
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return return true
	 */
	public function check()
	{
		$this->asset_id = intval($this->asset_id);
		if (!$this->asset_id)
		{
			$this->setError(Lang::txt('COM_COURSES_MUST_HAVE_ASSET_ID'));
			return false;
		}

		$this->scope_id = intval($this->scope_id);
		if (!$this->scope_id)
		{
			$this->setError(Lang::txt('COM_COURSES_MUST_HAVE_SCOPE_ID'));
			return false;
		}

		$this->scope = trim($this->scope);
		if (!$this->scope)
		{
			$this->setError(Lang::txt('COM_COURSES_MUST_HAVE_SCOPE'));
			return false;
		}

		if (!$this->id)
		{
			$high = $this->getHighestOrdering($this->scope_id, $this->scope);
			$this->ordering = ($high + 1);
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      integer $asset_id Asset ID
	 * @param      integer $scope_id Scope ID
	 * @param      string  $scope    Scope
	 * @return     boolean True on success
	 */
	public function loadByAssetScope($asset_id=null, $scope_id=null, $scope=null)
	{
		if ($asset_id === null || $scope_id === null || $scope === null)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE asset_id=" . $this->_db->quote(intval($asset_id)) . " AND scope_id=" . $this->_db->quote(intval($scope_id)) . " AND scope=" . $this->_db->quote($scope);

		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Get the last page in the ordering
	 *
	 * @param      string  $offering_id    Course alias (cn)
	 * @return     integer
	 */
	public function getHighestOrdering($scope_id, $scope)
	{
		$sql = "SELECT ordering FROM $this->_tbl WHERE scope_id=" . $this->_db->quote(intval($scope_id)) . " AND scope=" . $this->_db->quote($scope) . " ORDER BY ordering DESC LIMIT 1";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Build query method
	 *
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS caa";

		$where = array();

		if (isset($filters['asset_id']))
		{
			$where[] = "caa.asset_id=" . $this->_db->quote((int) $filters['asset_id']);
		}
		if (isset($filters['scope_id']))
		{
			$where[] = "caa.scope_id=" . $this->_db->quote((int) $filters['scope_id']);
		}
		if (isset($filters['scope']))
		{
			$where[] = "caa.scope=" . $this->_db->quote((string) $filters['scope']);
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a count of records
	 *
	 * @param  array $filters
	 * @return integer
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an object list of course units
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function find($filters=array())
	{
		$query  = "SELECT caa.*";
		$query .= $this->_buildQuery($filters);

		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
