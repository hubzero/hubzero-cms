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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Projects\Tables;

/**
 * Table class for project repos
 */
class Repo extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__project_repos', 'id', $db );
	}

	/**
	 * Get repos
	 *
	 * @return     object or NULL
	 */
	public function getRepos ($projectid)
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE project_id=" . $this->_db->Quote($projectid));
		return $this->_db->loadObjectList();
	}

	/**
	 * Load project repo
	 *
	 * @param      integer $projectid
	 * @param      string  $name
	 * @return     object or false
	 */
	public function loadRepo($projectid = NULL, $name = NULL)
	{
		if ($projectid === NULL || $name === NULL)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE project_id=" . $this->_db->Quote($projectid) . " AND name=" . $this->_db->Quote($name) . " LIMIT 1";

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
}
