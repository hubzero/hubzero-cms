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

namespace Hubzero\Bank;

/**
 * Table class for bak transactions
 */
class Transaction extends \JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id          = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $uid         = NULL;

	/**
	 * varchar(20)
	 *
	 * @var string
	 */
	var $type        = NULL;

	/**
	 * varchar(50)
	 *
	 * @var string
	 */
	var $category    = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $referenceid = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $amount      = NULL;

	/**
	 * varchar(250)
	 *
	 * @var string
	 */
	var $description = NULL;

	/**
	 * datetime
	 *
	 * @var string
	 */
	var $created     = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $balance     = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__users_transactions', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->uid = intval($this->uid);
		if (!$this->uid)
		{
			$this->setError(\JText::_('Entry must have a user ID.'));
			return false;
		}

		$this->type = trim($this->type);
		if (!$this->type)
		{
			$this->setError(\JText::_('Entry must have a type (e.g., deposit, withdraw).'));
			return false;
		}

		$this->category = trim($this->category);
		if (!$this->category)
		{
			$this->setError(\JText::_('Entry must have a category.'));
			return false;
		}

		$this->referenceid = intval($this->referenceid);
		$this->amount      = intval($this->amount);
		$this->balance     = intval($this->balance);

		if (!$this->created)
		{
			$this->created = \JFactory::getDate()->toSql();
		}

		return true;
	}

	/**
	 * Get a history of transactions for a user
	 *
	 * @param      integer $limit Number of records to return
	 * @param      integer $uid   User ID
	 * @return     mixed False if errors, array on success
	 */
	public function history($limit=50, $uid=null)
	{
		if ($uid == null)
		{
			$uid = $this->uid;
		}
		if ($uid == null)
		{
			return false;
		}

		$lmt = "";
		if ($limit > 0)
		{
			$lmt .= " LIMIT " . $limit;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE uid=" . $this->_db->Quote($uid) . " ORDER BY created DESC, id DESC" . $lmt);
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete records for a given category, type, and reference combination
	 *
	 * @param      string  $category    Transaction category (royalties, etc)
	 * @param      string  $type        Transaction type (deposit, withdraw, etc)
	 * @param      integer $referenceid Reference ID (resource ID, etc)
	 * @return     boolean False if errors, True on success
	 */
	public function deleteRecords($category=null, $type=null, $referenceid=null)
	{
		if ($referenceid == null)
		{
			$referenceid = $this->referenceid;
		}
		if ($referenceid == null)
		{
			return false;
		}
		if ($type == null)
		{
			$type = $this->type;
		}
		if ($category == null)
		{
			$category = $this->category;
		}

		$query = "DELETE FROM $this->_tbl WHERE category=" . $this->_db->Quote($category) . " AND type=" . $this->_db->Quote($type) . " AND referenceid=" . $this->_db->Quote($referenceid);

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Get a list of transactions of a certain type for a reference item and, optionally, user
	 *
	 * @param      string  $category    Transaction category (royalties, etc)
	 * @param      string  $type        Transaction type (deposit, withdraw, etc)
	 * @param      integer $referenceid Reference ID (resource ID, etc)
	 * @param      integer $uid   User ID
	 * @return     mixed False if errors, array on success
	 */
	public function getTransactions($category=null, $type=null, $referenceid=null, $uid=null)
	{
		if ($referenceid == null)
		{
			$referenceid = $this->referenceid;
		}
		if ($referenceid == null)
		{
			return false;
		}
		if ($type == null)
		{
			$type = $this->type;
		}
		if ($category == null)
		{
			$category = $this->category;
		}
		$query = "SELECT amount, SUM(amount) as sum, count(*) as total FROM $this->_tbl WHERE category=" . $this->_db->Quote($category) . " AND type=" . $this->_db->Quote($type) . " AND referenceid=" . $this->_db->Quote($referenceid);
		if ($uid)
		{
			$query .= " AND uid=" . $this->_db->Quote($uid);
		}
		$query .= " GROUP BY referenceid";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get get the transaction amount for a category, type, reference item and, optionally, user
	 *
	 * @param      string  $category    Transaction category (royalties, etc)
	 * @param      string  $type        Transaction type (deposit, withdraw, etc)
	 * @param      integer $referenceid Reference ID (resource ID, etc)
	 * @param      integer $uid         User ID
	 * @return     mixed False if errors, integer on success
	 */
	public function getAmount($category=null, $type=null, $referenceid=null, $uid=null)
	{
		if ($referenceid == null)
		{
			$referenceid = $this->referenceid;
		}
		if ($referenceid == null)
		{
			return false;
		}
		if ($type == null)
		{
			$type = $this->type;
		}
		if ($category == null)
		{
			$category = $this->category;
		}

		$query = "SELECT amount FROM $this->_tbl WHERE category=" . $this->_db->Quote($category) . " AND type=" . $this->_db->Quote($type) . " AND referenceid=" . $this->_db->Quote($referenceid);
		if ($uid)
		{
			$query .= " AND uid=" . $this->_db->Quote($uid);
		}
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a point total/average for a combination of category, type, user, etc.
	 *
	 * @param      string  $category    Transaction category (royalties, etc)
	 * @param      string  $type        Transaction type (deposit, withdraw, etc)
	 * @param      integer $referenceid Reference ID (resource ID, etc)
	 * @param      integer $royalty     If getting royalties
	 * @param      string  $action      Action to filter by (asked, answered, misc)
	 * @param      integer $uid         User ID
	 * @param      integer $allusers    Get total for all users?
	 * @param      string  $when        Datetime filter
	 * @param      integer $calc        How total is calculated (record sum, avg, record count)
	 * @return     integer
	 */
	public function getTotals($category=null, $type=null, $referenceid=null, $royalty=0, $action=null, $uid=null, $allusers = 0, $when=null, $calc=0)
	{
		if ($referenceid == null)
		{
			$referenceid = $this->referenceid;
		}
		if ($type == null)
		{
			$type = $this->type;
		}
		if ($category == null)
		{
			$category = $this->category;
		}

		if ($uid == null)
		{
			$juser = \JFactory::getUser();
			$uid = $juser->get('id');
		}

		$query = "SELECT ";
		if ($calc == 0)
		{
			$query .= " SUM(amount)";
		}
		else if ($calc == 1)
		{
			// average
			$query .= " AVG(amount)";
		}
		else if ($calc == 2)
		{
			// num of transactions
			$query .= " COUNT(*)";
		}
		$query .= " FROM $this->_tbl WHERE type=" . $this->_db->Quote($type) . " ";
		if ($category)
		{
			$query .= " AND category=" . $this->_db->Quote($category) . " ";
		}
		if ($referenceid)
		{
			$query .= " AND referenceid=" . $this->_db->Quote($referenceid);
		}
		if ($royalty)
		{
			$query .= " AND description like 'Royalty payment%' ";
		}
		if ($action == 'asked')
		{
			$query .= " AND description like '%posting question%' ";
		}
		else if ($action == 'answered')
		{
			$query .= " AND (description like '%answering question%' OR description like 'Answer for question%' OR description like 'Answered question%') ";
		}
		else if ($action == 'misc')
		{
			$query .= " AND (description NOT LIKE '%posting question%' AND description NOT LIKE '%answering question%'
							AND description NOT LIKE 'Answer for question%' AND description NOT LIKE 'Answered question%') ";
		}
		if (!$allusers)
		{
			$query .= " AND uid=$uid ";
		}
		if ($when)
		{
			$query .= " AND created LIKE '" . $when . "%' ";
		}

		$this->_db->setQuery($query);
		$total = $this->_db->loadResult();
		return $total ? $total : 0;
	}
}

