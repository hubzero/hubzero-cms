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

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for job stats
 */
class JobStats extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id         	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $itemid			= NULL;

	/**
	 * varchar(11) job / seeker  / employer
	 *
	 * @var string
	 */
	var $category		= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $total_viewed	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $total_shared	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $viewed_today	= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $lastviewed		= NULL;

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
			$this->setError(JText::_('Missing item id.'));
			return false;
		}

		if (intval($this->category) == '')
		{
			$this->setError(JText::_('Missing category.'));
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

		$query  = "SELECT * FROM $this->_tbl WHERE itemid=" . $this->_db->Quote($itemid) . " AND category=" . $this->_db->Quote($category) . " ORDER BY ";
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
		$lastweek  = JFactory::getDate(time() - (7 * 24 * 60 * 60))->format('Y-m-d H:i:s');
		$lastmonth = JFactory::getDate(time() - (30 * 24 * 60 * 60))->format('Y-m-d H:i:s');
		$today     = JFactory::getDate(time() - (24 * 60 * 60))->format('Y-m-d H:i:s');

		$query  = "SELECT ";
		if ($type == 'viewed')
		{
			$query .= $when ? " SUM(viewed_today) AS times " : " MAX(total_viewed) AS times ";
		}
		else
		{
			$query .= " MAX(p.total_shared) AS times ";
		}
		$query .= " FROM $this->_tbl WHERE itemid=" . $this->_db->Quote($itemid) . " AND category=" . $this->_db->Quote($category) . " AND ";

		switch ($when)
		{
			case 'thisweek':
				$query .= " lastviewed > " . $this->_db->Quote($lastweek) . " ";
			break;
			case 'thismonth':
				$query .= " lastviewed > " . $this->_db->Quote($lastmonth) . " ";
			break;
			case 'today':
				$query .= " lastviewed > " . $this->_db->Quote($today) . " ";
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

		$today = JFactory::getDate()->format('Y-m-d');
		$now = JFactory::getDate()->toSql();

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
				$this->setError(JText::_('Failed to store item view.'));
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
		$lastmonth = JFactory::getDate(time() - (30 * 24 * 60 * 60))->toSql();
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE lastviewed < " . $this->_db->Quote($lastmonth));
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
			$this->setError(JText::_('Missing argument'));
			return false;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE itemid =" . $this->_db->Quote($itemid) . " AND category =" . $this->_db->Quote($category));
		$this->_db->query();
	}
}

