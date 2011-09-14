<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JobSeeker extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $uid		= NULL;  // @var int(11)
	var $active		= NULL;  // @var int(11)
	var $lookingfor	= NULL;
	var $tagline	= NULL;
	var $linkedin	= NULL;
	var $url		= NULL;
	var $updated	= NULL;
	var $sought_cid	= NULL;
	var $sought_type= NULL;

	public function __construct( &$db )
	{
		parent::__construct( '#__jobs_seekers', 'id', $db );
	}

	public function check()
	{
		if (intval( $this->uid ) == 0) {
			$this->setError( JText::_('ERROR_MISSING_UID') );
			return false;
		}

		return true;
	}

	public function load( $name=NULL )
	{
		if ($name !== NULL) {
			$this->_tbl_key = 'uid';
		}
		$k = $this->_tbl_key;
		if ($name !== NULL) {
			$this->$k = $name;
		}
		$name = $this->$k;
		if ($name === NULL) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE $this->_tbl_key='$name' LIMIT 1" );
		if($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
		 return false;
		}
	}

	public function countShortlistedBy( $uid=0)
	{
		if ($uid == NULL) {
			return 0;
		}

		$this->_db->setQuery( "SELECT COUNT(*) FROM #__jobs_shortlist AS W WHERE W.seeker=".$uid."" );
		return $this->_db->loadResult();
	}

	public function countSeekers( $filters, $uid=0, $excludeme = 0, $admin = 0)
	{
		$filters['limit'] = 0;
		$filters['start'] = 0;

		$seekers = $this->getSeekers( $filters, $uid, $excludeme, $admin, 1);

		// Exclude duplicates
		$array = array();
		foreach ($seekers as $seeker)
		{
			$array[] = $seeker->uid;
		}

		$array = array_unique($array);
		return count($array);
	}

	public function getSeekers( $filters, $uid=0, $excludeme = 0, $admin = 0, $count = 0)
	{
		$query  = "SELECT DISTINCT x.name, x.countryresident, r.title, r.filename, r.created, ";
		$query .= "s.uid, s.lookingfor, s.tagline, s.sought_cid, s.sought_type, s.updated, s.linkedin, s.url ";
		$empid = $admin ? 1 : $uid;

		if ($uid && !$count) {
			// shortlisted users
			$query.= "\n , (SELECT count(*) FROM #__jobs_shortlist AS W WHERE W.seeker=s.uid AND W.emp=".$uid." AND s.uid != '".$uid."' AND s.uid=r.uid AND W.category='resume') AS shortlisted ";
			// is this my profile?
			$query.= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid='".$uid."' AND s.uid=r.uid ) AS mine ";
		}

		// determine relevance to search keywords
		if ($filters['search'] && !$count) {
			$words   = explode(',', $filters['search']);
			$s = array();
			foreach ($words as $word)
			{
				if (trim($word) != "") {
					$s[] = trim($word);
				}
			}

			if (count($s) > 0 ) {
				$kw = '';
				for ($i=0, $n=count( $s ); $i < $n; $i++)
				{
					$query .= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid=r.uid ";
					$query .= "AND  LOWER(s.tagline) LIKE '%$s[$i]%') AS keyword$i ";
					$kw .= $i == ($n-1) ? 'keyword'.$i.' + 2' : 'keyword'.$i.' + ';
				}

				$query .= "\n , (SELECT ".$kw." ) AS keywords ";
			} else {
				$query .= "\n , (SELECT 0 ) AS keywords ";
			}
		} else {
			$query.= "\n , (SELECT 0 ) AS keywords ";
		}

		// Categories
		$catquery = 'AND 1=2';
		if ($filters['category']) {
			$catquery = 'AND (s.sought_cid = '.$filters['category'].' OR  s.sought_cid = 0) ';
		}

		$query.= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid=r.uid ".$catquery.") AS category ";

		// Types
		$typequery = 'AND 1=2';
		if ($filters['type']) {
			$typequery = 'AND (s.sought_type = '.$filters['type'].' OR  s.sought_type = 0) ';
		}

		$query.= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid=r.uid ".$typequery.") AS type ";

		// Matching
		$query.= "\n , (SELECT (type + category + keywords)) AS matching ";

		// Join with profile & current resume
		$query .= "FROM #__xprofiles AS x JOIN #__jobs_seekers AS s ON s.uid=x.uidNumber JOIN #__jobs_resumes AS r ON r.uid=s.uid  ";

		// Get shortlisted only
		$query .= 	$filters['filterby'] == 'shortlisted' ? " JOIN #__jobs_shortlist AS W ON W.seeker=s.uid AND W.emp=".$uid." AND s.uid != '".$uid."' AND s.uid=r.uid AND W.category='resume' " : "";

		// Get applied only
		$query .= 	$filters['filterby'] == 'applied' ? " JOIN #__jobs_openings AS J ON J.employerid='$empid' JOIN #__jobs_applications AS A ON A.jid=J.id AND A.uid=s.uid AND A.status=1  " : "";
		$query .= "WHERE s.active=1 AND r.main=1 ";

		// Ordering
		$query .= "ORDER BY ";
		switch ($filters['sortby'])
		{
			case 'lastupdate':  $query .= 'r.created DESC ';
								break;
			case 'position':    $query .= 's.sought_cid ASC, s.sought_type ASC';
								break;
			case 'bestmatch':   $query .= 'matching DESC ';
								break;
			default: 			$query .= 'r.created DESC ';
								break;
		}

		// Paging
		$query .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $filters['start'] . ", " . $filters['limit'] : "";

		$this->_db->setQuery( $query );
		$seekers = $this->_db->loadObjectList();

		// Exclude duplicates
		if ($filters['filterby'] == 'applied') {
			$uids = array();
			foreach ($seekers as $i => $seeker)
			{
				if (!in_array($seeker->uid, $uids)) {
					$uids[] = $seeker->uid;
				} else {
				 	unset($seekers[$i]);
				}
			}
			$seekers = array_values($seekers);
		}
		return $seekers;
	}

	public function getSeeker( $uid, $eid=0, $admin = 0)
	{
		if ($uid === NULL) {
			return false;
		}

		$juser =& JFactory::getUser();

		$query  = "SELECT DISTINCT x.name, x.countryresident, r.title, r.filename, r.created, ";
		$query .= "s.uid, s.lookingfor, s.tagline, s.sought_cid, s.sought_type, s.updated, s.linkedin, s.url ";
		if ($eid) {
			$query.= "\n , (SELECT count(*) FROM #__jobs_shortlist AS W WHERE W.seeker=s.uid AND W.emp=".$eid." AND s.uid=r.uid AND s.uid='".$uid."' AND s.uid != '".$eid."' AND W.category='resume') AS shortlisted ";
		}
		$query .= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid='".$uid."' AND s.uid=r.uid AND s.uid = '".$juser->get('id')."') AS mine ";
		$query .= "FROM #__xprofiles AS x JOIN #__jobs_seekers AS s ON s.uid=x.uidNumber JOIN #__jobs_resumes AS r ON r.uid=s.uid  ";

		$query .= "WHERE s.active=1 AND r.main=1 AND s.uid='".$uid."' LIMIT 1";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

