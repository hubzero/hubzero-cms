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
 * Table class for job openings
 */
class Job extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id         		= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $cid       			= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $employerid      	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $code      			= NULL;

	/**
	 * varchar(200)
	 *
	 * @var string
	 */
	var $title				= NULL;

	/**
	 * varchar(200)
	 *
	 * @var string
	 */
	var $companyName		= NULL;

	/**
	 * varchar(200)
	 *
	 * @var string
	 */
	var $companyLocation	= NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $companyLocationCountry	= NULL;

	/**
	 * varchar(200)
	 *
	 * @var string
	 */
	var $companyWebsite		= NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $description		= NULL;

	/**
	 * int(50)
	 *
	 * @var integer
	 */
	var $addedBy 			= NULL;

	/**
	 * int(50)
	 *
	 * @var integer
	 */
	var $editedBy 			= NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $added    			= NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $edited	    		= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $status				= NULL;

	/**
	 * int(3)
	 *
	 * 0 pending approval
	 * 1 published
	 * 2 deleted
	 * 3 inactive
	 * 4 draft
	 *
	 * @var integer
	 */
	var $type				= NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $opendate    		= NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $closedate    		= NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $startdate    		= NULL;

	/**
	 * varchar(250)
	 *
	 * @var string
	 */
	var $applyExternalUrl	= NULL;

	/**
	 * varchar(50)
	 *
	 * @var string
	 */
	var $applyInternal 		= NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $contactName		= NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $contactEmail		= NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $contactPhone		= NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_openings', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->title) == '')
		{
			$this->setError(JText::_('ERROR_MISSING_JOB_TITLE'));
			return false;
		}

		if (trim($this->companyName) == '')
		{
			$this->setError(JText::_('ERROR_MISSING_EMPLOYER_NAME'));
			return false;
		}

		return true;
	}

	/**
	 * Get a user's openings
	 *
	 * @param      integer $uid     User ID
	 * @param      integer $current Get current?
	 * @param      integer $admin   Admin access?
	 * @param      integer $active  Active openings?
	 * @return     array
	 */
	public function get_my_openings($uid = NULL, $current = 0, $admin = 0, $active = 0)
	{
		if ($uid === NULL)
		{
			$juser 	= JFactory::getUser();
			$uid 	= $juser->get('id');
		}

		$sql  = "SELECT j.id, j.title, j.status, j.added, j.code, ";
		$sql .= $current ? "(SELECT j.id FROM $this->_tbl AS j WHERE j.id=" . $this->_db->Quote($current) . ") as current, " : "0 as current, ";
		$sql .= "(SELECT count(*) FROM  #__jobs_applications AS a WHERE a.jid=j.id AND a.status=1) as applications ";
		$sql .= " FROM $this->_tbl AS j ";
		$sql .= " WHERE  j.status!=2 ";
		$sql .= $active ? " AND  j.status!=3 " : "";
		$sql .= $admin ? " AND j.employerid=1 " : " AND j.employerid=" . $this->_db->Quote($uid) . " ";
		$sql .= " ORDER BY j.status ASC";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'countMyActiveOpenings'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      integer $onlypublished Parameter description (if any) ...
	 * @param      mixed $admin Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function countMyActiveOpenings($uid = NULL, $onlypublished = 0, $admin = 0)
	{
		if ($uid === NULL)
		{
			$juser 	= JFactory::getUser();
			$uid 	= $juser->get('id');
		}

		$sql  = "SELECT count(*) FROM $this->_tbl AS j ";
		if ($onlypublished)
		{
			$sql .= " WHERE  j.status=1 ";
		}
		else
		{
			$sql .= " WHERE  j.status!=2 AND  j.status!=3 ";
		}
		$sql .= $admin ? " AND j.employerid=1 " : " AND j.employerid=" . $this->_db->Quote($uid) . " ";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get job openings
	 *
	 * @param      array   $filters      Filters to build query from
	 * @param      integer $uid          User ID
	 * @param      itneger $admin        Admin access?
	 * @param      string  $subscription Subscription code
	 * @return     object Return description (if any) ...
	 */
	public function get_openings($filters, $uid = 0, $admin = 0, $subscription = '', $count = 0)
	{
		$defaultsort = isset($filters['defaultsort']) && $filters['defaultsort'] == 'type' ? 'type' : 'category';
		$category    = isset($filters['category']) ? $filters['category'] : 'all';
		$now 		 = JFactory::getDate()->toSql();
		$juser 		 = JFactory::getUser();
		$filters['start'] = isset($filters['start']) ? $filters['start'] : 0;
		$active = isset($filters['active']) && $filters['active'] == 1 ? 1 : 0;

		$sort = $filters['search'] != '' ? 'keywords DESC, ' : '';

		$sortdir = isset($filters['sortdir']) ? $filters['sortdir'] : 'DESC';

		// list  sorting
		switch ($filters['sortby'])
		{
			case 'opendate':    $sort .= 'j.status ASC, j.opendate ' . $sortdir . ', ';
								$sort .= $defaultsort=='type' ? 'j.type ASC' : 'c.ordernum ASC ';
								break;
			case 'category':    $sort .= 'isnull ' . $sortdir . ', c.ordernum ' . $sortdir . ', j.status ASC, j.opendate DESC ';
								break;
			case 'type':    	$sort .= 'typenull ' . $sortdir . ', j.type ' . $sortdir . ', j.opendate DESC ';
								break;
			// admin sorting
			case 'added':    	$sort .= 'j.added ' . $sortdir . ' ';
								break;
			case 'status':    	$sort .= 'j.status ' . $sortdir . ' ';
								break;
			case 'title':    	$sort .= 'j.title ' . $sortdir . ' ';
								break;
			case 'location':    $sort .= 'j.companyName ' . $sortdir . ', j.companyLocation ' . $sortdir;
								break;
			case 'adminposting':$sort .= 'j.employerid ' . $sortdir . ' ';
								break;
			default: 			$sort .= $defaultsort=='type'
								? 'j.type ASC, j.status ASC, j.opendate DESC'
								: 'c.ordernum ASC, j.status ASC, j.opendate DESC ';
								break;
		}

		if ($count)
		{
			$sql = "SELECT COUNT(*) ";
		}
		else
		{
			$sql  = "SELECT DISTINCT j.id, j.*, c.category AS categoryname, c.category IS NULL AS isnull, j.type=0 as typenull, ";

			$sql .= $admin ? "s.expires  AS inactive,  " : ' NULL AS inactive, ';
			if ($uid)
			{
				$sql.= "\n (SELECT count(*) FROM #__jobs_admins AS B WHERE B.jid=j.id AND B.uid=" . $this->_db->Quote($uid) . ") AS manager,";
			}
			else
			{
				$sql.= "\n NULL AS manager,";
			}
			$sql.= "\n (SELECT count(*) FROM #__jobs_applications AS a WHERE a.jid=j.id) AS applications,";
			if (!$juser->get('guest'))
			{
				$myid = $juser->get('id');
				$sql .= "\n (SELECT a.applied FROM #__jobs_applications AS a WHERE a.jid=j.id AND a.uid=" . $this->_db->Quote($myid) . " AND a.status=1) AS applied,";
				$sql .= "\n (SELECT a.withdrawn FROM #__jobs_applications AS a WHERE a.jid=j.id AND a.uid=" . $this->_db->Quote($myid) . " AND a.status=2) AS withdrawn,";
			}
			else
			{
				$sql .= "\n NULL AS applied,";
				$sql .= "\n NULL AS withdrawn,";
			}
			$sql .= "\n (SELECT t.category FROM #__jobs_types AS t WHERE t.id=j.type) AS typename ";

			if (trim($filters['search']))
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
					$kw = 0;
					for ($i=0, $n=count($s); $i < $n; $i++)
					{
						$sql .= "\n , (SELECT count(*) FROM $this->_tbl AS o WHERE o.id=j.id ";
						$sql .= "AND  LOWER(o.title) LIKE " . $this->_db->quote('%' . $s[$i] . '%') . ") AS keyword$i ";
						$sql .= "\n , (SELECT count(*) FROM $this->_tbl AS o WHERE o.id=j.id ";
						$sql .= "AND  LOWER(o.description) LIKE " . $this->_db->quote('%' . $s[$i] . '%') . ") AS bodykeyword$i ";
						$kw .= '+ keyword' . $i . ' * 2 ';
						$kw .= '+ bodykeyword' . $i;
					}

					$sql .= "\n , (SELECT " . $kw . ") AS keywords ";
				}
				else
				{
					$sql .= "\n , (SELECT 0) AS keywords ";
				}
			}
			else
			{
				$sql .= "\n , (SELECT 0) AS keywords ";
			}
		}

		$sql .= "\n FROM $this->_tbl AS j";
		$sql .= "\n LEFT JOIN #__jobs_categories AS c ON c.id=j.cid ";

		// make sure the employer profile is active
		$sql .= $admin ? "\n LEFT JOIN #__jobs_employers AS e ON e.uid=j.employerid " : "\n JOIN #__jobs_employers AS e ON e.uid=j.employerid ";
		$sql .= "LEFT JOIN #__users_points_subscriptions AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
		$sql .= " WHERE ";
		// only show active ads
		$sql .= $admin ? "\n  j.status!=2" : "\n  j.status=1 AND s.status=1 AND s.expires > " . $this->_db->Quote($now) . " ";

		if ($category!='all') {
			$sql .= "\n AND j.cid=" . $this->_db->Quote($category);
		}
		if ($subscription)
		{
			$sql .= "\n AND s.code=" . $this->_db->Quote($subscription);
		}
		if ($active)
		{
			$sql .= "\n AND (j.closedate ='0000-00-00 00:00:00' OR j.closedate IS NULL OR j.closedate > " . $this->_db->Quote($now) . ") ";
		}

		if (!$count)
		{
			$sql .= " ORDER BY ". $sort;
		}

		if (!$count && isset ($filters['limit']) && $filters['limit']!=0)
		{
			$sql .= " LIMIT " . $filters['start'] . ", " . $filters['limit'];
		}

		$this->_db->setQuery($sql);
		return $count ? $this->_db->loadResult() : $this->_db->loadObjectList();
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      string $code Job code
	 * @return     boolean True upon success
	 */
	public function loadJob($code=NULL)
	{
		if ($code === NULL)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE code=" . $this->_db->Quote($code) . " LIMIT 1");
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}

	/**
	 * Delete an open job
	 *
	 * @param      integer $jid Job ID
	 * @return     boolean True upon success
	 */
	public function delete_opening($jid)
	{
		if ($jid === NULL)
		{
			$jid == $this->id;
		}
		if ($jid === NULL)
		{
			return false;
		}

		$query  = "UPDATE $this->_tbl SET status='2' WHERE id=" . $this->_db->Quote($jid);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Get job opening
	 *
	 * @param      integer $jid     Job ID
	 * @param      integer $uid     User ID
	 * @param      integer $admin   Admin access?
	 * @param      string  $jobcode Job code
	 * @return     mixed False if errors, array if records found, null if not
	 */
	public function get_opening($jid = 0, $uid = 0, $admin = 0, $jobcode = '')
	{
		if ($jid === NULL && $jobcode == '')
		{
			return false;
		}

		$now 	= JFactory::getDate()->toSql();
		$juser 	= JFactory::getUser();
		$myid 	= $juser->get('id');

		$sql  = "SELECT j.*, ";
		$sql .= $admin ? "s.expires IS NULL AS inactive,  " : ' NULL AS inactive, ';
		$sql .= "\n (SELECT count(*) FROM #__jobs_applications AS a WHERE a.jid=j.id) AS applications,";
		if (!$juser->get('guest'))
		{
			$sql .= "\n (SELECT a.applied FROM #__jobs_applications AS a WHERE a.jid=j.id AND a.uid=" . $this->_db->Quote($myid) . " AND a.status=1) AS applied,";
			$sql .= "\n (SELECT a.withdrawn FROM #__jobs_applications AS a WHERE a.jid=j.id AND a.uid=" . $this->_db->Quote($myid) . " AND a.status=2) AS withdrawn,";
		}
		else
		{
			$sql .= "\n NULL AS applied,";
			$sql .= "\n NULL AS withdrawn,";
		}
		$sql .= "\n (SELECT t.category FROM #__jobs_types AS t WHERE t.id=j.type) AS typename ";
		$sql .= "\n FROM $this->_tbl AS j";
		$sql .= $admin ? "\n LEFT JOIN #__jobs_employers AS e ON e.uid=j.employerid " : "\n JOIN #__jobs_employers AS e ON e.uid=j.employerid ";
		$sql .= "LEFT JOIN #__users_points_subscriptions AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
		$sql .= "AND s.status=1 AND s.expires > " . $this->_db->Quote($now) . " WHERE ";

		if ($admin)
		{
			$sql .= " j.status != 2 ";
		}
		else if ($uid)
		{
			$sql .= "\n  (j.status=1 OR (j.status != 1 AND j.status!=2 AND j.employerid = " . $this->_db->Quote($uid) . ")) ";
		}
		else
		{
			$sql .= " j.status = 1 ";
		}
		if ($jid)
		{
			$sql .= "\n AND j.id=" . $this->_db->Quote($jid);
		}
		else if ($jobcode)
		{
			$sql .= "\n AND j.code=" . $this->_db->Quote($jobcode);
		}

		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();
		$result = $result ? $result[0] : NULL;
		return $result;
	}
}

