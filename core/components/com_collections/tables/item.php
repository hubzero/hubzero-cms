<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Tables;

use Date;
use User;
use Lang;

/**
 * Table class for collection items
 */
class Item extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__collections_items', 'id', $db);

		$this->access = 0;
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->title       = trim($this->title);
		$this->description = trim($this->description);
		$this->url         = trim($this->url);

		if ($this->type != 'image' && $this->type != 'file'
		 && (!$this->title && !$this->description && !$this->url))
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_CONTENT'));
			return false;
		}

		if ($this->access === null)
		{
			$this->access = 0;
		}

		$this->access    = intval($this->access);
		$this->state     = intval($this->state);
		$this->object_id = intval($this->object_id);

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');
		}
		else
		{
			$this->modified    = Date::toSql();
			$this->modified_by = User::get('id');
			if (!$this->created_by)
			{
				$this->created    = $this->modified;
				$this->created_by = $this->modified_by;
			}
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   string   $oid  Description
	 * @return  boolean  True on success
	 */
	public function loadByDescription($oid=NULL)
	{
		$fields = array(
			'description' => trim((string) $oid)
		);

		return parent::load($fields);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   string   $oid  Title
	 * @return  boolean  True on success
	 */
	public function loadByTitle($oid=NULL)
	{
		$fields = array(
			'title' => trim((string) $oid)
		);

		return parent::load($fields);
	}

	/**
	 * Load a record by its alias and bind data to $this
	 *
	 * @param   string   $oid  Record alias
	 * @return  boolean  True upon success, False if errors
	 */
	public function loadType($object_id=null, $object_type=null)
	{
		$fields = array(
			'object_id' => (int) $object_id,
			'type'      => (string) $object_type
		);

		return parent::load($fields);
	}

	/**
	 * Return data based on a set of filters. Returned value 
	 * can be integer, object, or array
	 * 
	 * @param   string $what
	 * @param   array  $filters
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
				$query = "SELECT b.*, u.name AS poster_name, s.description AS user_description, s.created AS posted, s.created_by AS poster, s.original, s.id AS post_id, s.collection_id,
						(SELECT COUNT(*) FROM `#__collections_posts` AS s WHERE s.item_id=b.id AND s.original=0) AS reposts,
						(SELECT COUNT(*) FROM `#__item_comments` AS c WHERE c.item_id=b.id AND c.item_type='collection' AND c.state IN (1, 3)) AS comments";
				if (isset($filters['user_id']) && $filters['user_id'])
				{
					$query .= ", v.id AS voted ";
				}
				if (!isset($filters['collection_id']) || !$filters['collection_id'])
				{
					$query .= ", d.id AS collection_id, d.title AS board_title, d.object_id, d.object_type ";
				}
				$query .= $this->_buildQuery($filters);

				if (!isset($filters['sort']) || !$filters['sort'])
				{
					$filters['sort'] = 'posted';
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
		$query  = " FROM $this->_tbl AS b";
		$query .= " INNER JOIN #__collections_posts AS s ON s.item_id=b.id";
		if (!isset($filters['collection_id']) || !$filters['collection_id'])
		{
			$query .= " INNER JOIN #__collections AS d ON s.collection_id=d.id";
		}
		$query .= " LEFT JOIN #__users AS u ON s.created_by=u.id";

		if (isset($filters['user_id']) && $filters['user_id'])
		{
			$query .= " LEFT JOIN #__collections_votes AS v ON v.item_id=b.id AND v.user_id=" . $this->_db->quote($filters['user_id']);
		}

		$where = array();

		if (isset($filters['collection_id']) && $filters['collection_id'])
		{
			if (is_array($filters['collection_id']))
			{
				$filters['collection_id'] = array_map('intval', $filters['collection_id']);
				$where[] = "s.collection_id IN (" . implode(',', $filters['collection_id']) . ")";
			}
			else
			{
				$where[] = "s.collection_id=" . $this->_db->quote($filters['collection_id']);
			}
		}
		else
		{
			if (isset($filters['created_by']) && $filters['created_by'])
			{
				$where[] = "b.created_by=" . $this->_db->quote($filters['created_by']);
			}
			else if (!\App::isAdmin())
			{
				$where[] = "d.access=0";
				$where[] = "s.id = (SELECT MAX(s2.id) FROM #__collections_posts s2 WHERE s.item_id = s2.item_id)";
				if (isset($filters['trending']))
				{
					$where[] = "s.created >= DATE_FORMAT(UTC_TIMESTAMP(), '%Y-%m-01 00:00:00')";
				}
			}
		}

		if (isset($filters['state']))
		{
			if (is_array($filters['state']))
			{
				$filters['state'] = array_map('intval', $filters['state']);
				$where[] = "b.state IN (" . implode(',', $filters['state']) . ")";
			}
			else if ($filters['state'] >= 0)
			{
				$where[] = "b.state=" . $this->_db->quote(intval($filters['state']));
			}
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(b.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(b.description) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
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

	/**
	 * Get a repost count
	 *
	 * @param   mixed    $id
	 * @return  integer
	 */
	public function getReposts($id=null)
	{
		if (!$id)
		{
			$id = $this->id;
		}
		if (!$id)
		{
			return false;
		}

		if (is_array($id))
		{
			$query = "SELECT COUNT(*) FROM `#__collections_posts` AS s INNER JOIN `$this->_tbl` AS i ON s.item_id=i.id WHERE i.type=" . $this->_db->quote($id['object_type']) . " AND i.object_id=" . $this->_db->quote($id['object_id']) . " AND s.original=" . $this->_db->quote('0');
		}
		else
		{
			$query = "SELECT COUNT(*) FROM `#__collections_posts` AS s WHERE s.item_id=" . $this->_db->quote(intval($id)) . " AND s.original=" . $this->_db->quote('0');
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a record count
	 *
	 * @param   array    $filters  Filters to construct query from
	 * @return  integer
	 */
	public function getVote($id=null)
	{
		if (!$id)
		{
			$id = $this->id;
		}
		$id = intval($id);
		if (!$id)
		{
			return false;
		}

		$query = "SELECT v.id FROM `#__collections_votes` AS v WHERE v.item_id=" . $this->_db->quote($id) . " AND v.user_id=" . $this->_db->quote(User::get('id'));

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a total of all likes
	 *
	 * @param   array    $filters  Filters to construct query from
	 * @return  integer
	 */
	public function getLikes($filters=array())
	{
		if (isset($filters['object_id']))
		{
			$query = "SELECT positive FROM `$this->_tbl` WHERE type=" . $this->_db->quote($filters['object_type']) . " AND object_id=" . $this->_db->quote($filters['object_id']);
		}
		else
		{
			$query = "SELECT positive FROM `$this->_tbl` WHERE id=" . $this->_db->quote(intval($filters['id']));
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
		if (isset($filters['object_id']))
		{
			$query = "SELECT negative FROM `$this->_tbl` WHERE type=" . $this->_db->quote($filters['object_type']) . " AND object_id=" . $this->_db->quote($filters['object_id']);
		}
		else
		{
			$query = "SELECT negative FROM `$this->_tbl` WHERE id=" . $this->_db->quote(intval($filters['id']));
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to construct query from
	 * @return  array
	 */
	public function getTrending($filters=array())
	{
		$filters['collection_id'] = 0;
		$query = "SELECT b.*, u.name, s.created AS posted, s.created_by AS poster, s.original, s.id AS post_id,
				(SELECT COUNT(*) FROM `#__collections_posts` AS s WHERE s.item_id=b.id AND s.original=0) AS reposts,
				(SELECT COUNT(*) FROM `#__item_comments` AS c WHERE c.item_id=b.id AND c.item_type='bulletin' AND c.state IN (1, 3)) AS comments";
		if (isset($filters['user_id']) && $filters['user_id'])
		{
			$query .= ", v.id AS voted ";
		}
		$query .= $this->buildQuery($filters);

		if ($filters['limit'] != 0)
		{
			$query .= ' LIMIT ' . intval($filters['start']) . ',' . intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
