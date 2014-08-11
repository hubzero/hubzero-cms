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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Session\Storage;
use Hubzero\Session\StorageInterface;
use JFactory;

class Database implements StorageInterface
{
	/**
	 * Get Connection to Database Client
	 * 
	 * @return mixed
	 */
	public static function getDBO()
	{
		return JFactory::getDBO();
	}

	/**
	 * Get single session data
	 * 
	 * @param  string $id Session Id
	 * @return mixed
	 */
	public static function session($id)
	{
		// get database
		$database = self::getDBO();

		// query database
		$database->setQuery(
			"SELECT * 
				FROM `#__session`
				WHERE session_id = " . $database->quote($id) . "
				GROUP BY userid, client_id
				ORDER BY time DESC"
		);

		// get session
		return $database->loadObject();
	}

	
	public static function sessionWithUserid($userid)
	{
		// get database
		$database = self::getDBO();

		// query database
		$database->setQuery(
			"SELECT * 
				FROM `#__session`
				WHERE userid = " . $database->quote($userid) . "
				GROUP BY userid, client_id
				ORDER BY time DESC"
		);

		// get session
		return $database->loadObject();
	}

	/**
	 * Get list of all sessions
	 * 
	 * @return mixed
	 */
	public static function allSessions($filters = array())
	{
		// get database
		$database = self::getDBO();

		// distinct filter
		$max     = '';
		$groupBy = '';
		if (isset($filters['distinct']) && $filters['distinct'] == 1)
		{
			$max     = "MAX(time) as time,";
			$groupBy = "GROUP BY userid, client_id";
		}

		$query  = "SELECT session_id, client_id, guest, time, ".$max." data, userid, username, ip FROM `#__session`";
		$wheres = array();

		// guest filter
		if (isset($filters['guest']))
		{
			$wheres[] = "guest=" . $database->quote($filters['guest']);
		}

		// client filter
		if (isset($filters['client']))
		{
			// make sure is array
			if (!is_array($filters['client']))
			{
				$filters['client'] = array($filters['client']);
			}

			$wheres[] = "client_id IN(". implode(',', $filters['client']) .")";
		}

		// append wheres
		if (count($wheres) > 0)
		{
			$query .= " WHERE " . implode("AND ", $wheres);
		}

		// add group by
		$query .= " " . $groupBy;	
			
		// order by time
		$query .= " ORDER BY time DESC";
		
		// return sessions
		$database->setQuery($query);
		return $database->loadObjectList();
	}
}