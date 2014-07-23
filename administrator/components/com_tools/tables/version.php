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
 * Short description for 'ToolVersion'
 *
 * Long description (if any) ...
 */
class ToolVersion extends  JTable
{

	/**
	 * Description for 'id'
	 *
	 * @var unknown
	 */
	var $id      	   = NULL;  // @var int (primary key)

	/**
	 * Description for 'toolid'
	 *
	 * @var string
	 */
	var $toolid        = NULL;  // @var int (11)

	/**
	 * Description for 'toolname'
	 *
	 * @var unknown
	 */
	var $toolname      = NULL;  // @var string (15)

	/**
	 * Description for 'instance'
	 *
	 * @var unknown
	 */
	var $instance      = NULL; // @var string (30)

	/**
	 * Description for 'title'
	 *
	 * @var unknown
	 */
	var $title         = NULL;  // @var string (127)

	/**
	 * Description for 'description'
	 *
	 * @var unknown
	 */
	var $description   = NULL;  // @var text

	/**
	 * Description for 'fulltxt'
	 *
	 * @var unknown
	 */
	var $fulltxt      = NULL;  // @var text

	/**
	 * Description for 'toolaccess'
	 *
	 * @var unknown
	 */
	var $toolaccess    = NULL;  // @var string (15)

	/**
	 * Description for 'codeaccess'
	 *
	 * @var unknown
	 */
	var $codeaccess	   = NULL;  // @var string (15)

	/**
	 * Description for 'wikiaccess'
	 *
	 * @var unknown
	 */
	var $wikiaccess	   = NULL;  // @var string (15)

	/**
	 * Description for 'version'
	 *
	 * @var unknown
	 */
	var $version       = NULL;  // @var string (15)

	/**
	 * Description for 'revision'
	 *
	 * @var unknown
	 */
	var $revision 	   = NULL;  // @var int

	/**
	 * Description for 'state'
	 *
	 * @var unknown
	 */
	var $state         = NULL;  // @var int (11)

	/**
	 * Description for 'vnc_geometry'
	 *
	 * @var unknown
	 */
	var $vnc_geometry  = NULL;  // @var string (15)

	/**
	 * Description for 'vnc_command'
	 *
	 * @var unknown
	 */
	var $vnc_command   = NULL;  // @var string (100)

	/**
	 * Description for 'mw'
	 *
	 * @var unknown
	 */
	var $mw   		   = NULL;  // @var string (15)

	/**
	 * Description for 'released'
	 *
	 * @var unknown
	 */
	var $released      = NULL;  // @var dateandtime

	/**
	 * Description for 'released_by'
	 *
	 * @var unknown
	 */
	var $released_by   = NULL;  // @var string

	/**
	 * Description for 'unpublished'
	 *
	 * @var unknown
	 */
	var $unpublished   = NULL;  // @var dateandtime

	/**
	 * Description for 'license'
	 *
	 * @var unknown
	 */
	var $license	   = NULL;  // @var text

	/**
	 * Description for 'params'
	 *
	 * @var unknown
	 */
	var $params        = NULL;  // @var text

	/**
	 * Description for 'exportControl'
	 *
	 * @var unknown
	 */
	var $exportControl = NULL;  // @var string (15)

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tool_version', 'id', $db);
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
		if (!$this->id && trim($this->toolname) == '')
		{
			$this->setError(JText::_('CONTRIBTOOL_ERROR_VERSION_NO_TOOLNAME'));
			return false;
		}

		if (!$this->id && trim($this->title) == '')
		{
			$this->setError(JText::_('CONTRIBTOOL_ERROR_VERSION_NO_TITLE'));
			return false;
		}

		if (!$this->id && trim($this->revision) == '')
		{
			$this->setError(JText::_('CONTRIBTOOL_ERROR_VERSION_NO_REVISION'));
			return false;
		}

		if (!$this->id && trim($this->version) == '')
		{
			$this->setError(JText::_('CONTRIBTOOL_ERROR_VERSION_NO_VERSION'));
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'loadFromInstance'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $tool Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadFromInstance($tool=NULL)
	{
		if ($tool === NULL)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl AS v WHERE v.instance=" . $this->_db->Quote($tool) . " LIMIT 1";

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
	 * Short description for 'getAll'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $includedev Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getAll($includedev = 1)
	{
		$sql = "SELECT * FROM #__tool_version";
		if (!$includedev)
		{
			$sql.= " WHERE state!='3'";
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getVersions'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $alias Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getVersions($alias)
	{
		// will load versions excluding dev
		if ($alias === NULL)
		{
			$alias = $this->toolname;
		}
		if (!$alias)
		{
			return false;
		}

		$rd = new ResourcesDoi($this->_db);

		$query  = "SELECT v.*, d.* ";
		$query .= "FROM $this->_tbl as v ";
		$query .= "LEFT JOIN $rd->_tbl as d ON d.alias=v.toolname  AND d.local_revision=v.revision ";
		$query .= "WHERE v.toolname = " . $this->_db->Quote($alias) . " AND v.state!=3 ORDER BY v.revision DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getVersionIdFromResource'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $version Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getVersionIdFromResource($rid=NULL, $version ='dev')
	{
		if ($rid=== NULL)
		{
			return false;
		}

		$query = "SELECT v.id FROM #__tool_version as v JOIN #__resources as r ON r.alias = v.toolname WHERE r.id=" . $this->_db->Quote($rid);
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
	 * Short description for 'loadFromName'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $alias Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadFromName($alias)
	{
		if ($alias === NULL)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl as v WHERE v.toolname=" . $this->_db->Quote($alias) . " AND state='1' ORDER BY v.revision DESC LIMIT 1";

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
	 * Short description for 'load_version'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolid Parameter description (if any) ...
	 * @param      string $version Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function load_version ($toolid=NULL, $version='dev')
	{
		if ($toolid === NULL)
		{
			return false;
		}
		$query = "SELECT * FROM $this->_tbl WHERE toolid=" . $this->_db->Quote($toolid) . " AND ";
		if (!$version or $version=='dev')
		{
			$query .= "state='3'";
		}
		else if ($version=='current')
		{
			$query .= "state='1'";
		}
		else
		{
			$query .= "version=" . $this->_db->Quote($version);
		}
		$query .=" ORDER BY revision DESC LIMIT 1";
		$this->_db->setQuery($query);
		return $this->_db->loadObject($this);
	}

	/**
	 * Short description for 'setUnpublishDate'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $toolid Parameter description (if any) ...
	 * @param      string $toolname Parameter description (if any) ...
	 * @param      mixed $vid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function setUnpublishDate($toolid=NULL, $toolname='', $vid=0)
	{
		if (!$toolid)
		{
			return false;
		}
		if ($toolname or $vid)
		{
			$query = "UPDATE #__tool_version SET unpublished='".JFactory::getDate()->toSql()."' WHERE ";
			if ($toolname)
			{
				$query .= "toolname=" . $this->_db->Quote($toolname) . " ";
			}
			else if ($vid)
			{
				$query.= "id=" . $this->_db->Quote($vid) . " ";
			}
			$query .= "AND state='1'";
			$this->_db->setQuery($query);
			if ($this->_db->query())
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Short description for 'unpublish'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolid Parameter description (if any) ...
	 * @param      mixed $vid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function unpublish($toolid=NULL, $vid=0)
	{
		$xlog =  JFactory::getLogger();

		if (!$toolid)
		{
			return false;
		}

		$query = "UPDATE #__tool_version SET state='0', unpublished='".JFactory::getDate()->toSql()."' WHERE ";
		if (intval($vid))
		{
			$query.= "id=" . $this->_db->Quote($vid) . " AND ";
		}
		$query.= "toolid=" . $this->_db->Quote($toolid) . " AND state='1'";

		$this->_db->setQuery($query);

		$xlog->debug(__FUNCTION__ . "()  $query");

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
	 * Short description for 'save'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $toolid Parameter description (if any) ...
	 * @param      string $version Parameter description (if any) ...
	 * @param      integer $create_new Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function save($toolid=NULL, $version='dev', $create_new = 0)
	{
		die('1');
		$xlog =  JFactory::getLogger();

		if (!$this->toolid)
		{
			$this->toolid= $toolid;
		}
		if (!$this->toolid)
		{
			return false;
		}

		$query = "SELECT id FROM #__tool_version WHERE toolid=" . $this->_db->Quote($this->toolid);
		if (!$version or $version=='dev')
		{
			$query.= " AND state='3'";
		}
		else if ($version=='current')
		{
			$query.= " AND state='1'";
		}
		else
		{
			$query.= " AND version=" . $this->_db->Quote($version);
		}
		$query.=" ORDER BY revision DESC LIMIT 1";

		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();

		$this->id = $result ? $result : 0;

		$xlog->debug(__FUNCTION__ . " $toolid $version $create_new ");

		if ((!$result && $create_new) or $this->id)
		{
			if (!$this->store())
			{
				$this->setError(JText::_('CONTRIBTOOL_ERROR_VERSION_UPDATE_FAILED'));
				return false;
			}
		}

		return true;
	}

	/**
	 * Short description for 'store'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function store($updateNulls = false)
	{
		if (empty($this->id))
		{
			$xlog =  JFactory::getLogger();
			$query = "SELECT id FROM #__tool_version WHERE toolname=" . $this->_db->Quote($this->toolname) .
					" AND instance=" . $this->_db->Quote($this->instance) . ";";
			$this->_db->setQuery($query);
			$result = $this->_db->loadResult();

			if ($result)
			{
				$xlog->debug(__FUNCTION__ . " someone created this before me. Fixed");
			}

			$this->id = $result ? $result : 0;
		}

		return parent::store();
	}

	/**
	 * Short description for 'getToolVersions'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolid Parameter description (if any) ...
	 * @param      array &$versions Parameter description (if any) ...
	 * @param      string $toolname Parameter description (if any) ...
	 * @param      integer $exclude_dev Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getToolVersions($toolid, &$versions, $toolname='', $exclude_dev = 0)
	{
		$xlog = JFactory::getLogger();

		$objA = new ToolAuthor($this->_db);

		$query  = "SELECT v.*, d.* ";
		$query .= "FROM #__tool_version as v LEFT JOIN #__doi_mapping as d ON d.alias = v.toolname AND d.local_revision=v.revision ";
		if ($toolid)
		{
			$query .= "WHERE v.toolid = " . $this->_db->Quote($toolid) . " ";
		}
		else if ($toolname)
		{
			$query .= "WHERE v.toolname = " . $this->_db->Quote($toolname) . " ";
		}
		if (($toolname or $toolid) && $exclude_dev)
		{
			$query .= "AND v.state != '3'";
		}
		$query .= " ORDER BY v.state DESC, v.revision DESC";

		$this->_db->setQuery($query);
		$versions = $this->_db->loadObjectList();

		if ($versions)
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'tool.php');

			foreach ($versions as $version)
			{
				// get list of authors
				if ($version->state!=3)
				{
					$version->authors = $objA->getToolAuthors($version->id);
				}
				else
				{
					$rid = ToolsModelTool::getResourceId($version->toolid);
					$version->authors = $objA->getToolAuthors('dev', $rid);
				}
			}
		}

		return $versions;
	}

	/**
	 * Short description for 'getVersionInfo'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $id Parameter description (if any) ...
	 * @param      string $version Parameter description (if any) ...
	 * @param      string $toolname Parameter description (if any) ...
	 * @param      string $instance Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getVersionInfo($id, $version='', $toolname='', $instance='')
	{
		$xlog = JFactory::getLogger();

		// data comes from mysql
		$juser  = JFactory::getUser();
		$query  = "SELECT v.*, d.* ";
		$query .= "FROM #__tool_version as v LEFT JOIN #__doi_mapping as d ON d.alias = v.toolname AND d.local_revision=v.revision ";
		if ($id)
		{
			$query .= "WHERE v.id = " . $this->_db->Quote($id) . " ";
		}
		else if ($version && $toolname)
		{
			$query.= "WHERE v.toolname=" . $this->_db->Quote($toolname) . " ";
			if ($version=='current')
			{
				$query .= "AND v.state=1 ORDER BY v.revision DESC LIMIT 1 ";
			}
			else if ($version=='dev')
			{
				$query .= "AND v.state=3 LIMIT 1";
			}
			else
			{
				$query .= "AND v.version = " . $this->_db->Quote($version) . " ";
			}
		}
		else if ($instance)
		{
			$query .= "WHERE v.instance=" . $this->_db->Quote($instance) . " ";
		}
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'compileResource'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $thistool Parameter description (if any) ...
	 * @param      mixed $curtool Parameter description (if any) ...
	 * @param      mixed $resource Parameter description (if any) ...
	 * @param      string $revision Parameter description (if any) ...
	 * @param      object $config Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function compileResource($thistool, $curtool='', $resource, $revision, $config)
	{
		if ($curtool)
		{
		//print_r($thistool);
			$resource->curversion    = $curtool->version;
			$resource->currevision   = $curtool->revision;
			$resource->cursource   	 = ($curtool->codeaccess=='@OPEN') ? 1: 0;
			if (!$thistool)
			{
				$resource->revision      = $curtool->revision;
				$revision 			 	 = $resource->revision;
				$resource->version       = $curtool->version;
				$resource->versionid     = $curtool->id;
				$resource->tool      	 = $curtool->instance;
				$resource->toolpublished = 1;
				$resource->license 		 = $curtool->license;
				$resource->title         = stripslashes($curtool->title);
				$resource->introtext     = stripslashes($curtool->description);
				$resource->fulltxt      = $curtool->fulltxt;
				$resource->toolsource    = ($curtool->codeaccess=='@OPEN') ? 1: 0;
				$resource->doi 			 = isset($curtool->doi) ? $curtool->doi : '';
				$resource->doi_label 	 = $curtool->doi_label;
			}
		}

		if ($thistool)
		{
			$resource->revision      = ($thistool) ? $thistool->revision : 1;
			$resource->revision      = ($revision !='dev') ? $resource->revision : 'dev';
			$revision 			 	 = $resource->revision;
			$resource->versionid     = ($revision && $thistool) ? $thistool->id  : 0;
			$resource->version       = ($revision && $thistool) ? $thistool->version  : 1;
			$resource->tool      	 = ($revision && $thistool) ? $thistool->instance : $resource->alias.'_r'.$revision;
			$resource->toolpublished = ($revision && $thistool) ? $thistool->state    : 1;
			$resource->license 		 = ($revision && $thistool) ? $thistool->license  : '';
			$resource->title         = ($revision && $thistool) ? stripslashes($thistool->title) : $resource->title;
			$resource->introtext     = ($revision && $thistool && isset($thistool->description)) ? stripslashes($thistool->description) : $resource->introtext;
			$resource->fulltxt      = ($revision && $thistool && isset($thistool->fulltxt)) ? $thistool->fulltxt : $resource->fulltxt;
			$resource->toolsource    = ($thistool && isset($thistool->codeaccess) && $thistool->codeaccess=='@OPEN') ? 1: 0;
			$resource->doi 			 = ($thistool && isset($thistool->doi)) ? $thistool->doi : '';
			$resource->doi_label 	 = ($thistool && isset($thistool->doi_label)) ? $thistool->doi_label : 0;
		}
		else if (!$curtool)
		{
			$resource->revision      = 1;
			$revision 			 	 = $resource->revision;
			$resource->version       = 1;
			$resource->versionid     = 0;
			$resource->tool      	 = $resource->alias.'_r'.$revision;
			$resource->toolpublished = 1;
			$resource->license 		 = '';
			$resource->title         = $resource->title;
			$resource->introtext     = $resource->introtext;
			$resource->fulltxt      = $resource->fulltxt;
			$resource->toolsource    = 0;
			$resource->doi 			 = '';
			$resource->doi_label 	 = 0;
		}
		$resource->revision      = ($revision !='dev') ? $resource->revision : 'dev';

		// Get some needed libraries
		//include_once(JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'html.php');
		$resource->tarname = $resource->alias.'-r'.$resource->revision.'.tar.gz';
		$tarball_path = $config->get('sourcecodePath','site/protected/source');
		if ($tarball_path[0] != DS)
		{
			$tarball_path = rtrim(JPATH_ROOT . DS . $tarball_path, DS);
		}
		$resource->tarpath = $tarball_path.DS.$resource->alias.DS;
		// Is tarball available?
		$resource->taravailable = (file_exists($resource->tarpath . $resource->tarname)) ? 1 : 0;

		return true;
	}

	/**
	 * Short description for 'validLicense'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $toolname Parameter description (if any) ...
	 * @param      array $license Parameter description (if any) ...
	 * @param      string $code Parameter description (if any) ...
	 * @param      unknown &$error Parameter description (if any) ...
	 * @param      integer $result Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function validLicense($toolname, $license, $code, &$error, $result=0)
	{
		preg_replace('/\[([^]]+)\]/', ' ', $license['text'], -1, $bingo);

		if (!$license['text'])
		{
			$error = JText::_('ERR_LICENSE_EMPTY');
		}
		else if ($bingo)
		{
			$error = JText::_('ERR_LICENSE_DEFAULTS');
		}
		else if (!$license['authorize'] && $code=='@OPEN')
		{
			$error = JText::_('ERR_LICENSE_AUTH_MISSING');
		}
		else
		{
			$result = 1;
		}

		return $result;
	}

	/**
	 * Short description for 'validToolReg'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array &$tool Parameter description (if any) ...
	 * @param      array &$err Parameter description (if any) ...
	 * @param      string $id Parameter description (if any) ...
	 * @param      object $config Parameter description (if any) ...
	 * @param      integer $checker Parameter description (if any) ...
	 * @param      integer $result Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function validToolReg(&$tool, &$err, $id, $config, $checker=0, $result=1)
	{
		$xlog = JFactory::getLogger();

		$tgObj = new ToolGroup($this->_db);

		//  check if toolname exists in tool table
		$query  = "SELECT t.id ";
		$query .= "FROM #__tool as t ";
		$query .= "WHERE t.toolname LIKE " . $this->_db->quote($tool['toolname']) . " ";
		if ($id)
		{
			$query .= "AND t.id!=" . $this->_db->Quote($id) . " ";
		}

		$this->_db->setQuery($query);
		$checker = $this->_db->loadResult();

		if ($checker or (in_array($tool['toolname'], array('test','shortname','hub','tool')) && !$id))
		{
			$err['toolname'] = JText::_('ERR_TOOLNAME_EXISTS');
		}
		else if (preg_match('#^[a-zA-Z0-9]{3,15}$#', $tool['toolname']) == '' && !$id)
		{
			$err['toolname'] = JText::_('ERR_TOOLNAME');
		}

		// check if title can be used - tool table
		$query  = "SELECT title, toolname ";
		$query .= "FROM #__tool ";
		if ($id)
		{
			$query .= "WHERE id!=" . $this->_db->Quote($id) . " ";
		}

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		if ($rows)
		{
			for ($i=0, $n=count($rows); $i < $n; $i++)
			{
				if (strtolower($rows[$i]->title) == strtolower($tool['title'])
				 && $rows[$i]->toolname != $tool['toolname'])
				{
					$checker = 1;
				}
			}
		}

		$tool['toolname'] = strtolower($tool['toolname']);	// make toolname lower case by default

		if ($checker)
		{  // check if title exists for other tools
			$err['title'] = JText::_('ERR_TITLE_EXISTS');
		}
		else if ($tool['title']=='')
		{
			$err['title'] = JText::_('ERR_TITLE');
		}

		if ($tool['description']=='')
		{
			$err['description'] = JText::_('ERR_DESC');
		}

		if ($tool['version'])
		{
			$this->validVersion($tool['toolname'], $tool['version'], $error_v, 0);
			if ($error_v)
			{
				$err['version'] = $error_v;
			}
		}

		if ($tool['exec']=='')
		{
			$err['exec'] = JText::_('ERR_EXEC');
		}

		if ($tool['exec']=='@GROUP' && $tool['membergroups']=='')
		{
			$err['membergroups'] = JText::_('ERR_GROUPS_EMPTY');
			$tool['membergroups'] = array();
		}
		else if ($tool['membergroups']=='' or $tool['exec']!='@GROUP')
		{
			$tool['membergroups'] = array();
		}
		else if ($tool['exec']=='@GROUP')
		{
			$tool['membergroups'] = $tgObj->writeMemberGroups($tool['membergroups'], $id, $this->_db, $error_g);
			if ($error_g)
			{
				$err['membergroups'] = $error_g;
			}
		}

		if ($tool['code']=='')
		{
			$err['code'] = JText::_('ERR_CODE');
		}

		if ($tool['wiki']=='')
		{
			$err['wiki'] = JText::_('ERR_WIKI');
		}

		if ($tool['developers']=='')
		{
			$tool['developers'] = array();
			$err['developers'] =  JText::_('ERR_TEAM_EMPTY');
		}
		else
		{
			$tool['developers'] = $tgObj->writeTeam($tool['developers'], $id, $this->_db, $error_t);
			if ($error_t)
			{
				$err['developers'] = $error_t;
			}
		}

		// format some data
		$vnc     = isset($config->parameters['default_vnc']) ? $config->parameters['default_vnc'] : '780x600';
		if ($tool['vncGeometryX']
		 && $tool['vncGeometryY']
		 && !preg_match('#[^0-9]#', $tool['vncGeometryX'])
		 && !preg_match('#[^0-9]#', $tool['vncGeometryY']))
		{
			$tool['vncGeometry'] = $tool['vncGeometryX'] . 'x' . $tool['vncGeometryY'];
		}
		else
		{
			$tool['vncGeometry'] = $vnc;
		}

		// return result and errors
		if (count($err) > 0)
		{
			$result = 0;
		}

		return $result;
	}

	/**
	 * Short description for 'validVersion'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $toolname Parameter description (if any) ...
	 * @param      unknown $newversion Parameter description (if any) ...
	 * @param      unknown &$error Parameter description (if any) ...
	 * @param      integer $required Parameter description (if any) ...
	 * @param      integer $result Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function validVersion($toolname, $newversion, &$error, $required=1, $result=1)
	{
		$toolhelper = new ToolsHelperUtils();

		if ($required && !$newversion)
		{ // was left blank
			$result = 0;
			$error = JText::_('ERR_VERSION_BLANK');
		}
		else if ($toolhelper->check_validInput($newversion))
		{ // illegal characters
			$result = 0;
			$error = JText::_('ERR_VERSION_ILLEGAL');
		}
		else if ($required)
		{
			$this->getToolVersions('', $versions, $toolname, 1);

			if ($versions)
			{
				foreach ($versions as $t)
				{
					if (strtolower($t->version) == strtolower($newversion))
					{
						$result = 0;
						$error = JText::_('ERR_VERSION_EXISTS');
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Short description for 'getToolname'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $instance Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getToolname($instance)
	{
		$database = JFactory::getDBO();
		$query  = "SELECT toolname FROM #__tool_version WHERE instance=" . $this->_db->Quote($instance) . " LIMIT 1";
		$this->_db->setQuery($query);
		$toolname = $this->_db->loadResult();
		if (!$toolname)
		{
			$toolname = $instance;
		}
		return $toolname;
	}

	/**
	 * Short description for 'getCurrentVersionProperty'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolname Parameter description (if any) ...
	 * @param      string $property Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCurrentVersionProperty($toolname, $property)
	{
		$database = JFactory::getDBO();
		$query  = "SELECT " . $property . " FROM #__tool_version  WHERE toolname=" . $this->_db->Quote($toolname) . " AND state=1 ORDER BY revision DESC LIMIT 1";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getDevVersionProperty'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolname Parameter description (if any) ...
	 * @param      string $property Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getDevVersionProperty($toolname, $property)
	{
		$database = JFactory::getDBO();
		$query  = "SELECT " . $property . " FROM #__tool_version WHERE toolname=" . $this->_db->Quote($toolname) . " AND state=3 ORDER BY revision DESC LIMIT 1";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}
