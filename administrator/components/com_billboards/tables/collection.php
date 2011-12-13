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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 *
 * Billboards collection class
 *
 */
class BillboardsCollection extends JTable
{

	/**
	 * Collection ID, primary key
	 * 
	 * @var int(11)
	 */
	var $id = NULL;

	/**
	 * Collection name
	 * 
	 * @var varchar(255)
	 */
	var $name = NULL;

	//-----------

	/**
	 * Constructor method
	 * 
	 * @param  &$db database
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__billboard_collection', 'id', $db);
	}

	/**
	 * Build query method, currently just adding "FROM..."
	 * 
	 * @param  $filters not needed yet
	 * @return $query
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS c";

		return $query;
	}

	/**
	 * Get count of collection rows, mainly used for pagination
	 * 
	 * @param  $filters not needed yet
	 * @return query result: number of collections
	 */
	public function getCount($filters)
	{
		$query  = "SELECT COUNT(c.id)";
		$query .= $this->buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get the billboard collections
	 * 
	 * @param  $filters start and limit, mainly used for pagination in Joomla
	 * @return object list of collections
	 */
	public function getRecords($filters)
	{
		$query  = "SELECT c.*";
		$query .= $this->buildquery($filters);
		$query .= " ORDER BY id ASC";
		$query .= " LIMIT ".$filters['start'].",".$filters['limit'];

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

