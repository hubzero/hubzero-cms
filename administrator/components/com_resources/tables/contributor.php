<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for resource contributor
 */
class ResourcesContributor extends JTable
{
	/**
	 * varchar(50) Primary Key
	 *
	 * @var string
	 */
	var $subtable = NULL;

	/**
	 * int(11) Primary Key
	 *
	 * @var integer
	 */
	var $subid    = NULL;

	/**
	 * int(11) Primary Key
	 *
	 * @var integer
	 */
	var $authorid = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $ordering = NULL;

	/**
	 * varchar(50)
	 *
	 * @var string
	 */
	var $role     = NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $name     = NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $organization = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__author_assoc', 'authorid', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (!$this->authorid)
		{
			$this->setError(JText::_('Must have an author ID.'));
			return false;
		}

		if (!$this->subid)
		{
			$this->setError(JText::_('Must have an item ID.'));
			return false;
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      integer $authorid Member ID
	 * @param      integer $subid    Object ID
	 * @param      string  $subtable Object type (resource)
	 * @return     boolean True on success
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
			$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE subid=" . $this->_db->Quote($subid) . " AND subtable=" . $this->_db->Quote($subtable) . " AND authorid=" . $this->_db->Quote($authorid));
		}
		else
		{
			$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE subid=" . $this->_db->Quote($subid) . " AND subtable=" . $this->_db->Quote($subtable) . " AND authorid < 0 AND name=" . $this->_db->Quote($authorid));
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
	 * @param      integer $id User ID
	 * @return     boolean True on success
	 */
	public function deleteAssociations($id=NULL)
	{
		if (!$id)
		{
			$id = $this->authorid;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE authorid=" . $this->_db->Quote($id));
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
	 * @param      integer $authorid Member ID
	 * @param      integer $subid    Object ID
	 * @param      string  $subtable Object type (resource)
	 * @return     boolean True on success
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
			$query = "DELETE FROM $this->_tbl WHERE subtable=" . $this->_db->Quote($subtable) . " AND subid=" . $this->_db->Quote($subid) . " AND authorid=" . $this->_db->Quote($authorid);
		/*}
		else
		{
			$query = "DELETE FROM $this->_tbl WHERE subtable=" . $this->_db->Quote($subtable) . " AND subid=" . $this->_db->Quote($subid) . " AND authorid=0 AND name=" . $this->_db->Quote($authorid);
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
	 * @return     boolean True on success
	 */
	public function createAssociation()
	{
		$query = "INSERT INTO $this->_tbl (subtable, subid, authorid, ordering, role, name, organization)
					VALUES(" . $this->_db->Quote($this->subtable) . ", " . $this->_db->Quote($this->subid) . ", " . $this->_db->Quote($this->authorid) . ", " . $this->_db->Quote($this->ordering) . ", " . $this->_db->Quote($this->role) . ", " . $this->_db->Quote($this->name) . ", " . $this->_db->Quote($this->organization) . ")";
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
	 * @return     boolean True on success
	 */
	public function updateAssociation()
	{
		$query = "UPDATE $this->_tbl
					SET ordering=" . $this->_db->Quote($this->ordering) . ", role=" . $this->_db->Quote($this->role) . ", name=" . $this->_db->Quote($this->name) . ", organization=" . $this->_db->Quote($this->organization) . "
					WHERE subtable=" . $this->_db->Quote($this->subtable) . " AND subid=" . $this->_db->Quote($this->subid) . " AND authorid=" . $this->_db->Quote($this->authorid);
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
	 * @param      integer $subid    Object ID
	 * @param      string  $subtable Object type ('resource')
	 * @return     integer
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
		$this->_db->setQuery("SELECT count(*) FROM $this->_tbl WHERE subid=" . $this->_db->Quote($subid) . " AND subtable=" . $this->_db->Quote($subtable));
		return $this->_db->loadResult();
	}

	/**
	 * Get the last number in an ordering
	 *
	 * @param      integer $subid    Object ID
	 * @param      string  $subtable Object type ('resource')
	 * @return     integer
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
		$this->_db->setQuery("SELECT ordering FROM $this->_tbl WHERE subid=" . $this->_db->Quote($subid) . " AND subtable=" . $this->_db->Quote($subtable) . " ORDER BY ordering DESC LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get the record directly before or after this record
	 *
	 * @param      string $move Direction to look
	 * @return     boolean True on success
	 */
	public function getNeighbor($move)
	{
		switch ($move)
		{
			case 'orderup':
				$sql = "SELECT * FROM $this->_tbl WHERE subid=" . $this->_db->Quote($this->subid) . " AND subtable=" . $this->_db->Quote($this->subtable) . " AND ordering < " . $this->_db->Quote($this->ordering) . " ORDER BY ordering DESC LIMIT 1";
			break;

			case 'orderdown':
				$sql = "SELECT * FROM $this->_tbl WHERE subid=" . $this->_db->Quote($this->subid) . " AND subtable=" . $this->_db->Quote($this->subtable) . " AND ordering > " . $this->_db->Quote($this->ordering) . " ORDER BY ordering LIMIT 1";
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
	 * @return     integer
	 */
	public function getLastUserId()
	{
		$this->_db->setQuery("SELECT authorid FROM $this->_tbl ORDER BY authorid ASC LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get the user ID for a name
	 *
	 * @param      string $name Name to look up
	 * @return     integer
	 */
	public function getUserId($name)
	{
		$this->_db->setQuery("SELECT authorid FROM $this->_tbl WHERE name=" . $this->_db->Quote($name) . " AND authorid < 0 LIMIT 1");
		$uid = $this->_db->loadResult();
		if (!$uid || $uid > 0)
		{
			$uid = $this->getLastUserId();

			// Check for potentially conflicting profile
			$this->_db->setQuery("SELECT uidNumber FROM #__xprofiles ORDER BY uidNumber ASC LIMIT 1");
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
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	public function buildQuery($filters=array())
	{
		$sql = "FROM $this->_tbl AS m ";

		$w = array();
		$w[] = "m.subtable='resources'";
		if (isset($filters['subid']) && $filters['subid'])
		{
			$w[] = "m.subid=" . $this->_db->Quote($filters['subid']);
		}
		if (isset($filters['state']))
		{
			$w[] = "m.state=" . $this->_db->Quote($filters['state']);
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$w[] = "m.name LIKE " . $this->_db->quote('%' . $filters['search'] . '%');
		}

		$sql .= (count($w) > 0) ? "WHERE " : "";
		$sql .= implode(" AND ", $w);

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
		$sql .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		if (isset($filters['limit']) && $filters['limit'] != '')
		{
			$sql .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		return $sql;
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Filters to build query from
	 * @return     integer
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
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getAuthorRecords($filters=array())
	{
		$query = "SELECT DISTINCT m.authorid, m.name, (SELECT COUNT(DISTINCT w.subid) FROM $this->_tbl AS w WHERE w.authorid = m.authorid) AS resources " . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get records for a specific author
	 *
	 * @param      integer $authorid Author ID
	 * @return     array
	 */
	public function getRecordsForAuthor($authorid)
	{
		$query = "SELECT * FROM $this->_tbl WHERE authorid=" . $this->_db->Quote($authorid);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

