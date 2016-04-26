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

namespace Components\Tools\Tables;

use Lang;
use Date;
use Log;

/**
 * Table class for a tool version
 */
class Version extends \JTable
{
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
			$this->setError(Lang::txt('CONTRIBTOOL_ERROR_VERSION_NO_TOOLNAME'));
			return false;
		}

		if (!$this->id && trim($this->title) == '')
		{
			$this->setError(Lang::txt('CONTRIBTOOL_ERROR_VERSION_NO_TITLE'));
			return false;
		}

		if (!$this->id && trim($this->revision) == '')
		{
			$this->setError(Lang::txt('CONTRIBTOOL_ERROR_VERSION_NO_REVISION'));
			return false;
		}

		if (!$this->id && trim($this->version) == '')
		{
			$this->setError(Lang::txt('CONTRIBTOOL_ERROR_VERSION_NO_VERSION'));
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

		$query  = "SELECT * FROM $this->_tbl AS v WHERE v.instance=" . $this->_db->quote($tool) . " LIMIT 1";

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

		$rd = new \Components\Resources\Tables\Doi($this->_db);

		$query  = "SELECT v.*, d.* ";
		$query .= "FROM $this->_tbl as v ";
		$query .= "LEFT JOIN $rd->_tbl as d ON d.alias=v.toolname  AND d.local_revision=v.revision ";
		$query .= "WHERE v.toolname = " . $this->_db->quote($alias) . " AND v.state!=3 ORDER BY v.revision DESC";

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

		$query = "SELECT v.id FROM #__tool_version as v JOIN #__resources as r ON r.alias = v.toolname WHERE r.id=" . $this->_db->quote($rid);
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
			$query.= " AND v.version=" . $this->_db->quote($version) . " LIMIT 1";
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

		$query  = "SELECT * FROM $this->_tbl as v WHERE v.toolname=" . $this->_db->quote($alias) . " AND state='1' ORDER BY v.revision DESC LIMIT 1";

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
	public function load_version($toolid=NULL, $version='dev')
	{
		if ($toolid === NULL)
		{
			return false;
		}
		$query = "SELECT * FROM $this->_tbl WHERE toolid=" . $this->_db->quote($toolid) . " AND ";
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
			$query .= "version=" . $this->_db->quote($version);
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
			$query = "UPDATE #__tool_version SET unpublished='".Date::toSql()."' WHERE ";
			if ($toolname)
			{
				$query .= "toolname=" . $this->_db->quote($toolname) . " ";
			}
			else if ($vid)
			{
				$query.= "id=" . $this->_db->quote($vid) . " ";
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
		if (!$toolid)
		{
			return false;
		}

		$query = "UPDATE #__tool_version SET state='0', unpublished='".Date::toSql()."' WHERE ";
		if (intval($vid))
		{
			$query.= "id=" . $this->_db->quote($vid) . " AND ";
		}
		$query.= "toolid=" . $this->_db->quote($toolid) . " AND state='1'";

		$this->_db->setQuery($query);

		Log::debug(__FUNCTION__ . "()  $query");

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
		if (!$this->toolid)
		{
			$this->toolid= $toolid;
		}
		if (!$this->toolid)
		{
			return false;
		}

		$query = "SELECT id FROM #__tool_version WHERE toolid=" . $this->_db->quote($this->toolid);
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
			$query.= " AND version=" . $this->_db->quote($version);
		}
		$query.=" ORDER BY revision DESC LIMIT 1";

		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();

		$this->id = $result ? $result : 0;

		Log::debug(__FUNCTION__ . " $toolid $version $create_new ");

		if ((!$result && $create_new) or $this->id)
		{
			if (!$this->store())
			{
				$this->setError(Lang::txt('CONTRIBTOOL_ERROR_VERSION_UPDATE_FAILED'));
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
			$query = "SELECT id FROM #__tool_version WHERE toolname=" . $this->_db->quote($this->toolname) .
					" AND instance=" . $this->_db->quote($this->instance) . ";";
			$this->_db->setQuery($query);
			$result = $this->_db->loadResult();

			if ($result)
			{
				Log::debug(__FUNCTION__ . " someone created this before me. Fixed");
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
		$objA = new \Components\Tools\Tables\Author($this->_db);

		$query  = "SELECT v.*, d.* ";
		$query .= "FROM #__tool_version as v LEFT JOIN #__doi_mapping as d ON d.alias = v.toolname AND d.local_revision=v.revision ";
		if ($toolid)
		{
			$query .= "WHERE v.toolid = " . $this->_db->quote($toolid) . " ";
		}
		else if ($toolname)
		{
			$query .= "WHERE v.toolname = " . $this->_db->quote($toolname) . " ";
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
			require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'tool.php');

			foreach ($versions as $version)
			{
				// get list of authors
				if ($version->state!=3)
				{
					$version->authors = $objA->getToolAuthors($version->id);
				}
				else
				{
					$rid = \Components\Tools\Models\Tool::getResourceId($version->toolid);
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
		// data comes from mysql
		$query  = "SELECT v.*, d.* ";
		$query .= "FROM #__tool_version as v LEFT JOIN #__doi_mapping as d ON d.alias = v.toolname AND d.local_revision=v.revision ";
		if ($id)
		{
			$query .= "WHERE v.id = " . $this->_db->quote($id) . " ";
		}
		else if ($version && $toolname)
		{
			$query.= "WHERE v.toolname=" . $this->_db->quote($toolname) . " ";
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
				$query .= "AND v.version = " . $this->_db->quote($version) . " ";
			}
		}
		else if ($instance)
		{
			$query .= "WHERE v.instance=" . $this->_db->quote($instance) . " ";
		}
		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();
		if ($data)
		{
			foreach ($data as $i => $datum)
			{
				$this->_db->setQuery("SELECT `hostreq` FROM `#__tool_version_hostreq` WHERE `tool_version_id`=" . $this->_db->quote($datum->id));
				$data[$i]->hostreq = $this->_db->loadColumn();
			}
		}
		return $data;
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
			$resource->curversion    = $curtool->version;
			$resource->currevision   = $curtool->revision;
			$resource->cursource     = ($curtool->codeaccess=='@OPEN') ? 1: 0;
			if (!$thistool)
			{
				$resource->revision      = $curtool->revision;
				$revision                = $resource->revision;
				$resource->version       = $curtool->version;
				$resource->versionid     = $curtool->id;
				$resource->tool          = $curtool->instance;
				$resource->toolpublished = 1;
				$resource->license       = $curtool->license;
				$resource->title         = stripslashes($curtool->title);
				$resource->introtext     = stripslashes($curtool->description);
				$resource->fulltxt       = $curtool->fulltxt;
				$resource->toolsource    = ($curtool->codeaccess=='@OPEN') ? 1: 0;
				$resource->doi           = isset($curtool->doi) ? $curtool->doi : '';
				$resource->doi_label     = $curtool->doi_label;
			}
		}

		if ($thistool)
		{
			$resource->revision      = ($thistool) ? $thistool->revision : 1;
			$resource->revision      = ($revision !='dev') ? $resource->revision : 'dev';
			$revision                = $resource->revision;
			$resource->versionid     = ($revision && $thistool) ? $thistool->id  : 0;
			$resource->version       = ($revision && $thistool) ? $thistool->version  : 1;
			$resource->tool          = ($revision && $thistool) ? $thistool->instance : $resource->alias.'_r'.$revision;
			$resource->toolpublished = ($revision && $thistool) ? $thistool->state    : 1;
			$resource->license       = ($revision && $thistool) ? $thistool->license  : '';
			$resource->title         = ($revision && $thistool) ? stripslashes($thistool->title) : $resource->title;
			$resource->introtext     = ($revision && $thistool && isset($thistool->description)) ? stripslashes($thistool->description) : $resource->introtext;
			$resource->fulltxt       = ($revision && $thistool && isset($thistool->fulltxt)) ? $thistool->fulltxt : $resource->fulltxt;
			$resource->toolsource    = ($thistool && isset($thistool->codeaccess) && $thistool->codeaccess=='@OPEN') ? 1: 0;
			$resource->doi           = ($thistool && isset($thistool->doi)) ? $thistool->doi : '';
			$resource->doi_label     = ($thistool && isset($thistool->doi_label)) ? $thistool->doi_label : 0;
		}
		else if (!$curtool)
		{
			$resource->revision      = 1;
			$revision                 = $resource->revision;
			$resource->version       = 1;
			$resource->versionid     = 0;
			$resource->tool          = $resource->alias.'_r'.$revision;
			$resource->toolpublished = 1;
			$resource->license       = '';
			$resource->title         = $resource->title;
			$resource->introtext     = $resource->introtext;
			$resource->fulltxt       = $resource->fulltxt;
			$resource->toolsource    = 0;
			$resource->doi           = '';
			$resource->doi_label     = 0;
		}
		$resource->revision      = ($revision !='dev') ? $resource->revision : 'dev';

		// Get some needed libraries
		//include_once(PATH_CORE.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'html.php');
		$resource->tarname = $resource->alias.'-r'.$resource->revision.'.tar.gz';
		$tarball_path = $config->get('sourcecodePath','site/protected/source');
		if ($tarball_path[0] != DS)
		{
			$tarball_path = rtrim(PATH_APP . DS . $tarball_path, DS);
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
			$error = Lang::txt('ERR_LICENSE_EMPTY');
		}
		else if ($bingo)
		{
			$error = Lang::txt('ERR_LICENSE_DEFAULTS');
		}
		else if (!$license['authorize'] && $code=='@OPEN')
		{
			$error = Lang::txt('ERR_LICENSE_AUTH_MISSING');
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
		$tgObj = new \Components\Tools\Tables\Group($this->_db);

		//  check if toolname exists in tool table
		$query  = "SELECT t.id ";
		$query .= "FROM #__tool as t ";
		$query .= "WHERE t.toolname LIKE " . $this->_db->quote($tool['toolname']) . " ";
		if ($id)
		{
			$query .= "AND t.id!=" . $this->_db->quote($id) . " ";
		}

		$this->_db->setQuery($query);
		$checker = $this->_db->loadResult();

		if ($checker or (in_array($tool['toolname'], array('test','shortname','hub','tool')) && !$id))
		{
			$err['toolname'] = Lang::txt('ERR_TOOLNAME_EXISTS');
		}
		else if (preg_match('#^[a-zA-Z0-9]{3,15}$#', $tool['toolname']) == '' && !$id)
		{
			$err['toolname'] = Lang::txt('ERR_TOOLNAME');
		}

		// check if title can be used - tool table
		$query  = "SELECT title, toolname ";
		$query .= "FROM #__tool ";
		if ($id)
		{
			$query .= "WHERE id!=" . $this->_db->quote($id) . " ";
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
			$err['title'] = Lang::txt('ERR_TITLE_EXISTS');
		}
		else if ($tool['title']=='')
		{
			$err['title'] = Lang::txt('ERR_TITLE');
		}

		if ($tool['description']=='')
		{
			$err['description'] = Lang::txt('ERR_DESC');
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
			$err['exec'] = Lang::txt('ERR_EXEC');
		}

		if ($tool['exec']=='@GROUP' && $tool['membergroups']=='')
		{
			$err['membergroups'] = Lang::txt('ERR_GROUPS_EMPTY');
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
			$err['code'] = Lang::txt('ERR_CODE');
		}

		if ($tool['wiki']=='')
		{
			$err['wiki'] = Lang::txt('ERR_WIKI');
		}

		if ($tool['developers']=='')
		{
			$tool['developers'] = array();
			$err['developers'] =  Lang::txt('ERR_TEAM_EMPTY');
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
		$toolhelper = new \Components\Tools\Helpers\Utils();

		if ($required && !$newversion)
		{ // was left blank
			$result = 0;
			$error = Lang::txt('ERR_VERSION_BLANK');
		}
		else if ($toolhelper->check_validInput($newversion))
		{ // illegal characters
			$result = 0;
			$error = Lang::txt('ERR_VERSION_ILLEGAL');
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
						$error = Lang::txt('ERR_VERSION_EXISTS');
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
		$database = \App::get('db');
		$query  = "SELECT toolname FROM #__tool_version WHERE instance=" . $this->_db->quote($instance) . " LIMIT 1";
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
		$database = \App::get('db');
		$query  = "SELECT " . $property . " FROM #__tool_version  WHERE toolname=" . $this->_db->quote($toolname) . " AND state=1 ORDER BY revision DESC LIMIT 1";
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
		$database = \App::get('db');
		$query  = "SELECT " . $property . " FROM #__tool_version WHERE toolname=" . $this->_db->quote($toolname) . " AND state=3 ORDER BY revision DESC LIMIT 1";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}
