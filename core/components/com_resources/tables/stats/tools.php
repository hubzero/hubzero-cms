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

namespace Components\Resources\Tables\Stats;

/**
 * Resources table class for tool stats
 */
class Tools extends \JTable
{
	/**
	 * Construct
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_stats_tools', 'id', $db);
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
	 * Load data for a resource and given period
	 *
	 * @param   integer  $resid   Resource ID
	 * @param   integer  $period  Time period
	 * @param   string   $dthis   YYYY-MM
	 * @return  boolean  True on success, False on error
	 */
	public function loadStats($resid=NULL, $period=NULL, $dthis=NULL)
	{
		if ($resid == NULL)
		{
			$resid = $this->resid;
		}
		if ($resid == NULL)
		{
			return false;
		}

		$sql = "SELECT id, users, sessions, simulations, jobs, avg_wall, tot_wall, avg_cpu, tot_cpu, avg_view, tot_view, avg_wait, tot_wait, avg_cpus, tot_cpus, period, LEFT(datetime,7) as datetime
				FROM $this->_tbl
				WHERE `period`=" . $this->_db->quote($period) . " AND `resid`=" . $this->_db->quote($resid);
		if ($dthis)
		{
			$sql .= " AND `datetime`=" . $this->_db->quote($dthis . "-00 00:00:00");
		}
		else
		{
			$sql .= " ORDER BY `datetime` DESC LIMIT 1";
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
