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
 * Table class for citation associations
 */
class CitationsAssociation extends JTable
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
	var $cid   = NULL;

	/**
	 * varchar(200)
	 *
	 * @var string
	 */
	var $oid   = NULL;

	/**
	 * int(3)
	 *
	 * @var integer
	 */
	var $type  = NULL;

	/**
	 * int(3)
	 *
	 * @var integer
	 */
	var $tbl = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__citations_assoc', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->cid) == '')
		{
			$this->setError(JText::_('ASSOCIATION_MUST_HAVE_CITATION_ID'));
			return false;
		}
		if (trim($this->tbl) == '')
		{
			$this->setError(JText::_('ASSOCIATION_MUST_HAVE_TYPE'));
			return false;
		}
		if (trim($this->oid) == '')
		{
			$this->setError(JText::_('ASSOCIATION_MUST_HAVE_OBJECT_ID'));
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
	public function buildQuery($filters)
	{
		$query = "";

		$ands = array();
		if (isset($filters['cid']) && $filters['cid'] != 0)
		{
			$ands[] = "r.cid=" . $this->_db->Quote($filters['cid']);
		}
		if (isset($filters['oid']) && $filters['oid'] != 0)
		{
			$ands[] = "r.oid=" . $this->_db->Quote($filters['oid']);
		}
		if (isset($filters['type']) && $filters['type'] != '')
		{
			$ands[] = "r.type=" . $this->_db->Quote($filters['type']);
		}
		if (isset($filters['type']) && $filters['type'] != '')
		{
			$ands[] = "r.type=" . $this->_db->Quote($filters['type']);
		}
		if (isset($filters['table']) && $filters['table'] != '')
		{
			$ands[] = "r.tbl=" . $this->_db->Quote($filters['table']);
		}
		if (count($ands) > 0)
		{
			//$query .= " WHERE r.published=1 AND ";
			$query .= " WHERE ";
			$query .= implode(" AND ", $ands);
		}
		if (isset($filters['sort']) && $filters['sort'] != '')
		{
			$query .= " ORDER BY " . $filters['sort'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= " LIMIT " . intval($filters['start']) . "," . intval($filters['limit']);
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl AS r" . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT * FROM $this->_tbl AS r" . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

