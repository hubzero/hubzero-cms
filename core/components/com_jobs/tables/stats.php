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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Lang;
use Date;

/**
 * Table class for job stats
 */
class JobStats extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_stats', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (intval($this->itemid) == 0)
		{
			$this->setError(Lang::txt('Missing item id.'));
			return false;
		}

		if (intval($this->category) == '')
		{
			$this->setError(Lang::txt('Missing category.'));
			return false;
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      integer $itemid   Job ID
	 * @param      string  $category Job type
	 * @param      string  $type     Record type
	 * @return     boolean True upon success
	 */
	public function loadStat($itemid = NULL, $category = NULL, $type = "viewed")
	{
		if ($itemid === NULL or $category === NULL)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE itemid=" . $this->_db->quote($itemid) . " AND category=" . $this->_db->quote($category) . " ORDER BY ";
		$query .= $type=='shared' ? "lastshared": "lastviewed";
		$query .= " DESC LIMIT 1";

		$this->_db->setQuery($query);

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}

	/**
	 * Short description for 'getStats'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $itemid   Job ID
	 * @param      string  $category Job type
	 * @param      integer $admin    Admin access?
	 * @return     mixed False if errors, Array upon success
	 */
	public function getStats($itemid = NULL, $category = 'employer', $admin = 0)
	{
		if ($itemid === NULL)
		{
			return false;
		}

		$stats = array(
			'total_resumes'    => 0,
			'shortlisted'      => 0,
			'applied'          => 0,
			'bookmarked'       => 0,
			'total_viewed'     => 0,
			'total_shared'     => 0,
			'viewed_today'     => 0,
			'viewed_thisweek'  => 0,
			'viewed_thismonth' => 0,
			'lastviewed'       => ''
		);

		// get total resumes in the pool
		$row = new JobSeeker($this->_db);
		$filters = array('filterby'=>'all', 'sortby'=>'', 'search'=>'', 'category'=>'', 'type'=>'');
		$stats['total_resumes'] = $row->countSeekers($filters);

		// get stats for employer
		if ($category == 'employer')
		{
			$filters['filterby'] = 'shortlisted';
			$stats['shortlisted'] = $row->countSeekers($filters, $itemid);

			$filters['filterby'] = 'applied';
			$itemid = $admin ? 1 : $itemid;
			$stats['applied'] = $row->countSeekers($filters, $itemid);
		}

		// get stats for seeker
		if ($category == 'seeker')
		{
			$stats['totalviewed']      = $this->getView($itemid, $category);
			$stats['viewed_today']     = $this->getView($itemid, $category, 'viewed', 'today');
			$stats['viewed_thisweek']  = $this->getView($itemid, $category, 'viewed', 'thisweek');
			$stats['viewed_thismonth'] = $this->getView($itemid, $category, 'viewed', 'thismonth');
			$stats['shortlisted']      = $row->countShortlistedBy($itemid);
		}

		return $stats;
	}

	/**
	 * Get a view
	 *
	 * @param      integer $itemid   Job ID
	 * @param      string  $category Job type
	 * @param      string  $type     Record type
	 * @param      string  $when     Timestamp
	 * @return     integer
	 */
	public function getView($itemid=NULL, $category=NULL, $type='viewed', $when ='')
	{
		$lastweek  = Date::of(time() - (7 * 24 * 60 * 60))->format('Y-m-d H:i:s');
		$lastmonth = Date::of(time() - (30 * 24 * 60 * 60))->format('Y-m-d H:i:s');
		$today     = Date::of(time() - (24 * 60 * 60))->format('Y-m-d H:i:s');

		$query  = "SELECT ";
		if ($type == 'viewed')
		{
			$query .= $when ? " SUM(viewed_today) AS times " : " MAX(total_viewed) AS times ";
		}
		else
		{
			$query .= " MAX(p.total_shared) AS times ";
		}
		$query .= " FROM $this->_tbl WHERE itemid=" . $this->_db->quote($itemid) . " AND category=" . $this->_db->quote($category) . " AND ";

		switch ($when)
		{
			case 'thisweek':
				$query .= " lastviewed > " . $this->_db->quote($lastweek) . " ";
			break;
			case 'thismonth':
				$query .= " lastviewed > " . $this->_db->quote($lastmonth) . " ";
			break;
			case 'today':
				$query .= " lastviewed > " . $this->_db->quote($today) . " ";
			break;
			default:
				$query .= " 1=1 ";
			break;
		}
		$query .= "GROUP BY itemid, category ";
		$query .= "ORDER BY times DESC ";
		$query .= "LIMIT 1";

		$this->_db->setQuery($query);
		$result =  $this->_db->loadResult();

		$result = $result ? $result : 0;
		return $result;
	}

	/**
	 * Save view
	 *
	 * @param      integer $itemid   Job ID
	 * @param      string  $category Job type
	 * @param      string  $type     Record type
	 * @return     boolean True upon success, False if errors
	 */
	public function saveView($itemid=NULL, $category=NULL, $type='viewed')
	{
		if ($itemid=== NULL)
		{
			$itemid = $this->itemid;
		}
		if ($category === NULL)
		{
			$category = $this->category;
		}

		if ($itemid === NULL or $category === NULL)
		{
			return false;
		}

		$today = Date::of('now')->format('Y-m-d');
		$now   = Date::toSql();

		// load existing entry
		$this->loadStat($itemid, $category);

		// create new entry for another day
		if (substr($this->lastviewed, 0, 10) != $today)
		{
			$this->id = 0;
			$this->itemid = $itemid;
			$this->category = $category;
			$this->viewed_today = 1;
		}
		else
		{
			$this->viewed_today = $this->viewed_today + 1;
		}

		$this->total_viewed = $this->total_viewed + 1;

		// avoid duplicates
		if ($this->lastviewed != $now)
		{
			$this->lastviewed = $now;

			if (!$this->store())
			{
				$this->setError(Lang::txt('Failed to store item view.'));
				return false;
			}
			else
			{
				// clean-up views older than 30 days
				$this->cleanup();
			}
		}
	}

	/**
	 * Remove records before a certaind ate
	 *
	 * @return     void
	 */
	public function cleanup()
	{
		$lastmonth = Date::of(time() - (30 * 24 * 60 * 60))->toSql();
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE lastviewed < " . $this->_db->quote($lastmonth));
		$this->_db->query();
	}

	/**
	 * Delete records for an item
	 *
	 * @param      integer $itemid   Job ID
	 * @param      string  $category Job category
	 * @return     boolean True upon success
	 */
	public function deleteStats($itemid, $category)
	{
		if ($itemid === NULL or $category === NULL)
		{
			$this->setError(Lang::txt('Missing argument'));
			return false;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE itemid =" . $this->_db->quote($itemid) . " AND category =" . $this->_db->quote($category));
		$this->_db->query();
	}
}

