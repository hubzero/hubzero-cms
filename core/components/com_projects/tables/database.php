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
 * Table class for project databases
 */
class Database extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__project_databases', 'id', $db );
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      string 	$identifier 	database name or id
	 * @return     object or false
	 */
	public function loadRecord ( $identifier = NULL )
	{
		if ($identifier === NULL)
		{
			return false;
		}
		$name = is_numeric($identifier) ? 'id' : 'database_name';

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE $name=" . $this->_db->quote($identifier) . " LIMIT 1" );
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind( $result );
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get items
	 *
	 * @param      integer 		$projectid
	 * @param      array 		$filters
	 * @param      boolean 		$skipPublished
	 * @return     object, integer or NULL
	 */
	public function getItems( $projectid = NULL, $filters = array(), $skipPublished = false)
	{
		if ($projectid == NULL)
		{
			return false;
		}

		$count  = isset($filters['count']) ? $filters['count'] : 0;

		$query  = "SELECT ";
		$query .= $count ? " COUNT(*) " : "*";
		$query .= " FROM $this->_tbl ";
		$query .= " WHERE project = " . $this->_db->quote($projectid);
		$query .= $skipPublished ? " AND revision IS NULL" : "";

		$this->_db->setQuery( $query );
		return $count ? $this->_db->loadResult() :  $this->_db->loadObjectList();
	}

	/**
	 * Get items
	 *
	 * @param      integer $projectid
	 * @param      array $filters
	 * @return     object, integer or NULL
	 */
	public function getResource( $projectid = NULL, $id = NULL)
	{
		if ($projectid == NULL || $id == NULL)
		{
			return false;
		}

		$query = "SELECT database_name, title FROM $this->_tbl WHERE id="
			. $this->_db->quote($id) . " AND project=" . $this->_db->quote($projectid);

		$this->_db->setQuery( $query );
		return $this->_db->loadAssoc();
	}

	/**
	 * Get database list
	 *
	 * @param      integer $projectid
	 * @return     object, integer or NULL
	 */
	public function getList( $projectid = NULL)
	{
		if ($projectid == NULL)
		{
			return false;
		}

		$query = "SELECT db.id, db.project, db.database_name, db.title, db.source_file,
				db.source_dir, db.source_revision, db.description, db.data_definition,
				db.revision, db.created, db.created_by, c.name
				FROM $this->_tbl AS db LEFT JOIN #__users AS c ON (c.id=db.created_by)
				WHERE project = " . $this->_db->quote($projectid) . " ORDER BY db.created DESC";

		$this->_db->setQuery( $query );
		return $this->_db->loadAssocList();
	}

	/**
	 * Get used items
	 *
	 * @param      integer $projectid
	 * @return     object, integer or NULL
	 */
	public function getUsedItems( $projectid = NULL)
	{
		if ($projectid == NULL)
		{
			return false;
		}

		$query = "SELECT DISTINCT IF(source_dir != '', CONCAT(source_dir, '/', source_file), source_file) as file
				  FROM $this->_tbl
				  WHERE project = " . $this->_db->quote($projectid);

		$this->_db->setQuery( $query );
		return $this->_db->loadColumn();
	}
}