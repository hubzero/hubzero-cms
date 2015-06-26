<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Feedaggregator\Tables;

/**
 * Feeds table
 */
class Feeds extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__feedaggregator_feeds', 'id', $db);
	}

	/**
	* Returns all source feeds
	*
	* @return  object  list of source feeds
	*/
	public function getRecords()
	{
		$query = 'SELECT * FROM '. $this->_tbl;
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Returns feed as selected by ID
	 *
	 * @param   integer  $id
	 * @return  object   list of feed
	 */
	public function getById($id = NULL)
	{
		$query = 'SELECT * FROM ' . $this->_tbl . ' WHERE id=' . (int) $id;
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}

	/**
	 * Enables or disables a feed
	 *
	 * @param   integer  $id      ID of feed
	 * @param   integer  $status  Status of category
	 * @return  void
	 */
	public function updateActive($id, $status)
	{
		$query = 'UPDATE ' . $this->_tbl . ' SET enabled=' . (int) $status . ' WHERE id=' . (int) $id;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
}

