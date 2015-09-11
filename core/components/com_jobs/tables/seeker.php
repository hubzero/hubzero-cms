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
use User;

/**
 * Table class for job seeker
 */
class JobSeeker extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_seekers', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (intval($this->uid) == 0)
		{
			$this->setError(Lang::txt('ERROR_MISSING_UID'));
			return false;
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      integer $name User id
	 * @return     boolean True upon success
	 */
	public function loadSeeker($name=NULL)
	{
		if ($name !== NULL)
		{
			$this->_tbl_key = 'uid';
		}
		$k = $this->_tbl_key;
		if ($name !== NULL)
		{
			$this->$k = $name;
		}
		$name = $this->$k;
		if ($name === NULL)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE $this->_tbl_key=" . $this->_db->quote($name) . " LIMIT 1");
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}

	/**
	 * Get a shortlist count for a yser
	 *
	 * @param      integer $uid User ID
	 * @return     integer
	 */
	public function countShortlistedBy($uid=0)
	{
		if ($uid == NULL)
		{
			return 0;
		}

		$this->_db->setQuery("SELECT COUNT(*) FROM #__jobs_shortlist AS W WHERE W.seeker=" . $this->_db->quote($uid));
		return $this->_db->loadResult();
	}

	/**
	 * Count seekers
	 *
	 * @param      array   $filters   Filters to build query
	 * @param      integer $uid       User ID
	 * @param      integer $excludeme Exclude a user?
	 * @param      integer $admin     Admin access?
	 * @return     integer
	 */
	public function countSeekers($filters, $uid=0, $excludeme = 0, $admin = 0)
	{
		$filters['limit'] = 0;
		$filters['start'] = 0;

		$seekers = $this->getSeekers($filters, $uid, $excludeme, $admin, 1);

		// Exclude duplicates
		$array = array();
		foreach ($seekers as $seeker)
		{
			$array[] = $seeker->uid;
		}

		$array = array_unique($array);
		return count($array);
	}

	/**
	 * Get a list of seekers
	 *
	 * @param      array   $filters   Filters to build query
	 * @param      integer $uid       User ID
	 * @param      integer $excludeme Exclude a user?
	 * @param      integer $admin     Admin access?
	 * @param      integer $count     Get record counts
	 * @return     array
	 */
	public function getSeekers($filters, $uid=0, $excludeme = 0, $admin = 0, $count = 0)
	{
		$query  = "SELECT DISTINCT x.name, x.countryresident, r.title, r.filename, r.created, ";
		$query .= "s.uid, s.lookingfor, s.tagline, s.sought_cid, s.sought_type, s.updated, s.linkedin, s.url ";
		$empid = $admin ? 1 : $uid;

		if ($uid && !$count)
		{
			// shortlisted users
			$query.= "\n , (SELECT count(*) FROM #__jobs_shortlist AS W WHERE W.seeker=s.uid AND W.emp=" . $this->_db->quote($uid) . " AND s.uid != " . $this->_db->quote($uid) . " AND s.uid=r.uid AND W.category='resume') AS shortlisted ";
			// is this my profile?
			$query.= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid=" . $this->_db->quote($uid) . " AND s.uid=r.uid) AS mine ";
		}

		// determine relevance to search keywords
		if ($filters['search'] && !$count)
		{
			$words   = explode(',', $filters['search']);
			$s = array();
			foreach ($words as $word)
			{
				if (trim($word) != '')
				{
					$s[] = trim($word);
				}
			}

			if (count($s) > 0)
			{
				$kw = '';
				for ($i=0, $n=count($s); $i < $n; $i++)
				{
					$query .= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid=r.uid ";
					$query .= "AND  LOWER(s.tagline) LIKE " . $this->_db->quote('%' . $s[$i] . '%') . ") AS keyword$i ";
					$kw .= $i == ($n-1) ? 'keyword' . $i . ' + 2' : 'keyword' . $i . ' + ';
				}

				$query .= "\n , (SELECT " . $kw . ") AS keywords ";
			}
			else
			{
				$query .= "\n , (SELECT 0) AS keywords ";
			}
		}
		else
		{
			$query.= "\n , (SELECT 0) AS keywords ";
		}

		// Categories
		$catquery = 'AND 1=2';
		if ($filters['category'])
		{
			$catquery = "AND (s.sought_cid = " . $this->_db->quote($filters['category']) . " OR  s.sought_cid = 0) ";
		}

		$query.= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid=r.uid " . $catquery . ") AS category ";

		// Types
		$typequery = 'AND 1=2';
		if ($filters['type'])
		{
			$typequery = "AND (s.sought_type = " . $this->_db->quote($filters['type']) . " OR  s.sought_type = 0) ";
		}

		$query.= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid=r.uid " . $typequery . ") AS type ";

		// Matching
		$query.= "\n , (SELECT (type + category + keywords)) AS matching ";

		// Join with profile & current resume
		$query .= "FROM #__xprofiles AS x JOIN #__jobs_seekers AS s ON s.uid=x.uidNumber JOIN #__jobs_resumes AS r ON r.uid=s.uid  ";

		// Get shortlisted only
		$query .= 	$filters['filterby'] == 'shortlisted' ? " JOIN #__jobs_shortlist AS W ON W.seeker=s.uid AND W.emp=" . $this->_db->quote($uid) . " AND s.uid != " . $this->_db->quote($uid) . " AND s.uid=r.uid AND W.category='resume' " : "";

		// Get applied only
		$query .= 	$filters['filterby'] == 'applied' ? " JOIN #__jobs_openings AS J ON J.employerid=" . $this->_db->quote($empid) . " JOIN #__jobs_applications AS A ON A.jid=J.id AND A.uid=s.uid AND A.status=1  " : "";
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

		$this->_db->setQuery($query);
		$seekers = $this->_db->loadObjectList();

		// Exclude duplicates
		if ($filters['filterby'] == 'applied')
		{
			$uids = array();
			foreach ($seekers as $i => $seeker)
			{
				if (!in_array($seeker->uid, $uids))
				{
					$uids[] = $seeker->uid;
				}
				else
				{
					unset($seekers[$i]);
				}
			}
			$seekers = array_values($seekers);
		}
		return $seekers;
	}

	/**
	 * Get a seeker
	 *
	 * @param      integer $uid   User ID
	 * @param      integer $eid   Employer ID
	 * @param      integer $admin Admin access?
	 * @return     array
	 */
	public function getSeeker($uid, $eid=0, $admin = 0)
	{
		if ($uid === NULL)
		{
			return false;
		}

		$query  = "SELECT DISTINCT x.name, x.countryresident, r.title, r.filename, r.created, ";
		$query .= "s.uid, s.lookingfor, s.tagline, s.sought_cid, s.sought_type, s.updated, s.linkedin, s.url ";
		if ($eid)
		{
			$query.= "\n , (SELECT count(*) FROM #__jobs_shortlist AS W WHERE W.seeker=s.uid AND W.emp=" . $this->_db->quote($eid) . " AND s.uid=r.uid AND s.uid="  . $this->_db->quote($uid) . " AND s.uid != " . $this->_db->quote($eid) . " AND W.category='resume') AS shortlisted ";
		}
		$query .= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid=" . $this->_db->quote($uid) . " AND s.uid=r.uid AND s.uid = " . $this->_db->quote(User::get('id')) . ") AS mine ";
		$query .= "FROM #__xprofiles AS x JOIN #__jobs_seekers AS s ON s.uid=x.uidNumber JOIN #__jobs_resumes AS r ON r.uid=s.uid  ";

		$query .= "WHERE s.active=1 AND r.main=1 AND s.uid=" . $this->_db->quote($uid) . " LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

