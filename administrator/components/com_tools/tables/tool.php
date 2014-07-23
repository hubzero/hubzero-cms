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
defined('_JEXEC') or die('Restricted access');

/**
 * Short description for 'Tool'
 *
 * Long description (if any) ...
 */
class Tool extends JTable
{
	/**
	 * Description for 'id'
	 *
	 * @var unknown
	 */
	var $id            = NULL;  // @var int (primary key)

	/**
	 * Description for 'toolname'
	 *
	 * @var unknown
	 */
	var $toolname      = NULL;  // @var string (15)

	/**
	 * Description for 'published'
	 *
	 * @var unknown
	 */
	var $published	   = NULL;  // @var tinyint

	/**
	 * Description for 'state'
	 *
	 * @var unknown
	 */
	var $state         = NULL;  // @var int (11)

	/**
	 * Description for 'priority'
	 *
	 * @var unknown
	 */
	var $priority      = NULL;  // @var int (11)

	/**
	 * Description for 'registered'
	 *
	 * @var unknown
	 */
	var $registered    = NULL;  // @var dateandtime

	/**
	 * Description for 'registered_by'
	 *
	 * @var unknown
	 */
	var $registered_by = NULL;  // @var string (31)

	/**
	 * Description for 'ticketid'
	 *
	 * @var unknown
	 */
	var $ticketid	   = NULL;  // @var int

	/**
	 * Description for 'state_changed'
	 *
	 * @var unknown
	 */
	var $state_changed = NULL;  // @var dateandtime

	/**
	 * Description for 'title'
	 *
	 * @var unknown
	 */
	var $title         = NULL;  // @var string (127)

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tool', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->toolname) == '')
		{
			$this->setError(JText::_('CONTRIBTOOL_ERROR_NO_TOOLNAME'));
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'loadFromName'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolname Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadFromName($toolname)
	{
		if ($toolname === NULL)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl as t WHERE t.toolname=" . $this->_db->Quote($toolname) . " LIMIT 1";

		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Short description for 'buildQuery'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $filters Parameter description (if any) ...
	 * @param      unknown $admin Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery($filters, $admin)
	{
		$juser = JFactory::getUser();

		// get and set record filter
		$filter = ($admin) ? " WHERE f.id!=0": " WHERE f.state!=9";

		switch ($filters['filterby'])
		{
			case 'mine':      $filter .= " AND f.registered_by=" . $this->_db->Quote($juser->get('username')) . " "; break;
			case 'published': $filter .= " AND f.published='1' AND f.state!='9' ";                  break;
			case 'dev':       $filter .= " AND f.published='0' AND f.state!='9' AND f.state!='8' "; break;
			case 'all':       $filter .= " ";                                                       break;
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$search = $filters['search'];
			if (intval($search))
			{
				$filter .= " AND f.id=" . $this->_db->quote('%' . $search . '%') . " ";
			}
			else
			{
				$filter .= " AND LOWER(f.toolname) LIKE " . $this->_db->quote('%' . $search . '%') . " ";
			}
		}
		if (!$admin)
		{
			$filter .= " AND m.uidNumber=" . $this->_db->Quote($juser->get('id')) . " ";
			$sortby = ($filters['sortby']) ? $filters['sortby'] : 'f.state, f.registered';
		}
		else
		{
			$sortby = ($filters['sortby']) ? $filters['sortby'] : 'f.state_changed DESC';
		}

		$query = "#__tool as f "
				. "JOIN #__tool_version AS v ON f.id=v.toolid AND v.state=3 "
				. "JOIN #__tool_groups AS g ON f.id=g.toolid AND g.cn=CONCAT('app-',f.toolname) AND g.role=1 "
				. "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		if (!$admin)
		{
			$query .= "JOIN #__xgroups_members AS m ON xg.gidNumber=m.gidNumber ";
		}
		$query .= "$filter"
				. " ORDER BY $sortby";

		return $query;
	}

	/**
	 * Short description for 'getToolCount'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $filters Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getToolCount($filters=array(), $admin=false)
	{
		$sql = "SELECT count(*) FROM " . $this->buildQuery($filters, $admin);

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getMyTools'
	 *
	 * Long description (if any) ...
	 *
	 * @return     object Return description (if any) ...
	 */
	public function getMyTools()
	{
		$sql = "SELECT r.alias, v.toolname, v.title, v.description, v.toolaccess AS access, v.mw, v.instance, v.revision
				FROM #__resources AS r, #__tool_version AS v
				WHERE r.published=1
				AND r.type=7
				AND r.standalone=1
				AND r.access!=4
				AND r.alias=v.toolname
				AND v.state=1
				ORDER BY v.title, v.toolname, v.revision DESC";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getTools'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $filters Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getTools($filters=array(), $admin=false)
	{
		$sql = "SELECT f.id, f.toolname, f.registered, f.published, f.state_changed, f.priority, f.ticketid, f.state as state, v.title, v.version, g.cn as devgroup"
				. " FROM " . $this->buildQuery($filters, $admin);
		if (isset($filters['start']) && isset($filters['limit']) && $filters['limit'] > 0)
		{
			$sql .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getToolsOldScheme'
	 *
	 * Long description (if any) ...
	 *
	 * @return     object Return description (if any) ...
	 */
	public function getToolsOldScheme()
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl");
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getTicketId'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getTicketId($toolid=NULL)
	{
		if ($toolid=== NULL)
		{
			return false;
		}
		$this->_db->setQuery("SELECT ticketid FROM #__tool WHERE id=" . $this->_db->Quote($toolid));
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getResourceId'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getResourceId($toolid=NULL)
	{
		if ($toolid=== NULL)
		{
			return false;
		}
		$this->_db->setQuery("SELECT r.id FROM #__tool as t LEFT JOIN #__resources as r ON r.alias = t.toolname WHERE t.id=" . $this->_db->Quote($toolid));
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getToolInstanceFromResource'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $version Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getToolInstanceFromResource($rid=NULL, $version ='dev')
	{
		if ($rid=== NULL)
		{
			return false;
		}

		$query = "SELECT v.instance FROM #__tool_version as v JOIN #__resources as r ON r.alias = v.toolname WHERE r.id=" . $this->_db->Quote($rid);
		if ($version=='dev')
		{
			$query.= " AND v.state=3 LIMIT 1";
		}
		else if ($version=='current')
		{
			$query.= " AND v.state=1 ORDER BY revision DESC LIMIT 1";
		}
		else
		{
			$query.= " AND v.version=" . $this->_db->Quote($version) . " LIMIT 1";
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getToolIdFromResource'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $rid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getToolIdFromResource($rid=NULL)
	{
		if ($rid=== NULL)
		{
			return false;
		}
		$this->_db->setQuery("SELECT t.id FROM #__tool as t JOIN #__resources as r ON r.alias = t.toolname WHERE r.id=" . $this->_db->Quote($rid) . " LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getToolnameFromResource'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $rid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getToolnameFromResource($rid=NULL)
	{
		if ($rid=== NULL)
		{
			return false;
		}
		$this->_db->setQuery("SELECT t.toolname FROM #__tool as t JOIN #__resources as r ON r.alias = t.toolname WHERE r.id=" . $this->_db->Quote($rid) . " LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getToolId'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolname Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getToolId($toolname=NULL)
	{
		if ($toolname=== NULL)
		{
			return false;
		}
		$this->_db->setQuery("SELECT id FROM #__tool WHERE toolname=" . $this->_db->Quote($toolname) . " LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'saveTicketId'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolid Parameter description (if any) ...
	 * @param      string $ticketid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function saveTicketId($toolid=NULL, $ticketid=NULL)
	{
		if ($toolid=== NULL or $ticketid=== NULL)
		{
			return false;
		}
		$query = "UPDATE #__tool SET ticketid=" . $this->_db->Quote($ticketid) . " WHERE id=" . $this->_db->Quote($toolid);
		$this->_db->setQuery($query);
		if ($this->_db->query())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Short description for 'updateTool'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolid Parameter description (if any) ...
	 * @param      string $newstate Parameter description (if any) ...
	 * @param      string $priority Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function updateTool($toolid=NULL, $newstate=NULL, $priority=NULL)
	{
		if ($toolid=== NULL)
		{
			return false;
		}
		if ($newstate or $priority)
		{
			$query = "UPDATE #__tool SET ";
			if ($newstate)
			{
				$query.= "state=" . $this->_db->Quote($newstate) . ", state_changed='" . JFactory::getDate()->toSql() . "'";
			}
			if ($newstate && $priority)
			{
				$query.= ", ";
			}
			if ($priority)
			{
				$query.= "priority=" . $this->_db->Quote($priority);
			}
			$query.= " WHERE id=" . $this->_db->Quote($toolid);
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				return false;
			}
		}
		return true;

	}

	/**
	 * Short description for 'getToolInfo'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolid Parameter description (if any) ...
	 * @param      string $toolname Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getToolInfo($toolid, $toolname='')
	{
		$juser = JFactory::getUser();
		$query  = "SELECT t.id, t.toolname, t.published, t.state, t.priority, t.registered, t.registered_by, t.ticketid, t.state_changed, r.id as rid, g.cn as devgroup";
		$query .= ", r.created as rcreated, r.modified as rmodified, r.fulltxt as rfulltxt";
		/*$query .= ", (SELECT COUNT(*) FROM #__support_comments AS sc LEFT JOIN #__tool_statusviews AS v ON v.ticketid=sc.ticket WHERE sc.ticket=t.ticketid AND
		 (UNIX_TIMESTAMP(sc.created)-UNIX_TIMESTAMP(t.state_changed))>=10 AND sc.access=0 AND sc.comment!='' AND sc.created_by!='".$juser->get('username')."'
		 AND (UNIX_TIMESTAMP(v.viewed)-UNIX_TIMESTAMP(sc.created))<= v.elapsed AND v.uid=".$juser->get('id').") AS comments ";*/
		$query .= ", (SELECT COUNT(*) FROM #__tool) AS ntools ";
		$query .= ", (SELECT COUNT(*) FROM #__tool WHERE published=0 AND state!='9' AND state!='8') AS ntoolsdev ";
		$query .= ", (SELECT COUNT(*) FROM #__tool WHERE published=1) AS ntoolspublished ";
		$query .= "FROM #__tool as t LEFT JOIN #__resources as r ON r.alias = t.toolname ";
		$query .= "JOIN #__tool_groups AS g ON t.id=g.toolid AND g.role=1 ";
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		if ($toolid)
		{
			$query .= "WHERE t.id = " . $this->_db->Quote($toolid);
		}
		else if ($toolname)
		{
			$query .= "WHERE t.toolname = " . $this->_db->Quote($toolname);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getToolDevGroup'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getToolDevGroup($toolid)
	{
		$query  = "SELECT g.cn FROM #__tool_groups AS g ";
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		$query .= "WHERE g.toolid = " . $this->_db->Quote($toolid) . " AND g.role=1 LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getToolDevelopers'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolid Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getToolDevelopers($toolid)
	{
		$query  = "SELECT m.uidNumber FROM #__tool_groups AS g ";
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		$query .= "JOIN #__xgroups_members AS m ON xg.gidNumber=m.gidNumber ";
		$query .= "WHERE g.toolid = " . $this->_db->Quote($toolid) . " AND g.role=1 ";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getToolGroups'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolid Parameter description (if any) ...
	 * @param      array $groups Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getToolGroups($toolid, $groups = array())
	{
		$query  = "SELECT DISTINCT g.cn FROM #__tool_groups AS g "; // @FIXME cn should be unique, this was a workaround for a nanohub data bug
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		$query .= "WHERE g.toolid = " . $this->_db->Quote($toolid) . " AND g.role=0 ";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getToolStatus'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $toolid Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      array &$status Parameter description (if any) ...
	 * @param      string $version Parameter description (if any) ...
	 * @return     void
	 */
	public function getToolStatus($toolid, $option, &$status, $version='dev')
	{
		$toolinfo = $this->getToolInfo(intval($toolid));
		if ($toolinfo)
		{
			$objV = new ToolVersion($this->_db);
			$objA = new ToolAuthor($this->_db);
			$version = $objV->getVersionInfo(0, $version, $toolinfo[0]->toolname);
			$developers = $this->getToolDevelopers($toolid);
			$authors = $objA->getToolAuthors($version, $toolinfo[0]->rid, $toolinfo[0]->toolname);

			$this->buildToolStatus($toolinfo, $developers, $authors, $version, $status, $option);
		}
		else
		{
			$status = array();
		}
	}

	/**
	 * Short description for 'buildToolStatus'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $toolinfo Parameter description (if any) ...
	 * @param      array $developers Parameter description (if any) ...
	 * @param      array $authors Parameter description (if any) ...
	 * @param      array $version Parameter description (if any) ...
	 * @param      array &$status Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function buildToolStatus($toolinfo, $developers=array(), $authors=array(), $version, &$status, $option)
	{
		// Create a Version object
		$objV = new ToolVersion($this->_db);

		// Get the component parameters
		$this->config = JComponentHelper::getParams($option);
		$invokedir  = $this->config->get('invokescript_dir', DS . 'apps');
		$dev_suffix = $this->config->get('dev_suffix', '_dev');
		$vnc        = $this->config->get('default_vnc', '780x600');
		$mw         = $this->config->get('default_mw', 'narwhal');

		// build status array
		$status = array(
			'resourceid'    => isset($toolinfo[0]->rid) ? $toolinfo[0]->rid : 0,
			'resource_created' => isset($toolinfo[0]->rcreated) ? $toolinfo[0]->rcreated : '',
			'resource_modified' => (isset($toolinfo[0]) && isset($toolinfo[0]->rmodified)
				&& $toolinfo[0]->rmodified !='0000-00-00 00:00:00' && isset($version[0]) && $version[0]->fulltxt != '') ? 1 : 0,
			'fulltxt'      => isset($version[0]->fulltxt) ? $version[0]->fulltxt : $toolinfo[0]->rfulltxt,
			'toolname'      => isset($toolinfo[0]->toolname) ? $toolinfo[0]->toolname : '',
			'toolid'        => isset($toolinfo[0]->id) ? $toolinfo[0]->id : 0,
			'title'         => isset($version[0]->title) ? $version[0]->title : '',
			'version'       => isset($version[0]->version) ? $version[0]->version : '1.0',
			'revision'      => isset($version[0]->revision) ? $version[0]->revision : 0,
			'description'   => isset($version[0]->description) ? $version[0]->description : '',
			'exec'          => isset($version[0]->toolaccess) ? $version[0]->toolaccess : '@OPEN',
			'code'          => isset($version[0]->codeaccess) ? $version[0]->codeaccess : '@OPEN',
			'wiki'          => isset($version[0]->wikiaccess) ? $version[0]->wikiaccess : '@OPEN',
			'published'     => isset($toolinfo[0]->published) ? $toolinfo[0]->published : 0,
			'state'         => isset($toolinfo[0]->state) ? $toolinfo[0]->state : 0,
			'version_state' => isset($version[0]->state) ? $version[0]->state : 3,
			'version_id'    => isset($version[0]->id) ? $version[0]->id : 0,
			'priority'      => isset($toolinfo[0]->priority) ? $toolinfo[0]->priority : 3,
			'doi'           => isset($version[0]->doi) ? $version[0]->doi : 0,
			'authors'       => $authors,
			'developers'    => $developers,
			'devgroup'      => isset($toolinfo[0]->devgroup) ? $toolinfo[0]->devgroup : '',
			'membergroups'  => (isset($version[0]->toolaccess) && $version[0]->toolaccess=='@GROUP') ? $this->getToolGroups($toolinfo[0]->id) : array(),
			'ntools'        => isset($toolinfo[0]->ntools) ? $toolinfo[0]->ntools : 0,
			'ntoolsdev'     => isset($toolinfo[0]->ntoolsdev) ? $toolinfo[0]->ntoolsdev : 0,
			'ntools_published' => isset($toolinfo[0]->ntoolspublished) ? $toolinfo[0]->ntoolspublished : 0,
			'newmessages'   => isset($toolinfo[0]->comments) ? $toolinfo[0]->comments : 0,
			'changed'       => (isset($toolinfo[0]->state_changed) && $toolinfo[0]->state_changed!='0000-00-00 00:00:00') ? $toolinfo[0]->state_changed : $toolinfo[0]->registered,
			'registered_by' => isset($toolinfo[0]->registered_by) ? $toolinfo[0]->registered_by : '',
			'registered'    => isset($toolinfo[0]->registered) ? $toolinfo[0]->registered : '',
			'ticketid'      => isset($toolinfo[0]->ticketid) ? $toolinfo[0]->ticketid : '',
			'mw'            => isset($version[0]->mw) ? $version[0]->mw : $mw,
			'vncCommand'    => isset($version[0]->vnc_command) ? $version[0]->vnc_command :  $invokedir . DS . $toolinfo[0]->toolname . DS . 'invoke',
			'vncGeometry'   => (isset($version[0]->vnc_geometry) && $version[0]->vnc_geometry !='') ? $version[0]->vnc_geometry : $vnc,
			'license'       => isset($version[0]->license) ? $version[0]->license : ''
		);

		list($status['vncGeometryX'], $status['vncGeometryY']) = preg_split('#[x]#', $status['vncGeometry']);

		// get latest version information
		if ($status['published'])
		{
			$current = $objV->getVersionInfo('', 'current', $toolinfo[0]->toolname);
		}

		$status['currenttool']     = isset($current[0]->instance) ? $current[0]->instance : $status['toolname'] . $dev_suffix;
		$status['currentrevision'] = isset($current[0]->revision) ? $current[0]->revision : $status['revision'];
		$status['currentversion']  = isset($current[0]->version)  ? $current[0]->version  : $status['version'];

		return $status;
	}
}
