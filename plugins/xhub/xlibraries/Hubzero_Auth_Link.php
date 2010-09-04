<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2010 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 *
 * Copyright 2010 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 3 as published by the Free Software Foundation.
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

class Hubzero_Auth_Link
{
	private $id;
	private $user_id;
	private $auth_domain_id;
	private $username;
	private $email;
	private $password;
	private $params;
	private $_updatedkeys = array();
	private $_updateAll = false;
   
	private function __construct()
    {
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

    public function getInstance($auth_domain_id, $username)
    {
        $hzal = new Hubzero_Auth_Link();
		$hzal->auth_domain_id = $auth_domain_id;
		$hzal->username = $username;
		
        $hzal->read();
 		if (!$hzal->id)
        {
            return false;
        }

        return $hzal;
    }
    
    public function find_by_id($id)
    {
		$hzal = new Hubzero_Auth_Link();
		$hzal->id = $id;
		
        $hzal->read();
        
        if (empty($hzal->auth_domain_id))
			return false;
			
        return $hzal;
    	
    }

    public function createInstance($auth_domain_id,$username)
    {
        if (empty($auth_domain_id) || empty($username))
        {
            return false;
        }

        $instance = new Hubzero_Auth_Link();

        $instance->auth_domain_id = $auth_domain_id;
        $instance->username = $username;

        $instance->create();
        
        if (!$instance->id)
        {
            return false;
        }

        return $instance;
    }
    
    public function create()
    {
        $db = &JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }
        
        if (is_numeric($this->id))
        {
            $query = "INSERT INTO #__auth_link (id,user_id,auth_domain_id,username,email,password,params) VALUES ( " 
            	. $db->Quote($this->id) .
                "," . $db->Quote($this->user_id) . 
                "," . $db->Quote($this->auth_domain_id) . 
                "," . $db->Quote($this->username) . 
            	"," . $db->Quote($this->email) . 
            	"," . $db->Quote($this->password) . 
            	"," . $db->Quote($this->params) . 
				");";

            $db->setQuery();

            $result = $db->query();

            if ($result !== false || $db->getErrorNum() == 1062)
            {
                return true;
            }
        }
        else
        {
            $query = "INSERT INTO #__auth_link (user_id,auth_domain_id,username,email,password,params) VALUES ( " 
            	. $db->Quote($this->user_id) . 
                "," . $db->Quote($this->auth_domain_id) . 
                "," . $db->Quote($this->username) . 
            	"," . $db->Quote($this->email) . 
            	"," . $db->Quote($this->password) . 
            	"," . $db->Quote($this->params) . 
				");";
            	
            $db->setQuery($query);

            $result = $db->query();

            if ($result === false && $db->getErrorNum() == 1062)
            {
                $query = "SELECT id FROM #__auth_link WHERE " .
                	"auth_domain_id=" . $db->Quote($this->auth_domain_id) . " AND " .
                	"user_id=" . $db->Quote($this->user_id) . ";";

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
		
	public function read()
    {
        $db = JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }

        if (is_numeric($this->id))
        {
            $query = "SELECT id,user_id,auth_domain_id,username,email,password,params FROM #__auth_link WHERE id=" .
                $db->Quote($this->id) . ";";
        }
        else if (is_numeric($this->user_id))
        {
            $query = "SELECT id,user_id,auth_domain_id,username,email,password,params FROM #__auth_link WHERE " .
                " user_id=" . $db->Quote($this->user_id) . " AND auth_domain_id=" . $db->Quote($this->auth_domain_id) . ";";
        }
        else if (is_string($this->username))
        {
        	$query = "SELECT id,user_id,auth_domain_id,username,email,password,params FROM #__auth_link WHERE " .
                " username=" . $db->Quote($this->username) . " AND auth_domain_id=" . $db->Quote($this->auth_domain_id) . ";";
        	
        }
        
        if (empty($query)) {
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
		
 	function update($all = false)
    {
        $db = &JFactory::getDBO();

        $query = "UPDATE #__auth_link SET ";

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

        return true;
    }
		
	public function delete()
    {
        if (!isset($this->toolname) && !isset($this->id))
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
            $db->setQuery("SELECT id FROM #__auth_link WHERE user_id=" .
                $db->Quote($this->user_id) . " AND auth_domain_id=" . $db->Quote($this->auth_domain_id) . ";");

            $this->id = $db->loadResult();
        }

        if (empty($this->id))
        {
            return false;
        }

        $db->setQuery("DELETE FROM #__auth_link WHERE id= " . $db->Quote($this->id) . ";");

        if (!$db->query())
        {
            return false;
        }

        return true;
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
    
    public function delete_by_user_id($uid = null)
    {
    	if (empty($uid))
    		return true;
    		
		$db = JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }
    	
    	$db->setQuery("DELETE FROM #__auth_link WHERE user_id= " . $db->Quote($uid) . ";");

        if (!$db->query())
        {
            return false;
        }

        return true;
    }
      
    public function find_or_create($type,$authenticator,$domain,$username)
    {
    	$hzad = Hubzero_Auth_Domain::find_or_create($type,$authenticator,$domain);
    	
    	if (!is_object($hzad))
    		return false;
    		
    	if (empty($username))
    		return false;
    		 	
    	$hzal = new Hubzero_Auth_Link();
    	$hzal->username = $username;
    	$hzal->auth_domain_id = $hzad->id;
		$hzal->read();

		if (empty($hzal->id) && !$hzal->create())
			return false;
			
		return $hzal;
	}
	
	public function find_trusted_emails( $user_id )
	{
		if (empty($user_id))
			return false;
			
		if (!is_numeric($user_id))
			return false;
			
		$db = JFactory::getDBO();
		
		if (empty($db))
			return false;
			
		$sql = "SELECT email FROM #__auth_link WHERE user_id = " . $db->Quote($user_id) . ";";

		$db->setQuery($sql);
		
        $result = $db->loadResultArray();

        if (empty($result))
        {
            return false;
        }
        
        return $result;
	}
}
