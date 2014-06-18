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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------

/**
 * Table class for support resolutions
 */
class SupportResolution extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var unknown
	 */
	var $id    = NULL;

	/**
	 * varchar(100)
	 *
	 * @var unknown
	 */
	var $title = NULL;

	/**
	 * varchar(100)
	 *
	 * @var unknown
	 */
	var $alias = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__support_resolutions', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->title = trim($this->title);

		if (!$this->title)
		{
			$this->setError(JText::_('SUPPORT_ERROR_BLANK_FIELD'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = $this->title;
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($this->alias));

		return true;
	}

	/**
	 * Get all resolutions
	 *
	 * @return     array
	 */
	public function getResolutions()
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl ORDER BY title");
		return $this->_db->loadObjectList();
	}

	/**
	 * Build a query from filters
	 *
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl";
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
	public function getCount($filters=array())
	{
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
		$filters['order'] = 'title, alias';

		$query  = "SELECT *" . $this->buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

