<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Tables;

use Hubzero\Database\Table;
use Date;
use User;
use Lang;

/**
 * Table class for following something
 */
class Following extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__collections_following', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   string   $oid             Record alias
	 * @param   string   $following_type  Following type
	 * @param   string   $follower_id     Follower ID
	 * @param   string   $follower_type   Follower type
	 * @return  boolean  True on success
	 */
	public function load($oid=null, $following_type=null, $follower_id=null, $follower_type=null)
	{
		if ($oid === null)
		{
			return false;
		}

		if (!$following_type && !$follower_id && !$follower_type)
		{
			return parent::load($oid);
		}

		$fields = array(
			'following_id'   => (int) $oid,
			'following_type' => (string) $following_type
		);
		if ($follower_id !== null)
		{
			$fields['follower_id'] = (int) $follower_id;
		}
		if ($follower_type !== null)
		{
			$fields['follower_type'] = strtolower(trim((string) $follower_type));
		}

		return parent::load($fields);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->_errors = array();

		$this->follower_id = intval($this->follower_id);
		if (!$this->follower_id)
		{
			$this->follower_type = 'member';
			$this->follower_id   = User::get('id');
		}

		$this->following_id = intval($this->following_id);
		if (!$this->following_id)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_FOLLOWING_ID'));
		}

		$this->following_type = trim($this->following_type);
		if (!$this->following_type)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_FOLLOWING_TYPE'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->id)
		{
			$this->created = Date::toSql();
		}

		return true;
	}

	/**
	 * Build a query based off of filters passed
	 *
	 * @param   array   $filters  Filters to construct query from
	 * @return  string  SQL
	 */
	private function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS f";

		$where = array();

		if (isset($filters['following_id']) && $filters['following_id'])
		{
			$where[] = "f.following_id=" . $this->_db->quote($filters['following_id']);
		}
		if (isset($filters['following_type']) && $filters['following_type'])
		{
			$where[] = "f.following_type=" . $this->_db->quote($filters['following_type']);
		}
		if (isset($filters['follower_id']) && $filters['follower_id'])
		{
			$where[] = "f.follower_id=" . $this->_db->quote($filters['follower_id']);
		}
		if (isset($filters['follower_type']) && $filters['follower_type'])
		{
			$where[] = "f.follower_type=" . $this->_db->quote($filters['follower_type']);
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
	 * @param   array    $filters  Filters to construct query from
	 * @return  integer
	 */
	public function count($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to construct query from
	 * @return  array
	 */
	public function find($filters=array())
	{
		$query = "SELECT f.*,
			(SELECT COUNT(*) FROM `#__collections_following` AS fg WHERE fg.following_id=f.following_id AND fg.following_type=f.following_type) AS followers,
			(SELECT COUNT(*) FROM `#__collections_following` AS fr WHERE fr.follower_id=f.following_id AND fr.follower_type=f.following_type) AS following";
		$query .= $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'f.created';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= ' LIMIT ' . intval($filters['start']) . ',' . intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
