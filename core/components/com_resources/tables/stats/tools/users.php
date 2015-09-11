<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Resources\Tables\Stats\Tools;

/**
 * Resources table class for tool user stats
 */
class Users extends \JTable
{
	/**
	 * Construct
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_stats_tools_users', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if valid, False if not
	 */
	public function check()
	{
		if (trim($this->resid) == '')
		{
			$this->setError(\Lang::txt('Your entry must have a resource ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Get top users for a resource
	 *
	 * @param   integer  $resid   Resource ID
	 * @param   integer  $period  Time period
	 * @param   string   $dthis   YYYY-MM
	 * @param   integer  $top     Top value
	 * @return  array
	 */
	public function getTopUsersRes($resid, $dthis, $period, $top)
	{
		$sql = "SELECT u.name, s.user, u.email, u.organization, s.jobs, s.sessions, s.simulations, s.tot_wall, s.tot_cpu, s.tot_view
				FROM $this->_tbl AS s, user AS u
				WHERE u.user = s.user AND s.datetime=" . $this->_db->quote($dthis . "-00") . " AND s.period=" . $this->_db->quote($period) . " AND s.resid=" . $this->_db->quote($resid) . "
				ORDER BY s.jobs DESC limit 25";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}
