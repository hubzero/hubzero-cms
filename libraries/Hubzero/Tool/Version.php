<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.helper');

/**
 * Short description for 'Hubzero_Tool_VersionHelper'
 * 
 * Long description (if any) ...
 */
class Hubzero_Tool_VersionHelper
{

	/**
	 * Short description for 'iterate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $func Parameter description (if any) ...
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
    public function iterate($func, $storage = 'mysql')
    {
        $db = &JFactory::getDBO();

        if (!empty($storage) && !in_array($storage, array('mysql', 'ldap')))
        {
            return false;
        }

        if ($storage == 'ldap')
        {
            $xhub = &Hubzero_Factory::getHub();
            $conn = &Hubzero_Factory::getPLDC();

            $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

            $dn = 'ou=tools,' . $hubLDAPBaseDN;
            $filter = '(objectclass=hubTool)';

            $attributes[] = 'tool';

            $sr = ldap_search($conn, $dn, $filter, $attributes, 0, 0, 0);

            if ($sr === false)
            {
                return false;
            }

            $count = ldap_count_entries($conn, $sr);

            if ($count === false)
            {
                return false;
            }

            $entry = ldap_first_entry($conn, $sr);

            do
            {
                $attributes = ldap_get_attributes($conn, $entry);
                call_user_func($func, $attributes['tool'][0]);
                $entry = ldap_next_entry($conn, $entry);
            }
            while ($entry !== false);
        }
        else if ($storage == 'mysql')
        {
            $query = "SELECT instance FROM #__tool_version;";

            $db->setQuery($query);

            $result = $db->query();

            if ($result === false)
            {
                return false;
            }

            while ($row = mysql_fetch_row($result))
            {
                call_user_func($func, $row[0]);
            }

            mysql_free_result($result);
        }

        return true;
    }

	/**
	 * Short description for 'getCurrentToolVersion'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $toolid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
    public function getCurrentToolVersion($toolid)
    {
        $db = & JFactory::getDBO();

        if (is_numeric($toolid))
        {
            $query = "SELECT instance FROM #__tool_version AS v WHERE v.toolid=" .
                $db->Quote($toolid) . " AND v.state=1 ORDER BY v.revision DESC LIMIT 1";
        }
        else
        {
            $query = "SELECT instance FROM #__tool_version AS v, #__tool AS t WHERE t.toolname=" .
                $db->Quote($toolid) . " AND v.toolid=t.id AND v.state=1 ORDER BY v.revision " .
                " DESC LIMIT 1";
        }

        $db->setQuery($query);
        $result = $db->loadResult();

        if (empty($result))
        {
            return false;
        }

        return Hubzero_Tool_Version::getInstance($result);
    }

	/**
	 * Short description for 'getDevelopmentToolVersion'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $toolid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
    public function getDevelopmentToolVersion($toolid)
    {
        $db = & JFactory::getDBO();

        if (is_numeric($toolid))
        {
            $query = "SELECT instance FROM #__tool_version AS v WHERE v.toolid=" .
                $db->Quote($toolid) . " AND v.state=3 ORDER BY v.revision DESC LIMIT 1";
        }
        else
        {
            $query = "SELECT instance FROM #__tool_version AS v, #__tool AS t WHERE t.toolname=" .
                $db->Quote($toolid) . " AND v.toolid=t.id AND v.state=3 ORDER BY v.revision " .
                " DESC LIMIT 1";
        }

        $db->setQuery($query);
        $result = $db->loadResult();

        if (empty($result))
        {
            return false;
        }

        return Hubzero_Tool_Version::getInstance($result);
    }

	/**
	 * Short description for 'getToolRevision'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $toolid Parameter description (if any) ...
	 * @param      string $revision Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function getToolRevision($toolid, $revision)
	{
		$db = &JFactory::getDBO();

		if ($revision == 'dev' || $revision == 'development')
		{
        	if (is_numeric($toolid))
        	{
            	$query = "SELECT instance FROM #__tool_version AS v WHERE v.toolid=" .
                	$db->Quote($toolid) . " AND v.state=3 ORDER BY v.revision DESC LIMIT 1";
        	}
        	else
        	{
            	$query = "SELECT instance FROM #__tool_version AS v, #__tool AS t WHERE t.toolname=" .
                	$db->Quote($toolid) . " AND v.toolid=t.id AND v.state=3 ORDER BY v.revision " .
                	" DESC LIMIT 1";
        	}
		}
		else if ($revision == 'current')
		{
        	if (is_numeric($toolid))
        	{
            	$query = "SELECT instance FROM #__tool_version AS v WHERE v.toolid=" .
                	$db->Quote($toolid) . " AND v.state=1 ORDER BY v.revision DESC LIMIT 1";
        	}
        	else
        	{
            	$query = "SELECT instance FROM #__tool_version AS v, #__tool AS t WHERE t.toolname=" .
                	$db->Quote($toolid) . " AND v.toolid=t.id AND v.state=1 ORDER BY v.revision " .
                	" DESC LIMIT 1";
        	}
		}
		else
		{
        	if (is_numeric($toolid))
        	{
				$query = "SELECT instance FROM #__tool_version AS v WHERE v.toolid=" .
			    	$db->Quote($toolid) . " AND v.state<>'3' AND v.revision=" . $db->Quote($revision) . "  LIMIT 1";
			}
			else
			{
				$query = "SELECT instance FROM #__tool_version AS v, #__tool AS t WHERE t.toolname=" .
				   	$db->Quote($toolid) . " AND v.toolid=" .  $db->Quote($toolid) . " AND v.state<>'3' AND " .
					" v.revision=" . $db->Quote($revision) . "  LIMIT 1";
			}
		}

        $db->setQuery($query);

        $result = $db->loadResult();

        if (empty($result))
        {
            return false;
        }

        return Hubzero_Tool_Version::getInstance($result);
	}
}

/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class Hubzero_Tool_Version
{
    //  Database Column Name			LDAP Field Name		Database Table Name
    //  ======================================================================

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
    private $id = null;                 //					    jos_tool_version

	/**
	 * Description for 'toolname'
	 * 
	 * @var unknown
	 */
    private $toolname = null;           //					    jos_tool_version

	/**
	 * Description for 'instance'
	 * 
	 * @var string
	 */
    private $instance = null;           // tool				    jos_tool_version

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
    private $title = null;              // cn				    jos_tool_version

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
    private $description = null;        // description			jos_tool_version

	/**
	 * Description for 'fulltext'
	 * 
	 * @var unknown
	 */
    private $fulltext = null;           //					    jos_tool_version

	/**
	 * Description for 'version'
	 * 
	 * @var unknown
	 */
    private $version = null;            // version			    jos_tool_version

	/**
	 * Description for 'revision'
	 * 
	 * @var unknown
	 */
    private $revision = null;           // revision			    jos_tool_version

	/**
	 * Description for 'toolaccess'
	 * 
	 * @var unknown
	 */
    private $toolaccess = null;         // public				jos_tool_version

	/**
	 * Description for 'codeaccess'
	 * 
	 * @var unknown
	 */
    private $codeaccess = null;         // sourcePublic		    jos_tool_version

	/**
	 * Description for 'wikiaccess'
	 * 
	 * @var unknown
	 */
    private $wikiaccess = null;         // projectPublic		jos_tool_version

	/**
	 * Description for 'state'
	 * 
	 * @var integer
	 */
    private $state = null;              // state				jos_tool_version

	/**
	 * Description for 'released_by'
	 * 
	 * @var unknown
	 */
    private $released_by = null;        //					    jos_tool_version

	/**
	 * Description for 'released'
	 * 
	 * @var unknown
	 */
    private $released = null;           // publishDate			jos_tool_version

	/**
	 * Description for 'unpublished'
	 * 
	 * @var unknown
	 */
    private $unpublished = null;        // unpublishDate		jos_tool_version

	/**
	 * Description for 'exportControl'
	 * 
	 * @var unknown
	 */
    private $exportControl = null;      // exportControl		jos_tool_version

	/**
	 * Description for 'license'
	 * 
	 * @var unknown
	 */
    private $license = null;            // usageAgreementText   jos_tool_version

	/**
	 * Description for 'vnc_geometry'
	 * 
	 * @var unknown
	 */
    private $vnc_geometry = null;       // vncGeometry			jos_tool_version

	/**
	 * Description for 'vnc_depth'
	 * 
	 * @var unknown
	 */
    private $vnc_depth = null;          // vncDepth			    jos_tool_version

	/**
	 * Description for 'vnc_timeout'
	 * 
	 * @var unknown
	 */
    private $vnc_timeout = null;        // vncTimeout			jos_tool_version

	/**
	 * Description for 'vnc_command'
	 * 
	 * @var unknown
	 */
    private $vnc_command = null;        // vncCommand			jos_tool_version

	/**
	 * Description for 'mw'
	 * 
	 * @var unknown
	 */
    private $mw = null;                 // defaultMiddleware	jos_tool_version

	/**
	 * Description for 'priority'
	 * 
	 * @var unknown
	 */
    private $priority = null;           // priority			    jos_tool_version

	/**
	 * Description for 'toolid'
	 * 
	 * @var unknown
	 */
    private $toolid = null;             //					    jos_tool_version

	/**
	 * Description for 'toolid'
	 * 
	 * @var unknown
	 */
    private $params = null;             //					    jos_tool_version

	/**
	 * Description for 'alias'
	 * 
	 * @var array
	 */
    private $alias = array();           // alias [array]		jos_tool_aliases

	/**
	 * Description for 'middleware'
	 * 
	 * @var array
	 */
    private $middleware = array();      // middleware [array]	jos_tool_middleware

	/**
	 * Description for 'hostreq'
	 * 
	 * @var array
	 */
    private $hostreq = array();         // vncHostReq [array]	jos_tool_hostreq

	/**
	 * Description for 'author'
	 * 
	 * @var array
	 */
    private $author = array();          // author [array]		jos_tool_authors

	/**
	 * Description for 'member'
	 * 
	 * @var array
	 */
    private $member = array();          // member [array]		jos_tool_groups

	/**
	 * Description for 'owner'
	 * 
	 * @var array
	 */
    private $owner = array();           // owner [array]		jos_tool_groups

	/**
	 * Description for '_list_keys'
	 * 
	 * @var array
	 */
    private $_list_keys = array('alias', 'middleware', 'hostreq', 'author', 'member', 'owner');

	/**
	 * Description for '_ldapToolMirror'
	 * 
	 * @var boolean
	 */
    private $_ldapToolMirror = false;

	/**
	 * Description for '_updateAll'
	 * 
	 * @var boolean
	 */
    private $_updateAll = false;

	/**
	 * Description for '_propertyattrmap'
	 * 
	 * @var array
	 */
    static $_propertyattrmap = array('title'=>'cn', 'description'=>'description',
        'version'=>'version', 'revision'=>'revision', 'released'=>'publishDate',
        'unpublished'=>'unpublishDate', 'exportControl'=>'exportControl',
        'vnc_geometry'=>'vncGeometry', 'vnc_depth'=>'vncDepth', 'vnc_timeout'=>'vncTimeout',
        'vnc_command'=>'vncCommand', 'mw'=>'defaultMiddleware', 'priority'=>'priority',
        'alias'=>'alias', 'hostreq'=>'vncHostReq', 'member'=>'member',
        'owner'=>'owner', 'state'=>'state', 'codeaccess'=>'sourcePublic', 'toolaccess'=>'public',
        'wikiaccess'=>'projectPublic', 'author'=>'author', 'middleware'=>'middleware', 'params'=>'params');

	/**
	 * Description for '_updatedkeys'
	 * 
	 * @var array
	 */
    private $_updatedkeys = array();

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
    private function __construct()
    {
        $config = & JComponentHelper::getParams('com_contribtool');
        $this->_ldapToolMirror = $config->get('ldap_save') == '1';
    }

	/**
	 * Short description for 'clear'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
    public function clear()
    {
        $cvars = get_class_vars(__CLASS__);

        $this->_updatedkeys = array();

        foreach ($cvars as $key=>$value)
        {
            if ($key{0} != '_')
            {
                unset($this->$key);

                if (!in_array($key, $this->_list_keys))
                {
                    $this->$key = null;
                }
                else
                {
                    $this->$key = array();
                }
            }
        }

        $this->_updateAll = false;
        $this->_updatedkeys = array();
    }

	/**
	 * Short description for 'logDebug'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $msg Parameter description (if any) ...
	 * @return     void
	 */
    private function logDebug($msg)
    {
        $xlog = &Hubzero_Factory::getLogger();
        $xlog->logDebug($msg);
    }

	/**
	 * Short description for 'getTool_VersionNames'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $tool Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
    public function getTool_VersionNames($tool = null)
    {
        $db = &JFactory::getDBO();

        if (!isset($this))
        { // static method call
            if (is_numeric($tool))
            {
                $where_clause = "toolid=" . $db->Quote($tool);
            }
            else
            {
                $where_clause = "toolname=" . $db->Quote($tool);
            }
        }
        else
        { // object method call
            if (is_numeric($tool))
            {
                $where_clause = "toolid=" . $db->Quote($tool);
            }
            else if (!empty($tool))
            {
                $where_clause = "toolname=" . $db->Quote($tool);
            }
            else if (empty($this->toolname))
            {
                $where_clause = "toolid=" . $db->Quote($this->id);
            }
            else if (empty($this->toolid))
            {
                $where_clause = "toolname=" . $db->Quote($this->toolname);
            }
            else
            {
                $where_clause = "toolname=" . $db->Quote($this->toolname) . " AND toolid=" .
                    $db->Quote($this->toolid);
            }
        }

        $db->setQuery("SELECT instance FROM #__tool_version WHERE $where_clause;");
        $result = $db->loadResultArray();

        return $result;
    }

	/**
	 * Short description for 'toArray'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $format Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
    public function toArray($format = 'mysql')
    {
        $xhub = &Hubzero_Factory::getHub();
        $result = array();
        $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

        if ($format == 'mysql')
        {
            foreach (self::$_propertyattrmap as $key=>$value)
            {
                $current = $this->__get($key);

                $result[$key] = $current;
            }

            return $result;
        }
        else if ($format == 'ldap')
        {
            foreach (self::$_propertyattrmap as $key=>$value)
            {
                $current = $this->__get($key);

                if (isset($current) && !is_null($current))
                {
                    $result[$value] = $current;
                }
                else
                {
                    $result[$value] = array();
                }
            }

            foreach ($result['member'] as $key=>$member)
            {
                if (!empty($member))
                {
                    $result['member'][$key] = "gid=$member,ou=groups," . $hubLDAPBaseDN;
                }
            }

            foreach ($result['owner'] as $key=>$owner)
            {
                if (!empty($owner))
                {
                    $result['owner'][$key] = "gid=$owner,ou=groups," . $hubLDAPBaseDN;
                }
            }

            // toolaccess
            $current = $this->__get('toolaccess');

            if ($current == '@GROUP')
            {
                $current = 'FALSE';
            }
            else if (!empty($current))
            {
                $current = 'TRUE';
            }

            if (!empty($current))
            {
                $result['public'] = $current;
            }
            else
            {
                $result['public'] = array();
            }

            // codeaccess
            $current = $this->__get('codeaccess');

            if ($current == '@OPEN')
            {
                $current = 'TRUE';
            }
            else if (!empty($current))
            {
                $current = 'FALSE';
            }

            if (!empty($current))
            {
                $result['sourcePublic'] = $current;
            }
            else
            {
                $result['sourcePublic'] = array();
            }

            // wikiaccess
            $current = $this->__get('wikiaccess');

            if ($current == '@OPEN')
            {
                $current = 'TRUE';
            }
            else if (!empty($current))
            {
                $current = 'FALSE';
            }

            if (!empty($current))
            {
                $result['projectPublic'] = $current;
            }
            else
            {
                $result['projectPublic'] = array();
            }

            // state
            $current = $this->__get('state');

            $state = array('retired', 'published', 'unknown', 'created', 'installed', 'approved',
                'uploaded', 'abandoned');

            if (is_numeric($current))
            {
                $result['state'] = $state[$current];
            }
            else
            {
                $result['state'] = array();
            }

            return $result;
        }

        return false;
    }

	/**
	 * Short description for 'getInstance'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $instance Parameter description (if any) ...
	 * @param      unknown $storage Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
    public function getInstance($instance, $storage = null)
    {
        $hztv = new Hubzero_Tool_Version();

        if ($hztv->read($instance, $storage) === false)
        {
            return false;
        }

        return $hztv;
    }

	/**
	 * Short description for 'createInstance'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $toolname Parameter description (if any) ...
	 * @param      unknown $instance Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
    public function createInstance($toolname,$instance)
    {
        if (empty($toolname))
        {
            return false;
        }

        if (empty($instance))
        {
            return false;
        }

        $newinstance = new Hubzero_Tool_Version();
		$newinstance->toolname = $toolname;
        $newinstance->instance = $instance;

        if ($newinstance->create())
        {
            return $newinstance;
        }

        return false;
    }

	/**
	 * Short description for '_ldap_create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
    private function _ldap_create()
    {
        $xhub = &Hubzero_Factory::getHub();
        $conn = &Hubzero_Factory::getPLDC();

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        $dn = 'tool=' . $this->instance . ',ou=tools,' . $xhub->getCfg('hubLDAPBaseDN');
        $attr["objectclass"][0] = "top";
        $attr["objectclass"][1] = "hubTool";
        $attr['tool'] = $this->instance;

        if (!@ldap_add($conn, $dn, $attr) && @ldap_errno($conn) != 68)
        {
            return false;
        }

        return true;
    }

	/**
	 * Short description for '_mysql_create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
    private function _mysql_create()
    {
        $db = &JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }
        else if (is_numeric($this->id))
        {
            $query = "INSERT INTO #__tool_version (id,toolname,instance) VALUES ( " .
                $db->Quote($this->id) . "," . $db->Quote($this->toolname) . "," .
                $db->Quote($this->instance) . ");";

            $db->setQuery($query);

            $result = $db->query();

            if ($result !== false || $db->getErrorNum() == 1062)
            {
                return true;
            }
        }
        else
        {
            $query = "INSERT INTO #__tool_version (toolname,instance) VALUES ( " .
                $db->Quote($this->toolname) . "," . $db->Quote($this->instance) . ");";

            $db->setQuery($query);

            $result = $db->query();

            if ($result === false && $db->getErrorNum() == 1062)
            {
                $query = "SELECT id FROM #__tool_version where instance=" .
                    $db->Quote($this->instance) . ";";

                $db->setQuery($query);

                $result = $db->loadResult();

                if ($result == null)
                {
                    return false;
                }

                $this->id = $result;
                return true;
            }
            else if ($result !== false)
            {
                $this->id = $db->insertid();
                return true;
            }
        }

        return false;
    }

	/**
	 * Short description for 'create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
    public function create($storage = null)
    {
        if (is_null($storage))
        {
            $storage = ($this->_ldapToolMirror) ? 'all' : 'mysql';
        }

        if (!is_string($storage))
        {
            $this->_error(__FUNCTION__ . ": Argument #1 is not a string", E_USER_ERROR);
            die();
        }

        if (!in_array($storage, array('mysql', 'ldap', 'all')))
        {
            $this->_error(__FUNCTION__ . ": Argument #1 [$storage] is not a valid value",
                E_USER_ERROR);
            die();
        }

        $result = true;

        if ($storage == 'mysql' || $storage == 'all')
        {
            $result = $this->_mysql_create();

            if ($result === false)
            {
                $this->_error(__FUNCTION__ . ": MySQL create failed", E_USER_WARNING);
            }
        }

        if ($result === true && ($storage == 'ldap' || $storage == 'all'))
        {
            $result = $this->_ldap_create();

            if ($result === false)
            {
                $this->_error(__FUNCTION__ . ": LDAP create failed", E_USER_WARNING);
            }
        }
        return $result;
    }

	/**
	 * Short description for '_ldap_read'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
    private function _ldap_read()
    {
        $xhub = &Hubzero_Factory::getHub();
        $conn = &Hubzero_Factory::getPLDC();
        $xlog = &Hubzero_Factory::getLogger();

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        $dn = "tool=" . $this->instance . ",ou=tools," . $xhub->getCfg('hubLDAPBaseDN');

        $reqattr = array('tool', 'cn', 'usageAgreementText', 'alias', 'public', 'middleware',
            'defaultMiddleware', 'description', 'vncGeometry', 'vncDepth', 'vncTimeout',
            'vncCommand', 'vncHostReq', 'exportControl', 'version', 'revision', 'state',
            'sourcePublic', 'priority', 'author', 'member', 'owner', 'publishDate',
            'unpublishDate', 'projectPublic');

        $entry = @ldap_search($conn, $dn, "(objectClass=hubTool)", $reqattr, 0, 0, 0, 3);

        if (empty($entry))
        {
            $xlog->logDebug(__FUNCTION__ . "() $dn search failed");
            return false;
        }

        $count = ldap_count_entries($conn, $entry);

        if ($count <= 0)
        {
            $xlog->logDebug(__FUNCTION__ . "() $dn no results");
            return false;
        }

        $firstentry = ldap_first_entry($conn, $entry);
        $attr = ldap_get_attributes($conn, $firstentry);
        $toolinfo = array();

        foreach ($reqattr as $key=>$value)
        {
            if (isset($attr[$reqattr[$key]][0]))
            {
                if (count($attr[$reqattr[$key]]) <= 2)
                {
                    $toolinfo[$value] = $attr[$reqattr[$key]][0];
                }
                else
                {
                    $toolinfo[$value] = $attr[$reqattr[$key]];
                    unset($toolinfo[$value]['count']);
                }
            }
            else
            {
                unset($toolinfo[$value]);
            }
        }

        if (!empty($toolinfo['member']))
        {
            if (!is_array($toolinfo['member']))
            {
                $toolinfo['member'] = array($toolinfo['member']);
            }

            foreach ($toolinfo['member'] as $key=>$value)
            {
                if (strncmp($value, "gid=", 4) == 0)
                {
                    $endpos = strpos($value, ',', 4);

                    if ($endpos)
                    {
                        $value = substr($value, 4, $endpos - 4);
                    }
                    else
                    {
                        $value = substr($value, 4);
                    }

                    $toolinfo['member'][$key] = $value;
                }
            }
        }

        if (!empty($toolinfo['owner']))
        {
            if (!is_array($toolinfo['owner']))
            {
                $toolinfo['owner'] = array($toolinfo['owner']);
            }

            foreach ($toolinfo['owner'] as $key=>$value)
            {
                if (strncmp($value, "gid=", 4) == 0)
                {
                    $endpos = strpos($value, ',', 4);

                    if ($endpos)
                    {
                        $value = substr($value, 4, $endpos - 4);
                    }
                    else
                    {
                        $value = substr($value, 4);
                    }

                    $toolinfo['owner'][$key] = $value;
                }
            }
        }

        if (!empty($toolinfo['state']))
        {
            $state = array('retired'=>'0', 'published'=>'1', 'unknown'=>'2', 'created'=>'3',
                'installed'=>'4', 'approved'=>'5', 'uploaded'=>'6', 'abandoned'=>'7');

            $toolinfo['state'] = $state[$toolinfo['state']];
        }

        if (!empty($toolinfo['public']))
        {
            if ($toolinfo['public'] == 'TRUE')
            {
                $toolinfo['public'] = '@OPEN';
            }
            else
            {
                $toolinfo['public'] = '@GROUP';
            }
        }

        if (!empty($toolinfo['sourcePublic']))
        {
            if ($toolinfo['sourcePublic'] == 'TRUE')
            {
                $toolinfo['sourcePublic'] = '@OPEN';
            }
            else if ($toolinfo['sourcePublic'] == 'FALSE')
            {
                $toolinfo['sourcePublic'] = '@DEV';
            }
            else
            {
                unset($toolinfo['sourcePublic']);
            }
        }

        if (!empty($toolinfo['projectPublic']))
        {
            if ($toolinfo['projectPublic'] == 'TRUE')
            {
                $toolinfo['projectPublic'] = '@OPEN';
            }
            else
            {
                $toolinfo['projectPublic'] = '@DEV';
            }
        }

        $this->clear();

        foreach (self::$_propertyattrmap as $key=>$value)
        {
            if (isset($toolinfo[$value]))
            {
                $this->__set($key, $toolinfo[$value]);
            }
            else
            {
                $this->__set($key, null);
            }
        }

        $this->_updatedkeys = array();

        $xlog->logDebug(__FUNCTION__ . "() $dn successful");
        return true;
    }

	/**
	 * Short description for '_mysql_read'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
    private function _mysql_read()
    {
        $db = JFactory::getDBO();
        $lazyloading = false;

        if (empty($db))
        {
            return false;
        }

        if (is_numeric($this->instance))
        {
            $query = "SELECT * FROM #__tool_version WHERE id=" . $db->Quote($this->instance) . ";";
        }
        else
        {
            $query = "SELECT * FROM #__tool_version WHERE instance=" .
                $db->Quote($this->instance) . ";";
        }

        $db->setQuery($query);

        $result = $db->loadAssoc();

        if (empty($result))
        {
            return false;
        }

        $this->clear();

        foreach ($result as $key=>$value)
        {
            $this->__set($key, $value);
        }

        $this->__unset('alias');
        $this->__unset('middleware');
        $this->__unset('hostreq');
        $this->__unset('author');
        $this->__unset('member');
        $this->__unset('owner');

        if (!$lazyloading)
        {
            $this->__get('alias');
            $this->__get('middleware');
            $this->__get('hostreq');
            $this->__get('author');
            $this->__get('member');
            $this->__get('owner');
        }

        $this->_updatedkeys = array();

        return true;
    }

	/**
	 * Short description for 'read'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $instance Parameter description (if any) ...
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
    public function read($instance = null, $storage = 'mysql')
    {
        if (is_null($storage))
        {
            $storage = 'mysql';
        }

        if (is_null($instance))
        {
            $instance = $this->instance;

            if (!empty($instance) && !is_string($instance) && !is_numeric($instance))
            {
                $this->_error(__FUNCTION__ . ": invalid tool version instance defined",
                    E_USER_ERROR);
                die();
            }
        }

        if (!empty($instance) && !is_string($instance) && !is_numeric($instance))
        {
            $this->_error(__FUNCTION__ . ": Argument #1 is not a valid string and not numeric",
                E_USER_ERROR);
            die();
        }

        if (!is_string($storage))
        {
            $this->_error(__FUNCTION__ . ": Argument #2 is not a string", E_USER_ERROR);
            die();
        }

        if (!in_array($storage, array('mysql', 'ldap')))
        {
            $this->_error(__FUNCTION__ . ": Argument #2 [$storage] is not a valid value",
                E_USER_ERROR);
            die();
        }

        $result = true;

        if ($storage == 'mysql')
        {
            $this->clear();
            $this->instance = $instance;

            $result = $this->_mysql_read();

            if ($result === false)
            {
                $this->clear();
            }
        }
        else if ($storage == 'ldap')
        {
            $this->clear();
            $this->instance = $instance;

            $result = $this->_ldap_read();

            if ($result === false)
            {
                $this->clear();
            }
        }

        return $result;
    }

	/**
	 * Short description for '_ldap_update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $all Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
    private function _ldap_update($all = false)
    {
        $xhub = &Hubzero_Factory::getHub();
        $conn = &Hubzero_Factory::getPLDC();
        $xlog = &Hubzero_Factory::getLogger();

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        if (empty($this->instance))
        {
            return false;
        }

        $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

        $toolinfo = $this->toArray('ldap');

        $current_hztv = Hubzero_Tool_Version::getInstance($this->instance, 'ldap');

        if (!is_object($current_hztv))
        {
            if ($this->_ldap_create() === false)
            {
                $xlog->logDebug(__FUNCTION__ . "() " . $this->instance .
                    " doesn't exist and create failed.");
                return false;
            }

            $current_hztv = Hubzero_Tool_Version::getInstance($this->instance, 'ldap');

            if (!is_object($current_hztv))
            {
                $xlog->logDebug(__FUNCTION__ . "() " . $this->instance .
                    " created but doesn't read back.");
                return false;
            }
        }

        $currentinfo = $current_hztv->toArray('ldap');

        $dn = 'tool=' . $this->instance . ',ou=tools,' . $hubLDAPBaseDN;

        $replace_attr = array();
        $add_attr = array();
        $delete_attr = array();

        $_attrpropertymap = array_flip(self::$_propertyattrmap);

        foreach ($currentinfo as $key=>$value)
        {
            if (!$all && !in_array($_attrpropertymap[$key], $this->_updatedkeys))
            {
                continue;
            }
            else if ($toolinfo[$key] == array() && $currentinfo[$key] != array())
            {
                $delete_attr[$key] = array();
            }
            else if ($toolinfo[$key] != array() && $currentinfo[$key] == array())
            {
                if ($toolinfo[$key] != '')
                {
                    $add_attr[$key] = $toolinfo[$key];
                }
            }
            else if ($toolinfo[$key] != $currentinfo[$key])
            {
                $replace_attr[$key] = $toolinfo[$key];
            }
        }

        $errno = 0;

        if (!ldap_mod_replace($conn, $dn, $replace_attr))
        {
            $errno = @ldap_errno($conn);
            $xlog->logDebug(__FUNCTION__ . "() ldap replace failed with $errno.");
        }
        if (!ldap_mod_add($conn, $dn, $add_attr))
        {
            $errno = @ldap_errno($conn);
            $xlog->logDebug(__FUNCTION__ . "() ldap add failed with $errno. " .
                var_export($add_attr, true));
        }
        if (!ldap_mod_del($conn, $dn, $delete_attr))
        {
            $errno = @ldap_errno($conn);
            $xlog->logDebug(__FUNCTION__ . "() ldap del failed with $errno.");
        }

        if ($errno != 0)
        {
            $xlog->logDebug(__FUNCTION__ . "() ldap failed with $errno.");
            return false;
        }

        return true;
    }

	/**
	 * Short description for '_mysql_update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $all Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
    private function _mysql_update($all = false)
    {
        $db = &JFactory::getDBO();
        $xlog = &Hubzero_Factory::getLogger();

    	$xlog->logDebug('_mysql_update() start');
        $query = "UPDATE #__tool_version SET ";

        $classvars = get_class_vars(__CLASS__);

        $first = true;

        foreach ($classvars as $property=>$value)
        {
            if (($property{0} == '_') || in_array($property, $this->_list_keys))
            {
                continue;
            }

            if (!$all && !in_array($property, $this->_updatedkeys))
            {
                continue;
            }

            if (!$first)
            {
                $query .= ',';
            }
            else
            {
                $first = false;
            }

            $value = $this->__get($property);

            if ($value === null)
            {
                $query .= "`$property`=NULL";
            }
            else
            {
                $query .= "`$property`=" . $db->Quote($value);
            }
        }

        $query .= " WHERE `id`=" . $db->Quote($this->__get('id')) . ";";

        if ($first == true)
        {
            $query = '';
        }

        $db->setQuery($query);

        if (!empty($query))
        {
            $result = $db->query();

            if ($result === false)
            {
                return false;
            }
        }

        foreach ($this->_list_keys as $property)
        {
            if (!$all && !in_array($property, $this->_updatedkeys))
                continue;

            if ($property == 'author' || $property == 'xauthor')
            {
                $aux_table = '#__tool_authors';
            }
            else if ($property == 'member' || $property == 'owner')
            {
                $aux_table = '#__tool_groups';
            }
            else
            {
                $aux_table = "#__tool_version_" . $property;
            }

            $list = $this->__get($property);

            if (!is_null($list) && !is_array($list))
            {
                $list = array($list);
            }

            if (is_array($list) && count($list) > 0)
            {
                $first = true;

                if ($property == 'author')
                {
                    $query = "REPLACE INTO $aux_table (toolname,revision,uid,ordering," .
                        "version_id) VALUES ";
                }
		else if ($property == 'xauthor')
		{
                    $query = "REPLACE INTO $aux_table (toolname,revision,uid,ordering," .
                        "version_idi,name,organization) VALUES ";
		}
                else if ($property == 'member' || $property == 'owner')
                {
                    $query = "REPLACE INTO $aux_table (cn,toolid,role) VALUES ";
                }
                else
                {
                    $query = "REPLACE INTO $aux_table (tool_version_id, " . $property .
                        ") VALUES ";
                }

                $order = 0;

                foreach ($list as $value)
                {
                    if (!$first)
                    {
                        $query .= ',';
                    }

                    $first = false;

                    if ($property == 'author')
                    {
                        $query .= '(' . $db->Quote($this->toolname) . ',' .
                            $db->Quote($this->revision) . ',' . $db->Quote($value) . ',' .
                            $db->Quote($order) . ',' . $db->Quote($this->id) . ')';
                    }
                    else if ($property == 'xauthor')
                    {
                        $query .= '(' . $db->Quote($this->toolname) . ',' .
                            $db->Quote($this->revision) . ',' . $db->Quote($value['uid']) . ',' .
                            $db->Quote($order) . ',' . $db->Quote($this->id) . ',' .
                            $db->Quote($value['name']) . ',' . $db->Quote($value['organization']) . ')';
		            }
                    else if ($property == 'member')
                    {
                        $query .= '(' . $db->Quote($value) . ',' . $db->Quote($this->toolid) . ',' .
                            $db->Quote('0') . ')';
                    }
                    else if ($property == 'owner')
                    {
                        if ($value == 'apps' || $value == 'contribtooladmin')
                        {
                            $query .= '(' . $db->Quote($value) . ',' . $db->Quote($this->toolid) .
                                ',' . $db->Quote('2') . ')';
                        }
                        else
                        {
                            $query .= '(' . $db->Quote($value) . ',' . $db->Quote($this->toolid) .
                                ',' . $db->Quote('1') . ')';
                        }
                    }
                    else
                    {
                        $query .= '(' . $db->Quote($this->id) . ',' . $db->Quote($value) . ')';
                    }

                    $order++;
                }

                $db->setQuery($query);

                if (!$db->query())
                {
                    return false;
                }

            }

            if (!is_array($list) || count($list) == 0)
            {
                if ($property == 'author' || $property == 'xauthor')
                {
                    $query = "DELETE FROM $aux_table WHERE version_id=" .
                        $db->Quote($this->id) . ";";
                }
                else if ($property == 'member')
                {
                    $query = "DELETE FROM $aux_table WHERE toolid=" .
                        $db->Quote($this->toolid) . " AND role='0';";
                }
                else if ($property == 'owner')
                {
                    $query = "DELETE FROM $aux_table WHERE toolid=" .
                        $db->Quote($this->toolid) . " AND role='1';";
                }
                else
                {
                    $query = "DELETE FROM $aux_table WHERE tool_version_id=" .
                        $db->Quote($this->id) . ";";
                }
            }
            else
            {
                foreach ($list as $key=>$value)
                {
                    $list[$key] = $db->Quote($value);
                }

                $valuelist = implode($list, ",");

                if (empty($valuelist))
                {
                    $valuelist = "''";
                }

                if ($property == 'author' || $property == 'xauthor')
                {
                    $query = "DELETE FROM $aux_table WHERE version_id=" . $db->Quote($this->id) .
                        " AND uid NOT IN ($valuelist);";
                }
                else if ($property == 'member')
                {
                    $query = "DELETE FROM $aux_table WHERE role='0' AND toolid=" .
                        $db->Quote($this->toolid) . " AND cn NOT IN ($valuelist);";
                }
                else if ($property == 'owner')
                {
                    $query = "DELETE FROM $aux_table WHERE (role='1' OR role='2') AND toolid=" .
                        $db->Quote($this->toolid) . " AND cn NOT IN ($valuelist);";
                }
                else
                {
                    $query = "DELETE FROM $aux_table WHERE tool_version_id=" .
                        $db->Quote($this->id) . " AND $property NOT IN ($valuelist);";
                }
            }

            $db->setQuery($query);

            if (!$db->query())
            {
        		$xlog->logDebug('_mysql_update_failed');
                return false;
            }
        }

        return true;
    }

	/**
	 * Short description for 'sync'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
    public function sync()
    {
        $this->_updateAll = true;
        return $this->update();
    }

	/**
	 * Short description for 'syncldap'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
    public function syncldap()
    {
        $this->_updateAll = true;
        return $this->update('ldap');
    }

	/**
	 * Short description for 'update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
    public function update($storage = null)
    {
		$xlog = &Hubzero_Factory::getLogger();

		$xlog->logDebug("update $storage");

        if (is_null($storage))
        {
            $storage = ($this->_ldapToolMirror) ? 'all' : 'mysql';
        }

        if (!is_string($storage))
        {
            $this->_error(__FUNCTION__ . ": Argument #1 is not a string", E_USER_ERROR);
            die();
        }

        if (!in_array($storage, array('mysql', 'ldap', 'all')))
        {
            $this->_error(__FUNCTION__ . ": Argument #1 [$storage] is not a valid value",
                E_USER_ERROR);
            die();
        }

        $result = true;

        if ($storage == 'mysql' || $storage == 'all')
        {
            $result = $this->_mysql_update($this->_updateAll);

            if ($result === false)
            {
                $this->_error(__FUNCTION__ . ": MySQL update failed", E_USER_WARNING);
            }
        }

        if ($result === true && ($storage == 'ldap' || $storage == 'all'))
        {
            $result = $this->_ldap_update($this->_updateAll);

            if ($result === false)
            {
                $this->_error(__FUNCTION__ . ": LDAP update failed", E_USER_WARNING);
            }
        }

        $this->_updateAll = false;
        return $result;
    }

	/**
	 * Short description for '_ldap_delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
    private function _ldap_delete()
    {
        $conn = &Hubzero_Factory::getPLDC();
        $xhub = &Hubzero_Factory::getHub();

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        if (!isset($this->instance))
        {
            return false;
        }

        $dn = "tool=" . $this->instance . ",ou=tools," . $xhub->getCfg('hubLDAPBaseDN');

        if (!@ldap_delete($conn, $dn))
        {
            return false;
        }

        return true;
    }

	/**
	 * Short description for '_mysql_delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
    private function _mysql_delete()
    {
        if (!isset($this->instance) && !isset($this->id))
        {
            return false;
        }

        $db = JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }

        if (!isset($this->id))
        {
            $db->setQuery("SELECT id FROM #__tool_version WHERE instance=" .
                $db->Quote($this->instance) . ";");

            $this->id = $db->loadResult();
        }

        if (empty($this->id))
        {
            return false;
        }

        $db->setQuery("DELETE FROM #__tool_version WHERE id=" . $db->Quote($this->id) . ";");

        if (!$db->query())
        {
            return false;
        }

        $db->setQuery("DELETE FROM #__tool_version_alias WHERE tool_version_id=" .
            $db->Quote($this->id) . ";");
        $db->query();
        $db->setQuery("DELETE FROM #__tool_version_hostreq WHERE tool_version_id=" .
            $db->Quote($this->id) . ";");
        $db->query();
        $db->setQuery("DELETE FROM #__tool_version_middleware WHERE tool_version_id=" .
            $db->Quote($this->id) . ";");
        $db->query();

		if ($this->state == 3)
		{
        	$db->setQuery("DELETE FROM #__trac_user_action as a,#__trac as t WHERE a.trac_id=t.id AND t.scope='tool' AND t.name=" .
				$db->Quote($this->toolname));
        	$db->query();
        	$db->setQuery("DELETE FROM #__trac_group_action as a,#__trac as t WHERE a.trac_id=t.id AND t.scope='tool' AND t.name=" .
				$db->Quote($this->toolname));
        	$db->query();
        	$db->setQuery("DELETE FROM #__trac as t WHERE t.scope='tool' AND t.name=" .
				$db->Quote($this->toolname));
        	$db->query();
		}

        return true;
    }

	/**
	 * Short description for 'delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
    public function delete($storage = null)
    {
        $xlog = &Hubzero_Factory::getLogger();

        if (func_num_args() > 1)
        {
            $this->_error(__FUNCTION__ . ": Invalid number of arguments", E_USER_ERROR);
            die();
        }

        if (is_null($storage))
        {
            $storage = ($this->_ldapToolMirror) ? 'all' : 'mysql';
        }

        if (!is_string($storage))
        {
            $this->_error(__FUNCTION__ . ": Argument #1 is not a string", E_USER_ERROR);
            die();
        }

        if (!in_array($storage, array('mysql', 'ldap', 'all')))
        {
            $this->_error(__FUNCTION__ . ": Argument #1 [$storage] is not a valid value",
                E_USER_ERROR);
            die();
        }

        $result = true;

        if ($storage == 'mysql' || $storage == 'all')
        {
            $result = $this->_mysql_delete();

            if ($result === false)
            {
                $this->_error(__FUNCTION__ . ": MySQL deletion failed", E_USER_WARNING);
            }
        }

        if ($result === true && ($storage == 'ldap' || $storage == 'all'))
        {
            $result = $this->_ldap_delete();

            if ($result === false)
            {
                $this->_error(__FUNCTION__ . ": LDAP deletion failed", E_USER_WARNING);
            }
        }

        return $result;
    }

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $property Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
    public function __get($property = null)
    {
        $xlog = &Hubzero_Factory::getLogger();

        if (!property_exists(__CLASS__, $property) || $property{0} == '_')
        {
            if (empty($property))
                $property = '(null)';

            $this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
            die();
        }
        if (in_array($property, $this->_list_keys))
        {
            if (!array_key_exists($property, get_object_vars($this)))
            {
                $db = &JFactory::getDBO();

                if (is_object($db))
                {
                    if (in_array($property, array('alias', 'middleware', 'hostreq')))
                    {
                        $aux_table = "#__tool_version_" . $property;

                        $query = "SELECT $property FROM $aux_table AS aux WHERE " .
                            " aux.tool_version_id=" . $db->Quote($this->id) .
                            " ORDER BY $property" . " ASC;";
                    }
                    else if ($property == 'author')
                    {
                        $query = "SELECT uid FROM #__tool_authors WHERE version_id=" .
                            $db->Quote($this->id) . " ORDER BY ordering ASC;";
                    }
                    else if ($property == 'xauthor')
                    {
                        $query = "SELECT uid,name,organization FROM #__tool_authors WHERE version_id=" .
                            $db->Quote($this->id) . " ORDER BY ordering ASC;";
                    }
                    else if ($property == 'member')
                    {
                        $query = "SELECT cn FROM #__tool_groups WHERE role='0' AND toolid=" .
                            $db->Quote($this->toolid) . " ORDER BY cn ASC;";
                    }
                    else if ($property == 'owner')
                    {
                        $query = "SELECT cn FROM #__tool_groups WHERE (role='1' OR role='2') AND " .
                            " toolid=" . $db->Quote($this->toolid) . " ORDER BY cn ASC;";
                    }
                    else
                    {
                        $query = null;
                    }

                    $db->setQuery($query);

                    if ($property == 'xauthor')
                    {
                        $result = $db->loadAssocList();
                    }
                    else
                        $result = $db->loadResultArray();

                    if ($result !== false)
                    {
                        $this->$property = (isset($result[0])) ? $result : array();
                        $this->_updatedkeys = array_diff($this->_updatedkeys, array($property));
                    }
                }
            }
        }

        if (isset($this->$property))
            return $this->$property;

        if (array_key_exists($property, get_object_vars($this)))
            return null;

        $this->_error("Undefined property " . __CLASS__ . "::$" . $property, E_USER_NOTICE);

        return null;
    }

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $property Parameter description (if any) ...
	 * @param      array $value Parameter description (if any) ...
	 * @return     void
	 */
    public function __set($property = null, $value = null)
    {
        if (!property_exists(__CLASS__, $property) || $property{0} == '_')
        {
            if (empty($property))
            {
                $property = '(null)';
            }

            $this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
            die();
        }

        if ($property == 'owner' || $property == 'member')
        {
            $this->$property = array_map("strtolower",
                array_values(array_unique(array_diff((array) $value, array('')))));
        }
		else if ($property == 'xauthor')
		{
			if (array_key_exists('uid',$value))
				$value = array($value);
			else if (is_numeric($value))
			{
				$val['uid'] = $value;
				$value[0] = $val;
			}

			foreach($value as $nvalue)
			{
				unset($val);

				if (is_numeric($nvalue))
					$val['uid'] = $nvalue;

				$val['uid'] = isset($nvalue['uid']) ? $nvalue['uid'] : '';
				$val['name'] = isset($nvalue['name']) ? $nvalue['name'] : '';
				$val['organization'] = isset($nvalue['organization']) ? $nvalue['organization'] : '';

				if (array_key_exists('uid',$val) && is_numeric($val['uid']))
				{
					$found = false;

					foreach($this->$property as $prop)
					{
						if ($prop['uid'] == $val['uid'])
						{
							$found = true;
							break;
						}
					}

					if (!$found)
						$this->xauthor[] = $val ;
				}
			}
		}
        else if (in_array($property, $this->_list_keys))
        {
            $this->$property = array_values(array_unique(array_diff((array) $value, array(''))));
        }
        else
        {
            $this->$property = $value;
        }

        if (!in_array($property, $this->_updatedkeys))
            $this->_updatedkeys[] = $property;
    }

	/**
	 * Short description for '__isset'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $property Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
    public function __isset($property = null)
    {
        if (!property_exists(__CLASS__, $property) || $property{0} == '_')
        {
            if (empty($property))
                $property = '(null)';

            $this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
            die();
        }

        return isset($this->$property);
    }

	/**
	 * Short description for '__unset'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $property Parameter description (if any) ...
	 * @return     void
	 */
    public function __unset($property = null)
    {
        if (!property_exists(__CLASS__, $property) || $property{0} == '_')
        {
            if (empty($property))
                $property = '(null)';

            $this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
            die();
        }

        $this->_updatedkeys = array_diff($this->_updatedkeys, array($property));

        unset($this->$property);
    }

	/**
	 * Short description for '_error'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $message Parameter description (if any) ...
	 * @param      integer $level Parameter description (if any) ...
	 * @return     void
	 */
    private function _error($message, $level = E_USER_NOTICE)
    {
        $caller = next(debug_backtrace());

        switch ($level)
        {
            case E_USER_NOTICE:
                echo "Notice: ";
                break;
            case E_USER_ERROR:
                echo "Fatal error: ";
                break;
            default:
                echo "Unknown error: ";
                break;
        }

        echo $message . ' in ' . $caller['file'] . ' on line ' . $caller['line'] . "\n";
    }

	/**
	 * Short description for 'get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $key Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
    public function get($key)
    {
        return $this->__get($key);
    }

	/**
	 * Short description for 'set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $key Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
    public function set($key, $value)
    {
        return $this->__set($key, $value);
    }

	/**
	 * Short description for 'add'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $key Parameter description (if any) ...
	 * @param      array $value Parameter description (if any) ...
	 * @return     void
	 */
    public function add($key = null, $value = array())
    {
        $this->__set($key, array_merge($this->__get($key), (array) $value));
    }

	/**
	 * Short description for 'remove'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $key Parameter description (if any) ...
	 * @param      array $value Parameter description (if any) ...
	 * @return     void
	 */
    public function remove($key = null, $value = array())
    {
        $this->__set($key, array_diff($this->__get($key), (array) $value));
    }

	/**
	 * Short description for 'getDevelopmentGroup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $byid Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getDevelopmentGroup($byid = false)
	{
		$db = &JFactory::getDBO();

		if ($byid == false)
		{
			$query = "SELECT cn FROM #__tool_groups AS tg WHERE tg.role='1' AND tg.toolid=" . $db->Quote($this->toolid) . " LIMIT 1";
		}
		else
		{
			$query = "SELECT gidNumber FROM #__xgroups AS xg, #_tool_groups AS tg WHERE tg.cn=xg.cn AND tg.role='1' AND tg.toolid=" . $db->Quote($this->toolid) . " LIMIT 1";
		}
		$db->setQuery($query);
        $result = $db->loadResult();
		return $result;
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
	public function getVersionInfo($id, $version=null, $toolname=null, $instance=null)
    {
		$db = &JFactory::getDBO();
        // data comes from mysql
        $query  = "SELECT v.*, d.doi_label as doi ";
        $query .= "FROM #__tool_version as v LEFT JOIN #__doi_mapping as d ON d.alias = v.toolname AND d.local_revision=v.revision ";
        if($id) {
            $query .= "WHERE v.id = '".$id."' ";
        }
        else if($version && $toolname) {
            if (is_array($toolname)) {
				$query .= "LEFT JOIN #__tool_version AS v2 ON v2.revision < v.revision AND v2.toolname=v.toolname ";
				$query .= "WHERE v.toolname IN ('".implode("','", $toolname)."') ";
			} else {
				$query.= "WHERE v.toolname='".$toolname."' ";
			}
            switch ($version) 
			{
                case 'current': 
					$query .= "AND v.state=1 ORDER BY v.revision DESC";
					if (!is_array($toolname))
					{
						$query .= " LIMIT 1";
					}
				break;
            	case 'dev':
                	$query .= "AND v.state=3";
					if (!is_array($toolname))
					{
						$query .= " LIMIT 1";
					}
				break;
				default:
                	$query .= "AND v.version = '".$version."' ";
				break;
            }
        }
        else if($instance) {
            $query.= "WHERE v.instance='".$instance."' ";
        }
        $db->setQuery( $query );
        return $db->loadObjectList();
    }
}
?>
