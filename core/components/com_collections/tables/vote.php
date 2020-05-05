<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Tables;

use Hubzero\Database\Table;
use Date;
use User;
use Lang;

/**
 * Table class for post votes
 */
class Vote extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__collections_votes', 'id', $db);
	}

	/**
	 * Load a record by its bulletin and user IDs
	 *
	 * @param   integer  $item_id  Bulletin ID
	 * @param   integer  $user_id  User ID
	 * @return  boolean  True upon success, False if errors
	 */
	public function loadByBulletin($item_id=null, $user_id=null)
	{
		$fields = array(
			'item_id' => (int) $item_id,
			'user_id' => (int) $user_id
		);

		return parent::load($fields);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->item_id = intval($this->item_id);
		if (!$this->item_id)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_ITEM_ID'));
			return false;
		}

		if (!$this->id)
		{
			$this->voted   = Date::toSql();
			$this->user_id = User::get('id');
		}

		return true;
	}

	/**
	 * Get a total of all likes
	 *
	 * @param   array    $filters  Filters to construct query from
	 * @return  integer
	 */
	public function getLikes($filters=array())
	{
		$query = "SELECT COUNT(*) FROM $this->_tbl AS v
				INNER JOIN `#__collections_posts` AS s ON s.item_id=v.item_id";

		$where = array();

		if (isset($filters['user_id']) && $filters['user_id'])
		{
			$where[] = "v.user_id=" . $this->_db->quote(intval($filters['user_id']));
		}
		if (isset($filters['collection_id']) && $filters['collection_id'])
		{
			if (is_array($filters['collection_id']))
			{
				$filters['collection_id'] = array_map('intval', $filters['collection_id']);
				$where[] = "s.collection_id IN (" . implode(',', $filters['collection_id']) . ")";
			}
			else
			{
				$where[] = "s.collection_id=" . $this->_db->quote(intval($filters['collection_id']));
			}
		}
		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a total of all dislikes
	 *
	 * @param   array    $filters  Filters to construct query from
	 * @return  integer
	 */
	public function getDislikes($filters=array())
	{
		$query = "SELECT COUNT(*) FROM $this->_tbl AS v
				INNER JOIN `#__collections_posts` AS s ON s.item_id=v.item_id";

		$where = array();

		if (isset($filters['user_id']) && $filters['user_id'])
		{
			$where[] = "v.user_id=" . $this->_db->quote(intval($filters['user_id']));
		}
		if (isset($filters['collection_id']) && $filters['collection_id'])
		{
			if (is_array($filters['collection_id']))
			{
				$filters['collection_id'] = array_map('intval', $filters['collection_id']);
				$where[] = "s.collection_id IN (" . implode(',', $filters['collection_id']) . ")";
			}
			else
			{
				$where[] = "s.collection_id=" . $this->_db->quote(intval($filters['collection_id']));
			}
		}
		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}
