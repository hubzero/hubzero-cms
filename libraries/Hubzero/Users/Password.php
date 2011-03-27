<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
class Hubzero_Users_Password
{
    private $user_id = null;
    private $passhash = null;
    private $shadowLastChange = null;
    private $shadowMin = array();
    private $shadowMax = null;
    private $shadowWarning = null;
    private $shadowInactive = null;
    private $shadowExpire = null;
    private $shadowFlag = null;

    private $_ldapPasswordMirror = true;
    private $_updateAll = false;

    static $_propertyattrmap = array("user_id"=>"uidNumber","passhash"=>"userPassword","shadowLastChange"=>"shadowLastChange",
                                     "shadowMin"=>"shadowMin","shadowMax"=>"shadowMax","shadowWarning"=>"shadowWarning",
                                     "shadowInactive"=>"shadowInactive","shadowExpire"=>"shadowExpire","shadowFlag"=>"shadowFlag");

    private $_updatedkeys = array();

    private function __construct()
    {
        $config = & JComponentHelper::getParams('com_members');
        $this->_ldapPasswordMirror = $config->get('ldap_save') == '1';
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

                $this->$key = null;
            }
        }

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
        $xhub = &XFactory::getHub();
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

            return $result;
        }

        return false;
    }

    public function getInstance($instance, $storage = null)
    {
        $hzup = new Hubzero_Users_Password();

        if ($hzup->read($instance, $storage) === false)
        {
            return false;
        }

        return $hzup;
    }

    public function createInstance($user_id)
    {
        if (empty($name))
        {
            return false;
        }

        $instance = new Hubzero_Users_Password();

        $instance->user_id = $user_id;

        if ($instance->create())
        {
            return $instance;
        }

        return false;
    }

    private function _ldap_create()
    {
		// @FIXME: should check if it exists in LDAP, return true if it does, otherwise false
		return false;
    }

    public function _mysql_create()
    {
        $db = &JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }

		// @FIXME: this should fail if id doesn't exist in jos_users

        if ($this->user_id > 0)
        {
            $query = "INSERT INTO #__users_password (user_id) VALUES ( " . $db->Quote($this->user_id) . ");";

            $db->setQuery();

            $result = $db->query();

            if ($result !== false || $db->getErrorNum() == 1062)
            {
                return true;
            }
        }

        return false;
    }

    public function create($storage = null)
    {
        if (is_null($storage))
        {
            $storage = ($this->_ldapPasswordMirror) ? 'all' : 'mysql';
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

    private function _ldap_read()
    {
        $xhub = &XFactory::getHub();
        $conn = &XFactory::getPLDC();

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        $dn = "uidNumber=" . $this->user_id . ",ou=users," . $xhub->getCfg('hubLDAPBaseDN');

        $reqattr = array('uidNumber', 'userPassword', 'shadowLastChange', 
						'shadowMin', 'shadowMax', 'shadowWarning', 
						'shadowInactive', 'shadowExpire', 'shadowFlag');

        $entry = @ldap_search($conn, $dn, "(objectClass=hubAccount)", $reqattr, 0, 0, 0, 3);

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
        $pwinfo = array();

        foreach ($reqattr as $key=>$value)
        {
            if (isset($attr[$reqattr[$key]][0]))
            {
                if (count($attr[$reqattr[$key]]) <= 2)
                {
                    $pwinfo[$value] = $attr[$reqattr[$key]][0];
                }
                else
                {
                    $pwinfo[$value] = $attr[$reqattr[$key]];
                    unset($pwinfo[$value]['count']);
                }
            }
            else
            {
                unset($pwinfo[$value]);
            }
        }

        $this->clear();

        foreach (self::$_propertyattrmap as $key=>$value)
        {
            if (isset($pwinfo[$value]))
            {
                $this->__set($key, $pwinfo[$value]);
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

        if (empty($db))
        {
            return false;
        }

        if ($this->user_id > 0)
        {
            $query = "SELECT user_id,passhash,shadowLastChange,shadowMin,shadowMax,shadowWarning,shadowInactive,shadowExpire,shadowFlag FROM #__users_password WHERE user_id=" . $db->Quote($this->user_id) . ";";
        }
        else
        {
			return false;
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

        $this->_updatedkeys = array();

        return true;
    }

    public function read($user_id = null, $storage = 'mysql')
    {
        if (is_null($storage))
        {
            $storage = 'mysql';
        }

        if (is_null($user_id))
        {
            $user_id = $this->user_id;

            if ($user_id <= 0)
            {
                $this->_error(__FUNCTION__ . ": invalid user id defined", E_USER_ERROR);
                die();
            }
        }

        if ($user_id <= 0)
        {
            $this->_error(__FUNCTION__ . ": Argument #1 is not numeric", E_USER_ERROR);
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
            $this->user_id = $user_id;

            $result = $this->_mysql_read();

            if ($result === false)
            {
                $this->clear();
            }
        }
        else if ($storage == 'ldap')
        {
            $this->clear();
            $this->user_id = $user_id;

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
        $errno = 0;

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        if ($this->user_id <= 0)
        {
            return false;
        }

        $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

        $pwinfo = $this->toArray('ldap');

        $current_hzup = Hubzero_Users_Password::getInstance($this->user_id, 'ldap');

        if (!is_object($current_hzup))
        {
            if ($this->_ldap_create() === false)
            {
                return false;
            }

            $current_hzup = Hubzero_Users_Password::getInstance($this->user_id, 'ldap');

            if (!is_object($current_hzup))
            {
                return false;
            }
        }

        $currentinfo = $current_hzup->toArray('ldap');

        $dn = 'uidNumber=' . $this->user_id . ',ou=users,' . $hubLDAPBaseDN;

        $replace_attr = array();
        $add_attr = array();
        $delete_attr = array();
        $_attrpropertymap = array_flip(self::$_propertyattrmap);

        // @FIXME Check for empty strings, use delete instead of replace as
        // LDAP disallows empty values

        foreach ($currentinfo as $key=>$value)
        {
            if (!$all && !in_array($_attrpropertymap[$key], $this->_updatedkeys))
            {
                continue;
            }
            else if ($pwinfo[$key] == array() && $currentinfo[$key] != array())
            {
                $delete_attr[$key] = array();
            }
            else if ($pwinfo[$key] != array() && $currentinfo[$key] == array())
            {
                $add_attr[$key] = $pwinfo[$key];
            }
            else if ($pwinfo[$key] != $currentinfo[$key])
            {
                $replace_attr[$key] = $pwinfo[$key];
            }
        }

        if (!@ldap_mod_replace($conn, $dn, $replace_attr))
        {
            $errno = @ldap_errno($conn);
        }
        if (!@ldap_mod_add($conn, $dn, $add_attr))
        {
            $errno = @ldap_errno($conn);
        }
        if (!@ldap_mod_del($conn, $dn, $delete_attr))
        {
            $errno = @ldap_errno($conn);
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

        $query = "UPDATE #__users_password SET ";

        $classvars = get_class_vars(__CLASS__);

        $first = true;

        foreach ($classvars as $property=>$value)
        {
            if (($property{0} == '_'))
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

        $query .= " WHERE `user_id`=" . $db->Quote($this->__get('user_id')) . ";";

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
            $storage = ($this->_ldapPasswordMirror) ? 'all' : 'mysql';
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
        $conn = & XFactory::getPLDC();
        $xhub = & XFactory::getHub();

		return false; 

		// WARNING: THIS WOULD BE BAD, it would delete the ldap account record
        // at best we could delete some/all of the password fields but even
        // that is questionable

        if (empty($conn) || empty($xhub))
        {
            return false;
        }

        if (empty($this->user_id))
        {
            return false;
        }

        $dn = "uidNumber=" . $this->user_id . ",ou=users," . $xhub->getCfg('hubLDAPBaseDN');

        if (!@ldap_delete($conn, $dn))
        {
            return false;
        }

        return true;
    }

    public function _mysql_delete()
    {
        if ($this->user_id <= 0)
        {
            return false;
        }

        $db = JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }

        if (!isset($this->user_id))
        {
            $db->setQuery("SELECT user_id FROM #__users_password WHERE user_id" .
                $db->Quote($this->user_id) . ";");

            $this->user_id = $db->loadResult();
        }

        if (empty($this->user_id))
        {
            return false;
        }

        $db->setQuery("DELETE FROM #__users_password WHERE user_id= " . $db->Quote($this->user_id) . ";");

        if (!$db->query())
        {
            return false;
        }

        return true;
    }

    public function delete($storage = null)
    {
        if (func_num_args() > 1)
        {
            $this->_error(__FUNCTION__ . ": Invalid number of arguments", E_USER_ERROR);
            die();
        }

        if (is_null($storage))
        {
            $storage = ($this->_ldapPasswordMirror) ? 'all' : 'mysql';
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
        if (!property_exists(__CLASS__, $property) || $property{0} == '_')
        {
            if (empty($property))
            {
                $property = '(null)';
            }

            $this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
            die();
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

        $this->$property = $value;

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
}

