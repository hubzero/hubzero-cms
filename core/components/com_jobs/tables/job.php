<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;
use Lang;
use User;
use Date;

/**
 * Table class for job openings
 */
class Job extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_openings', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->title) == '')
		{
			$this->setError(Lang::txt('ERROR_MISSING_JOB_TITLE'));
			return false;
		}

		if (trim($this->companyName) == '')
		{
			$this->setError(Lang::txt('ERROR_MISSING_EMPLOYER_NAME'));
			return false;
		}

		return true;
	}

	/**
	 * Get a user's openings
	 *
	 * @param   integer  $uid      User ID
	 * @param   integer  $current  Get current?
	 * @param   integer  $admin    Admin access?
	 * @param   integer  $active   Active openings?
	 * @return  array
	 */
	public function get_my_openings($uid = null, $current = 0, $admin = 0, $active = 0)
	{
		if ($uid === null)
		{
			$uid = User::get('id');
		}

		$sql  = "SELECT j.id, j.title, j.status, j.added, j.code, ";
		$sql .= $current ? "(SELECT j.id FROM `$this->_tbl` AS j WHERE j.id=" . $this->_db->quote($current) . ") as current, " : "0 as current, ";
		$sql .= "(SELECT count(*) FROM `#__jobs_applications` AS a WHERE a.jid=j.id AND a.status=1) as applications ";
		$sql .= " FROM $this->_tbl AS j ";
		$sql .= " WHERE  j.status!=2 ";
		$sql .= $active ? " AND  j.status!=3 " : "";
		$sql .= $admin ? " AND j.employerid=1 " : " AND j.employerid=" . $this->_db->quote($uid) . " ";
		$sql .= " ORDER BY j.status ASC";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Count active openings for a user
	 *
	 * @param   integer  $uid
	 * @param   integer  $onlypublished
	 * @param   mixed    $admin
	 * @return  object
	 */
	public function countMyActiveOpenings($uid = null, $onlypublished = 0, $admin = 0)
	{
		if ($uid === null)
		{
			$uid = User::get('id');
		}

		$sql  = "SELECT count(*) FROM `$this->_tbl` AS j ";
		if ($onlypublished)
		{
			$sql .= " WHERE j.status=1 ";
		}
		else
		{
			$sql .= " WHERE j.status!=2 AND j.status!=3 ";
		}
		$sql .= $admin ? " AND j.employerid=1 " : " AND j.employerid=" . $this->_db->quote($uid) . " ";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get job openings
	 *
	 * @param   array    $filters       Filters to build query from
	 * @param   integer  $uid           User ID
	 * @param   integer  $admin         Admin access?
	 * @param   string   $subscription  Subscription code
	 * @return  object
	 */
	public function get_openings($filters = array(), $uid = 0, $admin = 0, $subscription = '', $count = 0)
	{
		$defaultsort = isset($filters['defaultsort']) && $filters['defaultsort'] == 'type' ? 'type' : 'category';
		$category    = isset($filters['category']) ? $filters['category'] : 'all';
		$now         = Date::toSql();

		$filters['start'] = isset($filters['start']) ? $filters['start'] : 0;
		$active = isset($filters['active']) && $filters['active'] == 1 ? 1 : 0;

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$sort = 'keywords DESC, ';
		}
		else
		{
			$sort = '';
		}

		if (isset($filters['sortdir']) && $filters['sortdir'] != '')
		{
			$sortdir = $filters['sortdir'];
		}
		else
		{
			$sortdir = 'DESC';
		}

		if (!isset($filters['sortby']) || $filters['sortby'] == '')
		{
			$filters['sortby'] = 'type';
		}

		// list  sorting
		switch ($filters['sortby'])
		{
			case 'opendate':
				$sort .= 'j.status ASC, j.opendate ' . $sortdir . ', ';
				$sort .= $defaultsort=='type' ? 'j.type ASC' : 'c.ordernum ASC ';
				break;
			case 'category':
				$sort .= 'isnull ' . $sortdir . ', c.ordernum ' . $sortdir . ', j.status ASC, j.opendate DESC ';
				break;
			case 'type':
				$sort .= 'typenull ' . $sortdir . ', j.type ' . $sortdir . ', j.opendate DESC ';
				break;
			// admin sorting
			case 'added':
				$sort .= 'j.added ' . $sortdir . ' ';
				break;
			case 'status':
				$sort .= 'j.status ' . $sortdir . ' ';
				break;
			case 'title':
				$sort .= 'j.title ' . $sortdir . ' ';
				break;
			case 'location':
				$sort .= 'j.companyName ' . $sortdir . ', j.companyLocation ' . $sortdir;
				break;
			case 'adminposting':
				$sort .= 'j.employerid ' . $sortdir . ' ';
				break;
			default:
				$sort .= $defaultsort=='type'
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
			$sql  = "SELECT DISTINCT j.id, j.*, c.category AS categoryname, c.category IS null AS isnull, j.type=0 as typenull, ";

			$sql .= $admin ? "s.expires  AS inactive,  " : ' null AS inactive, ';
			if ($uid)
			{
				$sql.= "\n (SELECT count(*) FROM `#__jobs_admins` AS B WHERE B.jid=j.id AND B.uid=" . $this->_db->quote($uid) . ") AS manager,";
			}
			else
			{
				$sql.= "\n null AS manager,";
			}
			$sql.= "\n (SELECT count(*) FROM `#__jobs_applications` AS a WHERE a.jid=j.id) AS applications,";
			if (!User::isGuest())
			{
				$myid = User::get('id');
				$sql .= "\n (SELECT a.applied FROM `#__jobs_applications` AS a WHERE a.jid=j.id AND a.uid=" . $this->_db->quote($myid) . " AND a.status=1) AS applied,";
				$sql .= "\n (SELECT a.withdrawn FROM `#__jobs_applications` AS a WHERE a.jid=j.id AND a.uid=" . $this->_db->quote($myid) . " AND a.status=2) AS withdrawn,";
			}
			else
			{
				$sql .= "\n null AS applied,";
				$sql .= "\n null AS withdrawn,";
			}
			$sql .= "\n (SELECT t.category FROM `#__jobs_types` AS t WHERE t.id=j.type) AS typename ";

			if (isset($filters['search']) && trim($filters['search']))
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
						$sql .= "\n , (SELECT count(*) FROM `$this->_tbl` AS o WHERE o.id=j.id ";
						$sql .= "AND  LOWER(o.title) LIKE " . $this->_db->quote('%' . $s[$i] . '%') . ") AS keyword$i ";
						$sql .= "\n , (SELECT count(*) FROM `$this->_tbl` AS o WHERE o.id=j.id ";
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

		$sql .= "\n FROM `$this->_tbl` AS j";
		$sql .= "\n LEFT JOIN `#__jobs_categories` AS c ON c.id=j.cid ";

		// make sure the employer profile is active
		$sql .= $admin ? "\n LEFT JOIN `#__jobs_employers` AS e ON e.uid=j.employerid " : "\n JOIN `#__jobs_employers` AS e ON e.uid=j.employerid ";
		$sql .= "LEFT JOIN #__users_points_subscriptions AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
		$sql .= " WHERE ";
		// only show active ads
		$sql .= $admin ? "\n  j.status!=2" : "\n  j.status=1 AND s.status=1 AND s.expires > " . $this->_db->quote($now) . " ";

		if ($category!='all') {
			$sql .= "\n AND j.cid=" . $this->_db->quote($category);
		}
		if ($subscription)
		{
			$sql .= "\n AND s.code=" . $this->_db->quote($subscription);
		}
		if ($active)
		{
			$sql .= "\n AND (j.closedate ='0000-00-00 00:00:00' OR j.closedate IS null OR j.closedate > " . $this->_db->quote($now) . ") ";
			$sql .= "\n AND (j.expiredate ='0000-00-00 00:00:00' OR j.expiredate IS null OR j.expiredate > " . $this->_db->quote($now) . ") ";
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
	 * @param   string   $code  Job code
	 * @return  boolean  True upon success
	 */
	public function loadJob($code=null)
	{
		if ($code === null)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM `$this->_tbl` WHERE code=" . $this->_db->quote($code) . " LIMIT 1");
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}

	/**
	 * Delete an open job
	 *
	 * @param   integer  $jid  Job ID
	 * @return  boolean  True upon success
	 */
	public function delete_opening($jid)
	{
		if ($jid === null)
		{
			$jid == $this->id;
		}
		if ($jid === null)
		{
			return false;
		}

		$query  = "UPDATE `$this->_tbl` SET status='2' WHERE id=" . $this->_db->quote($jid);
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
	 * @param   integer  $jid      Job ID
	 * @param   integer  $uid      User ID
	 * @param   integer  $admin    Admin access?
	 * @param   string   $jobcode  Job code
	 * @return  mixed    False if errors, array if records found, null if not
	 */
	public function get_opening($jid = 0, $uid = 0, $admin = 0, $jobcode = '')
	{
		if ($jid === null && $jobcode == '')
		{
			return false;
		}

		$now  = Date::toSql();
		$myid = User::get('id');

		$sql  = "SELECT j.*, ";
		$sql .= $admin ? "s.expires IS null AS inactive,  " : ' null AS inactive, ';
		$sql .= "\n (SELECT count(*) FROM `#__jobs_applications` AS a WHERE a.jid=j.id) AS applications,";
		if (!User::isGuest())
		{
			$sql .= "\n (SELECT a.applied FROM `#__jobs_applications` AS a WHERE a.jid=j.id AND a.uid=" . $this->_db->quote($myid) . " AND a.status=1) AS applied,";
			$sql .= "\n (SELECT a.withdrawn FROM `#__jobs_applications` AS a WHERE a.jid=j.id AND a.uid=" . $this->_db->quote($myid) . " AND a.status=2) AS withdrawn,";
		}
		else
		{
			$sql .= "\n null AS applied,";
			$sql .= "\n null AS withdrawn,";
		}
		$sql .= "\n (SELECT t.category FROM `#__jobs_types` AS t WHERE t.id=j.type) AS typename ";
		$sql .= "\n FROM `$this->_tbl` AS j";
		$sql .= $admin ? "\n LEFT JOIN `#__jobs_employers` AS e ON e.uid=j.employerid " : "\n JOIN #__jobs_employers AS e ON e.uid=j.employerid ";
		$sql .= "LEFT JOIN `#__users_points_subscriptions` AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
		$sql .= "AND s.status=1 AND s.expires > " . $this->_db->quote($now) . " WHERE ";

		if ($admin)
		{
			$sql .= " j.status != 2 ";
		}
		else if ($uid)
		{
			$sql .= "\n  (j.status=1 OR (j.status != 1 AND j.status!=2 AND j.employerid = " . $this->_db->quote($uid) . ")) ";
		}
		else
		{
			$sql .= " j.status = 1 ";
		}
		if ($jid)
		{
			$sql .= "\n AND j.id=" . $this->_db->quote($jid);
		}
		else if ($jobcode)
		{
			$sql .= "\n AND j.code=" . $this->_db->quote($jobcode);
		}

		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();
		$result = $result ? $result[0] : null;
		return $result;
	}
}
