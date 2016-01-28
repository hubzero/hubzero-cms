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
 * Table class for collections
 */
class Collection extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__collections', 'id', $db);

		$this->access = 0;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   mixed    $oid          Record alias
	 * @param   integer  $object_id    Object ID
	 * @param   string   $object_type  Object type
	 * @return  boolean  True on success
	 */
	public function load($oid=NULL, $object_id=null, $object_type=null)
	{
		if ($oid === NULL)
		{
			return false;
		}

		if (is_numeric($oid))
		{
			return parent::load($oid);
		}

		$query = "SELECT * FROM $this->_tbl WHERE state!=2 AND alias=" . $this->_db->quote(trim($oid));
		if ($object_id !== null)
		{
			$query .= " AND object_id=" . $this->_db->quote(intval($object_id));
		}
		if ($object_type !== null)
		{
			$query .= " AND object_type=" . $this->_db->quote(strtolower(trim($object_type)));
		}

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
	 * Load a record by its alias and bind data to $this
	 *
	 * @param   integer  $object_id    Object ID
	 * @param   string   $object_type  Object type
	 * @return  boolean  True upon success, False if errors
	 */
	public function loadDefault($object_id=null, $object_type=null)
	{
		if (!$object_id || !$object_type)
		{
			return false;
		}

		$fields = array(
			'object_id'   => intval($object_id),
			'object_type' => trim($object_type),
			'is_default'  => 1
		);

		return parent::load($fields);
	}

	/**
	 * Populate the object with default data
	 *
	 * @param   integer  $object_id    Object ID
	 * @param   string   $object_type  Object type
	 * @return  boolean  True if data is bound to $this object
	 */
	public function setup($object_id=0, $object_type='')
	{
		Lang::load('com_collections');

		$result = array(
			'id'          => 0,
			'title'       => Lang::txt('COM_COLLECTIONS_DEFAULT_TITLE'),
			'description' => Lang::txt('COM_COLLECTIONS_DEFAULT_DESC'),
			'object_id'   => $object_id,
			'object_type' => $object_type,
			'is_default'  => 1,
			'created_by'  => $object_id,
			'access'      => 4 // Private by default
		);
		if (!$result['created_by'])
		{
			$result['created_by'] = User::get('id');
		}
		if (!$this->bind($result))
		{
			return false;
		}
		if (!$this->check())
		{
			return false;
		}
		if (!$this->store())
		{
			return false;
		}
		return $this->loadDefault($object_id, $object_type);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->title = trim($this->title);
		if (!$this->title)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_TITLE'));
		}

		$this->alias = str_replace(' ', '-', strtolower($this->title));
		$this->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', $this->alias);
		if (!$this->alias)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_ALIAS'));
		}

		$this->object_id = intval($this->object_id);
		if (!$this->object_id)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_OBJECT_ID'));
		}

		$this->object_type = trim($this->object_type);
		if (!$this->object_type)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_OBJECT_TYPE'));
		}

		if ($this->getError())
		{
			return false;
		}

		if ($this->access === null)
		{
			$this->access = 0;
		}

		$this->access    = intval($this->access);
		$this->state     = intval($this->state);

		$tbl = new self($this->_db);
		$tbl->load($this->alias, $this->object_id, $this->object_type);

		if (!$this->id)
		{
			if ($tbl->id && $tbl->state != 2)
			{
				$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_COLLECTION_EXISTS'));
				return false;
			}

			$this->created    = ($this->created ? $this->created : Date::toSql());
			$this->created_by = ($this->created_by ? $this->created_by : User::get('id'));
			$this->state      = 1;
		}
		else
		{
			if ($tbl->id
			 && $tbl->id != $this->id
			 && $tbl->state != 2)
			{
				$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_COLLECTION_EXISTS'));
				return false;
			}
		}

		return true;
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
		$query .= " LEFT JOIN `#__collections_items` AS im ON im.type=" . $this->_db->quote('collection') . " AND im.object_id=b.id";
		$query .= " LEFT JOIN `#__collections_following` AS f ON f.following_type=" . $this->_db->quote('collection') . " AND f.following_id=b.id";
		if (isset($filters['user_id']) && $filters['user_id'])
		{
			$query .= " AND f.follower_type='member' AND f.follower_id=" . $this->_db->quote($filters['user_id']);
		}
		if (isset($filters['object_type']) && $filters['object_type'] == 'group')
		{
			$query .= " LEFT JOIN `#__xgroups` AS g ON g.gidNumber=b.object_id AND b.object_type=" . $this->_db->quote('group');
		}

		$where = array();

		if (isset($filters['item_id']) && $filters['item_id'])
		{
			$query .= " INNER JOIN #__collections_posts AS p ON p.collection_id=b.id";
			$where[] = "p.item_id=" . $this->_db->quote($filters['item_id']);
			if (isset($filters['collection_id']))
			{
				if (is_array($filters['collection_id']))
				{
					$filters['collection_id'] = array_map('intval', $filters['collection_id']);
					$where[] = "b.id NOT IN (" . implode(',', $filters['collection_id']) . ")";
				}
				else if ($filters['collection_id'] > 0)
				{
					$where[] = "b.id != " . $this->_db->quote(intval($filters['collection_id']));
				}
			}
		}

		if (isset($filters['state']) && $filters['state'] >= 0)
		{
			$where[] = "b.state=" . $this->_db->quote(intval($filters['state']));
		}
		if (isset($filters['access']))
		{
			if (is_array($filters['access']))
			{
				$filters['access'] = array_map('intval', $filters['access']);
				$where[] = "b.access IN (" . implode(',', $filters['access']) . ")";
			}
			else if ($filters['access'] >= 0)
			{
				$where[] = "b.access=" . $this->_db->quote(intval($filters['access']));
			}
		}
		if (isset($filters['is_default']) && $filters['is_default'] >= 0)
		{
			$where[] = "b.is_default=" . $this->_db->quote(intval($filters['is_default']));
		}

		if (isset($filters['object_id']) && $filters['object_id'])
		{
			$where[] = "b.object_id=" . $this->_db->quote(intval($filters['object_id']));
		}
		if (isset($filters['object_type']) && $filters['object_type'])
		{
			$where[] = "b.object_type=" . $this->_db->quote($filters['object_type']);
		}

		if (isset($filters['created']) && $filters['created'])
		{
			$where[] = "b.created=" . $this->_db->quote($filters['created']);
		}
		if (isset($filters['created_by']) && $filters['created_by'])
		{
			$where[] = "b.created_by=" . $this->_db->quote(intval($filters['created_by']));
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(b.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
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
				$query = "SELECT COUNT(DISTINCT b.id) " . $this->_buildQuery($filters);

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
				$access = "";
				if (isset($filters['access']))
				{
					if (is_array($filters['access']))
					{
						$filters['access'] = array_map('intval', $filters['access']);
						$access = "AND i.access IN (" . implode(',', $filters['access']) . ")";
					}
					else if ($filters['access'] >= 0)
					{
						$access = "AND i.access=" . $this->_db->quote(intval($filters['access']));
					}
				}

				$query = "SELECT DISTINCT b.*, im.id AS item_id, im.positive AS likes, f.following_id AS following, (SELECT COUNT(*) FROM `#__collections_items` AS i INNER JOIN `#__collections_posts` AS s ON s.item_id=i.id WHERE s.collection_id=b.id AND i.state=1 $access) AS posts";
				if (isset($filters['object_type']) && $filters['object_type'] == 'group')
				{
					$query .= ", g.cn AS group_alias";
				}
				$query .= $this->_buildQuery($filters);

				if (!isset($filters['sort']) || !$filters['sort'])
				{
					$filters['sort'] = 'created';
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
