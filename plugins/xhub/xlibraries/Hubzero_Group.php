<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.helper');
ximport('Hubzero_Validate');

class Hubzero_Group
{
    private $gidNumber = null;
    private $cn = null;
    private $description = null;
    private $published = null;
    private $type = null;
    private $access = null;
    private $public_desc = null;
    private $private_desc = null;
    private $restrict_msg = null;
    private $join_policy = null;
    private $privacy = null;
    private $members = array();
    private $managers = array();
    private $applicants = array();
    private $invitees = array();
    private $tracperm = array();

    private $_list_keys = array('members', 'managers', 'applicants', 'invitees', 'tracperm');
    private $_lists = array('add'=>array('managers'=>array(), 'applicants'=>array(),
        'members'=>array(), 'invitees'=>array()), 'delete'=>array('managers'=>array(),
        'applicants'=>array(), 'members'=>array(), 'invitees'=>array()));

    private $_ldapMirror = false;
    private $_updateAll = false;

    static $_propertyattrmap = array('gidNumber'=>'gidNumber', 'cn'=>'cn',
        'description'=>'description', 'published'=>'public', 'access'=>'privacy',
        'tracperm'=>'tracperm', 'members'=>'member', 'managers'=>'owner',
        'applicants'=>'applicant');

    private $_updatedkeys = array();

    private function __construct()
    {
        $config = & JComponentHelper::getParams('com_groups');
        $this->_ldapMirror = $config->get('ldapGroupMirror') == '1';
    }

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

        $this->_lists = array('add'=>array('managers'=>array(), 'applicants'=>array(),
            'members'=>array(), 'invitees'=>array()), 'delete'=>array('managers'=>array(),
            'applicants'=>array(), 'members'=>array(), 'invitees'=>array()));

        $this->_updateAll = false;
        $this->_updatedkeys = array();

        return true;
    }

    private function logDebug($msg)
    {
        $xlog = &XFactory::getLogger();
        $xlog->logDebug($msg);
    }

    public function toArray($format = 'mysql')
    {
        $xhub = &XFactory::getHub();
        $result = array();
        $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

        if ($format == 'mysql')
        {
            $cvars = get_class_vars(__CLASS__);

            foreach ($cvars as $key=>$value)
            {
                if ($key{0} == '_')
                {
                    continue;
                }

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

                $result[$value] = $current;
            }

            $current = $this->__get('type');

            if ($current == '0')
            {
                $result['closed'] = 'FALSE';
                $result['system'] = 'TRUE';
            }
            else if ($current == '1')
            {
                $result['closed'] = 'FALSE';
                $result['system'] = 'FALSE';
            }
            else if ($current == '2')
            {
                $result['closed'] = 'TRUE';
                $result['system'] = 'FALSE';
            }
            else
            {
                $result['closed'] = null;
                $result['system'] = null;
            }

            $current = $this->__get('access');

            if ($current == '0')
            {
                $result['privacy'] = '0';
            }
            else if ($current == '3')
            {
                $result['privacy'] = '1';
            }
            else if ($current == '4')
            {
                $result['privacy'] = '2';
            }
            else
            {
                $result['privacy'] = null;
            }

            $current = $this->__get('published');

            if ($current == '1')
            {
                $result['public'] = 'TRUE';
            }
            else if ($current == '0')
            {
                $result['public'] = 'FALSE';
            }
            else
            {
                $result['public'] = null;
            }

            foreach ($result['owner'] as $key=>$owner)
            {
                if (!empty($owner))
                {
                    $result['owner'][$key] = "uid=$owner,ou=users," . $hubLDAPBaseDN;
                }
            }

            foreach ($result['member'] as $key=>$member)
            {
                if (!empty($member))
                {
                    $result['member'][$key] = "uid=$member,ou=users," . $hubLDAPBaseDN;
                }
            }

            foreach ($result['applicant'] as $key=>$applicant)
            {
                if (!empty($applicant))
                {
                    $result['applicant'][$key] = "uid=$applicant,ou=users," . $hubLDAPBaseDN;
                }
            }

            return $result;
        }

        return false;
    }

    public function getInstance($instance, $storage = null)
    {
        $hzg = new Hubzero_Group();

        if ($hzg->read($instance, $storage) === false)
        {
            return false;
        }

        return $hzg;
    }

    public function createInstance($name)
    {
        if (empty($name))
        {
            return false;
        }

        $instance = new Hubzero_Group();

        $instance->cn = $name;

        if ($instance->create())
        {
            return $instance;
        }

        return false;
    }

    private function _ldap_create()
    {
        $xhub = &XFactory::getHub();
        $conn = &XFactory::getPLDC();

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        if (empty($this->cn))
        {
            return false;
        }

        $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

        $dn = 'gid=' . $this->cn . ',ou=groups,' . $hubLDAPBaseDN;
        $attr["objectclass"][0] = "top";
        $attr["objectclass"][1] = "posixGroup";
        $attr["objectclass"][2] = "hubGroup";
        $attr['gid'] = $this->cn;
        $attr['gidNumber'] = $this->gidNumber;
        $attr['cn'] = $this->cn;

        if (!ldap_add($conn, $dn, $attr) && @ldap_errno($conn) != 68)
        {
            return false;
        }

        return true;
    }

    private function _mysql_create()
    {
        $db = &JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }

        if (is_numeric($this->gidNumber))
        {
            $query = "INSERT INTO #__xgroups (gidNumber,cn) VALUES ( " .
                $db->Quote($this->gidNumber) . "," . $db->Quote($this->cn) . ");";

            $db->setQuery($query);

            $result = $db->query();

            if ($result !== false || $db->getErrorNum() == 1062)
            {
                return true;
            }
        }
        else
        {
            $query = "INSERT INTO #__xgroups (cn) VALUES ( " . $db->Quote($this->cn) . ");";

            $db->setQuery($query);

            $result = $db->loadResult();

            if ($result === false && $db->getErrorNum() == 1062)
            {
                $query = "SELECT gidNumber FROM #__xgroups WHERE cn=" . $db->Quote($this->cn) . ";";

                $db->setQeury($query);

                $result = $db->loadResult();

                if ($result == null)
                {
                    return false;
                }

                $this->gidNumber = $result;
                return true;
            }
            else if ($result !== false)
            {
                $this->gidNumber = $db->insertid();
                return true;
            }
        }

        return false;
    }

    public function create($storage = null)
    {
        if (is_null($storage))
        {
            $storage = ($this->_ldapMirror) ? 'all' : 'mysql';
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
        }

        if ($result === true && ($storage == 'ldap' || $storage == 'all'))
        {
            $result = $this->_ldap_create();

			if ($result == false) die('ldap group create');
        }

        return $result;
    }

    public function _ldap_read()
    {
        $xhub = &XFactory::getHub();
        $conn = &XFactory::getPLDC();

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        if (empty($this->cn) && empty($this->gidNumber))
        {
            return false;
        }

        $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

        if (!empty($this->gidNumber))
        {
            $dn = "ou=groups," . $hubLDAPBaseDN;
        }
        else
        {
            $dn = "gid=" . $this->cn . ",ou=groups," . $hubLDAPBaseDN;
        }

        if (!empty($this->gidNumber))
        {
            $filter = "(&(objectClass=hubGroup)(gidNumber=" . $this->gidNumber . "))";
        }
        else
        {
            $filter = "(objectClass=hubGroup)";
        }

        $entry = ldap_search($conn, $dn, $filter, array("*"), 0, 0, 0, 3);

        if (empty($entry))
        {
            return false;
        }

        $count = ldap_count_entries($conn, $entry);

        if ($count <= 0)
        {
            return false;
        }

        $firstentry = ldap_first_entry($conn, $entry);
        $attr = ldap_get_attributes($conn, $firstentry);

        for ($i = 0; $i < $attr['count']; $i++)
        {
            $key = $attr[$i];
            $value = $attr[$key];

            for ($j = 0; $j < $value['count']; $j++)
            {
                if ($value['count'] > 1)
                {
                    $info[$key][$j] = $value[$j];
                }
                else
                {
                    $info[$key] = $value[$j];
                }
            }
        }

        foreach (array('member', 'owner', 'applicant') as $list)
        {
            if (!empty($info[$list]))
            {
                if (!is_array($info[$list]))
                {
                    $info[$list] = array($info[$list]);
                }

                foreach ($info[$list] as $key=>$value)
                {
                    if (strncmp($value, "uid=", 4) == 0)
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

                        $info[$list][$key] = strtolower($value);
                    }
                }
            }
        }

        if (empty($info['system']))
        {
            $info['system'] = false;
        }

        if (empty($info['closed']))
        {
            $info['closed'] = false;
        }

        if (isset($info['public']) && $info['public'] == 'FALSE')
        {
            $info['public'] = '0';
        }

        if (isset($info['public']) && $info['public'] == 'TRUE')
        {
            $info['public'] = '1';
        }

        $this->clear();

        foreach (self::$_propertyattrmap as $key=>$value)
        {
            if (isset($info[$value]))
            {
                $this->__set($key, $info[$value]);
            }
            else
            {
                $this->__set($key, null);
            }
        }

        if ($info['system'] == 'TRUE' || ($info['closed'] === false && $info['system'] === false))
        {
            $this->__set('type', '0'); // system
        }
        elseif (($info['system'] == 'FALSE' && ($info['closed'] == 'FALSE' ||
            $info['closed'] === false)) || ($info['system'] === false &&
            $info['closed'] == 'FALSE'))
        {
            $this->__set('type', '1'); // hub
        }
        elseif (($info['closed'] == 'TRUE') && ($info['system'] === false ||
            $info['system'] === 'FALSE'))
        {
            $this->__set('type', '2'); // project
        }
        else
        {
            $this->__set('type', '1'); // hub
        }

        if (!isset($info['privacy']))
        {
            $this->__set('access', null);
        }
        else if ($info['privacy'] == '0')
        {
            $this->__set('access', '0');
        }
        else if ($info['privacy'] == '1')
        {
            $this->__set('access', '3');
        }
        else if ($info['privacy'] == '2')
        {
            $this->__set('access', '4');
        }
        else
        {
            $this->__set('access', null);
        }

        $this->_updatedkeys = array();
        return true;
    }

    public function _mysql_read()
    {
        $db = &JFactory::getDBO();

        $lazyloading = false;

        if (empty($db))
        {
            return false;
        }

        if (is_numeric($this->gidNumber))
        {
            $query = "SELECT * FROM #__xgroups WHERE gidNumber = " .
                $db->Quote($this->gidNumber) . ";";
        }
        else
        {
            $query = "SELECT * FROM #__xgroups WHERE cn = " . $db->Quote($this->cn) . ";";
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
            if (property_exists(__CLASS__, $key) && $key{0} != '_')
            {
                $this->__set($key, $value);
            }
        }

        $this->__unset('members');
        $this->__unset('invitees');
        $this->__unset('applicants');
        $this->__unset('managers');
        $this->__unset('tracperm');

        if (!$lazyloading)
        {
            $this->__get('members');
            $this->__get('invitees');
            $this->__get('applicants');
            $this->__get('managers');
            $this->__get('tracperm');
        }

        $this->_updatedkeys = array();

        return true;
    }

    public function read($name = null, $storage = 'mysql')
    {
        if (!is_null($name) && !is_string($name) && !is_integer($name))
        {
            $this->_error(__FUNCTION__ . ": Argument #1 is not a valid string or integer",
                E_USER_ERROR);
            die();
        }

        if (!is_null($storage) && !is_string($storage))
        {
            $this->_error(__FUNCTION__ . ": Argument #2 is not a string", E_USER_ERROR);
            die();
        }

        if (!in_array($storage, array('mysql', 'ldap', null)))
        {
            $this->_error(__FUNCTION__ . ": Argument #2 [$storage] is not a valid value",
                E_USER_ERROR);
            die();
        }

        if (!is_null($name))
        {
            $this->clear();

            if (Hubzero_Validate::is_positive_integer($name))
            {
                $this->gidNumber = $name;
            }
            else
            {
                $this->cn = $name;
            }
        }

        $result = true;

        if (is_null($storage) || $storage == 'mysql')
        {
            $result = $this->_mysql_read();
        }
        else if ($storage == 'ldap')
        {
            $result = $this->_ldap_read();
        }
        else
        {
            $result = false;
        }

        if ($result === false)
        {
            $this->clear();
        }

        return $result;
    }

    private function _ldap_update($all = false)
    {
        $xhub = &XFactory::getHub();
        $conn = &XFactory::getPLDC();
        $errno = 0;

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        if (empty($this->cn))
        {
            return false;
        }

        $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

        $info = $this->toArray('ldap');

        $current_hzg = Hubzero_Group::getInstance($this->cn, 'ldap');

        if (!is_object($current_hzg))
        {
            if ($this->_ldap_create() == false)
            {
                return false;
            }

            $current_hzg = Hubzero_Group::getInstance($this->cn, 'ldap');

            if (!is_object($current_hzg))
            {
                return false;
            }
        }

        $currentinfo = $current_hzg->toArray('ldap');

        $dn = 'gid=' . $this->cn . ',ou=groups,' . $hubLDAPBaseDN;

        $_attrpropertymap = array_flip(self::$_propertyattrmap);
        $_attrpropertymap['closed'] = 'type';
        $_attrpropertymap['system'] = 'type';

        // @FIXME Check for empty strings, use delete instead of replace as
        // LDAP disallows empty values


        foreach ($currentinfo as $key=>$value)
        {
            if (!$all && !in_array($_attrpropertymap[$key], $this->_updatedkeys))
            {
                continue;
            }
            else if (is_null($info[$key]) && !is_null($currentinfo[$key]))
            {
                $delete_attr[$key] = array();
            }
            else if (!is_null($info[$key]) && is_null($currentinfo[$key]))
            {
                $add_attr[$key] = $info[$key];
            }
            else if ($info[$key] != $currentinfo[$key])
            {
                $replace_attr[$key] = $info[$key];
            }
        }

        if (isset($replace_attr) && !ldap_mod_replace($conn, $dn, $replace_attr))
        {
			var_dump($dn);
			var_dump($replace_attr);
            $errno = ldap_errno($conn);
        }

        if (isset($add_attr) && !ldap_mod_add($conn, $dn, $add_attr))
        {
            $errno = ldap_errno($conn);
        }

        if (isset($delete_attr) && !ldap_mod_del($conn, $dn, $delete_attr))
        {
            $errno = ldap_errno($conn);
        }

        if ($errno != 0)
        {
            return false;
        }

        return true;
    }

    function _mysql_update($all = false)
    {
        $db = &JFactory::getDBO();

        $query = "UPDATE #__xgroups SET ";

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

        $query .= " WHERE `gidNumber`=" . $db->Quote($this->__get('gidNumber')) . ";";

        if ($first == true)
        {
            $query = '';
        }

        if (!empty($query))
        {
            $db->setQuery($query);

            $result = $db->query();

            if ($result === false)
            {
                return false;
            }

            $affected = mysql_affected_rows($db->_resource);

            if ($affected < 1)
            {
                $this->_mysql_create();

                $db->setQuery($query);

                $result = $db->query();

                if ($result === false)
                {
                    return false;
                }

                $affected = mysql_affected_rows($db->_resource);

                if ($affected < 1)
                {
                    return false;
                }
            }
        }

        foreach ($this->_list_keys as $property)
        {
            if (!$all && !in_array($property, $this->_updatedkeys))
            {
                continue;
            }

            $aux_table = "#__xgroups_" . $property;

            $list = $this->__get($property);

            if (!is_null($list) && !is_array($list))
            {
                $list = array($list);
            }

            $ulist = null;
            $tlist = null;

            foreach ($list as $value)
            {
                if (!is_null($ulist))
                {
                    $ulist .= ',';
                    $tlist .= ',';
                }

                $ulist .= $db->Quote($value);
                $tlist .= '(' . $db->Quote($this->gidNumber) . ',' . $db->Quote($value) . ')';
            }

            if (is_array($list) && count($list) > 0)
            {
                if ($property == 'tracperm')
                {
                    $query = "REPLACE INTO $aux_table (group_id, action) VALUES $tlist;";
                }
                else if (in_array($property, array('members', 'managers', 'applicants',
                    'invitees')))
                {
                    $query = "REPLACE INTO $aux_table (gidNumber,uidNumber) SELECT " .
                        $db->Quote($this->gidNumber) . ",id FROM #__users WHERE " .
                        " username IN ($ulist);";
                }

                $db->setQuery($query);

                if (!$db->query())
                {
                    return false;
                }

            }

            if (!is_array($list) || count($list) == 0)
            {
                if ($property == 'tracperm')
                {
                    $query = "DELETE FROM $aux_table WHERE group_id=" .
                        $db->Quote($this->gidNumber) . ";";
                }
                else if (in_array($property, array('members', 'managers', 'applicants',
                    'invitees')))
                {
                    $query = "DELETE FROM $aux_table WHERE gidNumber=" .
                        $db->Quote($this->gidNumber) . ";";
                }
            }
            else
            {
                if ($property == 'tracperm')
                {
                    $query = "DELETE FROM $aux_table WHERE group_id=" .
                        $db->Quote($this->gidNumber) . " AND action NOT IN ($ulist);";
                }
                else if (in_array($property, array('members', 'managers', 'applicants',
                    'invitees')))
                {
                    $query = "DELETE m FROM #__xgroups_$property AS m, #__users AS u WHERE " .
                        " m.gidNumber=" . $db->Quote($this->gidNumber) .
                        " AND m.uidNumber=u.id AND u.username NOT IN (" . $ulist . ");";
                }
            }

            $db->setQuery($query);

            if (!$db->query())
            {
                return false;
            }
        }

        return true;
    }

    public function sync()
    {
        $this->_updateAll = true;

        return $this->update();
    }

    public function syncldap()
    {
        $this->_updateAll = true;

        return $this->update('ldap');
    }

    public function update($storage = null)
    {
        if (is_null($storage))
        {
            $storage = ($this->_ldapMirror) ? 'all' : 'mysql';
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

    private function _ldap_delete()
    {
        $conn = &XFactory::getPLDC();
        $xhub = &XFactory::getHub();

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        if (!isset($this->instance))
        {
            return false;
        }

        $dn = "gid=" . $this->cn . ",ou=groups," . $xhub->getCfg('hubLDAPBaseDN');

        if (!@ldap_delete($conn, $dn))
        {
            return false;
        }

        return true;
    }

    private function _mysql_delete()
    {
        if (!isset($this->cn) && !isset($this->gidNumber))
        {
            return false;
        }

        $db = JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }

        if (!isset($this->gidNumber))
        {
            $db->setQuery("SELECT gidNumber FROM #__xgroups WHERE cn=" . $db->Quote($this->cn) .
                ";");

            $this->gidNumber = $db->loadResult();
        }

        if (empty($this->gidNumber))
        {
            return false;
        }

        $db->setQuery("DELETE FROM #__xgroups WHERE gidNumber=" . $db->Quote($this->gidNumber) .
            ";");

        if (!$db->query())
        {
            return false;
        }

        $db->setQuery("DELETE FROM #__xgroups_applicants WHERE gidNumber=" .
            $db->Quote($this->gidNumber) . ";");
        $db->query();
        $db->setQuery("DELETE FROM #__xgroups_invitees WHERE gidNumber=" .
            $db->Quote($this->gidNumber) . ";");
        $db->query();
        $db->setQuery("DELETE FROM #__xgroups_managers WHERE gidNumber=" .
            $db->Quote($this->gidNumber) . ";");
        $db->query();
        $db->setQuery("DELETE FROM #__xgroups_members WHERE gidNumber=" .
            $db->Quote($this->gidNumber) . ";");
        $db->query();
        $db->setQuery("DELETE FROM #__xgroups_tracperm WHERE group_id=" .
            $db->Quote($this->gidNumber) . ";");
        $db->query();

        return true;
    }

    public function delete($storage = null)
    {
        $xlog = &XFactory::getLogger();

        if (func_num_args() > 1)
        {
            $this->_error(__FUNCTION__ . ": Invalid number of arguments", E_USER_ERROR);
            die();
        }

        if (is_null($storage))
        {
            $storage = ($this->_ldapMirror) ? 'all' : 'mysql';
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

        if ($storage == 'mysql' || $storage == 'all')
        {
            $result = $this->_mysql_delete();

            if ($result === false)
            {
                $this->_error(__FUNCTION__ . ": MySQL deletion failed", E_USER_WARNING);
                return false;
            }
        }

        if ($result === true && ($storage == 'ldap' || $storage == 'all'))
        {
            $result = $this->_ldap_delete();

            if ($result === false)
            {
                $this->_error(__FUNCTION__ . ": LDAP deletion failed", E_USER_WARNING);
                return false;
            }
        }

        return true;
    }

    private function __get($property = null)
    {
        $xlog = &XFactory::getLogger();

        if (!property_exists(__CLASS__, $property) || $property{0} == '_')
        {
            if (empty($property))
            {
                $property = '(null)';
            }

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
                    if (in_array($property, array('tracperm')))
                    {
                        $aux_table = "#__xgroups_tracperm";

                        $query = "SELECT action FROM $aux_table AS aux WHERE aux.group_id=" .
                            $db->Quote($this->gidNumber) . " ORDER BY action" . " ASC;";
                    }
                    else if (in_array($property, array('members', 'applicants', 'managers',
                        'invitees')))
                    {
                        $aux_table = "#__xgroups_" . $property;
                        $query = "SELECT u.username FROM $aux_table AS aux,#__users AS u " .
                            " WHERE u.id=aux.uidNumber AND " . " aux.gidNumber=" .
                            $db->Quote($this->gidNumber) . " ORDER BY uidNumber ASC;";
                    }
                    else
                    {
                        $query = null;
                    }

                    $db->setQuery($query);

                    $result = $db->loadResultArray();

                    if ($result !== false)
                    {
                        $this->__set($property, $result);
                    }
                }
            }
        }

        if (isset($this->$property))
        {
            return $this->$property;
        }

        if (array_key_exists($property, get_object_vars($this)))
        {
            return null;
        }

        $this->_error("Undefined property " . __CLASS__ . "::$" . $property, E_USER_NOTICE);

        return null;
    }

    private function __set($property = null, $value = null)
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

        if (in_array($property, $this->_list_keys))
        {
			$value = array_diff((array) $value, array(''));
			$value = array_unique($value);
			$value = array_values($value);
            $this->$property =  $value;
        }
        else
        {
            $this->$property = $value;
        }

        if (!in_array($property, $this->_updatedkeys))
        {
            $this->_updatedkeys[] = $property;
        }
    }

    private function __isset($property = null)
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

        return isset($this->$property);
    }

    private function __unset($property = null)
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

        $this->_updatedkeys = array_diff($this->_updatedkeys, array($property));

        unset($this->$property);
    }

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

    public function get($key)
    {
        return $this->__get($key);
    }

    public function set($key, $value)
    {
        return $this->__set($key, $value);
    }

    public function add($key = null, $value = array())
    {
        $this->__set($key, array_merge($this->__get($key), (array) $value));
    }

    public function remove($key = null, $value = array())
    {
        $this->__set($key, array_diff($this->__get($key), (array) $value));
    }

    static function iterate($func, $storage = 'mysql')
    {
        $db = &JFactory::getDBO();

        if (!in_array($storage, array('mysql', 'ldap', null)))
        {
            return false;
        }

        if ($storage == 'ldap')
        {
            $xhub = &XFactory::getHub();
            $conn = &XFactory::getPLDC();

            $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

            $dn = 'ou=groups,' . $hubLDAPBaseDN;
            $filter = '(objectclass=hubGroup)';

            $attributes[] = 'cn';

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

            while ($entry !== false)
            {
                $attributes = ldap_get_attributes($conn, $entry);
                call_user_func($func, $attributes['cn'][0]);
                $entry = ldap_next_entry($conn, $entry);
            }
        }
        else if ($storage == 'mysql' || is_null($storage))
        {
            $query = "SELECT cn FROM #__xgroups;";

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
}
?>
