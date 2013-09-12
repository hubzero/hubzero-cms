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
 * Short description for 'TagsGroup'
 * 
 * Long description (if any) ...
 */
class TagsTableGroup extends JTable
{

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $id      = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $groupid = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $tagid   = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $priority = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tags_group', 'id', $db);
	}

	/**
	 * Get a record count
	 * 
	 * @return     integer
	 */
	public function getCount()
	{
		$query = "SELECT COUNT(*) 
					FROM $this->_tbl AS tg,
					#__tags AS t,
					#__xgroups as g
					WHERE tg.tagid=t.id 
					AND g.gidNumber=tg.groupid ORDER BY tg.priority ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all records
	 * 
	 * @return     array
	 */
	public function getRecords()
	{
		$query = "SELECT tg.id, t.tag, g.cn, g.description, tg.tagid, tg.groupid, tg.priority 
					FROM $this->_tbl AS tg,
					#__tags AS t,
					#__xgroups as g
					WHERE tg.tagid=t.id 
					AND g.gidNumber=tg.groupid ORDER BY tg.priority ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the tag next to another tag
	 * 
	 * @param      string $move Dirction to look for neighbor
	 * @return     boolean True if tag was moved
	 */
	public function getNeighbor($move)
	{
		switch ($move)
		{
			case 'orderup':
				$sql = "SELECT * FROM $this->_tbl WHERE priority < " . $this->_db->Quote($this->priority) . " ORDER BY priority DESC LIMIT 1";
			break;

			case 'orderdown':
				$sql = "SELECT * FROM $this->_tbl WHERE priority > " . $this->_db->Quote($this->priority) . " ORDER BY priority ASC LIMIT 1";
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
}

