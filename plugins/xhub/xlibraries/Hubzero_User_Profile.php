<?php
/**
 * @package     HUBzero CMS
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2
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

class Hubzero_User_Profile
{
    private $uidNumber = null;
    private $name = null;
    private $username = null;
    private $email = null;
    private $registerDate = null;
    private $gidNumber = null;
    private $homeDirectory = null;
    private $loginShell = null;
    private $ftpShell = null;
    private $userPassword = null;
    private $shadowExpire = null;
    private $gid = null;
    private $orgtype = null;
    private $organization = null;
    private $countryresident = null;
    private $countryorigin = null;
    private $gender = null;
    private $url = null;
    private $reason = null;
    private $mailPreferenceOption = null;
    private $usageAgreement = null;
    private $jobsAllowed = null;
    private $modifiedDate = null;
    private $emailConfirmed = null;
    private $regIP = null;
    private $regHost = null;
    private $nativeTribe = null;
    private $phone = null;
    private $proxyPassword = null;
    private $proxyUidNumber = null;
    private $givenName = null;
    private $middleName = null;
    private $surname = null;
    private $picture = null;
    private $vip = null;
    private $public = null;
    private $params = null;
    private $note = null;
    private $bio = null;
    private $disability = array();
    private $hispanic = array();
    private $race = array();
    private $admin = array();
    private $host = array();
    private $manager = array();
    private $edulevel = array();
    private $role = array();

    private $_password = null;
    private $_params = null;

    static $_aux_keys = array('bio');
    static $_list_keys = array('disability', 'hispanic', 'race', 'admin', 'host', 'manager',
        'edulevel', 'role');
    static $_lists = array();

    private $_ldapMirror = false;
    private $_updateAll = false;

    static $_propertyattrmap = array('username'=>'uid', 'name'=>'cn', 'uidNumber'=>'uidNumber',
        'gidNumber'=>'gidNumber', 'homeDirectory'=>'homeDirectory', 'email'=>'mail',
        'registerDate'=>'regDate', 'loginShell'=>'loginShell', 'ftpShell'=>'ftpShell',
        'userPassword'=>'userPassword', 'gid'=>'gid', 'orgtype'=>'orgtype', 'organization'=>'o',
        'countryresident'=>'countryresident', 'countryorigin'=>'countryorigin', 'gender'=>'sex',
        'url'=>'url', 'reason'=>'description', 'mailPreferenceOption'=>'mailPreferenceOption',
        'usageAgreement'=>'usageAgreement', 'jobsAllowed'=>'jobsAllowed', 'modifiedDate'=>'modDate',
        'emailConfirmed'=>'emailConfirmed', 'regIP'=>'regIP', 'regHost'=>'regHost',
        'nativeTribe'=>'nativeTribe', 'phone'=>'homePhone', 'proxyUidNumber'=>'proxyUidNumber',
        'proxyPassword'=>'proxyPassword', 'disability'=>'disability', 'hispanic'=>'hispanic',
        'race'=>'race', 'admin'=>'admin', 'host'=>'host', 'edulevel'=>'edulevel', 'role'=>'role',
        'shadowExpire'=>'shadowExpire');

    private $_updatedkeys = array();

    private function __construct()
    {
        $config = & JComponentHelper::getParams('com_members');
        $this->_ldapMirror = $config->get('ldapProfileMirror') == '1';
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

        $this->_lists = array();

        $this->_updateAll = false;
        $this->_updatedkeys = array();
    }

    private function logDebug($msg)
    {
        $xlog = &XFactory::getLogger();
        $xlog->logDebug($msg);
    }

    public function toArray($format = 'mysql')
    {
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

            return $result;
        }

        return false;
    }

    public function getInstance($instance, $storage = null)
    {
        $hzup = new Hubzero_User_Profile();

        if ($hzup->read($instance, $storage) === false)
        {
            return false;
        }

        return $hzup;
    }

    public function createInstance($name)
    {
        if (empty($name))
        {
            return false;
        }

        $instance = new Hubzero_User_Profile();

        $instance->username = $name;

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

        $dn = 'uid=' . $this->username . ',ou=users,' . $hubLDAPBaseDN;
        $attr['objectclass'][] = 'top';
        $attr['objectclass'][] = 'person';
        $attr['objectclass'][] = 'organizationalPerson';
        $attr['objectclass'][] = 'inetOrgPerson';
        $attr['objectclass'][] = 'posixAccount';
        $attr['objectclass'][] = 'shadowAccount';
        $attr['objectclass'][] = 'hubAccount';
        $attr['uid'] = $this->username;

        if (!@ldap_add($conn, $dn, $attr) && @ldap_errno($conn) != 68)
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

        if (is_numeric($this->uidNumber))
        {
            $query = "INSERT INTO #__xprofile (uidNumber,username) VALUES ( " .
                $db->Quote($this->uidNumber) . "," . $db->Quote($this->username) . ");";

            $db->setQuery($query);

            $result = $db->query();

            if ($result !== false || $db->getErrorNum() == 1062)
            {
                return true;
            }
        }
        else
        {
            $query = "INSERT INTO #__xprofile (username) VALUES ( " .
                $db->Quote($this->username) . ");";

            $db->setQuery($query);

            $result = $db->loadResult();

            if ($result === false && $db->getErrorNum() == 1062)
            {
                $query = "SELECT uidNumber FROM #__xprofiles WHERE username=" .
                    $db->Quote($this->username) . ";";

                $db->setQeury($query);

                $result = $db->loadResult();

                if ($result == null)
                {
                    return false;
                }

                $this->uidNumber = $result;
                return true;
            }
            else if ($result !== false)
            {
                $this->uidNumber = $db->insertid();
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

            return false;
        }

        if ($result === true && ($storage == 'ldap' || $storage == 'all'))
        {
            $result = $this->_ldap_create();

            return false;
        }

        return $result;
    }

    private function _ldap_read()
    {
        $xhub = &XFactory::getHub();
        $conn = &XFactory::getPLDC();
        $xlog = &XFactory::getLogger();

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        if (empty($this->username) && empty($this->uidNumber))
        {
            return false;
        }

        $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

        if (!empty($this->uidNumber))
        {
            $dn = "ou=userss," . $hubLDAPBaseDN;
        }
        else
        {
            $dn = "uid=" . $this->username . ",ou=userss," . $xhubLDAPBaseDN;
        }

        if (!empty($this->uidNumber))
        {
            $filter = "(&(objectClass=hubUser)(uidNumber=" . $this->uidNumber . "))";
        }
        else
        {
            $filter = "(objectClass=hubUser)";
        }

        $entry = @ldap_search($conn, $dn, $filter, array('*'), 0, 0, 0, 3);

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

        $this->_updatedkeys = array();

        return true;
    }

    private function _mysql_read()
    {
        $db = JFactory::getDBO();

        $lazyloading = false;

        if (empty($db))
        {
            return false;
        }

        if (is_numeric($this->uidNumber))
        {
            $query = "SELECT * FROM #__xprofile WHERE uidNumber=" . $db->Quote($this->uidNumber) .
                ";";
        }
        else
        {
            $query = "SELECT * FROM #__xprofile WHERE username=" .
                $db->Quote($this->username) . ";";
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

        $this->__unset('bio');
        $this->__unset('disability');
        $this->__unset('hispanic');
        $this->__unset('race');
        $this->__unset('admin');
        $this->__unset('host');
        $this->__unset('manager');
        $this->__unset('edulevel');
        $this->__unset('role');

        if (!$lazyloading)
        {
            $this->__get('bio');
            $this->__get('disability');
            $this->__get('hispanic');
            $this->__get('race');
            $this->__get('admin');
            $this->__get('host');
            $this->__get('manager');
            $this->__get('edulevel');
            $this->__get('role');
        }

        $this->_params = new JParameter('');
        $this->_params->loadINI($this->params);

        $this->_updatedkeys = array();

        return true;
    }

    private function _load_author($authorid)
    {
        static $_propertyauthormap = array('uidNumber'=>'id', 'givenName'=>'firstname',
            'middleName'=>'middlename', 'surname'=>'lastname', 'organization'=>'org',
            'bio'=>'bio', 'url'=>'url', 'picture'=>'picture', 'vip'=>'principal_investigator');

        $db = & JFactory::getDBO();
        $xhub = &XFactory::getHub();

        $query = "SELECT * FROM #__author WHERE id=" . $db->Quote($authorid);

        $db->setQuery($query);

        $result = $db->loadAssoc();

        if ($result === false)
        {
            return false;
        }

        $this->clear();

        foreach ($_propertyauthormap as $property=>$aproperty)
            if (isset($result[$aproperty]))
                $this->__set($property, $result[$aproperty]);

        return true;
    }

    private function _load_xregistration($registration)
    {
        static $_propertyregmap = array('username'=>'login', 'name'=>'name', 'email'=>'email',
            'orgtype'=>'orgtype', 'organization'=>'org', 'countryresident'=>'countryresident',
            'countryorigin'=>'countryorigin', 'gender'=>'sex', 'url'=>'web', 'reason'=>'reason',
            'mailPreferenceOption'=>'mailPreferenceOption', 'usageAgreement'=>'usageAgreement',
            'nativeTribe'=>'nativeTribe', 'phone'=>'phone', 'disability'=>'disability',
            'hispanic'=>'hispanic', 'race'=>'race', 'admin'=>'admin', 'host'=>'host',
            'edulevel'=>'edulevel', 'role'=>'role');

        if (!is_object($registration))
        {
            return false;
        }

        foreach ($_propertyregmap as $property=>$rproperty)
        {
            if ($registration->get($rproperty) !== null)
            {
                $this->_set($property, $registration->get($rproperty));
            }
        }

        $this->__set('mailPreferenceOption', $this->mailPreferenceOption ? '2' : '0');
        $this->__set('usageAgreement', $this->usageAgreement ? '1' : '0');

        return true;
    }

    public function read($instance = null, $storage = 'mysql')
    {
        if (is_null($storage))
        {
            $storage = 'mysql';
        }

        if (is_null($instance))
        {
            $instance = $this->username;

            if (!empty($instance) && !is_string($instance) && !is_numeric($instance))
            {
                $this->_error(__FUNCTION__ . ": invalid user instance defined",
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
            $this->username = $instance;

            $result = $this->_ldap_read();

            if ($result === false)
            {
                $this->clear();
            }
        }

        return $result;
    }

    private function _ldap_update($all = false)
    {
        $xhub = &XFactory::getHub();
        $conn = &XFactory::getPLDC();
        $xlog = &XFactory::getLogger();

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        if (empty($this->instance))
        {
            return false;
        }

        $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

        $info = $this->toArray('ldap');

        $current_hzup = Hubzero_User_Profile::getInstance($this->username, 'ldap');

        if (!is_object($current_hzup))
        {
            if ($this->_ldap_create() === false)
            {
                $xlog->logDebug(__FUNCTION__ . "() " . $this->username .
                    " doesn't exist and create failed.");
                return false;
            }

            $current_hzup = Hubzero_User_Profile::getInstance($this->username, 'ldap');

            if (!is_object($current_hzup))
            {
                $xlog->logDebug(__FUNCTION__ . "() " . $this->username .
                    " created but doesn't read back.");
                return false;
            }
        }

        $currentinfo = $current_hzup->toArray('ldap');

        $dn = 'uid=' . $this->username . ',ou=users,' . $hubLDAPBaseDN;

        $_attrpropertymap = array_flip(self::$_propertyattrmap);

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
                if ($info[$key] != '')
                {
                    $add_attr[$key] = $info[$key];
                }
            }
            else if ($info[$key] != $currentinfo[$key])
            {
                $replace_attr[$key] = $info[$key];
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

    private function _mysql_update($all = false)
    {
        $db = &JFactory::getDBO();

        $query = "UPDATE #__xprofiles SET ";

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

        $query .= " WHERE `uidNumber`=" . $db->Quote($this->__get('uidNumber')) . ";";

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

            $aux_table = "#__xprofiles_" . $property;

            $list = $this->__get($property);

            if (!is_null($list) && !is_array($list))
            {
                $list = array($list);
            }

            if (is_array($list) && count($list) > 0)
            {
                $first = true;

                $query = "REPLACE INTO $aux_table (uidNumber, " . $property .
                        ") VALUES ";

                $order = 0;

                foreach ($list as $value)
                {
                    if (!$first)
                    {
                        $query .= ',';
                    }

                    $first = false;

                    $query .= '(' . $db->Quote($this->uidNumber) . ',' . $db->Quote($value) . ')';

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
                $query = "DELETE FROM $aux_table WHERE uidNumber=" .
                        $db->Quote($this->uidNumber) . ";";
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

                $query = "DELETE FROM $aux_table WHERE uidNumber=" .
                        $db->Quote($this->uidNumber) . " AND $property NOT IN ($valuelist);";
            }

            $db->setQuery($query);

            if (!$db->query())
            {
                return false;
            }
        }

        return true;
    }

    public function update($storage = null)
    {
        if (!empty($storage) && !in_array($storage, array('mysql', 'ldap')))
        {
            $this->setError('Invalid storage option requested [' . $storage . ']');
            return false;
        }

        $mconfig = & JComponentHelper::getParams('com_members');
        $ldapProfileMirror = $mconfig->get('ldapProfileMirror');

        $params = $this->_params;
        $this->params = (is_object($params)) ? $params->toString() : '';

        $modifiedDate = gmdate('Y-m-d H:i:s');
        $this->set('modifiedDate', $modifiedDate);

        if (empty($storage))
            $storage = ($ldapProfileMirror) ? 'all' : 'mysql';

        if ($storage == 'mysql' || $storage == 'all')
            if ($this->_mysql_update() === false)
                return false;

        if (($storage == 'ldap' || $storage == 'all'))
            if ($this->_ldap_update() === false)
                return false;

        return true;
    }

    public function store($updateOnly = false, $storage = null)
    {
        $db = &JFactory::getDBO();

        if (!empty($storage) && !in_array($storage, array('mysql', 'ldap')))
        {
            $this->setError('Invalid storage option requested [' . $storage . ']');
            return false;
        }

        $mconfig = & JComponentHelper::getParams('com_members');
        $ldapProfileMirror = $mconfig->get('ldapProfileMirror');

        $modifiedDate = gmdate('Y-m-d H:i:s');
        $this->set('modifiedDate', $modifiedDate);

        if (empty($storage))
            $storage = ($ldapProfileMirror) ? 'all' : 'mysql';

        if ($updateOnly && $this->get('uidNumber') == '')
        {
            $this->setError('No uidNumber property set for updateOnly action');
            return false;
        }

        if ($storage == 'all' || $storage == 'mysql')
        {
            $mysql_insert = false;

            if (!$updateOnly)
            {
                $query = "SELECT uidNumber FROM #__xprofiles WHERE uidNumber=" . $db->Quote($this->get('uidNumber'));

                $db->setQuery($query);

                if (!$db->query())
                {
                    $this->setError("Error retrieving data from xprofiles table: " . $db->getErrorMsg());

                    return false;
                }

                $result = $db->loadResult();
                $mysql_insert = empty($result);
            }

            if ($mysql_insert == true)
            {
                if ($this->_mysql_create() === false)
                    return false;
            }
            else if ($this->_mysql_update() === false)
                return false;
        }

        if ($storage == 'all' || $storage == 'ldap')
        {
            $userinfo = $this->_ldap_get_user($this->get('uidNumber'));

            if ($userinfo === false)
            {
                if ($this->_ldap_create() === false)
                    return false;
            }
            else if ($this->_ldap_update() === false)
                return false;
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

    public function syncmysql()
    {
        $this->_updateAll = true;
        return $this->update('mysql');
    }

    public function update($storage = null)
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

    function getParam($key, $default = null)
    {
        return $this->_params->get($key, $default);
    }

    function setParam($key, $value)
    {
        return $this->_params->set($key, $value);
    }

    function defParam($key, $value)
    {
        return $this->_params->def($key, $value);
    }

    function &getParameters()
    {
        return $this->_params;
    }

    function setParameters($params)
    {
        $this->_params = $params;
    }

    private function _ldap_delete()
    {
        $conn = &XFactory::getPLDC();
        $xhub = &XFactory::getHub();

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        if (!isset($this->username))
        {
            return false;
        }

        $dn = "uid=" . $this->username . ",ou=users," . $xhub->getCfg('hubLDAPBaseDN');

        if (!@ldap_delete($conn, $dn))
        {
            return false;
        }

        return true;
    }

    private function _mysql_delete()
    {
        if (!isset($this->uidNumber) && !isset($this->username))
        {
            return false;
        }

        $db = JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }

        if (!isset($this->uidNumber))
        {
            $db->setQuery("SELECT uidNumber FROM #__xprofiles WHERE username=" .
                $db->Quote($this->username) . ";");

            $this->uidNumber = $db->loadResult();
        }

        if (empty($this->uidNumber))
        {
            return false;
        }

        $db->setQuery("DELETE FROM #__xprofiles WHERE uidNumber=" . $db->Quote($this->uidNumber) .
            ";");

        if (!$db->query())
        {
            return false;
        }

        foreach($this->_list_keys as $property=>$value)
        {
            $db->setQuery("DELETE FROM #__xprofiles_$property WHERE uidNumber=" .
                $db->Quote($this->uidNumber) . ";");
            $db->query();
        }

        $db->setQuery("DELETE FROM #__xprofiles_bio WHERE uidNumber=" .
            $db->Quote($this->uidNumber) . ";");
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

    private function __get($property = null)
    {
        $xlog = &XFactory::getLogger();

        if ($property == 'password')
        {
            return $this->_password;
        }
        else if (!property_exists(__CLASS__, $property) || $property{0} == '_')
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
                    if (in_array($property, $this->_list_keys))
                    {
                        $aux_table = "#__xprofiles_" . $property;

                        $query = "SELECT $property FROM $aux_table AS aux WHERE " .
                            " aux.uidNumber=" . $db->Quote($this->uidNumber) .
                            " ORDER BY $property" . " ASC;";
                    }
                    else if ($property == 'bio')
                    {
                        $aux_table = "#__xprofiles_" . $property;

                        $query = "SELECT $property FROM $aux_table AS aux WHERE " .
                            " aux.uidNumber=" . $db->Quote($this->uidNumber) .
                            " ORDER BY $property" . " ASC LIMIT 1;";
                    }
                    else
                    {
                        $query = null;
                    }

                    $db->setQuery($query);

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

    private function __set($property = null, $value = null)
    {
        if ($property == 'password')
        {
            if ($value != '')
                $this->__set('userPassword', "{MD5}" . base64_encode(pack('H*', md5($value))));
            else
                $this->__set('userPassword','');

            $this->_password = $value;
        }
        else if (!property_exists(__CLASS__, $property) || $property{0} == '_')
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
            $this->$property = array_values(array_unique(array_diff((array) $value, array(''))));
        }
        else
        {
            if ($property == 'userPassword')
            {
                $this->_password = '';
            }

            $this->$property = $value;
        }

        if (!in_array($property, $this->_updatedkeys))
            $this->_updatedkeys[] = $property;
    }

    private function __isset($property = null)
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

    private function __unset($property = null)
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

    static public function iterate($func, $storage)
    {
        $db = &JFactory::getDBO();

        if (!empty($storage) && !in_array($storage, array('mysql', 'ldap')))
            return false;

        if ($storage == 'ldap')
        {
            $xhub = &XFactory::getHub();
            $conn = &XFactory::getPLDC();

            $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

            $dn = 'ou=users,' . $hubLDAPBaseDN;
            $filter = '(objectclass=posixAccount)';

            $attributes[] = 'uid';

            $sr = @ldap_search($conn, $dn, $filter, $attributes, 0, 0, 0);

            if ($sr === false)
                return false;

            $count = @ldap_count_entries($conn, $sr);

            if ($count === false)
                return false;

            $entry = @ldap_first_entry($conn, $sr);

            do
            {
                $attributes = ldap_get_attributes($conn, $entry);
                $func($attributes['uid'][0]);
                $entry = @ldap_next_entry($conn, $entry);
            }
            while ($entry !== false);
        }

        if ($storage == 'mysql')
        {
            $query = "SELECT uidNumber FROM #__xprofiles;";

            $db->setQuery($query);

            $result = $db->query();

            if ($result === false)
            {
                $this->setError('Error retrieving data from xprofiles table: ' . $db->getErrorMsg());
                return false;
            }

            while ($row = mysql_fetch_row($result))
                $func($row[0]);

            mysql_free_result($result);
        }

        return true;
    }

    static public function delete_profile($user, $storage)
    {
        if (!empty($storage) && !in_array($storage, array('mysql', 'ldap')))
            return false;

        $mconfig = & JComponentHelper::getParams('com_members');
        $ldapProfileMirror = $mconfig->get('ldapProfileMirror');

        if (empty($storage))
            $storage = ($ldapProfileMirror) ? 'all' : 'mysql';

        $profile = new XProfile();

        if ($storage == 'mysql' || $storage == 'all')
        {
            $profile->load($user, 'mysql');
            $profile->delete('mysql');
        }

        if ($storage == 'ldap' || $storage == 'all')
        {
            $profile->load($user, 'ldap');
            $profile->delete('ldap');
        }
    }
}
?>
