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
 * Table class for watching a ticket
 */
class SupportTableWatching extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id    = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $ticket_id = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $user_id = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct($db)
	{
		parent::__construct('#__support_watching', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      string $oid Record alias
	 * @return     boolean True on success
	 */
	public function load($oid=null, $user_id=null)
	{
		if ($oid === null)
		{
			return false;
		}
		if ($user_id === null)
		{
			return parent::load($oid);
		}

		$query = "SELECT * FROM $this->_tbl WHERE ticket_id=" . $this->_db->Quote(trim($oid)) . " AND user_id=" . $this->_db->Quote(intval($user_id));

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
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->ticket_id = intval($this->ticket_id);
		if (!$this->ticket_id)
		{
			$this->setError(JText::_('A ticket ID must be provided.'));
			return false;
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id)
		{
			$this->setError(JText::_('A user ID must be provided.'));
			return false;
		}

		return true;
	}

	/**
	 * Build a query from filters
	 *
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	public function _buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS w";
					//JOIN #__users AS u ON w.user_id=u.id";

		$where = array();

		if (isset($filters['ticket_id']) && $filters['ticket_id'] > 0)
		{
			$where[] = "w.ticket_id=" . $this->_db->Quote($filters['ticket_id']);
		}
		if (isset($filters['user_id']) && $filters['user_id'] > 0)
		{
			$where[] = "w.user_id=" . $this->_db->Quote($filters['user_id']);
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		if (isset($filters['order']) && $filters['order'] != '')
		{
			$query .= " ORDER BY " . $filters['order'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(*)" . $this->_buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function find($filters=array())
	{
		$query  = "SELECT w.*" . $this->_buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

