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
 * Table class for project activity
 */
class Activity extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__project_activity', 'id', $db );
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      integer $id activity id
	 * @param      integer $projectid
	 * @return     object or false
	 */
	public function loadActivity( $id = NULL, $projectid = NULL )
	{
		if ($id === NULL || !intval($id) || $projectid === NULL)
		{
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE id=" . intval($id) . " AND projectid=" . intval($projectid) . " LIMIT 1" );
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
	 * Load activity by reference
	 *
	 * @param      integer $projectid
	 * @param      integer $refid
	 * @param      string $class
	 * @param      string $activity
	 * @return     object or false
	 */
	public function loadActivityByRef ( $projectid = NULL, $refid = 0, $class = '', $activity = '' )
	{
		if ($projectid === NULL || $refid == 0 || !$class || !$activity || intval($projectid) == 0)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE referenceid = '$refid'
				AND projectid = " . $this->_db->quote($projectid) . " AND class = "
				. $this->_db->quote($class) . " AND activity = "
				. $this->_db->quote($activity) . " LIMIT 1";
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
	 * Get items
	 *
	 * @param      integer $projectid
	 * @param      array $filters
	 * @param      boolean $count
	 * @param      integer $uid
	 * @param      array $projects
	 * @return     object or integer or false
	 */
	public function getActivities ( $projectid = NULL, $filters = array(), $count = 0, $uid = 0, $projects = array() )
	{
		if (!$projectid && empty($projects))
		{
			return false;
		}

		$sortby  	= isset($filters['sortby']) ? $filters['sortby'] : 'recorded';
		$limit   	= isset($filters['limit']) ? $filters['limit'] : 0;
		$limitstart = isset($filters['start']) ? $filters['start'] : 0;
		$class 		= isset($filters['class']) ? $filters['class'] : '';
		$sortdir    = isset($filters['sortdir']) && strtoupper($filters['sortdir']) == 'ASC'  ? 'ASC' : 'DESC';
		$managers 	= isset($filters['managers']) ? $filters['managers'] : 0;
		$role 		= isset($filters['role']) ? $filters['role'] : 0;
		$id 		= isset($filters['id']) ? $filters['id'] : 0;

		$query   =  "SELECT ";
		if ($count)
		{
			$query  .=  " COUNT(*) ";
		}
		else
		{
			$query .= " DISTINCT a.*, x.name, x.username ";
		}
		$query  .= " FROM $this->_tbl AS a ";
		if (!$count)
		{
			$query  .= "JOIN #__xprofiles as x ON x.uidNumber=a.userid";
		}
		if ($projectid)
		{
		$query  .= " WHERE a.projectid=" . $this->_db->quote($projectid);
		}
		else
		{
			$query  .= " WHERE a.projectid IN ( ";
			$tquery = '';
			foreach ($projects as $project)
			{
				$tquery .= "'" . intval($project) . "',";
			}
			$tquery = substr($tquery, 0, strlen($tquery) - 1);
			$query .= $tquery . ") ";
		}
		if ($class)
		{
			$query  .= " AND a.class=" . $this->_db->quote($class);
		}
		if ($managers && $role == 1)
		{
			$query  .= " AND a.managers_only=1 ";
		}
		if ($role == 0)
		{
			$query  .= " AND a.managers_only=0 ";
		}
		if ($id)
		{
			if (is_array($id))
			{
				$query  .= " AND a.id IN ( ";
				$tquery = '';
				foreach ($id as $a)
				{
					$tquery .= "'" . intval($a) . "',";
				}
				$tquery = substr($tquery, 0, strlen($tquery) - 1);
				$query .= $tquery . ") ";
			}
			elseif (intval($id))
			{
				$query  .= " AND a.id=" . $this->_db->quote($id);
			}
		}

		$query  .= " AND a.state != 2 ";

		if (isset($filters['recorded']) && $filters['recorded'])
		{
			$query  .= " AND a.recorded > " . $this->_db->quote($filters['recorded']);
		}

		$query  .= " ORDER BY ";
		$query  .=  $sortby == 'recorded' ? " a.recorded $sortdir " : "";
		$query  .=  $sortby == 'class' ? " a.class $sortdir " : "";

		if (!$count)
		{
			if (isset ($limit) && $limit!=0)
			{
				$query.= " LIMIT " . $limitstart . ", " . $limit;
			}
		}

		$this->_db->setQuery( $query );
		return $count ? $this->_db->loadResult() : $this->_db->loadObjectList();
	}

	/**
	 * Record activity
	 *
	 * @param      integer $projectid
	 * @param      integer $by
	 * @param      string $activity
	 * @param      string $referenceid
	 * @param      string $highlighted
	 * @param      string $url
	 * @param      string $class
	 * @param      boolean $commentable
	 * @param      boolean $admin
	 * @param      boolean $managers_only
	 * @return     integer (activity id) or false
	 */
	public function recordActivity ( $projectid = NULL, $by = NULL, $activity = NULL,
		$referenceid = 0, $highlighted = '', $url = '', $class = 'project',
		$commentable = 0, $admin = 0, $managers_only = 0 )
	{
		if ($projectid === NULL || $activity === NULL || $by === NULL || intval($projectid) == 0)
		{
			return false;
		}

		// Collapse some repeated activities by the same actor
		if ($referenceid || $class == 'project')
		{
			$this->_db->setQuery( "UPDATE $this->_tbl SET state = 2 WHERE class="
				. $this->_db->quote($class) . " AND activity="
				. $this->_db->quote($activity) . " AND userid="
				. $this->_db->quote($by) . " AND projectid="
				. $this->_db->quote($projectid) . " AND referenceid="
				. $this->_db->quote($referenceid));
			$this->_db->query();
		}

		$this->commentable 	 = $commentable;
		$this->admin 		 = $admin;
		$this->managers_only = $managers_only;

		// Collapse checked/posted to-do item activities
		if ($class == 'todo' && $activity == Lang::txt('COM_PROJECTS_ACTIVITY_TODO_COMPLETED'))
		{
			$this->loadActivityByRef($projectid, $referenceid, $class,
				Lang::txt('COM_PROJECTS_ACTIVITY_TODO_ADDED'));
		}

		$this->projectid 	= $projectid;
		$this->userid 		= $by;
		$this->recorded 	= Date::toSql();
		$this->activity 	= $activity;
		$this->highlighted 	= $highlighted;
		$this->referenceid 	= $referenceid;
		$this->url 			= $url;
		$this->class 		= $class;

		if (!$this->store())
		{
			return false;
		}
		else
		{
			return $this->id;
		}
	}

	/**
	 * Delete activity by reference
	 *
	 * @param      integer $projectid
	 * @param      string $refid
	 * @param      string $class
	 * @param      boolean $permanent
	 * @return     boolean true on success
	 */
	public function deleteActivityByReference ( $projectid = 0, $refid = 0, $class = '', $permanent = false )
	{
		if (!$refid || !$projectid || !$class || intval($projectid) == 0)
		{
			return false;
		}

		$query  = ($permanent) ? "DELETE FROM $this->_tbl " : "UPDATE $this->_tbl SET state = 2 ";
		$query .= " WHERE projectid=" . $this->_db->quote($projectid) . " AND referenceid=" . $this->_db->quote($refid) . " AND class=" . $this->_db->quote($class);

		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	/**
	 * Delete activity
	 *
	 * @param      integer $aid
	 * @param      boolean $permanent
	 * @return     boolean true on success
	 */
	public function deleteActivity ( $aid = 0, $permanent = false )
	{
		if (!$aid)
		{
			$aid = $this->id;
		}
		if (!$aid || intval($aid) == 0)
		{
			return false;
		}

		$query  = ($permanent) ? "DELETE FROM $this->_tbl " : "UPDATE $this->_tbl SET state = 2 ";
		$query .= " WHERE id=" . $aid;

		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	/**
	 * Save activity preview
	 *
	 * @param      integer $aid
	 * @param      text $preview
	 * @return     boolean true on success
	 */
	public function saveActivityPreview ( $aid = 0, $preview = NULL )
	{
		if (!$aid || intval($aid) == 0)
		{
			return false;
		}
		$query  = "UPDATE $this->_tbl SET preview =" . $this->_db->quote($preview);
		$query .= " WHERE id=" . $aid;

		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	/**
	 * Delete activities
	 *
	 * @param      integer $projectid
	 * @param      boolean $permanent
	 * @return     boolean true on success
	 */
	public function deleteActivities ( $projectid = 0, $permanent = false )
	{
		if (!$projectid)
		{
			$projectid = $this->projectid;
		}
		if (!$projectid || intval($projectid) == 0)
		{
			return false;
		}

		$query  = ($permanent) ? "DELETE FROM $this->_tbl " : "UPDATE $this->_tbl SET state = 2 ";
		$query .= " WHERE projectid=" . $projectid;

		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	/**
	 * Get count of new activity since member last visit (single project)
	 *
	 * @param      integer $projectid
	 * @param      integer $uid
	 * @return     integer or NULL
	 */
	public function getNewActivityCount ( $projectid = NULL, $uid = 0 )
	{
		if ($projectid === NULL || !$uid || intval($projectid) == 0)
		{
			return false;
		}

		$query  = " SELECT COUNT(*) FROM #__project_activity AS X ";
		$query .= " LEFT JOIN #__project_owners as o ON o.projectid=X.projectid AND o.userid=" . $this->_db->quote($uid);
		$query .= " WHERE X.projectid=" . $projectid . "
					AND (X.recorded >= o.lastvisit AND o.lastvisit IS NOT NULL
				    AND X.state != 2 AND (X.managers_only = 0
				    OR (X.managers_only=1 AND o.role=1)) )";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Get activity count
	 *
	 * @param      integer $projectid
	 * @param      integer $uid
	 * @return     integer or NULL
	 */
	public function getActivityCount ( $projectid = NULL, $uid = 0 )
	{
		if ($projectid === NULL || !$uid || intval($projectid) == 0)
		{
			return false;
		}

		$query  = " SELECT COUNT(*) FROM #__project_activity AS X ";
		$query .= " WHERE X.projectid=" . $projectid . "
				    AND X.state != 2";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Get top active projects
	 *
	 * @param      array 	$exclude
	 * @param      integer  $limit
	 * @param      boolean  $publicOnly
	 * @return     mixed
	 */
	public function getTopActiveProjects ( $exclude = array(), $limit = 3, $publicOnly = false)
	{
		$query  = " SELECT p.id, p.alias, p.title, p.picture, p.private, COUNT(PA.id) as activity ";
		$query .= " FROM #__projects AS p";
		$query .= " JOIN $this->_tbl as PA ON PA.projectid = p.id WHERE PA.projectid = p.id ";

		if ($publicOnly)
		{
			$query .= " AND p.private = 0 ";
		}

		if (!empty($exclude))
		{
			$query .= " AND p.id NOT IN ( ";

			$tquery = '';
			foreach ($exclude as $ex)
			{
				$tquery .= "'" . intval($ex) . "',";
			}
			$tquery = substr($tquery, 0, strlen($tquery) - 1);
			$query .= $tquery . ") ";
		}

		$query .= " GROUP BY p.id ";
		$query .= " ORDER BY activity DESC ";
		$query .= " LIMIT 0," . intval($limit);

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();

	}

	/**
	 * Get activity stats
	 *
	 * @param      array 	$validProjects
	 * @param      string 	$get
	 * @return     mixed
	 */
	public function getActivityStats ( $validProjects = array(), $get = 'total')
	{
		if (empty($validProjects))
		{
			return NULL;
		}

		$query  = " SELECT COUNT(*) as activity ";
		$query .= " FROM $this->_tbl ";

		if (!empty($validProjects))
		{
			$query .= " WHERE projectid IN ( ";

			$tquery = '';
			foreach ($validProjects as $v)
			{
				$tquery .= "'" . intval($v) . "',";
			}
			$tquery = substr($tquery, 0, strlen($tquery) - 1);
			$query .= $tquery . ") ";
		}

		if ($get == 'average')
		{
			$query .= " GROUP BY projectid ";
		}

		$this->_db->setQuery( $query );

		if ($get == 'total')
		{
			return $this->_db->loadResult();
		}
		elseif ($get == 'average')
		{
			$result = $this->_db->loadObjectList();

			$c = 0;
			$d = 0;

			foreach ($result as $r)
			{
				$c = $c + $r->activity;
				$d++;
			}

			return number_format($c/$d,0);
		}
	}

	/**
	 * Match activity
	 *
	 * @param      integer $projectid
	 * @param      string $check
	 * @return     integer or false
	 */
	public function checkActivity ( $projectid = NULL, $check = NULL )
	{
		if ($projectid === NULL || intval($projectid) == 0)
		{
			return false;
		}

		$query  = "SELECT IF(admin = 0, 2, 1) FROM $this->_tbl ";
		$query .= "WHERE projectid=" .  $this->_db->quote($projectid);
		$query .= " AND activity=" . $this->_db->quote($check) . " AND state!=2 ";
		$query .= " ORDER BY recorded DESC LIMIT 1";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();
		if (!$result)
		{
			return false;
		}
		else
		{
			return $result;
		}
	}
}
