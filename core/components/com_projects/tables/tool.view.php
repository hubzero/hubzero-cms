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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Projects\Tables;

/**
 * Project Tool View class
 *
 */
class ToolView extends  \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_tool_views', 'id', $db);
	}

	/**
	 * Get last view
	 *
	 * @param      integer $toolid 	Project tool id
	 * @param      integer $userid	User id
	 * @return     mixed Return string or NULL
	 */
	public function loadView( $toolid = 0, $userid = 0)
	{
		if (!intval($toolid) || !intval($userid))
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl
				   WHERE parent_id=" . $this->_db->Quote($toolid) . "
				   AND userid=" . $this->_db->Quote($userid) . "
				   ORDER BY viewed DESC LIMIT 1";

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
	 * Check if page was viewed recently
	 *
	 * @param      integer $toolid 	Project tool id
	 * @param      integer $userid	User id
	 * @return     mixed Return string or NULL
	 */
	public function checkView( $toolid = 0, $userid = 0)
	{
		if (!intval($toolid) || !intval($userid))
		{
			return false;
		}

		$now    	= Date::toSql();
		$lastView 	= NULL;

		if ($this->loadView($toolid, $userid))
		{
			$lastView = $this->viewed;
		}
		else
		{
			$this->parent_id = $toolid;
			$this->userid 	 = $userid;
		}

		// Record new viewing time for future comparison
		$this->viewed = $now;
		$this->store();

		return $lastView;
	}
}
