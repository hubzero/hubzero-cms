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
ximport('Hubzero_Validate');

/**
 * Short description for 'Hubzero_Group'
 * 
 * Long description (if any) ...
 */
class Hubzero_Group
{

	/**
	 * Description for 'gidNumber'
	 * 
	 * @var unknown
	 */
	private $gidNumber = null;

	/**
	 * Description for 'cn'
	 * 
	 * @var string
	 */
	private $cn = null;

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
	private $description = null;

	/**
	 * Description for 'published'
	 * 
	 * @var unknown
	 */
	private $published = null;

	/**
	 * Description for 'type'
	 * 
	 * @var unknown
	 */
	private $type = null;

	/**
	 * Description for 'access'
	 * 
	 * @var unknown
	 */
	private $access = null;

	/**
	 * Description for 'public_desc'
	 * 
	 * @var unknown
	 */
	private $public_desc = null;

	/**
	 * Description for 'private_desc'
	 * 
	 * @var unknown
	 */
	private $private_desc = null;

	/**
	 * Description for 'restrict_msg'
	 * 
	 * @var unknown
	 */
	private $restrict_msg = null;

	/**
	 * Description for 'join_policy'
	 * 
	 * @var unknown
	 */
	private $join_policy = null;

	/**
	 * Description for 'privacy'
	 * 
	 * @var unknown
	 */
	private $privacy = null;

	/**
	 * Description for 'members'
	 * 
	 * @var array
	 */
	private $members = array();

	/**
	 * Description for 'managers'
	 * 
	 * @var array
	 */
	private $managers = array();

	/**
	 * Description for 'applicants'
	 * 
	 * @var array
	 */
	private $applicants = array();

	/**
	 * Description for 'invitees'
	 * 
	 * @var array
	 */
	private $invitees = array();

	/**
	 * Description for 'tracperm'
	 * 
	 * @var array
	 */
	private $tracperm = array();

	/**
	 * Description for '_list_keys'
	 * 
	 * @var array
	 */
	private $_list_keys = array('members', 'managers', 'applicants', 'invitees', 'tracperm');

	/**
	 * Description for '_ldapMirror'
	 * 
	 * @var boolean
	 */
	private $_ldapMirror = false;

	/**
	 * Description for '_ldapLegacy'
	 * 
	 * @var boolean
	 */
	private $_ldapLegacy = true;

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
	private $_propertyattrmap = array('gidNumber'=>'gidNumber', 'cn'=>'cn', 'members'=>'memberUid');

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
	public function __construct()
	{
		$config = & JComponentHelper::getParams('com_groups');
		$this->_ldapMirror = $config->get('ldapGroupMirror') == '1';
		$this->_ldapLegacy = $config->get('ldapGroupLegacy') == '1';

		if ($this->_ldapLegacy) {
			$this->_propertyattrmap['members'] = 'member';
			$this->_propertyattrmap['managers'] = 'owner';
			$this->_propertyattrmap['tracperm'] = 'tracperm';
			$this->_propertyattrmap['description'] = 'description';
			$this->_propertyattrmap['published'] = 'public';
			$this->_propertyattrmap['access'] = 'privacy';
			$this->_propertyattrmap['applicants'] = 'applicant';
		}
	}

	/**
	 * Short description for 'clear'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function clear()
	{
		$cvars = get_class_vars(__CLASS__);

		$this->_updatedkeys = array();

		foreach ($cvars as $key=>$value) {
			if ($key{0} != '_') {
				unset($this->$key);

				if (!in_array($key, $this->_list_keys)) {
					$this->$key = null;
				}
				else {
					$this->$key = array();
				}
			}
		}

		$this->_updateAll = false;
		$this->_updatedkeys = array();

		return true;
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

		if ($format == 'mysql') {
			$cvars = get_class_vars(__CLASS__);

			foreach ($cvars as $key=>$value) {
				if ($key{0} == '_') {
					continue;
				}

				$current = $this->__get($key);

				$result[$key] = $current;
			}

			return $result;
		}
		else if ($format == 'ldap') {

			foreach ($this->_propertyattrmap as $key=>$value) {
				$current = $this->__get($key);

				$result[$value] = $current;
			}

			if ($this->_ldapLegacy) {
				$current = $this->__get('type');

				if ($current == '0') {
					$result['closed'] = 'FALSE';
					$result['system'] = 'TRUE';
				}
				else if ($current == '1') {
					$result['closed'] = 'FALSE';
					$result['system'] = 'FALSE';
				}
				else if ($current == '2') {
					$result['closed'] = 'TRUE';
					$result['system'] = 'FALSE';
				}
				else {
					$result['closed'] = null;
					$result['system'] = null;
				}

				$current = $this->__get('access');

				if ($current == '0') {
					$result['privacy'] = '0';
				}
				else if ($current == '3') {
					$result['privacy'] = '1';
				}
				else if ($current == '4') {
					$result['privacy'] = '2';
				}
				else {
					$result['privacy'] = null;
				}

				$current = $this->__get('published');

				if ($current == '1') {
					$result['public'] = 'TRUE';
				}
				else if ($current == '0') {
					$result['public'] = 'FALSE';
				}
				else {
					$result['public'] = null;
				}

				// $result['owner'] = $this->_usernames($result['owner']);

				foreach ($result['owner'] as $key=>$owner) {
					if (!empty($owner)) {
						$result['owner'][$key] = "uid=$owner,ou=users," . $hubLDAPBaseDN;
					}
				}

				foreach ($result['applicant'] as $key=>$applicant) {
					if (!empty($applicant)) {
						$result['applicant'][$key] = "uid=$applicant,ou=users," . $hubLDAPBaseDN;
					}
				}

				// $result['member'] = $this->_usernames($result['member']);

				foreach ($result['member'] as $key=>$member) {
					if (!empty($member)) {
						$result['member'][$key] = "uid=$member,ou=users," . $hubLDAPBaseDN;
						$result['memberUid'][$key] = $member;
					}
				}

				if (!isset($result['memberUid']))
					$result['memberUid'] = array();

				// $result['memberUid'] = $this->_usernames($result['memberUid']);
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
		$hzg = new Hubzero_Group();

		if ($hzg->read($instance, $storage) === false) {
			return false;
		}

		return $hzg;
	}

	/**
	 * Short description for 'createInstance'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $name Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function createInstance($name)
	{
		if (empty($name)) {
			return false;
		}

		$instance = new Hubzero_Group();

		$instance->cn = $name;

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
	 * @return     boolean Return description (if any) ...
	 */
	private function _ldap_create()
	{
		$xhub = &Hubzero_Factory::getHub();
		$conn = &Hubzero_Factory::getPLDC();

		if (empty($conn) || empty($xhub)) {
			return false;
		}

		if (empty($this->cn)) {
			return false;
		}

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		if ($this->_ldapLegacy) {
			$dn = 'gid=' . $this->cn . ',ou=groups,' . $hubLDAPBaseDN;
		}
		else {
			$dn = 'cn=' . $this->cn . ',ou=groups,' . $hubLDAPBaseDN;
		}

		$attr["objectclass"][0] = "top";
		$attr["objectclass"][1] = "posixGroup";

		if ($this->_ldapLegacy) {
			$attr["objectclass"][2] = "hubGroup";
		}

		$attr['gid'] = $this->cn;
		$attr['gidNumber'] = $this->gidNumber;
		$attr['cn'] = $this->cn;

		if (!ldap_add($conn, $dn, $attr) && @ldap_errno($conn) != 68) {
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

		if (empty($db)) {
			return false;
		}

		if (is_numeric($this->gidNumber)) {
			$query = "INSERT INTO #__xgroups (gidNumber,cn) VALUES ( " . $db->Quote($this->gidNumber) . "," . $db->Quote($this->cn) .
				 ");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result !== false || $db->getErrorNum() == 1062) {
				return true;
			}
		}
		else {
			$query = "INSERT INTO #__xgroups (cn) VALUES ( " . $db->Quote($this->cn) . ");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false && $db->getErrorNum() == 1062) {
				$query = "SELECT gidNumber FROM #__xgroups WHERE cn=" . $db->Quote($this->cn) . ";";

				$db->setQeury($query);

				$result = $db->loadResult();

				if ($result == null) {
					return false;
				}

				$this->gidNumber = $result;
				return true;
			}
			else if ($result !== false) {
				$this->gidNumber = $db->insertid();
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
		if (is_null($storage)) {
			$storage = ($this->_ldapMirror) ? 'all' : 'mysql';
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
		}

		if ($result === true && ($storage == 'ldap' || $storage == 'all')) {
			$result = $this->_ldap_create();

			if ($result == false)
				die('ldap group create');
		}

		return $result;
	}

	/**
	 * Short description for '_ldap_load'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $cn Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function _ldap_load($cn)
	{
		$xhub = &Hubzero_Factory::getHub();
		$conn = &Hubzero_Factory::getPLDC();

		if (empty($conn) || empty($xhub)) {
			return false;
		}

		if (empty($cn)) {
			return false;
		}

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		if (is_numeric($cn))
		{
			$gidNumber = $cn;
			$cn = '';
		}
		else
		{
			$gidNumber = '';
		}

		if (!empty($gidNumber)) {
			$dn = "ou=groups," . $hubLDAPBaseDN;
		}
		else {
			if ($this->_ldapLegacy) {
				$dn = "gid=" . $cn . ",ou=groups," . $hubLDAPBaseDN;
			}
			else {
				$dn = "cn=" . $cn . ",ou=groups," . $hubLDAPBaseDN;
			}
		}

		if ($this->_ldapLegacy) {
			$oclass = 'hubGroup';
		}
		else {
			$oclass = 'posixGroup';
		}

		if (!empty($gidNumber)) {
			$filter = "(&(objectClass=$oclass)(gidNumber=" . $gidNumber . "))";
		}
		else {
			$filter = "(objectClass=$oclass)";
		}

		$entry = ldap_search($conn, $dn, $filter, array("*"), 0, 0, 0, 3);

		if (empty($entry)) {
			return false;
		}

		$count = ldap_count_entries($conn, $entry);

		if ($count <= 0) {
			return false;
		}

		$firstentry = ldap_first_entry($conn, $entry);
		$attr = ldap_get_attributes($conn, $firstentry);

		for ($i = 0; $i < $attr['count']; $i++) {
			$key = $attr[$i];
			$value = $attr[$key];

			for ($j = 0; $j < $value['count']; $j++) {
				if ($value['count'] > 1) {
					$info[$key][$j] = $value[$j];
				}
				else {
					$info[$key] = $value[$j];
				}
			}
		}

		if (!isset($info['memberUid']))
			$info['memberUid'] = array();

		return $info;
	}

	/**
	 * Short description for '_ldap_read'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function _ldap_read()
	{
		$xhub = &Hubzero_Factory::getHub();
		$conn = &Hubzero_Factory::getPLDC();

		if (empty($conn) || empty($xhub)) {
			return false;
		}

		if (empty($this->cn) && empty($this->gidNumber)) {
			return false;
		}

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		$info = $this->_ldap_load($this->cn);

		if (empty($info))
			return $info;

		if ($this->_ldapLegacy) {
			if (empty($info['system'])) {
				$info['system'] = false;
			}

			if (empty($info['closed'])) {
				$info['closed'] = false;
			}

			if (isset($info['public']) && $info['public'] == 'FALSE') {
				$info['public'] = '0';
			}

			if (isset($info['public']) && $info['public'] == 'TRUE') {
				$info['public'] = '1';
			}
		}

		$this->clear();

		foreach ($this->_propertyattrmap as $key=>$value) {
			if (isset($info[$value])) {
				$this->__set($key, $info[$value]);
			}
			else {
				$this->__set($key, null);
			}
		}

		if ($this->_ldapLegacy) {
			if ($info['system'] == 'TRUE' || ($info['closed'] === false && $info['system'] === false)) {
				$this->__set('type', '0'); // system
			}
			elseif (($info['system'] == 'FALSE' && ($info['closed'] == 'FALSE' || $info['closed'] === false)) || ($info['system'] === false &&
			 	$info['closed'] == 'FALSE')) {
					$this->__set('type', '1'); // hub
			}
			elseif (($info['closed'] == 'TRUE') && ($info['system'] === false || $info['system'] === 'FALSE')) {
				$this->__set('type', '2'); // project
			}
			else {
				$this->__set('type', '1'); // hub
			}

			if (!isset($info['privacy'])) {
				$this->__set('access', null);
			}
			else if ($info['privacy'] == '0') {
				$this->__set('access', '0');
			}
			else if ($info['privacy'] == '1') {
				$this->__set('access', '3');
			}
			else if ($info['privacy'] == '2') {
				$this->__set('access', '4');
			}
			else {
				$this->__set('access', null);
			}
		}

		$this->_updatedkeys = array();
		return true;
	}

	/**
	 * Short description for '_mysql_read'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function _mysql_read()
	{
		$db = &JFactory::getDBO();

		$lazyloading = false;

		if (empty($db)) {
			return false;
		}

		if (is_numeric($this->gidNumber)) {
			$query = "SELECT * FROM #__xgroups WHERE gidNumber = " . $db->Quote($this->gidNumber) . ";";
		}
		else {
			$query = "SELECT * FROM #__xgroups WHERE cn = " . $db->Quote($this->cn) . ";";
		}

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if (empty($result)) {
			return false;
		}

		$this->clear();

		foreach ($result as $key=>$value) {
			if (property_exists(__CLASS__, $key) && $key{0} != '_') {
				$this->__set($key, $value);
			}
		}

		$this->__unset('members');
		$this->__unset('invitees');
		$this->__unset('applicants');
		$this->__unset('managers');
		$this->__unset('tracperm');

		if (!$lazyloading) {
			$this->__get('members');
			$this->__get('invitees');
			$this->__get('applicants');
			$this->__get('managers');
			$this->__get('tracperm');
		}

		$this->_updatedkeys = array();

		return true;
	}

	/**
	 * Short description for 'read'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function read($name = null, $storage = 'mysql')
	{
		if (!is_null($name) && !is_string($name) && !is_integer($name)) {
			$this->_error(__FUNCTION__ . ": Argument #1 is not a valid string or integer", E_USER_ERROR);
			die();
		}

		if (!is_null($storage) && !is_string($storage)) {
			$this->_error(__FUNCTION__ . ": Argument #2 is not a string", E_USER_ERROR);
			die();
		}

		if (!in_array($storage, array('mysql', 'ldap', null))) {
			$this->_error(__FUNCTION__ . ": Argument #2 [$storage] is not a valid value", E_USER_ERROR);
			die();
		}

		if (!is_null($name)) {
			$this->clear();

			if (Hubzero_Validate::is_positive_integer($name)) {
				$this->gidNumber = $name;
			}
			else {
				$this->cn = $name;
			}
		}

		$result = true;

		if (is_null($storage) || $storage == 'mysql') {
			$result = $this->_mysql_read();
		}
		else if ($storage == 'ldap') {
			$result = $this->_ldap_read();
		}
		else {
			$result = false;
		}

		if ($result === false) {
			$this->clear();
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
		$errno = 0;

		if (empty($conn) || empty($xhub)) {
			return false;
		}

		if (empty($this->cn)) {
			return false;
		}

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		$info = $this->toArray('ldap');

		$current_hzg = Hubzero_Group::getInstance($this->cn, 'ldap');

		if (!is_object($current_hzg)) {
			if ($this->_ldap_create() == false) {
				return false;
			}

			$current_hzg = Hubzero_Group::getInstance($this->cn, 'ldap');

			if (!is_object($current_hzg)) {
				return false;
			}
		}

		$currentinfo = $this->_ldap_load($this->cn);

		if ($this->_ldapLegacy) {
			$dn = 'gid=' . $this->cn . ',ou=groups,' . $hubLDAPBaseDN;
		}
		else {
			$dn = 'cn=' . $this->cn . ',ou=groups,' . $hubLDAPBaseDN;
		}

		$_attrpropertymap = array_flip($this->_propertyattrmap);

		if ($this->_ldapLegacy) {
			$_attrpropertymap['closed'] = 'type';
			$_attrpropertymap['system'] = 'type';
			$_attrpropertymap['memberUid'] = 'members';
		}

		// @FIXME Check for empty strings, use delete instead of replace as
		// LDAP disallows empty values

		foreach ($currentinfo as $key=>$value) {
			if (!array_key_exists($key, $_attrpropertymap))
				continue;
			else if (!$all && !in_array($_attrpropertymap[$key], $this->_updatedkeys)) {
				continue;
			}
			else if (is_null($info[$key]) && !is_null($currentinfo[$key])) {
				$delete_attr[$key] = array();
			}
			else if (!is_null($info[$key]) && is_null($currentinfo[$key])) {
				$add_attr[$key] = $info[$key];
			}
			else if ($info[$key] != $currentinfo[$key]) {
				$replace_attr[$key] = $info[$key];
			}
		}

		if (isset($replace_attr) && !ldap_mod_replace($conn, $dn, $replace_attr)) {
			$errno = ldap_errno($conn);
		}

		if (isset($add_attr) && !ldap_mod_add($conn, $dn, $add_attr)) {
			$errno = ldap_errno($conn);
		}

		if (isset($delete_attr) && !ldap_mod_del($conn, $dn, $delete_attr)) {
			$errno = ldap_errno($conn);
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
	 * @param      boolean $all Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function _mysql_update($all = false)
	{
		$db = &JFactory::getDBO();

		$query = "UPDATE #__xgroups SET ";

		$classvars = get_class_vars(__CLASS__);

		$first = true;

		foreach ($classvars as $property=>$value) {
			if (($property{0} == '_') || in_array($property, $this->_list_keys)) {
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

		$query .= " WHERE `gidNumber`=" . $db->Quote($this->__get('gidNumber')) . ";";

		if ($first == true) {
			$query = '';
		}

		if (!empty($query)) {
			$db->setQuery($query);

			$result = $db->query();

			if ($result === false) {
				return false;
			}

			$affected = mysql_affected_rows($db->_resource);

			if ($affected < 1) {
				$this->_mysql_create();

				$db->setQuery($query);

				$result = $db->query();

				if ($result === false) {
					return false;
				}

				$affected = mysql_affected_rows($db->_resource);

				if ($affected < 1) {
					return false;
				}
			}
		}

		foreach ($this->_list_keys as $property) {
			if (!$all && !in_array($property, $this->_updatedkeys)) {
				continue;
			}

			$aux_table = "#__xgroups_" . $property;

			$list = $this->__get($property);

			if (!is_null($list) && !is_array($list)) {
				$list = array($list);
			}

			$ulist = null;
			$tlist = null;

			foreach ($list as $value) {
				if (!is_null($ulist)) {
					$ulist .= ',';
					$tlist .= ',';
				}

				$ulist .= $db->Quote($value);
				$tlist .= '(' . $db->Quote($this->gidNumber) . ',' . $db->Quote($value) . ')';
			}

			if (is_array($list) && count($list) > 0) {
				if ($property == 'tracperm') {
					$query = "REPLACE INTO $aux_table (group_id, action) VALUES $tlist;";
				}
				else if (in_array($property, array('members', 'managers', 'applicants', 'invitees'))) {
					$query = "REPLACE INTO $aux_table (gidNumber,uidNumber) SELECT " . $db->Quote($this->gidNumber) . ",id FROM #__users WHERE " .
						 " username IN ($ulist);";
				}

				$db->setQuery($query);

				if (!$db->query()) {
					return false;
				}

			}

			if (!is_array($list) || count($list) == 0) {
				if ($property == 'tracperm') {
					$query = "DELETE FROM $aux_table WHERE group_id=" . $db->Quote($this->gidNumber) . ";";
				}
				else if (in_array($property, array('members', 'managers', 'applicants', 'invitees'))) {
					$query = "DELETE FROM $aux_table WHERE gidNumber=" . $db->Quote($this->gidNumber) . ";";
				}
			}
			else {
				if ($property == 'tracperm') {
					$query = "DELETE FROM $aux_table WHERE group_id=" . $db->Quote($this->gidNumber) . " AND action NOT IN ($ulist);";
				}
				else if (in_array($property, array('members', 'managers', 'applicants', 'invitees'))) {
					$query = "DELETE m FROM #__xgroups_$property AS m, #__users AS u WHERE " . " m.gidNumber=" . $db->Quote($this->gidNumber) .
						 " AND m.uidNumber=u.id AND u.username NOT IN (" . $ulist . ");";
				}
			}

			$db->setQuery($query);

			if (!$db->query()) {
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
		if (is_null($storage)) {
			$storage = ($this->_ldapMirror) ? 'all' : 'mysql';
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
	 * @return     boolean Return description (if any) ...
	 */
	private function _ldap_delete()
	{
		$conn = &Hubzero_Factory::getPLDC();
		$xhub = &Hubzero_Factory::getHub();

		if (empty($conn) || empty($xhub)) {
			return false;
		}

		if (!isset($this->instance)) {
			return false;
		}

		if ($this->_ldapLegacy) {
			$dn = "gid=" . $this->cn . ",ou=groups," . $xhub->getCfg('hubLDAPBaseDN');
		}
		else {
			$dn = "cn=" . $this->cn . ",ou=groups," . $xhub->getCfg('hubLDAPBaseDN');
		}

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
	 * @return     boolean Return description (if any) ...
	 */
	private function _mysql_delete()
	{
		if (!isset($this->cn) && !isset($this->gidNumber)) {
			return false;
		}

		$db = JFactory::getDBO();

		if (empty($db)) {
			return false;
		}

		if (!isset($this->gidNumber)) {
			$db->setQuery("SELECT gidNumber FROM #__xgroups WHERE cn=" . $db->Quote($this->cn) . ";");

			$this->gidNumber = $db->loadResult();
		}

		if (empty($this->gidNumber)) {
			return false;
		}

		$db->setQuery("DELETE FROM #__xgroups WHERE gidNumber=" . $db->Quote($this->gidNumber) . ";");

		if (!$db->query()) {
			return false;
		}

		$db->setQuery("DELETE FROM #__xgroups_applicants WHERE gidNumber=" . $db->Quote($this->gidNumber) . ";");
		$db->query();
		$db->setQuery("DELETE FROM #__xgroups_invitees WHERE gidNumber=" . $db->Quote($this->gidNumber) . ";");
		$db->query();
		$db->setQuery("DELETE FROM #__xgroups_managers WHERE gidNumber=" . $db->Quote($this->gidNumber) . ";");
		$db->query();
		$db->setQuery("DELETE FROM #__xgroups_members WHERE gidNumber=" . $db->Quote($this->gidNumber) . ";");
		$db->query();
		$db->setQuery("DELETE FROM #__xgroups_tracperm WHERE group_id=" . $db->Quote($this->gidNumber) . ";");
		$db->query();

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

		if (func_num_args() > 1) {
			$this->_error(__FUNCTION__ . ": Invalid number of arguments", E_USER_ERROR);
			die();
		}

		if (is_null($storage)) {
			$storage = ($this->_ldapMirror) ? 'all' : 'mysql';
		}

		if (!is_string($storage)) {
			$this->_error(__FUNCTION__ . ": Argument #1 is not a string", E_USER_ERROR);
			die();
		}

		if (!in_array($storage, array('mysql', 'ldap', 'all'))) {
			$this->_error(__FUNCTION__ . ": Argument #1 [$storage] is not a valid value", E_USER_ERROR);
			die();
		}

		if ($storage == 'mysql' || $storage == 'all') {
			$result = $this->_mysql_delete();

			if ($result === false) {
				$this->_error(__FUNCTION__ . ": MySQL deletion failed", E_USER_WARNING);
				return false;
			}
		}

		if ($result === true && ($storage == 'ldap' || $storage == 'all')) {
			$result = $this->_ldap_delete();

			if ($result === false) {
				$this->_error(__FUNCTION__ . ": LDAP deletion failed", E_USER_WARNING);
				return false;
			}
		}

		return true;
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

		if (!property_exists(__CLASS__, $property) || $property{0} == '_') {
			if (empty($property)) {
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		if (in_array($property, $this->_list_keys)) {
			if (!array_key_exists($property, get_object_vars($this))) {
				$db = &JFactory::getDBO();

				if (is_object($db)) {
					if (in_array($property, array('tracperm'))) {
						$aux_table = "#__xgroups_tracperm";

						$query = "SELECT action FROM $aux_table AS aux WHERE aux.group_id=" . $db->Quote($this->gidNumber) . " ORDER BY action" .
							 " ASC;";
					}
					else if (in_array($property, array('members', 'applicants', 'managers', 'invitees'))) {
						$aux_table = "#__xgroups_" . $property;
						$query = "SELECT u.username FROM $aux_table AS aux,#__users AS u " . " WHERE u.id=aux.uidNumber AND " .
							 " aux.gidNumber=" . $db->Quote($this->gidNumber) . " ORDER BY uidNumber ASC;";
					}
					else {
						$query = null;
					}

					$db->setQuery($query);

					$result = $db->loadResultArray();

					if ($result !== false) {
						$this->__set($property, $result);
					}
				}
			}
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
	 * @param      string $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
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

		if (in_array($property, $this->_list_keys)) {
			$value = array_diff((array) $value, array(''));

			if (in_array($property, array('managers','members','applicants','invitees'))) {
				$value = $this->_usernames($value);
				//$value = $this->_userids($value);
			}

			$value = array_unique($value);
			$value = array_values($value);
			$this->$property = $value;
		}
		else {
			$this->$property = $value;
		}

		if (!in_array($property, $this->_updatedkeys)) {
			$this->_updatedkeys[] = $property;
		}
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
	 * @param      string $property Parameter description (if any) ...
	 * @return     void
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
	 * Short description for '_usernames'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $users Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function _usernames($users)
	{
		$db = JFactory::getDBO();

		if (empty($db)) {
			return false;
		}

		$usernames = array();
		$userids = array();

		if (!is_array($users))
			$users = array($users);

		foreach($users as $u)
		{
			if (is_numeric($u))
				$userids[] = $db->Quote($u);
			else
				$usernames[] = $u;
		}

		if (empty($userids))
			return $usernames;

		$set = implode($userids,",");

		$sql = "SELECT username FROM #__users WHERE id IN ( $set );";

		$db->setQuery($sql);

		$result = $db->loadResultArray();

		if (empty($result))
			$result = array();

		$result = array_merge($result,$usernames);

		return $result;
	}

	/**
	 * Short description for '_userids'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $users Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
    private function _userids($users)
    {
        $db = JFactory::getDBO();

        if (empty($db)) {
            return false;
        }

        $usernames = array();
        $userids = array();

        if (!is_array($users))
            $users = array($users);

        foreach($users as $u)
        {
            if (is_numeric($u))
                $userids[] = $u;
            else
                $usernames[] = $db->Quote($u);
        }

        if (empty($usernames))
            return $userids;

        $set = implode($usernames,",");

        $sql = "SELECT id FROM #__users WHERE id IN ( $set );";

        $db->setQuery($sql);

        $result = $db->loadResultArray();

        if (empty($result))
            $result = array();

        $result = array_merge($result,$userids);

        return $result;
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
		$users = $this->_usernames($value);
		// $users = $this->_userids($value);

		$this->__set($key, array_merge($this->__get($key), $users));
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
		$users = $this->_usernames($value);
		// $users = $this->_userids($value);

		$this->__set($key, array_diff($this->__get($key), $users));
	}

	/**
	 * Short description for 'iterate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $func Parameter description (if any) ...
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	static function iterate($func, $storage = 'mysql')
	{
		$db = &JFactory::getDBO();

		if (!in_array($storage, array('mysql', 'ldap', null))) {
			return false;
		}

		if ($storage == 'ldap') {
			$xhub = &Hubzero_Factory::getHub();
			$conn = &Hubzero_Factory::getPLDC();

			$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

			$dn = 'ou=groups,' . $hubLDAPBaseDN;
			$filter = '(objectclass=hubGroup)';

			$attributes[] = 'cn';

			$sr = ldap_search($conn, $dn, $filter, $attributes, 0, 0, 0);

			if ($sr === false) {
				return false;
			}

			$count = ldap_count_entries($conn, $sr);

			if ($count === false) {
				return false;
			}

			$entry = ldap_first_entry($conn, $sr);

			while ($entry !== false) {
				$attributes = ldap_get_attributes($conn, $entry);
				call_user_func($func, $attributes['cn'][0]);
				$entry = ldap_next_entry($conn, $entry);
			}
		}
		else if ($storage == 'mysql' || is_null($storage)) {
			$query = "SELECT cn FROM #__xgroups;";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false) {
				return false;
			}

			while ($row = mysql_fetch_row($result)) {
				call_user_func($func, $row[0]);
			}

			mysql_free_result($result);
		}

		return true;
	}

	/**
	 * Short description for 'exists'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $group Parameter description (if any) ...
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	static public function exists($group, $storage = 'mysql')
	{
		$db = &JFactory::getDBO();

		if (empty($group))
			return false;

		if (is_numeric($group))
			$query = 'SELECT gidNumber FROM #__xgroups WHERE gidNumber=' . $db->Quote($group);
		else
			$query = 'SELECT gidNumber FROM #__xgroups WHERE cn=' . $db->Quote($group);

		$db->setQuery($query);

		if (!$db->query())
			return false;

		if ($db->loadResult() > 0)
			return true;

		return false;
	}

	/**
	 * Short description for 'find'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	static function find($filters = array())
	{
		$db = &JFactory::getDBO();

		$type = !empty($filters['type']) ? $filters['type'] : 'all';

		if (!in_array($type, array('system', 'hub', 'project', 'all', '0', '1', '2')))
			return false;

		if ($type == 'all')
			$where_clause = '';
		else {
			if ($type == 'system')
				$type = '0';
			elseif ($type == 'hub')
				$type = '1';
			elseif ($type == 'project')
				$type = '2';

			$where_clause = 'WHERE type=' . $db->Quote($type);
		}

		if (isset($filters['search']) && $filters['search'] != '') {
			if ($where_clause != '') {
				$where_clause .= " AND";
			}
			else {
				$where_clause = "WHERE";
			}
			$where_clause .= " (LOWER(description) LIKE '%" . $filters['search'] . "%' OR LOWER(cn) LIKE '%" . $filters['search'] .
				 "%')";
		}

		if (isset($filters['index']) && $filters['index'] != '') {
			if ($where_clause != '') {
				$where_clause .= " AND";
			}
			else {
				$where_clause = "WHERE";
			}
			$where_clause .= " (LOWER(description) LIKE '" . $filters['index'] . "%') ";
		}

		if (isset($filters['authorized']) && $filters['authorized']) {
			if ($filters['authorized'] === 'admin') {
				$where_clause .= "";
			}
			else {
				if ($where_clause != '') {
					$where_clause .= " AND";
				}
				else {
					$where_clause .= "WHERE";
				}
				$where_clause .= " privacy<=1";
			}
		}
		else {
			if ($where_clause != '') {
				$where_clause .= " AND";
			}
			else {
				$where_clause .= "WHERE";
			}
			$where_clause .= " privacy=0";
		}

		if (empty($filters['fields']))
			$filters['fields'][] = 'cn';

		$field = implode(',', $filters['fields']);

		$query = "SELECT $field FROM #__xgroups $where_clause";
		if (isset($filters['sortby']) && $filters['sortby'] != '') {
			$query .= " ORDER BY " . $filters['sortby'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 'all') {
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}
		$query .= ";";

		$db->setQuery($query);

		if (!in_array('COUNT(*)', $filters['fields'])) {
			$result = $db->loadObjectList();
		}
		else {
			$result = $db->loadResult();
		}

		if (empty($result))
			return false;

		return $result;
	}

	/**
	 * Short description for 'is_member_of'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $table Parameter description (if any) ...
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function is_member_of($table, $uid)
	{
		$db =& JFactory::getDBO();

		if (!is_numeric($uid))
			$uidNumber = JUserHelper::getUserId($uid);
		else
			$uidNumber = $uid;

		if (!in_array($table, array('applicants','members','managers','invitees')))
			return false;

		$table = '#__xgroups_' . $table;

		$query = "SELECT * FROM $table WHERE gidNumber=" . $db->Quote($this->gidNumber) . " AND uidNumber=" . $db->Quote($uidNumber) . ";";

		$db->setQuery($query);
		//$db->query();

		$result = $db->loadResultArray();

		return !empty($result);
	}

	/**
	 * Short description for 'isMember'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function isMember($uid)
	{
		return $this->is_member_of('members',$uid);
	}

	/**
	 * Short description for 'isApplicant'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function isApplicant($uid)
	{
		return $this->is_member_of('applicants',$uid);
	}

	/**
	 * Short description for 'isManager'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function isManager($uid)
	{
		return $this->is_member_of('managers',$uid);
	}

	/**
	 * Short description for 'isInvitee'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function isInvitee($uid)
	{
		return $this->is_member_of('invitees',$uid);
	}

	/**
	 * Short description for 'getEmails'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $key Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getEmails($key='managers')
	{
		ximport('Hubzero_User_Profile');
		$emails = array();
		$users = $this->get($key);
		if ($users) {
			foreach ($users as $user)
			{
				$u =& Hubzero_User_Profile::getInstance($user);
				if (is_object($u)) {
					$emails[] = $u->get('email');
				}
			}
		}
		return $emails;
	}

	/**
	 * Short description for 'search'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $tbl Parameter description (if any) ...
	 * @param      string $q Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function search($tbl='', $q='')
	{
		if (!in_array($tbl, array('applicants','members','managers','invitees')))
			return false;

		$table = '#__xgroups_' . $tbl;

		$db = & JFactory::getDBO();

		$query = "SELECT u.id FROM $table AS t,#__users AS u WHERE t.gidNumber=" . $db->Quote($this->gidNumber) . " AND u.id=t.uidNumber AND LOWER(u.name) LIKE '%".strtolower($q)."%';";

		$db->setQuery($query);
		return $db->loadResultArray();
	}

	/**
	 * Short description for 'select'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $group Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function select($group)
	{
		$db = &JFactory::getDBO();

		if (empty($group))
			$group = $this->gidNumber;

		if (empty($group))
			$group = $this->cn;

		if (empty($group))
			return false;

		if (is_numeric($group))
			$query = "SELECT * FROM #__xgroups WHERE gidNumber = " . $db->Quote( intval($group) ) . ";";
		else
			$query = "SELECT * FROM #__xgroups WHERE cn = " . $db->Quote( $group ) . ";";

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if (empty($result))
			return false;

		$this->gidNumber = $result['gidNumber'];
		$this->cn = $result['cn'];
		$this->description = $result['description'];
		$this->published = $result['published'];
		$this->type = $result['type'];
		$this->access = $result['access'];
		$this->public_desc = $result['public_desc'];
		$this->private_desc = $result['private_desc'];
		$this->restrict_msg = $result['restrict_msg'];
		$this->join_policy = $result['join_policy'];
		$this->privacy = $result['privacy'];

		return true;
	}
}

