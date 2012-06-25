<?php
/**
 * HUBzero CMS
 *
 * Copyright 2010-2012 Purdue University. All rights reserved.
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
 * @copyright Copyright 2010-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Hubzero_User_Password'
 * 
 * Long description (if any) ...
 */
class Hubzero_User_Password
{

	/**
	 * Description for 'user_id'
	 * 
	 * @var mixed
	 */
	private $user_id = null;

	/**
	 * Description for 'passhash'
	 * 
	 * @var unknown
	 */
	private $passhash = null;

	/**
	 * Description for 'shadowLastChange'
	 * 
	 * @var unknown
	 */
	private $shadowLastChange = null;

	/**
	 * Description for 'shadowMin'
	 * 
	 * @var array
	 */
	private $shadowMin = array();

	/**
	 * Description for 'shadowMax'
	 * 
	 * @var unknown
	 */
	private $shadowMax = null;

	/**
	 * Description for 'shadowWarning'
	 * 
	 * @var unknown
	 */
	private $shadowWarning = null;

	/**
	 * Description for 'shadowInactive'
	 * 
	 * @var unknown
	 */
	private $shadowInactive = null;

	/**
	 * Description for 'shadowExpire'
	 * 
	 * @var unknown
	 */
	private $shadowExpire = null;

	/**
	 * Description for 'shadowFlag'
	 * 
	 * @var unknown
	 */
	private $shadowFlag = null;

	/**
	 * Description for '_uid'
	 * 
	 * @var unknown
	 */
	private $_uid = null;

	/**
	 * Description for '_ldapPasswordMirror'
	 * 
	 * @var boolean
	 */
	private $_ldapPasswordMirror = true;

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
	static $_propertyattrmap = array("user_id"=>"uidNumber","passhash"=>"userPassword","shadowLastChange"=>"shadowLastChange",
									 "shadowMin"=>"shadowMin","shadowMax"=>"shadowMax","shadowWarning"=>"shadowWarning",
									 "shadowInactive"=>"shadowInactive","shadowExpire"=>"shadowExpire","shadowFlag"=>"shadowFlag");

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
	 * @return	   void
	 */
	private function __construct()
	{
		$config = & JComponentHelper::getParams('com_members');
		$this->_ldapPasswordMirror = $config->get('ldapProfileMirror') == '1';
	}

	/**
	 * Short description for 'clear'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	   void
	 */
	public function clear()
	{
		$cvars = get_class_vars(__CLASS__);

		$this->_updatedkeys = array();

		foreach ($cvars as $key=>$value) {
			if ($key{0} != '_') {
				unset($this->$key);

				$this->$key = null;
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
	 * @param	   unknown $msg Parameter description (if any) ...
	 * @return	   void
	 */
	private function logDebug($msg)
	{
		$xlog = &Hubzero_Factory::getLogger();
		$xlog->logDebug($msg);
	}

	/**
	 * Short description for 'toArray'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	   string $format Parameter description (if any) ...
	 * @return	   mixed Return description (if any) ...
	 */
	public function toArray($format = 'mysql')
	{
		$xhub = &Hubzero_Factory::getHub();
		$result = array();
		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		if ($format == 'mysql') {
			foreach (self::$_propertyattrmap as $key=>$value) {
				$current = $this->__get($key);

				$result[$key] = $current;
			}

			return $result;
		}
		else if ($format == 'ldap') {
			foreach (self::$_propertyattrmap as $key=>$value) {
				$current = $this->__get($key);

				if (isset($current) && !is_null($current)) {
					$result[$value] = $current;
				}
				else {
					$result[$value] = array();
				}
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
	 * @param	   unknown $instance Parameter description (if any) ...
	 * @param	   unknown $storage Parameter description (if any) ...
	 * @return	   mixed Return description (if any) ...
	 */
	public function getInstance($instance, $storage = null)
	{
		$hzup = new Hubzero_User_Password();

		if ($hzup->read($instance, $storage) === false) {
			return false;
		}

		return $hzup;
	}

	/**
	 * Short description for 'createInstance'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	   unknown $user_id Parameter description (if any) ...
	 * @return	   mixed Return description (if any) ...
	 */
	public function createInstance($user_id)
	{
		if (empty($name)) {
			return false;
		}

		$instance = new Hubzero_User_Password();

		$instance->user_id = $user_id;

		if ($instance->create()) {
			return $instance;
		}

		return false;
	}

	/**
	 * Short description for '_ldap_create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	   boolean Return description (if any) ...
	 */
	private function _ldap_create()
	{
		// @FIXME: should check if it exists in LDAP, return true if it does, otherwise false
		return false;
	}

	/**
	 * Short description for '_mysql_create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	   boolean Return description (if any) ...
	 */
	public function _mysql_create()
	{
		$db = &JFactory::getDBO();

		if (empty($db)) {
			return false;
		}

		// @FIXME: this should fail if id doesn't exist in jos_users

		if ($this->user_id > 0) {
			$query = "INSERT INTO #__users_password (user_id) VALUES ( " . $db->Quote($this->user_id) . ");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result !== false || $db->getErrorNum() == 1062) {
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
	 * @param	   string $storage Parameter description (if any) ...
	 * @return	   boolean Return description (if any) ...
	 */
	public function create($storage = null)
	{
		if (is_null($storage)) {
			$storage = ($this->_ldapPasswordMirror) ? 'all' : 'mysql';
		}

		if (!is_string($storage)) {
			$this->_error(__FUNCTION__ . ": Argument #1 is not a string", E_USER_ERROR);
			die();
		}

		if (!in_array($storage, array('mysql', 'ldap', 'all'))) {
			$this->_error(__FUNCTION__ . ": Argument #1 [$storage] is not a valid value", E_USER_ERROR);
			die();
		}

		$result = true;

		if ($storage == 'mysql' || $storage == 'all') {
			$result = $this->_mysql_create();

			if ($result === false) {
				$this->_error(__FUNCTION__ . ": MySQL create failed", E_USER_WARNING);
			}
		}

		if ($result === true && ($storage == 'ldap' || $storage == 'all')) {
			$result = $this->_ldap_create();

			if ($result === false) {
				$this->_error(__FUNCTION__ . ": LDAP create failed", E_USER_WARNING);
			}
		}

		return $result;
	}

	/**
	 * Short description for '_mysql_read'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	   boolean Return description (if any) ...
	 */
	private function _mysql_read($instance = null)
	{
		$db = JFactory::getDBO();

		if (empty($db) || empty($instance)) {
			return false;
		}

		if (is_numeric($instance)) {
			if ($instance <= 0) {
				return false;
			}

			$query = "SELECT user_id,passhash,shadowLastChange,shadowMin," . 
				"shadowMax,shadowWarning,shadowInactive,shadowExpire," .
				"shadowFlag FROM #__users_password WHERE user_id=" . 
				$db->Quote($instance) . ";";
		}
		else {
			$query = "SELECT user_id,passhash,shadowLastChange,shadowMin," .
				"shadowMax,shadowWarning,shadowInactive,shadowExpire," .
				"shadowFlag FROM #__users_password,#__users WHERE user_id=id" .
				" AND username=" . $db->Quote($instance) . ";";
		}

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if (empty($result)) {
			return false;
		}

		$this->clear();

		foreach ($result as $key=>$value) {
			$this->__set($key, $value);
		}

		$this->_updatedkeys = array();

		return true;
	}

	/**
	 * Short description for 'read'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	   integer $user_id Parameter description (if any) ...
	 * @param	   string $storage Parameter description (if any) ...
	 * @return	   boolean Return description (if any) ...
	 */
	public function read($instance = null, $storage = 'all')
	{
		if (is_null($storage)) {
			$storage = 'all';
		}

		if (empty($instance)) {
			$instance = $this->user_id;

			if (empty($instance)) {
				$this->_error(__FUNCTION__ . ": invalid user instance defined", E_USER_ERROR);
				die();
			}
		}

		if (!is_string($storage)) {
			$this->_error(__FUNCTION__ . ": Argument #2 is not a string", E_USER_ERROR);
			die();
		}

		if (!in_array($storage, array('mysql', 'ldap', 'all'))) {
			$this->_error(__FUNCTION__ .  ": Argument #2 [$storage] is not a valid value", E_USER_ERROR);
			die();
		}

		$result = true;

		if ($storage == 'mysql' || $storage == 'all') {
			$this->clear();

			$result = $this->_mysql_read($instance);

			if ($result === false) {
				$this->clear();
			
				$hzp = Hubzero_User_Profile::getInstance($instance);
			
				if (is_object($hzp)) {
			
					$this->user_id = $hzp->get('uidNumber');
					$this->passhash = $hzp->get('userPassword');
			
					$this->create('mysql');
					$this->update('mysql');
					
					return true;
				}
			}
			else {
				return $result;
			}
		}

		if ($storage == 'ldap' || $storage == 'all') {
			$this->clear();

			$result = $this->_ldap_read($instance);

			if ($result === false) {
				$this->clear();
			}
			else if ($storage == 'all') {
				$this->create('mysql');
				$this->update('mysql');
			}
		}

		return $result;
	}
	
	
	/**
	 * Short description for '_ldap_read'
	 *
	 * Long description (if any) ...
	 *
	 * @return         boolean Return description (if any) ...
	 */
	private function _ldap_read($instance = null)
	{
		$xhub = &Hubzero_Factory::getHub();
		$conn = &Hubzero_Factory::getPLDC();
	
		if (empty($conn) || empty($xhub) || empty($instance)) {
			return false;
		}
	
		if (is_numeric($instance) && $instance > 0) {
			$dn = "ou=users," .  $xhub->getCfg('hubLDAPBaseDN');
			$filter = '(uidNumber=' . $instance . ')';
		}
		else {
			$dn = "uid=" . $instance . ",ou=users," .  $xhub->getCfg('hubLDAPBaseDN');
			$filter = '(objectclass=*)';
		}
	
		$reqattr = array('uidNumber', 'userPassword', 'shadowLastChange',
				'shadowMin', 'shadowMax', 'shadowWarning',
				'shadowInactive', 'shadowExpire', 'shadowFlag', 'uid');
	
		$entry = @ldap_search($conn, $dn, $filter, $reqattr,
				0, 0, 0, 3);
	
		if (empty($entry)) {
			return false;
		}
	
		$count = ldap_count_entries($conn, $entry);
	
		if ($count <= 0) {
			return false;
		}
	
		$firstentry = ldap_first_entry($conn, $entry);
		$attr = ldap_get_attributes($conn, $firstentry);
		$pwinfo = array();
	
		foreach ($reqattr as $key=>$value) {
			if (isset($attr[$reqattr[$key]][0])) {
				if (count($attr[$reqattr[$key]]) <= 2) {
					$pwinfo[$value] = $attr[$reqattr[$key]][0];
				}
				else {
					$pwinfo[$value] = $attr[$reqattr[$key]];
					unset($pwinfo[$value]['count']);
				}
			}
			else {
				unset($pwinfo[$value]);
			}
		}
	
		$this->clear();
	
		foreach (self::$_propertyattrmap as $key=>$value) {
			if (isset($pwinfo[$value])) {
				$this->__set($key, $pwinfo[$value]);
			}
			else {
				$this->__set($key, null);
			}
		}
	
		$this->_uid = $pwinfo['uid'];
	
		$this->_updatedkeys = array();
	
		return true;
	}
	
	/**
	 * Short description for '_ldap_update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	   boolean $all Parameter description (if any) ...
	 * @return	   boolean Return description (if any) ...
	 */
	private function _ldap_update($all = false)
	{
		$xhub = &Hubzero_Factory::getHub();
		$conn = &Hubzero_Factory::getPLDC();
		$errno = 0;

		if (empty($conn) || empty($xhub)) {
			return false;
		}

		if ($this->user_id <= 0) {
			return false;
		}

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		$pwinfo = $this->toArray('ldap');

		$current_hzup = self::getInstance($this->user_id, 'ldap');

		if (!is_object($current_hzup)) {
			if ($this->_ldap_create() === false) {
				return false;
			}

			$current_hzup = Hubzero_User_Password::getInstance($this->user_id, 'ldap');

			if (!is_object($current_hzup)) {
				return false;
			}
		}

		$currentinfo = $current_hzup->toArray('ldap');

		$dn = 'uid=' . $current_hzup->_uid . ',ou=users,' . $hubLDAPBaseDN;

		$replace_attr = array();
		$add_attr = array();
		$delete_attr = array();
		$_attrpropertymap = array_flip(self::$_propertyattrmap);

		// @FIXME Check for empty strings, use delete instead of replace as
		// LDAP disallows empty values

		foreach ($currentinfo as $key=>$value) {
			if (!$all && !in_array($_attrpropertymap[$key], $this->_updatedkeys)) {
				continue;
			}
			else if ($pwinfo[$key] == array() && $currentinfo[$key] != array()) {
				$delete_attr[$key] = array();
			}
			else if ($pwinfo[$key] != array() && $currentinfo[$key] == array()) {
				$add_attr[$key] = $pwinfo[$key];
			}
			else if ($pwinfo[$key] != $currentinfo[$key]) {
				$replace_attr[$key] = $pwinfo[$key];
			}
		}

		if (!@ldap_mod_replace($conn, $dn, $replace_attr)) {
			$errno = @ldap_errno($conn);
		}
		if (!@ldap_mod_add($conn, $dn, $add_attr)) {
			$errno = @ldap_errno($conn);
		}
		if (!@ldap_mod_del($conn, $dn, $delete_attr)) {
			$errno = @ldap_errno($conn);
		}

		if ($errno != 0) {
			return false;
		}

		return true;
	}

	/**
	 * Short description for '_mysql_update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	   boolean $all Parameter description (if any) ...
	 * @return	   boolean Return description (if any) ...
	 */
	function _mysql_update($all = false, $create_on_fail = true)
	{
		$db = &JFactory::getDBO();

		$query = "UPDATE #__users_password SET ";

		$classvars = get_class_vars(__CLASS__);

		$first = true;

		foreach ($classvars as $property=>$value) {
			if (($property{0} == '_')) {
				continue;
			}

			if (!$all && !in_array($property, $this->_updatedkeys)) {
				continue;
			}

			if (!$first) {
				$query .= ',';
			}
			else {
				$first = false;
			}

			$value = $this->__get($property);

			if ($value === null) {
				$query .= "`$property`=NULL";
			}
			else {
				$query .= "`$property`=" . $db->Quote($value);
			}
		}

		$query .= " WHERE `user_id`=" . $db->Quote($this->__get('user_id')) .  " LIMIT 1;";

		if ($first == true) {
			$query = '';
		}

		$db->setQuery($query);

		if (!empty($query)) {
			$result = $db->query();

			if ($result === false) {
				return false;
			}
		}

		if ($db->getAffectedRows() <= 0) {
			if ($create_on_fail) {
				$this->_mysql_create();
				return $this->_mysql_update($all,false);
			}

			return false;
		}

		return true;
	}

	/**
	 * Short description for 'sync'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	   unknown Return description (if any) ...
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
	 * @return	   string Return description (if any) ...
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
	 * @param	   string $storage Parameter description (if any) ...
	 * @return	   boolean Return description (if any) ...
	 */
	public function update($storage = null)
	{
		if (is_null($storage)) {
			$storage = ($this->_ldapPasswordMirror) ? 'all' : 'mysql';
		}

		if (!is_string($storage)) {
			$this->_error(__FUNCTION__ . ": Argument #1 is not a string", E_USER_ERROR);
			die();
		}

		if (!in_array($storage, array('mysql', 'ldap', 'all'))) {
			$this->_error(__FUNCTION__ . ": Argument #1 [$storage] is not a valid value", E_USER_ERROR);
			die();
		}

		$result = true;

		if ($storage == 'mysql' || $storage == 'all') {
			$result = $this->_mysql_update($this->_updateAll);

			if ($result === false) {
				$this->_error(__FUNCTION__ . ": MySQL update failed", E_USER_WARNING);
			}
		}

		if ($result === true && ($storage == 'ldap' || $storage == 'all')) {
			$result = $this->_ldap_update($this->_updateAll);

			if ($result === false) {
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
	 * @return	   boolean Return description (if any) ...
	 */
	private function _ldap_delete()
	{
		$conn = & Hubzero_Factory::getPLDC();
		$xhub = & Hubzero_Factory::getHub();

		return false;

		// WARNING: THIS WOULD BE BAD, it would delete the ldap account record
		// at best we could delete some/all of the password fields but even
		// that is questionable

		if (empty($conn) || empty($xhub)) {
			return false;
		}

		if (empty($this->user_id)) {
			return false;
		}

		$dn = "uidNumber=" . $this->user_id . ",ou=users," . $xhub->getCfg('hubLDAPBaseDN');

		if (!@ldap_delete($conn, $dn)) {
			return false;
		}

		return true;
	}

	/**
	 * Short description for '_mysql_delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return	   boolean Return description (if any) ...
	 */
	public function _mysql_delete()
	{
		if ($this->user_id <= 0) {
			return false;
		}

		$db = JFactory::getDBO();

		if (empty($db)) {
			return false;
		}

		if (!isset($this->user_id)) {
			$db->setQuery("SELECT user_id FROM #__users_password WHERE user_id" .
				$db->Quote($this->user_id) . ";");

			$this->user_id = $db->loadResult();
		}

		if (empty($this->user_id)) {
			return false;
		}

		$db->setQuery("DELETE FROM #__users_password WHERE user_id= " . $db->Quote($this->user_id) . ";");

		if (!$db->query()) {
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	   string $storage Parameter description (if any) ...
	 * @return	   boolean Return description (if any) ...
	 */
	public function delete($storage = null)
	{
		if (func_num_args() > 1) {
			$this->_error(__FUNCTION__ . ": Invalid number of arguments", E_USER_ERROR);
			die();
		}

		if (is_null($storage)) {
			$storage = ($this->_ldapPasswordMirror) ? 'all' : 'mysql';
		}

		if (!is_string($storage)) {
			$this->_error(__FUNCTION__ . ": Argument #1 is not a string", E_USER_ERROR);
			die();
		}

		if (!in_array($storage, array('mysql', 'ldap', 'all'))) {
			$this->_error(__FUNCTION__ . ": Argument #1 [$storage] is not a valid value", E_USER_ERROR);
			die();
		}

		$result = true;

		if ($storage == 'mysql' || $storage == 'all') {
			$result = $this->_mysql_delete();

			if ($result === false) {
				$this->_error(__FUNCTION__ . ": MySQL deletion failed", E_USER_WARNING);
			}
		}

		if ($result === true && ($storage == 'ldap' || $storage == 'all')) {
			$result = $this->_ldap_delete();

			if ($result === false) {
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
	 * @param	   string $property Parameter description (if any) ...
	 * @return	   string Return description (if any) ...
	 */
	public function __get($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_') {
			if (empty($property)) {
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		if (isset($this->$property)) {
			return $this->$property;
		}

		if (array_key_exists($property, get_object_vars($this))) {
			return null;
		}

		$this->_error("Undefined property " . __CLASS__ . "::$" . $property, E_USER_NOTICE);

		return null;
	}

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	   string $property Parameter description (if any) ...
	 * @param	   unknown $value Parameter description (if any) ...
	 * @return	   void
	 */
	public function __set($property = null, $value = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_') {
			if (empty($property)) {
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		$this->$property = $value;

		if (!in_array($property, $this->_updatedkeys)) {
			$this->_updatedkeys[] = $property;
		}
	}
	
	/**
	 * Short description for '__isset'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param	   string $property Parameter description (if any) ...
	 * @return	   string Return description (if any) ...
	 */
	public function __isset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_') {
			if (empty($property)) {
				$property = '(null)';
			}

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
	 * @param	   string $property Parameter description (if any) ...
	 * @return	   void
	 */
	public function __unset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_') {
			if (empty($property)) {
				$property = '(null)';
			}

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
	 * @param	   string $message Parameter description (if any) ...
	 * @param	   integer $level Parameter description (if any) ...
	 * @return	   void
	 */
	private function _error($message, $level = E_USER_NOTICE)
	{
		$caller = next(debug_backtrace());

		switch ($level) {
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
	 * @param	   unknown $key Parameter description (if any) ...
	 * @return	   unknown Return description (if any) ...
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
	 * @param	   unknown $key Parameter description (if any) ...
	 * @param	   unknown $value Parameter description (if any) ...
	 * @return	   unknown Return description (if any) ...
	 */
	public function set($key, $value)
	{
		return $this->__set($key, $value);
	}

	public function isPasswordExpired($user = null)
	{
		$hzup = self::getInstance($user);

		if (!is_object($hzup)) {
			return false;
		}

		if (empty($hzup->shadowLastChange)) {
			return false;
		}

		if ($hzup->shadowMax === '0') {
			return true;
		}

		if (empty($hzup->shadowMax)) {
			return false;
		}

		$chgtime = time();
		$chgtime = intval($chgtime / 86400);
		if ( ($hzup->shadowLastChange + $hzup->shadowMax) >= $chgtime) {
			return false;
		}

		return true;
	}

	public function changePassword($user = null, $password)
	{
		$passhash =  "{MD5}" . base64_encode(pack('H*', md5($password)));

		return self::changePasshash($user,$passhash);
	}

	public function changePasshash($user = null, $passhash)
	{
		ximport('Hubzero_User_Password_History');
		ximport('Hubzero_User_Profile');

		$hzup = self::getInstance($user);
		
		$oldhash = $hzup->__get('passhash');
				
		$hzup->__set('passhash',$passhash);
		$hzup->__set('shadowFlag',null);

		$chgtime = time();
		$chgtime = intval($chgtime / 86400);
		$hzup->__set('shadowLastChange', $chgtime);
		$hzup->__set('shadowMin', '0');
		$hzup->__set('shadowMax', '120');
		$hzup->__set('shadowWarning', '7');
		$hzup->__set('shadowInactive', '0');
		$hzup->__set('shadowExpire', null);
		$hzup->update();
		
		$hzp = Hubzero_User_Profile::getInstance($user);
		$hzp->set('userPassword', $passhash);
		$hzp->update('mysql');
		
		$jtu = JTable::getInstance('user');
		$jtu->load($hzup->user_id);
		$jtu->set('password',$passhash);
		$jtu->store();
		
		if (!empty($oldhash)) {
			Hubzero_User_Password_History::addPassword($oldhash, $user);
		}

		return true;
	}
	
	public function comparePasswords($passhash, $password)
	{
		if (empty($passhash) || empty($password)) {
			return false;
		}
		
		preg_match("/^\s*(\{(.*)\}\s*|)((.*?)\s*:\s*|)(.*?)\s*$/",$passhash,$matches);
		
		$encryption = strtolower($matches[2]);
		
		if (empty($encryption)) {		// Joomla
			$encryption = "md5-hex";
			
			if (!empty($matches[4])) {	// Joomla 1.5
				$crypt = $matches[4];
				$salt = $matches[5];
			} else {					// Joomla 1.0
				$crypt = $matches[5];
				$salt = '';
			}
		} else {
			$salt = $matches[4];
			$crypt = $matches[5];
		}

		if ($encryption == 'md5') {
			$encryption = "md5-base64";
		}
		
		if (empty($salt) && ($encryption == 'ssha')) {
			$salt = substr(base64_decode(substr($crypt,-32)),-4);
			$hashed = base64_encode( mhash(MHASH_SHA1, $password . $salt).$salt );
		}
		else {
			jimport('joomla.user.helper');
			$hashed = JUserHelper::getCryptedPassword($password, $salt, $encryption);
		}
		
		return ($crypt == $hashed);
	}

	public function passwordMatches($user = null, $password, $alltables = false)
	{
		$passhash = null;
		
		$hzup = self::getInstance($user);

		if (is_object($hzup) && !empty($hzup->passhash)) {
			$passhash = $hzup->passhash;
		}
		else if ($alltables) { 

			$profile = Hubzero_User_Profile::getInstance($user);
		
			if (is_object($profile) && ($profile->get('userPassword') != '')) {
				$passhash = $profile->get('userPassword');
			}
			else {
			
				$user = JUser::getInstance($user);

				if (is_object($user) && !empty($user->password)) {
					$passhash = $user->password;
				}
			}
		}
		
		return self::comparePasswords($passhash, $password);
	}

	public function invalidatePassword($user = null)
	{
		$hzup = self::getInstance($user);
		
		$hzup->__set('shadowFlag','-1');
		$hzup->update();
	
		return true;
	}
}
