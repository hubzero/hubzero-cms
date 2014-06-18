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
 * Table class for abuse items
 */
class ReportAbuse extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id         	= NULL;

	/**
	 * text
	 *
	 * @var unknown
	 */
	var $report   		= NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created    	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $created_by 	= NULL;

	/**
	 * int(3)
	 *
	 * @var integer
	 */
	var $state      	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $referenceid    = NULL;

	/**
	 * varchar(50)
	 *
	 * @var string
	 */
	var $category		= NULL;

	/**
	 * varchar(150)
	 *
	 * @var string
	 */
	var $subject		= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $reviewed		= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $reviewed_by    = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $note    = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__abuse_reports', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->report) == '' && trim($this->subject) == JText::_('OTHER'))
		{
			$this->setError(JText::_('Please describe the issue.'));
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
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS a WHERE";

		if (isset($filters['state']))
		{
			$query .= " a.state=" . $this->_db->Quote($filters['state']);
		}
		else
		{
			$query .= " a.state=0";
		}
		if (isset($filters['id']) && $filters['id'] != '')
		{
			$query .= " AND a.referenceid=" . $this->_db->Quote($filters['id']);
		}
		if (isset($filters['category']) && $filters['category'] != '')
		{
			$query .= " AND a.category=" . $this->_db->Quote($filters['category']);
		}
		if (isset($filters['reviewed_by']) && $filters['reviewed_by'] != '')
		{
			$query .= " AND a.reviewed_by=" . $this->_db->Quote($filters['reviewed_by']);
		}
		if (isset($filters['sortby']) && $filters['sortby'] != '')
		{
			$query .= " ORDER BY " . $filters['sortby'] . " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
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
		$filters['sortby'] = '';

		$query  = "SELECT COUNT(*)" . $this->buildQuery($filters);

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
		$query  = "SELECT *" . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

