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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Table class for publication access logs
 */
class PublicationLog extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         			= NULL;
	
	/**
	 * int(11) Publication ID
	 * 
	 * @var integer
	 */
	var $publication_id 		= NULL;
	
	/**
	 * int(11) Publication version ID
	 * 
	 * @var integer
	 */
	var $publication_version_id = NULL;
	
	/**
	 * int(11) Number of page views
	 * 
	 * @var integer
	 */
	var $page_views 			= NULL;
	
	/**
	 * int(11) Number of accesses (clicks on the black button)
	 * 
	 * @var integer
	 */
	var $primary_accesses 		= NULL;
	
	/**
	 * int(11) Number of accesses of supporting docs (clicks on the black button)
	 * 
	 * @var integer
	 */
	var $support_accesses 		= NULL;
	
	/**
	 * int(11) Number of unique users who viewed the publication page
	 * 
	 * @var integer
	 */
	var $users_view 			= NULL;
	
	/**
	 * int(11) Number of unique users who clicked on the black button
	 * 
	 * @var integer
	 */
	var $users_primary			= NULL;
	
	/**
	 * Datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $modified				= NULL;
			
	/**
	 * Month 
	 * 
	 * @var integer
	 */	
	var $month       			= NULL;
	
	/**
	 * Year
	 * 
	 * @var integer
	 */	
	var $year       			= NULL;
								
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db ) 
	{
		parent::__construct( '#__publication_logs', 'id', $db );
	}
	
	/**
	 * Check if bot
	 * 
	 * @return     void
	 */	
	public function checkBotIp( $ip ) 
	{		
		// Metrics db name
		$query = "SELECT DATABASE()";
		$this->_db->setQuery( $query );
		$dbname = $this->_db->loadResult();
		$metrics_db = $dbname . '_metrics';
		
		// Do we have metrics db and necessary table?
		$query = "SELECT COUNT(*) FROM information_schema.tables 
				WHERE table_schema = '$metrics_db' 
				AND table_name = 'exclude_list'";
				$this->_db->setQuery( $query );
				$exists = $this->_db->loadResult();
		
		// So it it bot or real user?		
		if ($exists)
		{
			$query = " SELECT COUNT(*) FROM $metrics_db.exclude_list WHERE type='ip' AND filter='$ip'";
			$this->_db->setQuery( $query );
			return $this->_db->loadResult();
		}
		
		return false;
	}
	
	/**
	 * Log access in file
	 * 
	 * @param      integer 	$pid		Publication ID
	 * @param      integer 	$vid		Publication version ID
	 * @param      string 	$type		view, primary or support
	 * @return     void
	 */	
	public function logAccessFile ( $pid = NULL, $vid = NULL, $type = 'view', $ip = '', $logPath = '' ) 
	{
		$filename = 'pub-' . $pid . '-v-' . $vid . '.' . date('Y-m') . '.log';
		
		$juser =& JFactory::getUser();
		$uid 	= $juser->get('id');
		$uid 	= $uid ? $uid : 'guest';
		
		$log = date( 'Y-m-d H:i:s' ) . "\t" . $ip . "\t" . $uid . "\t" . $type . "\n";
		
		$handle  = fopen(JPATH_ROOT . $logPath . DS . $filename, 'a');
		fwrite($handle, $log);
		fclose($handle);
	}
	
	/**
	 * Log user numbers
	 * 
	 * @return     void
	 */	
	public function logUserAccess ( $pid = NULL, $vid = NULL, $year = NULL, $month = NULL, $count = 0, $type = 'view' ) 
	{
		if (!$pid || !$vid || !$year || !$month || !$count)
		{
			return false;
		}

		$type  = $type == 'primary' ? 'primary' : 'view';
		$field = 'users_' . $type;
		
		// Add user count columns to publication log table
		$fields = $this->_db->getTableFields('jos_publication_logs');
		if (!array_key_exists($field, $fields['jos_publication_logs'] )) 
		{
			$this->_db->setQuery( "ALTER TABLE `jos_publication_logs` 
				ADD `$field` int(11) NOT NULL DEFAULT '0'" );
			if (!$this->_db->query()) 
			{
				echo $this->_db->getErrorMsg();
				return false;
			}
		}
	
		// Load record to update
		if (!$this->loadMonthLog( $pid, $vid, $year, $month ))
		{
			$this->publication_id 			= $pid;
			$this->publication_version_id 	= $vid;
			$this->year						= $year;
			$this->month					= $month;			
		}
		
		if ($this->$field == $count)
		{
			return true;
		}
		
		$this->$field = $count;
		$this->modified	= date( 'Y-m-d H:i:s' );
		
		if ($this->store())
		{
			return true;
		}

		return false;
	}
	
	/**
	 * Log access
	 * 
	 * @param      integer 	$pid		Publication ID
	 * @param      integer 	$vid		Publication version ID
	 * @param      string 	$type		view, primary or support
	 * @return     void
	 */	
	public function logAccess ( $publication = NULL, $type = 'view', $logPath = '' ) 
	{
		if (!$publication || !is_object($publication))
		{
			return false;
		}
		
		$pid = $publication->id;
		$vid = $publication->version_id;
		
		// Create log directory
		if ($logPath && !is_dir(JPATH_ROOT . $logPath))
		{
			jimport('joomla.filesystem.folder');
			JFolder::create( JPATH_ROOT . $logPath);
		}
		
		// We are only logging access to public resources
		if ($publication->state != 1)
		{
			return false;
		}
		
		// Get IP
		$ip = Hubzero_Environment::ipAddress();
		
		// Check if bot
		if ($this->checkBotIp( $ip ))
		{
			// Do not log a bot
			return false;
		}
		
		// Log in a file
		if (is_dir(JPATH_ROOT . $logPath))
		{
			$this->logAccessFile( $pid, $vid, $type, $ip, $logPath);
		}
				
		$thisYearNum 	= date('y', time());
		$thisMonthNum 	= date('m', time());
		
		// Load record to update
		if (!$this->loadMonthLog( $pid, $vid ))
		{
			$this->publication_id 			= $pid;
			$this->publication_version_id 	= $vid;
			$this->year						= $thisYearNum;
			$this->month					= $thisMonthNum;
		}
		
		$this->modified	= date( 'Y-m-d H:i:s' );			
				
		if ($type == 'primary')
		{
			$this->primary_accesses = $this->primary_accesses + 1;
		}
		elseif ($type == 'support')
		{
			$this->support_accesses = $this->support_accesses + 1;
		}
		else
		{
			$this->page_views = $this->page_views + 1;
		}
		
		if ($this->store())
		{
			return true;
		}

		return false;
	}
	
	/**
	 * Load log
	 * 
	 * @param      integer 	$pid		Publication ID
	 * @param      integer 	$vid		Publication version ID
	 * @return     void
	 */	
	public function loadMonthLog ( $pid = NULL, $vid = NULL, $yearNum = NULL, $monthNum = NULL ) 
	{
		if (!$pid || !$vid)
		{
			return false;
		}
				
		$thisYearNum 	= $yearNum ? $yearNum : date('y', time());
		$thisMonthNum 	= $monthNum ? $monthNum : date('m', time());
			
		$query  = "SELECT * FROM $this->_tbl WHERE publication_id=$pid ";
		$query .= "AND publication_version_id=$vid ";
		$query .= "AND year='$thisYearNum' AND month='$thisMonthNum' ";
		$query .= "ORDER BY modified DESC LIMIT 1";
		
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind( $result );
		} 
		else 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}		
	}
	
	/**
	 * Get stats for author
	 * 
	 * @param      integer 	$uid		User ID
	 * @param      integer 	$limit		Num of entries
	 * @return     void
	 */	
	public function getAuthorStats ( $uid = NULL, $limit = 0, $lastmonth = true ) 
	{
		if (!$uid)
		{
			return false;
		}
		
		if ($lastmonth == true)
		{
			$thisYearNum 	= intval(date('y', strtotime("-1 month")));
			$pastMonthNum 	= intval(date('m', strtotime("-1 month")));
		}
		else
		{
			$thisYearNum 	= intval(date('y'));
			$thisMonthNum 	= intval(date('m'));

			$pastYearNum 	= intval(date('y', strtotime("-1 month")));
			$pastMonthNum 	= intval(date('m', strtotime("-1 month")));

			$pastTwoYear 	= intval(date('y', strtotime("-2 month")));
			$pastTwoMonth 	= intval(date('m', strtotime("-2 month")));

			$pastThreeYear 	= intval(date('y', strtotime("-3 month")));
			$pastThreeMonth = intval(date('m', strtotime("-3 month")));
		}
			
		$query  = "SELECT V.id as publication_version_id, V.publication_id, V.title, V.version_label, 
					V.version_number, V.doi, V.published_up, P.alias as project_alias, P.title as project_title,
					t.url_alias as cat_url, t.name as cat_name, t.alias as cat_alias";
					
		$query .= ", (SELECT VV.published_up FROM #__publication_versions as VV 
			WHERE VV.publication_id=V.publication_id and VV.published_up IS NOT NULL 
			ORDER BY VV.published_up ASC limit 1) AS first_published ";		
					
		if ($lastmonth)
		{
			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$thisYearNum' AND month='$pastMonthNum' ) AS monthly_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$thisYearNum' AND month='$pastMonthNum' ) AS monthly_primary ";
			$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$thisYearNum' AND month='$pastMonthNum' ) AS monthly_support ";
			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 )  FROM $this->_tbl as L 
				WHERE L.publication_id=V.publication_id ) AS total_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L 
				WHERE L.publication_id=V.publication_id ) AS total_primary ";
		}
		else
		{
			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$thisYearNum' AND month='$thisMonthNum' ) AS thismonth_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$thisYearNum' AND month='$thisMonthNum' ) AS thismonth_primary ";
			$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$thisYearNum' AND month='$thisMonthNum' ) AS thismonth_support ";

			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$pastYearNum' AND month='$pastMonthNum' ) AS lastmonth_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$pastYearNum' AND month='$pastMonthNum' ) AS lastmonth_primary ";
			$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$pastYearNum' AND month='$pastMonthNum' ) AS lastmonth_support ";

			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$pastTwoYear' AND month='$pastTwoMonth' ) AS twomonth_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$pastTwoYear' AND month='$pastTwoMonth' ) AS twomonth_primary ";
			$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$pastTwoYear' AND month='$pastTwoMonth' ) AS twomonth_support ";

			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$pastThreeYear' AND month='$pastThreeMonth' ) AS threemonth_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$pastThreeYear' AND month='$pastThreeMonth' ) AS threemonth_primary ";
			$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
				AND year='$pastThreeYear' AND month='$pastThreeMonth' ) AS threemonth_support ";

			$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 )  FROM $this->_tbl as L 
				WHERE L.publication_id=V.publication_id ) AS total_views ";
			$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L 
				WHERE L.publication_id=V.publication_id ) AS total_primary ";
			$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L 
				WHERE L.publication_id=V.publication_id ) AS total_support ";
		}
			
		$query .= " FROM #__publications as C, #__projects as P, #__publication_categories AS t, #__publication_versions as V ";
		$query .= " JOIN #__publication_authors as A ON A.publication_version_id = V.id AND A.user_id='$uid' ";
		
		if ($lastmonth)
		{
			$query .= " JOIN $this->_tbl as L ON L.publication_id = V.publication_id AND L.year='$thisYearNum' 
						AND L.month='$pastMonthNum' AND L.page_views > 0  ";
		}

		$query .= " WHERE P.id=C.project_id AND C.id=V.publication_id AND C.category = t.id AND
					V.main=1 AND V.state=1 AND V.published_up < '" . date('Y-m-d H:i:s') . "'";
		$query .= " GROUP BY V.publication_id ";

		$query .= $lastmonth ? " ORDER BY L.page_views DESC, V.id ASC " :  "ORDER BY V.title ASC ";
		$query .= $limit? "LIMIT " . $limit : '';
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();		
	}
	
	/**
	 * Get date when logs table was created (= date of first log)
	 * 
	 * @return     void
	 */
	public function getFirstLogDate()
	{		
		// Get date when logs table was created (= date of first log)
		/*
		$query = "SELECT CREATE_TIME FROM information_schema.tables 
				WHERE table_name = 'jos_publication_logs' LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
		*/
		
		// Get approximate time when logs started
		$query = "SELECT modified FROM $this->_tbl ORDER BY id ASC LIMIT 1";
		$this->_db->setQuery( $query );
		$first = $this->_db->loadResult();
		
		if ($first)
		{
			return date('Y-m-01 00:00:00', strtotime($first));
		}		
	}
	
	/**
	 * Get totals for publication(s) in a project/for an author
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @param      integer 	$pid			Publication ID
	 * @return     void
	 */
	public function getTotals($id = 0, $type = 'author')
	{
		$query  = "SELECT COALESCE( SUM(L.page_views) , 0 ) as all_total_views, ";
		$query .= " COALESCE( SUM(L.primary_accesses) , 0 ) as all_total_primary, ";
		$query .= " COALESCE( SUM(L.support_accesses) , 0 ) as all_total_support ";
				
		$query .= " FROM $this->_tbl as L ";
		
		if ($type == 'author')
		{
			$query .= " JOIN #__publication_versions as V ON V.publication_id=L.publication_id ";
			$query .= " JOIN #__publication_authors as A ON A.publication_version_id = V.id AND A.user_id='$id' ";
		}
		else
		{
			$query .= " JOIN #__publications as P ON P.id = L.publication_id AND P.project_id='$id' ";
			$query .= " JOIN #__publication_versions as V ON V.publication_id=L.publication_id ";
		}

		$query .= " WHERE V.state=1 AND V.main=1 AND V.published_up < '" . date('Y-m-d H:i:s') . "'";
		
		$this->_db->setQuery( $query );
		$totals = $this->_db->loadObjectList();
		return $totals ? $totals[0] : NULL;
	}
	
	/**
	 * Get stats for publication(s) in a project
	 * 
	 * @param      integer 	$projectid		Project ID
	 * @param      integer 	$pubid			Publication ID
	 * @return     void
	 */	
	public function getPubStats ( $projectid = NULL, $pubid = 0 ) 
	{
		if (!$projectid)
		{
			return false;
		}
		
		$thisYearNum 	= intval(date('y'));
		$thisMonthNum 	= intval(date('m'));
		
		$pastYearNum 	= intval(date('y', strtotime("-1 month")));
		$pastMonthNum 	= intval(date('m', strtotime("-1 month")));
		
		$pastTwoYear 	= intval(date('y', strtotime("-2 month")));
		$pastTwoMonth 	= intval(date('m', strtotime("-2 month")));
		
		$pastThreeYear 	= intval(date('y', strtotime("-3 month")));
		$pastThreeMonth = intval(date('m', strtotime("-3 month")));
		
		$dthis 			= date('Y') . '-' . date('m');
			
		$query  = "SELECT V.id as publication_version_id, V.publication_id, V.title, V.version_label, 
					V.version_number, V.doi, V.published_up,
					t.url_alias as cat_url, t.name as cat_name, t.alias as cat_alias,
					S.users, S.downloads ";
					
		$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
			AND year='$thisYearNum' AND month='$thisMonthNum' ) AS thismonth_views ";
		$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
			AND year='$thisYearNum' AND month='$thisMonthNum' ) AS thismonth_primary ";
		$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
			AND year='$thisYearNum' AND month='$thisMonthNum' ) AS thismonth_support ";
							
		$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
			AND year='$pastYearNum' AND month='$pastMonthNum' ) AS lastmonth_views ";
		$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
			AND year='$pastYearNum' AND month='$pastMonthNum' ) AS lastmonth_primary ";
		$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
			AND year='$pastYearNum' AND month='$pastMonthNum' ) AS lastmonth_support ";
			
		$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
			AND year='$pastTwoYear' AND month='$pastTwoMonth' ) AS twomonth_views ";
		$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
			AND year='$pastTwoYear' AND month='$pastTwoMonth' ) AS twomonth_primary ";
		$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
			AND year='$pastTwoYear' AND month='$pastTwoMonth' ) AS twomonth_support ";
			
		$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
			AND year='$pastThreeYear' AND month='$pastThreeMonth' ) AS threemonth_views ";
		$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
			AND year='$pastThreeYear' AND month='$pastThreeMonth' ) AS threemonth_primary ";
		$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L WHERE L.publication_id=V.publication_id 
			AND year='$pastThreeYear' AND month='$pastThreeMonth' ) AS threemonth_support ";
				
		$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 )  FROM $this->_tbl as L 
			WHERE L.publication_id=V.publication_id ) AS total_views ";
		$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L 
			WHERE L.publication_id=V.publication_id ) AS total_primary ";
		$query .= ", (SELECT COALESCE( SUM(L.support_accesses) , 0 ) FROM $this->_tbl as L 
			WHERE L.publication_id=V.publication_id ) AS total_support ";
			
		$query .= ", (SELECT VV.published_up FROM #__publication_versions as VV 
			WHERE VV.publication_id=V.publication_id and VV.published_up IS NOT NULL 
			ORDER BY VV.published_up ASC limit 1) AS first_published ";

/*			
		$query .= ", (SELECT COALESCE( SUM(L.page_views) , 0 ) FROM $this->_tbl as L JOIN #__publications as J 
			ON J.id = L.publication_id WHERE J.project_id=$projectid) AS all_total_views ";
			
		$query .= ", (SELECT COALESCE( SUM(L.primary_accesses) , 0 ) FROM $this->_tbl as L JOIN #__publications as J 
			ON J.id = L.publication_id WHERE J.project_id=$projectid) AS all_total_primary ";
*/
					
		$query .= " FROM #__publications as C, #__publication_categories AS t, #__publication_versions as V ";
		$query .= " LEFT JOIN #__publication_stats as S ON S.publication_id = V.publication_id AND period='14' AND datetime='" . $dthis . "-01 00:00:00' ";
		
		$query .= " WHERE C.id=V.publication_id AND V.state=1 AND C.category = t.id 
					AND V.main=1 AND V.published_up < '" . date('Y-m-d H:i:s') . "' AND C.project_id=$projectid";
							
		$query .= $pubid ? " AND V.publication_id=$pubid " : "";
		$query .= " GROUP BY V.publication_id ";
		$query .= " ORDER BY S.users DESC, V.id ASC ";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();		
	}
}
