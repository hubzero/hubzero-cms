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

namespace Components\Projects\Tables;

/**
 * Table class for projects
 */
class Project extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__projects', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->alias) == '')
		{
			$this->setError(Lang::txt('PROJECT_MUST_HAVE_ALIAS'));
			return false;
		}

		if (trim($this->title) == '')
		{
			$this->setError(Lang::txt('PROJECT_MUST_HAVE_TITLE'));
			return false;
		}

		return true;
	}

	/**
	 * Build query
	 *
	 * @param   array    $filters
	 * @param   boolean  $admin
	 * @param   integer  $uid
	 * @param   boolean  $showall
	 * @param   integer  $setup_complete
	 * @return  string
	 */
	public function buildQuery($filters=array(), $admin = false, $uid = 0, $showall = 0, $setup_complete = 3)
	{
		// Process filters
		$mine     = isset($filters['mine']) && $filters['mine'] == 1 ? 1: 0;
		$sortby   = isset($filters['sortby']) ? $filters['sortby'] : 'title';
		$search   = isset($filters['search']) && $filters['search'] != ''  ? $filters['search'] : '';
		$filterby = isset($filters['filterby']) && $filters['filterby'] != ''  ? $filters['filterby'] : '';
		$getowner = isset($filters['getowner']) && $filters['getowner'] == 1 ? 1: 0;
		$type     = isset($filters['type']) ? intval($filters['type']) : null;
		$group    = isset($filters['group']) && intval($filters['group']) > 0 ? $filters['group'] : '';
		$reviewer = isset($filters['reviewer']) && $filters['reviewer'] != '' ? $filters['reviewer'] : '';
		$which    = isset($filters['which'])
					&& $filters['which'] != ''
					&& $filters['which'] != 'all'
					? $filters['which'] : '';

		$query  = " FROM $this->_tbl AS p ";

		if (isset($filters['timed']) && isset($filters['active']))
		{
			$query .= " JOIN #__project_activity AS pa
						ON pa.projectid=p.id AND pa.state != 2 ";
			$query .= "AND pa.recorded >= " . $this->_db->quote($filters['timed']);
		}

		$query .= " LEFT JOIN #__project_owners AS o ON o.projectid=p.id AND o.userid=" . $this->_db->quote($uid);
		$query .= " AND o.userid != 0 AND p.state!= 2 ";
		if ($getowner)
		{
			$query .=  " JOIN #__users as x ON x.id=p.owned_by_user ";
			$query .=  " LEFT JOIN #__xgroups as g ON g.gidNumber=p.owned_by_group ";
		}

		if ($reviewer == 'sensitive')
		{
			$query .= " WHERE ((p.params LIKE '%hipaa_data=yes%' ";
			$query .= " OR p.params LIKE '%ferpa_data=yes%' ";
			$query .= " OR p.params LIKE '%export_data=yes%' ";
			$query .= " OR p.params LIKE 'restricted_data=maybe%' ";
			$query .= " OR p.params LIKE '%followup=yes%') ";
			$query .= " AND p.state != 2 AND p.setup_stage >= " . $this->_db->quote($setup_complete) . ") ";
		}
		elseif ($reviewer == 'sponsored')
		{
			$query .= " WHERE (((p.params LIKE '%grant_title=%' AND p.params NOT LIKE '%grant_title=\\n%') ";
			$query .= " OR (p.params LIKE '%grant_agency=%' AND p.params NOT LIKE '%grant_agency=\\n%') ";
			$query .= " OR (p.params LIKE '%grant_budget=%' AND p.params NOT LIKE '%grant_budget=\\n%') ";
			$query .= ") AND p.state=1 AND p.setup_stage >= " . $this->_db->quote($setup_complete) . ") ";
		}
		elseif ($admin)
		{
			$query .= " WHERE 1=1 "; //p.provisioned = 0 ";
			if ($filterby == 'archived')
			{
				$query .= " AND p.state=3";
			}
			elseif ($filterby == 'active')
			{
				$query .= " AND p.state NOT IN (2, 3) ";
			}
			else
			{
				$query .= $showall ? "" : " AND p.state != 2 ";
			}
		}
		else
		{
			if ($mine)
			{
				if ($filterby == 'archived')
				{
					$query .= $uid
							? " WHERE (o.userid=" . $this->_db->quote($uid) . " AND o.status!=2 AND p.state = 3) "
							: " WHERE 1=2";
				}
				elseif ($filterby == 'active')
				{
					$query .= $uid
							? " WHERE (o.userid=" . $this->_db->quote($uid) . " AND o.status!=2 AND p.state NOT IN (2, 3)
								AND ((p.setup_stage >= " . $this->_db->quote($setup_complete) . ")
								OR (o.role = 1 AND p.owned_by_user=" . $this->_db->quote($uid) . "))) "
							: " WHERE 1=2";
				}
				else
				{
					$query .= $uid
							? " WHERE (o.userid=" . $this->_db->quote($uid) . " AND o.status!=2
								AND ((p.state!=2 AND p.setup_stage >= " . $this->_db->quote($setup_complete) . ")
								OR (o.role = 1 AND p.owned_by_user=" . $this->_db->quote($uid) . "))) "
							: " WHERE 1=2";
				}
				if (!empty($filters['editor']))
				{
					$query .= " AND o.role != 5 ";
				}
				if ($which == 'owned' && $uid)
				{
					$query .= " AND (p.owned_by_user =" . $this->_db->quote($uid) . " AND p.owned_by_group = 0) ";
				}
				if ($which == 'other' && $uid)
				{
					$query .= " AND (p.owned_by_user != " . $this->_db->quote($uid) . " OR p.owned_by_group != 0) ";
				}
			}
			else
			{
				if ($filterby == 'archived')
				{
					$query .= " WHERE p.state = 3 AND p.private = 0 ";
				}
				else
				{
					$query .= $uid
							? " WHERE ((p.state = 1 AND p.private = 0)
								OR (o.userid=" . $this->_db->quote($uid) . " AND o.status!=2 AND ((p.state = 1
								AND p.setup_stage >= " . $this->_db->quote($setup_complete) . ")
								OR (o.role = 1 AND p.owned_by_user=" . $this->_db->quote($uid) . " AND p.state != 3)))) "
							: " WHERE p.state = 1 AND p.private = 0 ";
				}
			}
		}
		if ($type)
		{
			$query .= " AND p.type =" . $this->_db->quote($type);
		}
		if ($group)
		{
			$query .= " AND p.owned_by_group =" . $this->_db->quote($group);
		}
		if (isset($filters['show_prov']))
		{
			$query .= $filters['show_prov'] == 1 ? " AND p.provisioned = 1 " : "";
		}
		else
		{
			$query .= " AND p.provisioned = 0 ";
		}
		if ($filterby == 'pending')
		{
			$query .= $reviewer == 'sponsored'
					? " AND (p.params NOT LIKE '%grant_status=1%'
						AND p.params NOT LIKE '%grant_status=2%') "
					: " AND p.state = 5 ";
		}
		if ($search)
		{
			$query .= " AND (p.title LIKE " . $this->_db->quote('%' . $search . '%') . " OR p.alias LIKE " . $this->_db->quote('%' . $search . '%') . ") ";
		}

		if (isset($filters['private']) && $filters['private'] >= 0)
		{
			$query .= " AND p.private = " . $filters['private'];
		}

		// Exclude
		if (!empty($filters['exclude']))
		{
			$query .= " AND p.id NOT IN (";

			$tquery = '';
			foreach ($filters['exclude'] as $ex)
			{
				$tquery .= "'" . $ex . "',";
			}
			$tquery = substr($tquery, 0, strlen($tquery) - 1);
			$query .= $tquery . ") ";
		}

		// Include
		if (!empty($filters['include']))
		{
			$tquery = array();
			foreach ($filters['include'] as $ex)
			{
				$tquery[] = $this->_db->quote($ex);
			}
			$query .= " AND p.id IN (" . implode(',', $tquery) . ") ";
		}

		if (isset($filters['created']))
		{
			$query .= " AND p.created LIKE '" . $filters['created'] . "%' ";
		}
		if (isset($filters['setup']) && $filters['setup'])
		{
			$query .= " AND p.setup_stage < $setup_complete ";
		}
		elseif (isset($filters['all']) && $filters['all'])
		{
			// all projects
		}
		elseif (isset($filters['active']) && $filters['active'])
		{
			$query .= " AND p.setup_stage >= $setup_complete ";
		}
		if (isset($filters['timed']) && isset($filters['active']))
		{
			$query .= " GROUP BY p.id ";
		}

		if (!$filters['count'])
		{
			$sort    = '';
			$sortdir = isset($filters['sortdir']) && strtoupper($filters['sortdir']) == 'DESC'  ? 'DESC' : 'ASC';

			switch ($sortby)
			{
				case 'title':
					$sort .= 'p.title ' . $sortdir . ' ';
					break;

				case 'id':
					$sort .= 'p.id ' . $sortdir . ' ';
					break;

				case 'myprojects':
					$sort .= 'p.state DESC, p.setup_stage DESC, p.title ' . $sortdir . ' ';
					break;
				case 'owner':
					if ($getowner)
					{
						$sort .= 'x.name ' . $sortdir . ', g.description ' . $sortdir . ' ';
					}
					else
					{
						$sort .= 'p.owned_by_group ' . $sortdir . ', p.owned_by_user ' . $sortdir . ' ';
					}
					break;

				case 'created':
					$sort .= 'p.created ' . $sortdir . ' ';
					break;

				case 'type':
					$sort .= 'p.type ' . $sortdir . ' ';
					break;

				case 'role':
					$sort .= 'o.role ' . $sortdir . ' ';
					break;

				case 'privacy':
					$sort .= 'p.private ' . $sortdir . ' ';
					break;

				case 'status':
					$sort .= 'p.setup_stage ' . $sortdir . ', p.state '
						. $sortdir . ', p.created ' . $sortdir;
					break;

				default:
					$sort .= 'p.title ' . $sortdir . ' ';
					break;
			}

			$query  .= " ORDER BY $sort ";
		}

		return $query;
	}

	/**
	 * Get item count
	 *
	 * @param   array    $filters
	 * @param   boolean  $admin
	 * @param   integer  $uid
	 * @param   boolean  $showall
	 * @param   integer  $setup_complete
	 * @return  integer
	 */
	public function getCount($filters = array(), $admin = false, $uid = 0 , $showall = 0, $setup_complete = 3)
	{
		$filters['count'] = true;
		$admin = $admin == 'admin' ? true : false;

		$query  = "SELECT count(DISTINCT p.id) ";
		$query .= $this->buildQuery($filters, $admin, $uid, $showall, $setup_complete);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array    $filters
	 * @param   boolean  $admin
	 * @param   integer  $uid
	 * @param   boolean  $showall
	 * @param   integer  $setup_complete
	 * @return  object
	 */
	public function getRecords($filters = array(), $admin = false, $uid = 0, $showall = 0, $setup_complete = 3)
	{
		$filters['count'] = false;
		$admin    = $admin == 'admin' ? true : false;
		$updates  = isset($filters['updates']) && $filters['updates'] == 1 ? 1: 0;
		$getowner = isset($filters['getowner']) && $filters['getowner'] == 1 ? 1: 0;
		$activity = isset($filters['activity']) && $filters['activity'] == 1 ? 1: 0;

		$query  = "SELECT p.*, IFNULL(o.role, 0) as role, o.id as owner, o.added as since, o.status as confirmed ";

		if ($getowner)
		{
			$query .= ", x.name as authorname, g.cn as groupcn, g.description as groupname ";
		}
		$query .= " ,(SELECT t.type FROM #__project_types as t WHERE t.id=p.type) as projecttype ";

		if ($updates)
		{
			$query .= ", (SELECT COUNT(*) FROM #__project_activity AS pa
						WHERE pa.projectid=p.id AND pa.recorded >= o.lastvisit
						AND o.lastvisit IS NOT NULL AND o.id IS NOT NULL
						AND pa.state != 2 AND (pa.managers_only = 0
						OR (pa.managers_only=1 AND o.role=1))) as newactivity ";
		}
		if ($activity)
		{
			$query .= ", (SELECT COUNT(*) FROM #__project_activity AS pa
						WHERE pa.projectid=p.id AND pa.state != 2) as activity ";
		}
		$query .= $this->buildQuery($filters, $admin, $uid, $showall, $setup_complete);

		if (isset($filters['limit']) && $filters['limit'] != 'all' && $filters['limit'] != 0)
		{
			$filters['start'] = isset($filters['start']) ? $filters['start'] : 0;
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get project ids/aliased by tag
	 *
	 * @param   string  $tag
	 * @param   bool    $include
	 * @param   string  $get
	 * @return  array
	 */
	public function getProjectsByTag($tag = 'test', $include = true, $get = 'id')
	{
		$ids = array();

		$get    = $get == 'id' ? 'id' : 'alias';
		$query  = "SELECT DISTINCT p.$get, ";
		$query .= "(SELECT COUNT(*) FROM #__tags_object AS RTA
					JOIN #__tags AS TA ON RTA.tagid = TA.id
					AND RTA.tbl='projects'
					WHERE TA.tag=" . $this->_db->quote($tag) . " AND RTA.objectid=p.id) as count";
		$query .= " FROM $this->_tbl AS p ";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if ($result)
		{
			foreach ($result as $r)
			{
				if (($include == true && $r->count > 0) || ($include == false && $r->count == 0))
				{
					$ids[] = $r->$get;
				}
			}
		}

		return $ids;
	}

	/**
	 * Get group projects
	 *
	 * @param   integer  $groupid
	 * @param   integer  $uid
	 * @param   array    $filters
	 * @param   integer  $setup_complete
	 * @return  object
	 */
	public function getGroupProjects($groupid = 0, $uid = 0, $filters = array(), $setup_complete = 3)
	{
		$query  = "SELECT DISTINCT p.*, IFNULL(o.role, 0) as role, o.id as owner, o.added as since, o.status as confirmed ";
		$query .= ", x.name as authorname, g.cn as groupcn, g.description as groupname ";
		$query .= ", (SELECT COUNT(*) FROM #__project_activity AS pa WHERE pa.projectid=p.id
					AND pa.recorded >= o.lastvisit AND o.lastvisit IS NOT NULL
					AND o.id IS NOT NULL AND pa.state != 2) as newactivity ";
		$query .= " FROM #__project_owners as po, $this->_tbl AS p";
		$query .= " LEFT JOIN #__project_owners AS o ON o.projectid=p.id AND o.userid=" . $this->_db->quote($uid) . " AND o.userid != 0 AND p.state!= 2 ";
		$query .=  " JOIN #__users as x ON x.id=p.created_by_user ";
		$query .=  " LEFT JOIN #__xgroups as g ON g.gidNumber=p.owned_by_group ";

		if (isset($filters['filterby']) && in_array($filters['filterby'], array('active', 'archived')))
		{
			if ($filters['filterby'] == 'archived')
			{
				$query .=  " WHERE p.id=po.projectid AND p.state=3 AND po.status=1 AND po.groupid=" . $groupid;
			}
			else if ($filters['filterby'] == 'active')
			{
				$query .=  " WHERE p.id=po.projectid AND p.state NOT IN (2, 3) AND po.status=1 AND po.groupid=" . $groupid;
			}
		}
		else
		{
			$query .=  " WHERE p.id=po.projectid AND p.state !=2 AND po.status=1 AND po.groupid=" . $groupid;
		}

		$filters['which'] = isset($filters['which']) ? $filters['which'] : '';
		if ($filters['which'] == 'owned')
		{
			$query .= " AND p.owned_by_group = '$groupid' ";
		}
		else if ($filters['which'] == 'other')
		{
			$query .= " AND p.owned_by_group != '$groupid' ";
		}

		$query .= $uid
				? " AND (p.state = 1 OR (o.userid=" . $this->_db->quote($uid) . " AND o.status!=2
					AND ((p.state = 1 AND p.setup_stage = " . $setup_complete . ")
					OR (o.role = 1 AND p.owned_by_user=" . $this->_db->quote($uid) . ")))) "
				: " AND p.state = 1 ";

		// Sorting
		if (!isset($filters['count']) or $filters['count'] == 0)
		{
			$sort = '';
			$sortby  = isset($filters['sortby']) ? $filters['sortby'] : 'title';
			$sortdir = isset($filters['sortdir']) && strtoupper($filters['sortdir']) == 'DESC'  ? 'DESC' : 'ASC';

			switch ($sortby)
			{
				case 'role':
					$sort .= 'o.role ' . $sortdir . ' ';
					break;

				case 'status':
					$sort .= 'p.setup_stage ' . $sortdir . ', p.state ' . $sortdir . ', p.title ASC';
					break;

				case 'title':
				default:
					$sort .= 'p.title ' . $sortdir . ' ';
					break;
			}

			$query  .= " ORDER BY $sort ";
		}

		if (isset($filters['count']) && $filters['count'] == 1)
		{
			$this->_db->setQuery($query);
			return $this->_db->loadResult();
		}
		else if (isset($filters['limit']) && $filters['limit'] != 'all' && $filters['limit'] != 0)
		{
			$filters['start'] = isset($filters['start']) ? $filters['start'] : 0;
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get user project ids
	 *
	 * @param   integer  $uid
	 * @param   integer  $active
	 * @param   integer  $include_provisioned
	 * @return  array
	 */
	public function getUserProjectIds($uid = 0, $active = 1, $include_provisioned = 0)
	{
		$ids = array();

		if ($uid)
		{
			$query  = "SELECT DISTINCT p.id ";
			$query .= " FROM $this->_tbl AS p, #__project_owners as o ";
			$query .= " WHERE p.id=o.projectid ";
			$query .= $active == 1
					? "AND (p.state=1 OR (o.role = 1 AND p.owned_by_user=" . $this->_db->quote($uid) . " AND p.state !=2)) "
					: "AND p.state !=2 ";
			$query .= $include_provisioned ? "" : " AND p.provisioned=0";
			$query .= " AND o.userid=" . $uid;
			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();
			if ($result)
			{
				foreach ($result as $r)
				{
					$ids[] = $r->id;
				}
			}
		}

		return $ids;
	}

	/**
	 * Get project ids for a group
	 *
	 * @param   integer  $groupid
	 * @param   integer  $uid
	 * @param   integer  $active
	 * @return  array
	 */
	public function getGroupProjectIds($groupid = 0, $uid = 0, $active = 1)
	{
		$ids = array();

		if ($uid)
		{
			$query  = "SELECT DISTINCT p.id ";
			$query .= " FROM #__project_owners as po, $this->_tbl AS p";
			$query .= " LEFT JOIN #__project_owners AS o ON o.projectid=p.id
						AND o.userid=" . $this->_db->quote($uid) . " AND o.userid != 0  ";
			$query .= " WHERE p.id=po.projectid AND po.status=1 AND po.groupid=" . $groupid;
			$query .= $active == 1
					? " AND (p.state=1 OR (o.role = 1 AND p.owned_by_user=" . $this->_db->quote($uid) . " AND p.state !=2))  "
					: " AND p.state !=2 ";
			$query .= " AND p.provisioned=0";
			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();
			if ($result)
			{
				foreach ($result as $r)
				{
					$ids[] = $r->id;
				}
			}
		}

		return $ids;
	}

	/**
	 * Get count of new activity since user last visit (multiple projects)
	 *
	 * @param   array    $projects
	 * @param   integer  $uid
	 * @return  integer
	 */
	public function getUpdateCount($projects = array(), $uid = 0)
	{
		if (!empty($projects) && $uid != 0)
		{
			$query  = "SELECT COUNT(*) FROM `#__project_activity` AS pa ";
			$query .= " JOIN `#__project_owners` as o ON o.projectid=pa.projectid AND o.userid=" . $this->_db->quote($uid);
			$query .= " WHERE pa.recorded >= o.lastvisit AND o.lastvisit IS NOT NULL
						AND pa.state !=2 AND pa.recorded >= o.added";
			$query .= " AND pa.projectid IN (";

			$tquery = array();
			foreach ($projects as $project)
			{
				$tquery[] = $this->_db->quote($project);
			}

			$query .= implode(',', $tquery) . ") ";
			$this->_db->setQuery($query);

			return $this->_db->loadResult();
		}

		return 0;
	}

	/**
	 * Get project
	 *
	 * @param   string   $identifier
	 * @param   integer  $uid
	 * @param   integer  $pubid
	 * @return  mixed    (array or false)
	 */
	public function getProject($identifier = null, $uid = 0, $pubid = 0)
	{
		if ($identifier === null && !$pubid)
		{
			return false;
		}

		$query  = "SELECT p.*, IFNULL(o.role, 0) as role, o.groupid as owner_group,
				   o.id as owner, o.added as since, o.lastvisit, o.num_visits,
				   o.params as owner_params, o.status as confirmed, ";
		$query .= " u.name, u.username, u.name as fullname ";
		$query .= " ,(SELECT t.type FROM #__project_types as t WHERE t.id=p.type) as projecttype ";

		if (intval($pubid) > 0)
		{
			$query .= " FROM #__publications AS pu JOIN $this->_tbl AS p ON pu.project_id=p.id ";
		}
		else
		{
			$query .= " FROM $this->_tbl AS p ";
		}
		$query .= " LEFT JOIN #__project_owners AS o ON o.projectid=p.id ";
		$query .= " AND o.userid=" . $this->_db->quote($uid) . " AND p.state!= 2 AND o.userid != 0 AND o.status !=2";
		$query .=  " JOIN #__users as u ON u.id=p.owned_by_user ";
		$query .= " WHERE ";

		if (intval($pubid) > 0)
		{
			$query .= " pu.id=" . $pubid;
		}
		else
		{
			$query .= is_numeric($identifier)
				? " p.id=" . $this->_db->quote($identifier)
				: " p.alias=" . $this->_db->quote($identifier);
		}
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : null;
	}

	/**
	 * Select item
	 *
	 * @param   string   $select
	 * @param   string   $where
	 * @param   integer  $limit
	 * @return  mixed    (string or object)
	 */
	public function selectWhere($select, $where, $limit = 0)
	{
		$query  = "SELECT $select FROM $this->_tbl WHERE $where";
		$query .= $limit ? " LIMIT 1 " : "";

		$this->_db->setQuery($query);
		return $limit ? $this->_db->loadResult() : $this->_db->loadObjectList();
	}

	/**
	 * Get project alias from ID
	 *
	 * @param   integer  $id
	 * @return  string
	 */
	public function getAlias($id = 0)
	{
		if (!$id)
		{
			return false;
		}
		$query = "SELECT alias FROM $this->_tbl WHERE id=" . $this->_db->quote($id);
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Save project parameter
	 *
	 * @param   integer  $projectid
	 * @param   string   $param
	 * @param   string   $value
	 * @return  void
	 */
	public function saveParam($projectid = null, $param = '', $value = 0)
	{
		if ($projectid === null)
		{
			return false;
		}

		// Clean up value
		$value = preg_replace('/=/', '', $value);

		if ($this->loadProject($projectid))
		{
			if ($this->params)
			{
				$params = explode("\n", $this->params);
				$in = '';
				$found = 0;

				// Change param
				if (!empty($params))
				{
					foreach ($params as $p)
					{
						if (trim($p) != '' && trim($p) != '=')
						{
							$extracted = explode('=', $p);
							if (!empty($extracted))
							{
								$in .= $extracted[0] . '=';
								$default = isset($extracted[1]) ? $extracted[1] : 0;
								$in .= $extracted[0] == $param ? $value : $default;
								$in	.= "\n";
								if ($extracted[0] == $param)
								{
									$found = 1;
								}
							}
						}
					}
				}
				if (!$found)
				{
					$in .= "\n" . $param . '=' . $value;
				}
			}
			else
			{
				$in = $param . '=' . $value;
			}
			$this->params = $in;
			$this->store();
		}
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   string  $identifier   project id or alias name
	 * @return  mixed   object or false
	 */
	public function loadProject($identifier = null)
	{
		if ($identifier === null)
		{
			return false;
		}
		$name = is_numeric($identifier) ? 'id' : 'alias';

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE $name=" . $this->_db->quote($identifier) . " LIMIT 1");
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}

		return false;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   string  $identifier   project id or alias name
	 * @return  mixed   object or false
	 */
	public function loadProvisionedProject($pid = null)
	{
		if (!intval($pid))
		{
			return false;
		}

		$this->_db->setQuery("SELECT p.* FROM `#__publications` AS pu JOIN $this->_tbl AS p ON pu.project_id=p.id WHERE pu.id=" . $this->_db->quote($pid) . " LIMIT 1");
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}

		return false;
	}

	/**
	 * Check if name is unique
	 *
	 * @param   string   $name
	 * @param   integer  $pid
	 * @return  boolean
	 */
	public function checkUniqueName($name, $pid = 0)
	{
		if ($name === null)
		{
			return false;
		}
		$query  =  "SELECT id FROM $this->_tbl WHERE alias=" . $this->_db->quote($name);
		$query .= $pid ? " AND id!=" . $this->_db->quote($pid) : "";
		$query .= " LIMIT 1 ";
		$this->_db->setQuery($query);
		if ($this->_db->loadResult())
		{
			return false;
		}
		return true;
	}

	/**
	 * Save setup stage
	 *
	 * @param   integer  $projectid
	 * @param   integer  $stage
	 * @return  boolean
	 */
	public function saveStage($projectid = null, $stage = 0)
	{
		if ($projectid === null)
		{
			return false;
		}
		$query  = "SELECT * FROM $this->_tbl WHERE id=" . $this->_db->quote($projectid) . " LIMIT 1";
		$this->_db->setQuery($query);

		if ($result = $this->_db->loadAssoc())
		{
			$this->bind($result);
			$this->setup_stage = $stage;
			if (!$this->store())
			{
				$this->setError(Lang::txt('Failed to update setup stage.'));
				return false;
			}
			return true;
		}

		return false;
	}
}
