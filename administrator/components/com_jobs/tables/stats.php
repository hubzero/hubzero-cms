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
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Short description for 'JobStats'
 * 
 * Long description (if any) ...
 */
class JobStats extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var integer
	 */
	var $id         	= NULL;  // @var int(11) Primary key


	/**
	 * Description for 'itemid'
	 * 
	 * @var unknown
	 */
	var $itemid			= NULL;  // @var int(11)


	/**
	 * Description for 'category'
	 * 
	 * @var unknown
	 */
	var $category		= NULL;  // job / seeker  / employer


	/**
	 * Description for 'total_viewed'
	 * 
	 * @var unknown
	 */
	var $total_viewed	= NULL;

	/**
	 * Description for 'total_shared'
	 * 
	 * @var unknown
	 */
	var $total_shared	= NULL;

	/**
	 * Description for 'viewed_today'
	 * 
	 * @var integer
	 */
	var $viewed_today	= NULL;

	/**
	 * Description for 'lastviewed'
	 * 
	 * @var unknown
	 */
	var $lastviewed		= NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__jobs_stats', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (intval( $this->itemid ) == 0) {
			$this->setError( JText::_('Missing item id.') );
			return false;
		}

		if (intval( $this->category ) == '') {
			$this->setError( JText::_('Missing category.') );
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'loadStat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $itemid Parameter description (if any) ...
	 * @param      unknown $category Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadStat($itemid = NULL, $category = NULL, $type = "viewed")
	{
		if ($itemid === NULL or $category === NULL) {
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE itemid='$itemid' AND category='$category' ORDER BY ";
		$query .= $type=='shared' ? "lastshared": "lastviewed";
		$query .= " DESC LIMIT 1";

		$this->_db->setQuery( $query );

		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			return false;
		}
	}

	/**
	 * Short description for 'getStats'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $itemid Parameter description (if any) ...
	 * @param      string $category Parameter description (if any) ...
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getStats($itemid = NULL, $category = 'employer', $admin = 0)
	{
		if ($itemid === NULL) {
			return false;
		}

		$stats = array();
		$stats = array('total_resumes'=> 0,
						'shortlisted' => 0,
						'applied' => 0,
						'bookmarked' => 0,
						'total_viewed' => 0,
						'total_shared' => 0,
						'viewed_today' => 0,
						'viewed_thisweek' => 0,
						'viewed_thismonth' => 0,
						'lastviewed' => '');

		// get total resumes in the pool
		$row = new JobSeeker( $this->_db );
		$filters = array('filterby'=>'all', 'sortby'=>'', 'search'=>'', 'category'=>'', 'type'=>'');
		$stats['total_resumes'] = $row->countSeekers( $filters);

		// get stats for employer
		if ($category == 'employer') {
			$filters['filterby'] = 'shortlisted';
			$stats['shortlisted'] = $row->countSeekers( $filters, $itemid);

			$filters['filterby'] = 'applied';
			$itemid = $admin ? 1 : $itemid;
			$stats['applied'] = $row->countSeekers( $filters, $itemid);
		}

		// get stats for seeker
		if ($category == 'seeker') {
			$stats['totalviewed'] = $this->getView($itemid, $category);
			$stats['viewed_today'] = $this->getView($itemid, $category, 'viewed', 'today');
			$stats['viewed_thisweek'] = $this->getView($itemid, $category, 'viewed', 'thisweek');
			$stats['viewed_thismonth'] = $this->getView($itemid, $category, 'viewed', 'thismonth');
			$stats['shortlisted'] = $row->countShortlistedBy($itemid);
		}

		return $stats;
	}

	/**
	 * Short description for 'getView'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $itemid Parameter description (if any) ...
	 * @param      unknown $category Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @param      string $when Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getView( $itemid=NULL, $category=NULL, $type='viewed', $when ='')
	{
		$lastweek = date('Y-m-d H:i:s', time() - (7 * 24 * 60 * 60));
		$lastmonth = date('Y-m-d H:i:s', time() - (30 * 24 * 60 * 60));
		$today = date('Y-m-d H:i:s', time() - (24 * 60 * 60));

		$query  = "SELECT ";
		if ($type == 'viewed') {
			$query .= $when ? " SUM(viewed_today) AS times " : " MAX(total_viewed) AS times ";
		} else {
			$query .= " MAX(p.total_shared) AS times ";
		}
		$query .= " FROM $this->_tbl WHERE itemid='$itemid' AND category='$category' AND ";

		if ($when == 'thisweek') {
			$query .= " lastviewed > '".$lastweek."' ";
		} else if($when == 'thismonth') {
			$query .= " lastviewed > '".$lastmonth."' ";
		} else if ($when == 'today') {
			$query .= " lastviewed > '".$today."' ";
		} else {
			$query .= " 1=1 ";
		}
		$query .= "GROUP BY itemid, category ";
		$query .= "ORDER BY times DESC ";
		$query .= "LIMIT 1";

		$this->_db->setQuery( $query );
		$result =  $this->_db->loadResult();

		$result = $result ? $result : 0;
		return $result;
	}

	/**
	 * Short description for 'saveView'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $itemid Parameter description (if any) ...
	 * @param      unknown $category Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function saveView( $itemid=NULL, $category=NULL, $type='viewed')
	{
		if ($itemid=== NULL) {
			$itemid = $this->itemid;
		}
		if ($category === NULL) {
			$category = $this->category;
		}

		if ($itemid === NULL or $category === NULL) {
			return false;
		}

		$today = date( 'Y-m-d');
		$now = date( 'Y-m-d H:i:s' );

		// load existing entry
		$this->loadStat( $itemid, $category);

		// create new entry for another day
		if (substr($this->lastviewed, 0, 10) != $today ) {
			$this->id = 0;
			$this->itemid = $itemid;
			$this->category = $category;
			$this->viewed_today = 1;
		} else {
			$this->viewed_today = $this->viewed_today + 1;
		}

		$this->total_viewed = $this->total_viewed + 1;

		// avoid duplicates
		if ($this->lastviewed != $now) {
			$this->lastviewed = $now;

			if (!$this->store()) {
				$this->setError( JText::_('Failed to store item view.') );
				return false;
			} else {
				// clean-up views older than 30 days
				$this->cleanup();
			}
		}
	}

	/**
	 * Short description for 'cleanup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function cleanup()
	{
		$lastmonth = date('Y-m-d H:i:s', time() - (30 * 24 * 60 * 60));
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE lastviewed < '".$lastmonth."'");
		$this->_db->query();
	}

	/**
	 * Short description for 'deleteStats'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $itemid Parameter description (if any) ...
	 * @param      unknown $category Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteStats($itemid, $category)
	{
		if ($itemid === NULL or $category === NULL) {
			return false;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE itemid ='$itemid' AND category ='$category'");
		$this->_db->query();
	}
}

