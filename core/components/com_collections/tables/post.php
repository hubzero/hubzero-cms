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
 * Table class for collection posts
 */
class Post extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__collections_posts', 'id', $db);
	}

	/**
	 * Load a record by its collection and item IDs
	 *
	 * @param   integer  $collection_id
	 * @param   integer  $item_id
	 * @return  boolean  True upon success, False if errors
	 */
	public function loadByBoard($collection_id=null, $item_id=null)
	{
		$fields = array(
			'collection_id' => (int) $collection_id,
			'item_id'       => (int) $item_id
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
		$this->collection_id = intval($this->collection_id);
		if (!$this->collection_id)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_COLLECTION_ID'));
		}

		$this->item_id = intval($this->item_id);
		if (!$this->item_id)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_ITEM_ID'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');

			if (array_key_exists('ordering', $this->getFields()) && !$this->ordering)
			{
				$this->_db->setQuery("SELECT MAX(ordering)+1 FROM $this->_tbl WHERE collection_id=" . $this->_db->quote($this->collection_id));

				$this->ordering = $this->_db->loadResult();
				$this->ordering = ($this->ordering ?: 1);
			}
		}

		return true;
	}

	/**
	 * Return data based on a set of filters. Returned value 
	 * can be integer, object, or array
	 * 
	 * @param   string  $what
	 * @param   array   $filters
	 * @return  mixed
	 */
	public function find($what='', $filters=array())
	{
		$what = strtolower(trim($what));

		switch ($what)
		{
			case 'count':
				$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

				$this->_db->setQuery($query);
				return $this->_db->loadResult();
			break;

			case 'one':
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'first':
				$filters['start'] = 0;
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'all':
				if (isset($filters['limit']))
				{
					unset($filters['limit']);
				}
				return $this->find('list', $filters);
			break;

			case 'list':
			default:
				$query = "SELECT p.*, c.alias, c.title, c.object_type, c.object_id, u.name,
						i.title AS item_title,
						i.description AS item_description,
						i.url AS item_url,
						i.created AS item_created,
						i.created_by AS item_created_by,
						i.positive AS item_positive,
						i.state AS item_state,
						i.access AS item_access,
						i.negative AS item_negative,
						i.type AS item_type,
						i.object_id As item_object_id,
						(SELECT COUNT(*) FROM `#__collections_posts` AS s WHERE s.item_id=p.item_id AND s.original=0) AS item_reposts,
						(SELECT COUNT(*) FROM `#__item_comments` AS ct WHERE ct.item_id=p.item_id AND ct.item_type='collection' AND ct.state IN (1, 3)) AS item_comments";
				if (isset($filters['user_id']) && $filters['user_id'])
				{
					$query .= ", v.id AS item_voted ";
				}
				$query .= $this->_buildQuery($filters);

				if (!isset($filters['sort']) || !$filters['sort'])
				{
					$filters['sort'] = 'p.created';
				}
				if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
				{
					$filters['sort_Dir'] = 'DESC';
				}
				$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

				if (isset($filters['limit']) && $filters['limit'] > 0)
				{
					$filters['start'] = (isset($filters['start']) ? $filters['start'] : 0);

					$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}

	/**
	 * Build a query based off of filters passed
	 *
	 * @param   array   $filters  Filters to construct query from
	 * @return  string  SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS p";
		$query .= " INNER JOIN #__collections AS c ON c.id=p.collection_id";
		$query .= " INNER JOIN #__collections_items AS i ON p.item_id=i.id";
		$query .= " LEFT JOIN #__users AS u ON p.created_by=u.id";

		if (isset($filters['user_id']) && $filters['user_id'])
		{
			$query .= " LEFT JOIN #__collections_votes AS v ON v.item_id=p.item_id AND v.user_id=" . $this->_db->quote($filters['user_id']);
		}

		$where = array();

		if (isset($filters['collection_id']) && $filters['collection_id'])
		{
			if (is_array($filters['collection_id']))
			{
				$filters['collection_id'] = array_map('intval', $filters['collection_id']);
				$where[] = "p.collection_id IN (" . implode(',', $filters['collection_id']) . ")";
			}
			else
			{
				$where[] = "p.collection_id=" . $this->_db->quote($filters['collection_id']);
			}
		}
		if (isset($filters['object_id']) && $filters['object_id'])
		{
			$where[] = "c.object_id=" . $this->_db->quote($filters['object_id']);
		}
		if (isset($filters['object_type']) && $filters['object_type'])
		{
			$where[] = "c.object_type=" . $this->_db->quote($filters['object_type']);
		}
		if (isset($filters['created_by']) && $filters['created_by'])
		{
			$where[] = "p.created_by=" . $this->_db->quote($filters['created_by']);
		}
		if (isset($filters['item_id']) && $filters['item_id'])
		{
			$where[] = "p.item_id=" . $this->_db->quote($filters['item_id']);
		}
		if (isset($filters['state']))
		{
			if (is_array($filters['state']))
			{
				$filters['state'] = array_map('intval', $filters['state']);
				$where[] = "i.state IN (" . implode(',', $filters['state']) . ")";
				$where[] = "c.state IN (" . implode(',', $filters['state']) . ")";
			}
			else if ($filters['state'] >= 0)
			{
				$where[] = "i.state=" . $this->_db->quote($filters['state']);
				$where[] = "c.state=" . $this->_db->quote($filters['state']);
			}
		}
		if (isset($filters['access']))
		{
			if (is_array($filters['access']))
			{
				$filters['access'] = array_map('intval', $filters['access']);
				$where[] = "i.access IN (" . implode(',', $filters['access']) . ")";
				$where[] = "c.access IN (" . implode(',', $filters['access']) . ")";
			}
			else if ($filters['access'] >= 0)
			{
				$where[] = "i.access=" . $this->_db->quote($filters['access']);
				$where[] = "c.access=" . $this->_db->quote($filters['access']);
			}
		}
		if (isset($filters['original']))
		{
			$where[] = "p.original=" . $this->_db->quote($filters['original']);
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(i.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . " OR
						LOWER(i.description) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . " OR
						LOWER(p.description) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
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
	public function getCount($filters=array())
	{
		return $this->find('count', $filters);
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to construct query from
	 * @return  array
	 */
	public function getRecords($filters=array())
	{
		return $this->find('list', $filters);
	}
}
