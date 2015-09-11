<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
