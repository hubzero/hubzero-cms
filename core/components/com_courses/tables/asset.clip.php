<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;
use User;
use Date;
use Lang;

/**
 * Course asset clips table class
 */
class AssetClip extends Table
{
	/**
	 * Contructor method for Table class
	 *
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_asset_clips', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return return true
	 */
	public function check()
	{
		if (!$this->scope)
		{
			$this->setError(Lang::txt('Missing scope ID'));
			return false;
		}

		$this->scope_id = intval($this->scope_id);
		if (!$this->scope_id)
		{
			$this->setError(Lang::txt('Missing scope ID'));
			return false;
		}

		$this->type = trim($this->type);
		if (!$this->type)
		{
			$this->setError(Lang::txt('Missing type'));
			return false;
		}

		$this->title = trim($this->title);
		if (!$this->title)
		{
			$this->setError(Lang::txt('Missing title'));
			return false;
		}

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');
		}

		return true;
	}

	/**
	 * Build query method
	 *
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS cac";
		$where = array();

		if (isset($filters['scope']) && $filters['scope'])
		{
			$where[] = "cac.scope=" . $this->_db->quote($filters['scope']);
		}
		if (isset($filters['type']) && $filters['type'])
		{
			$where[] = "cac.type=" . $this->_db->quote($filters['type']);
		}

		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "LOWER(cac.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param     array $filters Filters to build query from
	 * @return    integer
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(cac.id)";
		$query .= $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a list of course asset clips
	 *
	 * @param     array $filters Filters to build query from
	 * @return    array
	 */
	public function find($filters=array())
	{
		$query  = "SELECT cac.*";
		$query .= $this->_buildQuery($filters);

		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$query .= " ORDER BY cac.created DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
