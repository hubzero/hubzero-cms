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

use Hubzero\Database\Table;
use User;
use Lang;

/**
 * Table class for project tools
 */
class Tool extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_tools', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->name) == '')
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_NO_NAME'));
			return false;
		}

		return true;
	}

	/**
	 * Load item
	 *
	 * @param   string  $identifier
	 * @param   int     $projectid
	 * @return  boolean False or object
	 */
	public function loadTool($identifier = null, $projectid = 0)
	{
		if ($identifier === null)
		{
			return false;
		}
		$name = is_numeric($identifier) ? 'id' : 'name';

		$query = "SELECT * FROM $this->_tbl WHERE $name=" . $this->_db->quote($identifier);
		$query.= intval($projectid) > 0 ? " AND project_id=" . $this->_db->quote($projectid) : "";
		$query.= " LIMIT 1";

		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind( $result );
		}
		else
		{
			return false;
		}
	}

	/**
	 * Build query
	 *
	 * @param   array    $filters
	 * @param   boolean  $admin
	 * @param   integer  $count
	 * @return  string
	 */
	public function buildQuery($filters, $admin, $count = 0)
	{
		// get and set record filter
		$filter   = ($admin) ? " WHERE f.id!=0 ": " WHERE f.status !=2 ";
		$project  = isset($filters['project']) && intval($filters['project']) ? $filters['project'] : "";
		$dev      = isset($filters['dev']) && $filters['dev'] == 1 ? 1 : 0;
		$projects = isset($filters['projects']) && !empty($filters['projects'])
					? $filters['projects'] : array();
		$filterby = isset($filters['filterby']) ? $filters['filterby'] : "";

		switch ($filterby)
		{
			case 'mine':
				$filter .= " AND f.created_by='" . User::get('id') . "' ";
				break;
			case 'published':
				$filter .= " AND f.published='1' ";
				break;
			case 'dev':
				$filter .= " AND f.published='0' ";
				break;
			case 'all':
			default:
				$filter .= " ";
				break;
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$search = $filters['search'];
			if (intval($search))
			{
				$filter .= " AND f.id='%$search%' ";
			}
			else
			{
				$filter .= " AND ((LOWER(f.name) LIKE '%$search%') OR (LOWER(f.title) LIKE '%$search%')) ";
			}
		}

		$filter .= $project ? " AND f.project_id=" . $project : "";

		if ($projects)
		{
			$filter .= " AND f.project_id IN (";

			$tquery = '';
			foreach ($projects as $project)
			{
				$tquery .= "'" . $project . "',";
			}
			$tquery = substr($tquery, 0, strlen($tquery) - 1);
			$filter .= $tquery . ") ";
		}

		// Sorting
		$sort = '';
		$sortdir = isset($filters['sortdir']) ? $filters['sortdir'] : 'ASC';
		$sortby = (isset($filters['sortby'])) ? $filters['sortby'] : '';

		switch ($sortby)
		{
			case 'title':
				$sort .= 'f.title ' . $sortdir . ' ';
				break;

			case 'name':
				$sort .= 'f.name ' . $sortdir . ' ';
				break;

			case 'status':
				$sort .= 'f.status ' . $sortdir . ' ';
				break;

			case 'created':
				$sort .= 'f.created ' . $sortdir . ' ';
				break;

			case 'status_changed':
				$sort .= 'f.status_changed ' . $sortdir . ' ';
				break;

			default:
				$sort .= $admin ? 'f.status, f.created' : 'f.status_changed DESC';
		}

		$query  = "$this->_tbl as f "
				. "JOIN #__project_tool_instances AS v ON f.id=v.parent_id AND v.state=3 ";
		if (!$count)
		{
			$query .= "JOIN #__projects as p ON p.id=f.project_id ";

			// Get some extra info for sv tools
			/*
			$query .= "LEFT JOIN #__tool as T ON T.id=f.svntool_id ";
			$query .= "LEFT JOIN #__tool_version as TV ON TV.id=v.svntool_version_id ";

			// Get publication assoc
			$query .= "LEFT JOIN #__publication_attachments as PA ON PA.type ='tool' AND
					PA.object_id=f.id AND PA.object_instance=v.id ";
			$query .= "LEFT JOIN #__publication_versions as PV ON PV.id=PA.publication_version_id AND PV.main=1 ";
			*/
		}

		$query .= "$filter"
				. "\n ORDER BY $sort";

		return $query;
	}

	/**
	 * Check for unique name
	 *
	 * @param   string  $name  Name
	 * @param   string  $id    App id
	 * @return  mixed   integer or False
	 */
	public function checkUniqueName($name = null, $id = null)
	{
		$sql = "SELECT count(*) FROM $this->_tbl WHERE name=" . $this->_db->quote($name);
		if ($id)
		{
			$sql .= " AND id !=". $this->_db->quote($id);
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get item count
	 *
	 * @param   array    $filters
	 * @param   boolean  $admin
	 * @param   integer  $count
	 * @return  integer
	 */
	public function getRecordCount($filters = array(), $admin=false, $count = 1)
	{
		$sql = "SELECT count(*) FROM " . $this->buildQuery($filters, $admin);

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array    $filters
	 * @param   boolean  $admin
	 * @return  array    list
	 */
	public function getRecords($filters = array(), $admin = false)
	{
		$sql = "SELECT f.*, v.*, v.id as instanceId,
			f.repotype, f.svntool_id, v.svntool_version_id";

		$sql .= " FROM " . $this->buildQuery($filters, $admin);
		if (isset($filters['start']) && isset($filters['limit']) && $filters['limit'] != 0)
		{
			$sql .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get full record
	 *
	 * @param   string   $identifier
	 * @param   integer  $projectid
	 * @param   integer  $instanceId
	 * @return  object
	 */
	public function getFullRecord($identifier = null, $projectid = 0, $instanceId = 0)
	{
		if ($identifier === null)
		{
			return false;
		}
		$name = is_numeric($identifier) ? 'f.id' : 'f.name';

		$sql = "SELECT f.name, f.title, f.project_id, f.created as registered,
			f.created_by as registered_by, f.picture, f.status_changed_by,
			f.published, f.status_changed,
			p.alias as project_alias, p.title as project_title,
			f.repotype, f.svntool_id, v.svntool_version_id,
			TV.exportControl, TV.vnc_geometry, TV.vnc_timeout, TV.vnc_depth, TV.vnc_command,
			f.opendev, f.opensource, s.*, s.status as status_name, f.status,
			v.*, v.id as instanceId, v.parent_id as id, x.name as creator";

		$sql .= ', PA.publication_id, PA.publication_version_id,
				PV.title as publication_title, PV.state as publication_version_status ';

		$sql .= " FROM $this->_tbl as f "
				. "JOIN #__project_tool_instances AS v ON f.id=v.parent_id ";
		$sql .= intval($instanceId) ? "AND v.id=" . $this->_db->quote($instanceId) : "AND v.state=3 ";
		$sql .= " JOIN #__project_tool_statuses AS s ON f.status=s.id ";
		$sql .= "JOIN #__projects as p ON p.id=f.project_id ";
		$sql .= "JOIN #__xprofiles as x ON x.uidNumber=f.created_by ";

		// Get some extra info for sv tools
		$sql .= "LEFT JOIN #__tool as T ON T.id=f.svntool_id ";
		$sql .= "LEFT JOIN #__tool_version as TV ON TV.id=v.svntool_version_id ";

		// Get publication assoc
		$sql .= "LEFT JOIN #__publication_attachments as PA ON PA.type ='tool' AND
				PA.object_id=f.id AND PA.object_instance=v.id ";
		$sql .= "LEFT JOIN #__publication_versions as PV ON PV.id=PA.publication_version_id AND PV.main=1 ";

		$sql .= " WHERE $name=" . $this->_db->quote($identifier);
		$sql .= intval($projectid) > 0 ? " AND f.project_id=" . $this->_db->quote($projectid) : "";

		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : null;
	}

	/**
	 * Get ID
	 *
	 * @param   string  $name
	 * @return  integer
	 */
	public function getId($name = null)
	{
		if ($name === null)
		{
			return false;
		}
		$this->_db->setQuery("SELECT id FROM `#__project_tool` WHERE name=" . $this->_db->quote($name) . " LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Update status
	 *
	 * @param   string   $id
	 * @param   string   $status
	 * @param   string   $by
	 * @return  boolean  true on success
	 */
	public function updateStatus($id = null, $status = null, $by = null)
	{
		if ($id === null)
		{
			return false;
		}
		if ($status)
		{
			$query = "UPDATE $this->_tbl SET ";
			$query.= "status=" . $this->_db->quote($status) . ", status_changed='" . date('Y-m-d H:i:s', time()) . "'";
			$query.= $by ? ", status_changed_by=" . $this->_db->quote($by) : "";
			$query.= " WHERE id=" . $this->_db->quote($id);
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				return false;
			}
		}
		return true;
	}
}
