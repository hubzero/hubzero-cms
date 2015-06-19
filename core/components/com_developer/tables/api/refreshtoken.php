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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Developer\Tables\Api;

/**
 * Developer refresh token table class
 */
class RefreshToken extends \JTable
{
	/**
	 * Constructor
	 * 
	 * @param   object  $db  Database object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__developer_refresh_tokens', 'id', $db);
	}

	/**
	 * Get collection of access tokens
	 * 
	 * @param  array  $filters Filters for querying
	 * @return array           Array of applications
	 */
	public function find($filters = array())
	{
		$sql  = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		// limit (handle here so it doesnt effect count)
		if (isset($filters['limit']))
		{
			$sql .= " LIMIT " . $filters['limit'];
			if (isset($filters['start']))
			{
				$sql .= " OFFSET " . $filters['start'];
			}
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get count of records based on filters
	 * 
	 * @param  array  $filters  Filters for querying
	 * @return int              Record count
	 */
	public function count($filters = array())
	{
		$sql  = "SELECT COUNT(*) FROM {$this->_tbl}";
		$sql .= $this->_buildQuery( $filters );

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Generic method to take an array of filters & gnerate sql
	 * 
	 * @param  array  $filters  Filters for querying
	 * @return string           SQL query
	 */
	public function _buildQuery($filters = array())
	{
		// var to hold conditions
		$where = array();
		$sql   = '';

		// created by
		if (isset($filters['uidNumber']))
		{
			$where[] = "uidNumber=" . $this->_db->quote( $filters['uidNumber'] );
		}

		// state
		if (isset($filters['state']))
		{
			if (!is_array($filters['state']))
			{
				$filters['state'] = array($filters['state']);
			}

			$where[] = "state IN (" . implode(',', $filters['state']) . ")";
		}

		// app id
		if (isset($filters['application_id']))
		{
			$where[] = "application_id=" . $this->_db->quote( $filters['application_id'] );
		}

		// refresh token
		if (isset($filters['refresh_token']))
		{
			$where[] = "refresh_token=" . $this->_db->quote( $filters['refresh_token'] );
		}

		// if we have and conditions
		if (count($where) > 0)
		{
			$sql = " WHERE " . implode(" AND ", $where);
		}

		// order by param
		if (isset($filters['orderby']))
		{
			$sql .= " ORDER BY " . $filters['orderby'];
		}

		return $sql;
	}
}