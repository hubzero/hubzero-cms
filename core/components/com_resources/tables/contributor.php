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

namespace Components\Resources\Tables;

/**
 * Table class for resource contributor
 */
class Contributor extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__author_assoc', 'authorid', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (!$this->authorid)
		{
			$this->setError(\Lang::txt('Must have an author ID.'));
		}

		if (!$this->subid)
		{
			$this->setError(\Lang::txt('Must have an item ID.'));
		}

		if (!$this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   integer  $authorid  Member ID
	 * @param   integer  $subid     Object ID
	 * @param   string   $subtable  Object type (resource)
	 * @return  boolean  True on success
	 */
	public function loadAssociation($authorid=NULL, $subid=NULL, $subtable='')
	{
		if (!$authorid)
		{
			$authorid = $this->authorid;
		}
		if (!$authorid)
		{
			return false;
		}
		if (!$subid)
		{
			$subid = $this->subid;
		}
		if (!$subtable)
		{
			$subtable = $this->subtable;
		}

		if (is_numeric($authorid))
		{
			$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE subid=" . $this->_db->quote($subid) . " AND subtable=" . $this->_db->quote($subtable) . " AND authorid=" . $this->_db->quote($authorid));
		}
		else
		{
			$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE subid=" . $this->_db->quote($subid) . " AND subtable=" . $this->_db->quote($subtable) . " AND authorid < 0 AND name=" . $this->_db->quote($authorid));
		}
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
	 * Delete all associations for a user
	 *
	 * @param   integer  $id  User ID
	 * @return  boolean  True on success
	 */
	public function deleteAssociations($id=NULL)
	{
		if (!$id)
		{
			$id = $this->authorid;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE authorid=" . $this->_db->quote($id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Delete a record by user and resource
	 *
	 * @param   integer  $authorid  Member ID
	 * @param   integer  $subid     Object ID
	 * @param   string   $subtable  Object type (resource)
	 * @return  boolean  True on success
	 */
	public function deleteAssociation($authorid=NULL, $subid=NULL, $subtable='')
	{
		if (!$authorid)
		{
			$authorid = $this->authorid;
		}
		if (!$authorid)
		{
			return false;
		}
		if (!$subid)
		{
			$subid = $this->subid;
		}
		if (!$subtable)
		{
			$subtable = $this->subtable;
		}

		//if (is_numeric($authorid))
		//{
			$query = "DELETE FROM $this->_tbl WHERE subtable=" . $this->_db->quote($subtable) . " AND subid=" . $this->_db->quote($subid) . " AND authorid=" . $this->_db->quote($authorid);
		/*}
		else
		{
			$query = "DELETE FROM $this->_tbl WHERE subtable=" . $this->_db->quote($subtable) . " AND subid=" . $this->_db->quote($subid) . " AND authorid=0 AND name=" . $this->_db->quote($authorid);
		}*/

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Create a new record
	 *
	 * @return  boolean  True on success
	 */
	public function createAssociation()
	{
		$query = "INSERT INTO $this->_tbl (subtable, subid, authorid, ordering, role, name, organization)
					VALUES(" . $this->_db->quote($this->subtable) . ", " . $this->_db->quote($this->subid) . ", " . $this->_db->quote($this->authorid) . ", " . $this->_db->quote($this->ordering) . ", " . $this->_db->quote($this->role) . ", " . $this->_db->quote($this->name) . ", " . $this->_db->quote($this->organization) . ")";
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Update a record
	 *
	 * @return  boolean  True on success
	 */
	public function updateAssociation()
	{
		$query = "UPDATE $this->_tbl
					SET ordering=" . $this->_db->quote($this->ordering) . ", role=" . $this->_db->quote($this->role) . ", name=" . $this->_db->quote($this->name) . ", organization=" . $this->_db->quote($this->organization) . "
					WHERE subtable=" . $this->_db->quote($this->subtable) . " AND subid=" . $this->_db->quote($this->subid) . " AND authorid=" . $this->_db->quote($this->authorid);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Get a record count for a resource
	 *
	 * @param   integer  $subid     Object ID
	 * @param   string   $subtable  Object type ('resource')
	 * @return  integer
	 */
	public function getCount($subid=NULL, $subtable=null)
	{
		if (!$subid)
		{
			$subid = $this->subid;
		}
		if (!$subid)
		{
			return null;
		}
		if (!$subtable)
		{
			$subtable = $this->subtable;
		}
		if (!$subtable)
		{
			return null;
		}
		$this->_db->setQuery("SELECT count(*) FROM $this->_tbl WHERE subid=" . $this->_db->quote($subid) . " AND subtable=" . $this->_db->quote($subtable));
		return $this->_db->loadResult();
	}

	/**
	 * Get the last number in an ordering
	 *
	 * @param   integer  $subid     Object ID
	 * @param   string   $subtable  Object type ('resource')
	 * @return  integer
	 */
	public function getLastOrder($subid=NULL, $subtable=null)
	{
		if (!$subid)
		{
			$subid = $this->subid;
		}
		if (!$subid)
		{
			return null;
		}
		if (!$subtable)
		{
			$subtable = $this->subtable;
		}
		if (!$subtable)
		{
			return null;
		}
		$this->_db->setQuery("SELECT ordering FROM $this->_tbl WHERE subid=" . $this->_db->quote($subid) . " AND subtable=" . $this->_db->quote($subtable) . " ORDER BY ordering DESC LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get the record directly before or after this record
	 *
	 * @param   string   $move  Direction to look
	 * @return  boolean  True on success
	 */
	public function getNeighbor($move)
	{
		switch ($move)
		{
			case 'orderup':
				$sql = "SELECT * FROM $this->_tbl WHERE subid=" . $this->_db->quote($this->subid) . " AND subtable=" . $this->_db->quote($this->subtable) . " AND ordering < " . $this->_db->quote($this->ordering) . " ORDER BY ordering DESC LIMIT 1";
			break;

			case 'orderdown':
				$sql = "SELECT * FROM $this->_tbl WHERE subid=" . $this->_db->quote($this->subid) . " AND subtable=" . $this->_db->quote($this->subtable) . " AND ordering > " . $this->_db->quote($this->ordering) . " ORDER BY ordering LIMIT 1";
			break;
		}
		$this->_db->setQuery($sql);
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
	 * Get the last ID
	 *
	 * @return  integer
	 */
	public function getLastUserId()
	{
		$this->_db->setQuery("SELECT authorid FROM $this->_tbl ORDER BY authorid ASC LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get the user ID for a name
	 *
	 * @param   string   $name  Name to look up
	 * @return  integer
	 */
	public function getUserId($name)
	{
		$this->_db->setQuery("SELECT authorid FROM $this->_tbl WHERE name=" . $this->_db->quote($name) . " AND authorid < 0 LIMIT 1");
		$uid = $this->_db->loadResult();
		if (!$uid || $uid > 0)
		{
			$uid = $this->getLastUserId();

			// Check for potentially conflicting profile
			$this->_db->setQuery("SELECT uidNumber FROM `#__xprofiles` ORDER BY uidNumber ASC LIMIT 1");
			$pid = $this->_db->loadResult();
			if ($pid < $uid)
			{
				$uid = $pid;
			}

			if ($uid > 0)
			{
				$uid = 0;
			}
			$uid--;
		}
		return $uid;
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	public function buildQuery($filters=array())
	{
		$sql = "FROM $this->_tbl AS m ";

		$w = array();
		$w[] = "m.subtable='resources'";
		if (isset($filters['subid']) && $filters['subid'])
		{
			$w[] = "m.subid=" . $this->_db->quote($filters['subid']);
		}
		if (isset($filters['state']))
		{
			$w[] = "m.state=" . $this->_db->quote($filters['state']);
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$w[] = "m.name LIKE " . $this->_db->quote('%' . $filters['search'] . '%');
		}

		$sql .= (count($w) > 0) ? "WHERE " : "";
		$sql .= implode(" AND ", $w);

		return $sql;
	}

	/**
	 * Get a record count
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getAuthorCount($filters=array())
	{
		$filters['limit'] = '';
		$query = "SELECT count(DISTINCT m.authorid) " . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getAuthorRecords($filters=array())
	{
		$query = "SELECT DISTINCT m.authorid, m.name, (SELECT COUNT(DISTINCT w.subid) FROM $this->_tbl AS w WHERE w.authorid = m.authorid) AS resources " . $this->buildQuery($filters);
		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'name';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'ASC';
		}
		if (!in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		if (isset($filters['limit']) && $filters['limit'] != '')
		{
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get records for a specific author
	 *
	 * @param   integer  $authorid  Author ID
	 * @return  array
	 */
	public function getRecordsForAuthor($authorid)
	{
		$query = "SELECT * FROM $this->_tbl WHERE authorid=" . $this->_db->quote($authorid);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

