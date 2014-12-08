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
 * System routing table for SEF entries
 */
class SefEntry extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id	     = null;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $cpt     = null;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $oldurl	 = null;

	/**
	 * varchar(150)
	 *
	 * @var string
	 */
	var $newurl	 = null;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $dateadd = null;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$_db)
	{
		parent::__construct('#__redirection', 'id', $_db);
	}

	/**
	 * Validate data
	 *
	 * @return     True if data is valid
	 */
	public function check()
	{
		$this->oldurl = rtrim(trim($this->oldurl), DS);
		if (!$this->oldurl)
		{
			$this->setError(JText::_('Missing field.'));
			return false;
		}

		$this->newurl = trim($this->newurl);
		if (!$this->newurl)
		{
			$this->setError(JText::_('Missing field.'));
			return false;
		}

		if (!$this->id)
		{
			$this->dateadd = JFactory::getDate()->toSql();
		}

		return true;
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Filters to determien hwo to build query
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$sql  = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to determien hwo to build query
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$sql  = "SELECT * " . $this->_buildQuery($filters) . " LIMIT " . $filters['start'] . ", " . $filters['limit'];

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build a query from filters
	 *
	 * @param      array $filters Filters to determien hwo to build query
	 * @return     string SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$query = "FROM $this->_tbl";

		$where = array();

		if ($filters['ViewModeId'] == 1)
		{
			$where[] = "`dateadd` > '0000-00-00' AND `newurl` = '' ";
		}
		elseif ($filters['ViewModeId'] == 2)
		{
			$where[] = "`dateadd` > '0000-00-00' AND `newurl` != '' ";
		}
		else
		{
			if (isset($filters['dateadded']))
			{
				$where[] = "`dateadd`=" . $this->_db->quote($filters['dateadded']);
			}
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		$query .= " ORDER BY ";
		switch ($filters['SortById'])
		{
			case 1 :
				$query .= "`oldurl` DESC";
				break;
			case 2 :
				$query .= "`newurl`";
				break;
			case 3 :
				$query .= "`newurl` DESC";
				break;
			case 4 :
				$query .= "`cpt`";
				break;
			case 5 :
				$query .= "`cpt` DESC";
				break;
			default :
				$query .= "`oldurl`";
				break;
		}
		return $query;
	}
}

