<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2009-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	private $logo = null;
	private $overview_type = null;
	private $overview_content = null;
	private $plugins = null;
	private $created = null;
	private $created_by = null;
	private $params = null;
	private $members = array();
	private $managers = array();
	private $applicants = array();
	private $invitees = array();
	
	public $error = null;
	
	static $_list_keys = array('members', 'managers', 'applicants', 'invitees');
	
	private $_ldapMirror = false;
	private $_ldapLegacy = true;
	private $_updateAll = false;
	
	static $_propertyattrmap = array('gidNumber'=>'gidNumber', 'cn'=>'cn', 'members'=>'memberUid');
	static $_legacypropertyattrmap = array('gidNumber'=>'gidNumber', 'cn'=>'cn', 'members'=>'member', 'managers'=>'owner', 'description'=>'description', 'published'=>'public','access'=>'privacy', 'applicants'=>'applicant');
	
	private $_updatedkeys = array();

	public function __construct()
	{
		$config = & JComponentHelper::getParams('com_groups');

		$this->_ldapMirror = $config->get('ldapGroupMirror') == '1';
		$this->_ldapLegacy = $config->get('ldapGroupLegacy') == '1';
	}

	public function clear()
	{
		$cvars = get_class_vars(__CLASS__);
		
		$this->_updatedkeys = array();
		
		foreach ($cvars as $key=>$value) {
			if ($key{0} != '_') {
				unset($this->$key);
				
				if (!in_array($key, self::$_list_keys)) {
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

	private function logDebug($msg)
	{
		$xlog = &Hubzero_Factory::getLogger();
		$xlog->logDebug($msg);
	}

	public function toArray($format = 'mysql', $legacy = null, $deltaonly = false)
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
				
				if (!$deltaonly || in_array($key, $this->_updatedkeys))
					$result[$key] = $current;
			}
			
			return $result;
		}
		else if ($format == 'ldap') {

			if (is_null($legacy))
				$legacy = $this->_ldapLegacy;

			if ($legacy)
				$propertyattrmap = self::$_legacypropertyattrmap;
			else
				$propertyattrmap = self::$_propertyattrmap;

			foreach ($propertyattrmap as $key=>$value) {
				$current = $this->__get($key);
				
				if (!$deltaonly || in_array($key, $this->_updatedkeys)) {
					$result[$value] = $current;
                    //echo "setting result[$value] = $current <br>";
				}
			}
			
			if ($legacy) {
				if (!$deltaonly || in_array('type', $this->_updatedkeys))
				{
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
				}
			
				if (!$deltaonly || in_array('access', $this->_updatedkeys))
				{
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
				}

				if (!$deltaonly || in_array('published', $this->_updatedkeys))
				{
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
				}

				if (!$deltaonly || in_array('managers', $this->_updatedkeys))
				{
					$result['owner'] = self::_usernames($result['owner']);
		
					foreach ($result['owner'] as $key=>$owner) {
						if (!empty($owner)) {
							$result['owner'][$key] = "uid=$owner,ou=users," . $hubLDAPBaseDN;
						}
					}

					if (count($result['owner']) == 1)
						$result['owner'] = implode($result['owner']);
				}
	
				if (!$deltaonly || in_array('applicants', $this->_updatedkeys))
				{
					$result['applicant'] = self::_usernames($result['applicant']);

					foreach ($result['applicant'] as $key=>$applicant) {
						if (!empty($applicant)) {
							$result['applicant'][$key] = "uid=$applicant,ou=users," . $hubLDAPBaseDN;
						}
					}

					if (count($result['applicant']) == 1)
						$result['applicant'] = implode($result['applicant']);
				}

				if (!$deltaonly || in_array('members', $this->_updatedkeys))
				{
					$result['member'] = self::_usernames($result['member']);

					foreach ($result['member'] as $key=>$member) {
						if (!empty($member)) {
							$result['member'][$key] = "uid=$member,ou=users," . $hubLDAPBaseDN;
							$result['memberUid'][$key] = $member;
						}
					}

					if (count($result['member']) == 1)
						$result['member'] = implode($result['member']);

					if (!isset($result['memberUid']))
						$result['memberUid'] = array();
				}
			}

			if (!$deltaonly || in_array('members', $this->_updatedkeys))
			{
				if (isset($result['memberUid']))
					$result['memberUid'] = self::_usernames($result['memberUid']);

				if (count($result['memberUid']) == 1)
					$result['memberUid'] = implode($result['memberUid']);
			}

			return $result;
		}
		
		return false;
	}

	public function getInstance($instance, $storage = null)
	{
		$hzg = new Hubzero_Group();
		
		if ($hzg->read($instance, $storage) === false) {
			return false;
		}
		
		return $hzg;
	}

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

	private function _keyvalueok(&$array,$key) 
	{
		if (!isset($array[$key]))
			return false;

		if (is_null($array[$key]))
			return false;

		if ($array[$key] == "")
			return false;

		if ($array[$key] == array())
			return false;

		return true;
	}

	function _ldap_create_ou($ou = null)
	{
		$xhub = &Hubzero_Factory::getHub();
		$conn = &Hubzero_Factory::getPLDC();
		
		if (empty($conn) || empty($xhub)) {
			return false;
		}
	
		if (empty($ou))
			$ou = "groups";
		
		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		$dn = "ou=$ou," . $hubLDAPBaseDN;

		$attr["objectclass"][0] = "organizationalUnit";
		$attr["ou"] = $ou;
		$attr["description"] = "Root groups object";

		ldap_add($conn, $dn, $attr);

		$errno = @ldap_errno($conn);

		return $errno;
	}

	function _ldap_create($cn = null, $data = array(), $legacy = false, $verbose = false, $dryrun = false)
	{
		$xhub = &Hubzero_Factory::getHub();
		$conn = &Hubzero_Factory::getPLDC();
		
		if (empty($conn) || empty($xhub)) {
			return false;
		}
		
		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		if (empty($cn))
			return false;

		$data['cn'] = $cn;

		if ($legacy) {
			$dn = 'gid=' . $cn . ',ou=groups,' . $hubLDAPBaseDN;
		}
		else {
			$dn = 'cn=' . $cn . ',ou=groups,' . $hubLDAPBaseDN;
		}

		$attr["objectclass"][0] = "top";
		$attr["objectclass"][1] = "posixGroup";

		if ($legacy) {
			$attr["objectclass"][2] = "hubGroup";
			$attr['gid'] = $data['cn'];
		}

		$attr['gidNumber'] = (isset($data['gidNumber'])) ? $data['gidNumber'] : 0;
		$attr['cn'] = $data['cn'];

		if (!empty($data['memberUid']))
			$attr['memberUid'] = $data['memberUid'];
		
		if ($legacy) {
			if (self::_keyvalueok($data,'description'))
				$attr['description'] = $data['description'];
			if (self::_keyvalueok($data,'groupName'))
				$attr['groupName'] = $data['groupName'];
			if (self::_keyvalueok($data,'userPassword'))
				$attr['userPassword'] = $data['userPassword'];
			if (self::_keyvalueok($data,'o'))
				$attr['o'] = $data['o'];
			if (self::_keyvalueok($data,'comment'))
				$attr['comment'] = $data['comment'];
			if (self::_keyvalueok($data,'public'))
				$attr['public'] = $data['public'];
			if (self::_keyvalueok($data,'privacy'))
				$attr['privacy'] = $data['privacy'];
			if (self::_keyvalueok($data,'owner'))
				$attr['owner'] = $data['owner'];
			if (self::_keyvalueok($data,'member'))
				$attr['member'] = $data['member'];
			if (self::_keyvalueok($data,'applicant'))
				$attr['applicant'] = $data['applicant'];
			if (self::_keyvalueok($data,'system'))
				$attr['system'] = $data['system'];
			if (self::_keyvalueok($data,'closed'))
				$attr['closed'] = $data['closed'];
		}

		if ($verbose)
		{
			echo "CREATE $dn: "; 
            var_dump($attr); 
            echo "<br>";
		}

		if ($dryrun)
			return 0;

		@ldap_add($conn, $dn, $attr);

		$errno = @ldap_errno($conn);

		if ($errno == 32) { // LDAP_NO_SUCH_OBJECT
			if (self::_ldap_ou_exists() == 0) {

				$result = self::_ldap_create_ou();

				if ($result == 0) {
					@ldap_add($conn, $dn, $attr);
					$errno = @ldap_errno($conn);
				}
			}
		}

		return $errno;
	}

	private function _mysql_create($cn,$gidNumber)
	{
		$db = &JFactory::getDBO();
		
		if (empty($db)) {
			return false;
		}
		
		if (is_numeric($gidNumber)) {
			$query = "INSERT INTO #__xgroups (gidNumber,cn) VALUES ( " . $db->Quote($gidNumber) . "," . $db->Quote($cn) .
				 ");";
			
			$db->setQuery($query);
			
			$result = $db->query();
			
			if ($result !== false || $db->getErrorNum() == 1062) {
				return $gidNumber;
			}
		}
		else {
			$query = "INSERT INTO #__xgroups (cn) VALUES ( " . $db->Quote($cn) . ");";
			
			$db->setQuery($query);
			
			$result = $db->loadResult();
			
			if ($result === false && $db->getErrorNum() == 1062) {
				$query = "SELECT gidNumber FROM #__xgroups WHERE cn=" . $db->Quote($cn) . ";";
				
				$db->setQeury($query);
				
				$result = $db->loadResult();
				
				if ($result == null) {
					return false;
				}
				
				$gidNumber = $result;
				return $gidNumber;
			}
			else if ($result !== false) {
				$gidNumber = $db->insertid();
				return $gidNumber;
			}
		}
		
		return false;
	}

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
			$result = $this->_mysql_create($this->cn,$this->gidNumber);

			if ($result)
				$this->gidNumber = $result;
		}
		
		if ($result && ($storage == 'ldap' || $storage == 'all')) {
			$data = array();
			$data['gidNumber'] = $this->gidNumber;
			$result = (self::_ldap_create($this->cn,$data,$this->_ldapLegacy) == 0);
		}
		
		return $result;
	}

	private function _ldap_convert($info = null, $legacy = false)
	{
		if (empty($info))
			return $info;

		if ($legacy && !in_array('hubGroup',$info['objectClass']))
			$legacy = false;
		
		if ($legacy) {
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
		
		if ($legacy)
			$propertyattrmap = self::$_legacypropertyattrmap;
		else
			$propertyattrmap = self::$_propertyattrmap;

		foreach ($propertyattrmap as $key=>$value) {
			if (isset($info[$value])) {
				$result[$key] = $info[$value];
			}
		}
		
		if ($legacy) {
			if ($info['system'] == 'TRUE' || ($info['closed'] === false && $info['system'] === false)) {
				$result['type'] = '0'; // system
			}
			elseif (($info['system'] == 'FALSE' && ($info['closed'] == 'FALSE' || $info['closed'] === false)) || ($info['system'] === false &&
			 	$info['closed'] == 'FALSE')) {
					$result['type'] = '1'; // hub
			}
			elseif (($info['closed'] == 'TRUE') && ($info['system'] === false || $info['system'] === 'FALSE')) {
				$result['type'] = '2'; // project
			}
			else {
				$result['type'] = '1'; // hub
			}
		
			if (!isset($info['privacy'])) {
			}
			else if ($info['privacy'] == '0') {
				$result['access'] = '0';
			}
			else if ($info['privacy'] == '1') {
				$result['access'] = '3';
			}
			else if ($info['privacy'] == '2') {
				$result['access'] = '4';
			}
		}

		foreach(array('members','applicants','managers','invitees') as $list) {

			if (!empty($result[$list])) {

				$values = (array) $result[$list];
				$newvalues = array();
				foreach($values as $value) {
					if (strpos($value,'uid=') === 0) {
						$len = strlen($value);
						$len = $len - ($len - strpos($value,',')) - 4;
						$value = substr($value,4,$len);
					}

					$newvalues[] = $value;
				}
			
				$result[$list] = $newvalues;
			}

		}

		return $result;
	}

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
		
		if (!$lazyloading) {
			$this->__get('members');
			$this->__get('invitees');
			$this->__get('applicants');
			$this->__get('managers');
		}
		
		$this->_updatedkeys = array();
		
		return true;
	}

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
			$info = self::_ldap_read($this->cn, $this->_ldapLegacy);
			$data = self::_ldap_convert($info, $this->_ldapLegacy);
		
			$this->clear();
		
			foreach ($data as $key=>$value) {
				if (isset($data[$value])) {
					$this->__set($key, $data[$value]);
				}
			}
		
			$this->_updatedkeys = array();
		}
		else {
			$result = false;
		}
		
		if ($result === false) {
			$this->clear();
		}
		
		return $result;
	}

	private function _ldap_conn_exists()
	{
		$conn = &Hubzero_Factory::getPLDC();
		$xhub = &Hubzero_Factory::getHub();

		if (empty($conn))
		{
			$result['ldap'] = 'unavailable';
		}
		else
		{
			$result['ldap'] = 'connected';
		}

		return $result;
	}

	private function _ldap_ou_exists($ou = null)
	{
		$conn = &Hubzero_Factory::getPLDC();
		$xhub = &Hubzero_Factory::getHub();

		if (empty($conn))
		{
			return false;
		}

		if (empty($ou))
			$ou = "groups";

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');
		
		$dn = "ou=$ou," . $hubLDAPBaseDN;

		$filter = '(objectclass=organizationalUnit)';
			
		$attributes[] = 'ou';
			
		$entry = @ldap_search($conn, $dn, $filter, $attributes, 0, 0, 0, 3);

		$errno = @ldap_errno($conn);

		if ($errno == 32) // object not found
			return 0;

		if (empty($entry)) 
			return false;
		
		$count = ldap_count_entries($conn, $entry);
		
		if ($count <= 0)
			return 0;

		return 1;
	}

	private function _ldap_hubgroup_exists()
	{
		$conn = &Hubzero_Factory::getPLDC();
		$xhub = &Hubzero_Factory::getHub();

		if (empty($conn))
		{
			$result['groupou'] = 'unknown';
			return $result;
		}

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');
		
		$dn = "ou=groups," . $hubLDAPBaseDN;

		$filter = '(objectclass=hubGroup)';
			
		$attributes[] = 'cn';
			
		$entry = @ldap_search($conn, $dn, $filter, $attributes, 0, 1, 0, 3);
	
		if (!empty($entry)) {
		
			$count = ldap_count_entries($conn, $entry);
			
			if ($count >= 1) {
				$result['hubgroup'] = 'found';
				return $result;
			}
		}

		$myresult = self::_ldap_create('mw-testgroup',null,true,false,false);

		self::_ldap_delete('mw-testgroup',true);

		if ($myresult === 0)
			$result['hubgroup'] = 'found';
		else
			$result['hubgroup'] = 'not found';

		return $result;
	}

	private function _ldap_posixgroup_exists()
	{
		$conn = &Hubzero_Factory::getPLDC();
		$xhub = &Hubzero_Factory::getHub();

		if (empty($conn))
			return false;

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');
		
		$dn = "ou=groups," . $hubLDAPBaseDN;

		$filter = '(objectclass=posixGroup)';
			
		$attributes[] = 'cn';
			
		$entry = @ldap_search($conn, $dn, $filter, $attributes, 0, 1, 0, 3);
	
		if (empty($entry))
			return false;

		$count = ldap_count_entries($conn, $entry);
			
		if ($count >= 1) 
			return 1;

		$myresult = self::_ldap_create('mw-testgroup',null,false,false,false);

		self::_ldap_delete('mw-testgroup',false);

		if ($myresult === 0)
			return 1;

		return 0;
	}


	function status()
	{
		$result = self::_ldap_conn_exists();

		if ($result)
			$data['ldap'] = 'yes';
		else {
			$data['ldap'] = 'no';
			$data['ldap_groupou'] = 'unknown';
			$data['ldap_hubgroup'] = 'unknown';
			$data['ldap_posixgroup'] = 'unknown';

			return $data;
		}

		$result = self::_ldap_ou_exists();
		
		if ($result === false) {
			$data['ldap_groupou'] = 'unknown';
			$data['ldap_hubgroup'] = 'unknown';
			$data['ldap_posixgroup'] = 'unknown';

			return $data;
		}
		else if ($result === 0) {
			$data['ldap_groupou'] = 'no';
			$data['ldap_hubgroup'] = 'unknown';
			$data['ldap_posixgroup'] = 'unknown';

			return $data;
		}
		else
			$data['ldap_groupou'] = 'yes';

		$result = self::_ldap_hubgroup_exists();

		if ($result === false) {
			$data['ldap_hubgroup'] = 'unknown';
		}
		else if ($result === 0) {
			$data['ldap_hubgroup'] = 'no';
		}
		else
			$data['ldap_hubgroup'] = 'yes';

		$result = self::_ldap_posixgroup_exists();

		if ($result === false) {
			$data['ldap_posixgroup'] = 'unknown';
		}
		else if ($result === 0) {
			$data['ldap_posixgroup'] = 'no';
		}
		else
			$data['ldap_posixgroup'] = 'yes';

		return $data;
	}

	private function _ldap_update($cn = null, $info = null, $legacy = false, $all = false, $verbose = false, $dryrun = false)
	{
		$xhub = &Hubzero_Factory::getHub();
		$conn = &Hubzero_Factory::getPLDC();
		$errno = 0;
		
		if (empty($conn) || empty($xhub))
			return false;
		
		if (empty($info)) {
			return true;
		}

		if (empty($cn))
			return false;

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');
		
		$currentinfo = self::_ldap_read($cn,$legacy);

		if ($currentinfo == false)
		{
			return(0x20); // LDAP_NO_SUCH_OBJECT
		}
		if ($legacy) {
			$dn = 'gid=' . $cn . ',ou=groups,' . $hubLDAPBaseDN;
		}
		else {
			$dn = 'cn=' . $cn . ',ou=groups,' . $hubLDAPBaseDN;
		}

		if ($legacy)
			$propertyattrmap = self::$_legacypropertyattrmap;
		else
			$propertyattrmap = self::$_propertyattrmap;

		$_attrpropertymap = array_flip($propertyattrmap);

		if ($legacy) {
			$_attrpropertymap['closed'] = 'type';
			$_attrpropertymap['system'] = 'type';
			$_attrpropertymap['memberUid'] = 'members';
		}

		// @FIXME Check for empty strings, use delete instead of replace as
		// LDAP disallows empty values

		foreach($_attrpropertymap as $key=>$value)
		{
			if (!isset($currentinfo[$key]))
				$currentinfo[$key] = null;
		}

		if ($all) { // if all, assume missing keys in info should be deleted
			foreach ($currentinfo as $key=>$value) {
				if (!isset($info[$key]))
					$info[$key] = null;
			}
		}

		foreach ($currentinfo as $key=>$value) {
			if (!array_key_exists($key, $_attrpropertymap))
				continue;
			else if ((isset($info[$key]) && (is_null($info[$key]) || $info[$key] == array() || $info[$key] === "")) && (isset($currentinfo[$key]) && (!is_null($currentinfo[$key]) && $currentinfo[$key] != array())) ) {
				$delete_attr[$key] = array();
				if ($verbose) echo "DELETE $cn::$key<br>";
			}
			else if (self::_keyvalueok($info,$key) && is_null($currentinfo[$key])) {
				$add_attr[$key] = $info[$key];
				if ($verbose) echo "ADD $cn::$key<br>";
			}
			else if (isset($info[$key]) && ($info[$key] != $currentinfo[$key])) {

				if (empty($info[$key]))
				{
					if ($verbose) {
						echo "REPLACE $cn::$key: ";
						var_dump($info[$key]); echo "<br>";
						echo "<br>"; 
					}
					$replace_attr[$key] = $info[$key];
				}
				else if (empty($currentinfo[$key]))
				{
					if ($verbose) { 
						echo "REPLACE $cn::$key: ";
						var_dump($info[$key]);
						echo "<br>"; 
					}
					$replace_attr[$key] = $info[$key];
				}
				else
				{
				
					$diff1 = array_diff((array)$info[$key],(array)$currentinfo[$key]);
					$diff2 = array_diff((array)$currentinfo[$key],(array)$info[$key]);

					if ($verbose) {
						if (!empty($diff1)) { 
							echo "REPLACE $cn::$key(Adding): ";
							var_dump($diff1);
							echo "<br>"; 
						}

						if (!empty($diff2)) { 
							echo "REPLACE $cn::$key(Deleting): ";
							var_dump($diff2);
							echo "<br>"; 
						}
					}

					if (!empty($diff1) || !empty($diff2))
					{
						$replace_attr[$key] = $info[$key];
					}
				}
			}
		}

		if ($dryrun)
			return $errno;

		if (isset($replace_attr) && !ldap_mod_replace($conn, $dn, $replace_attr)) {
			if ($errno == 0)
				$errno = ldap_errno($conn);
		}

		
		if (isset($add_attr) && !ldap_mod_add($conn, $dn, $add_attr)) {
			if ($errno == 0)
				$errno = ldap_errno($conn);
		}

		if (isset($delete_attr) && !ldap_mod_del($conn, $dn, $delete_attr)) {
			if ($errno == 0)
				$errno = ldap_errno($conn);
		}

		return $errno;
	}

	private static function _mysql_update($gidNumber = null, $data)
	{
		$db = &JFactory::getDBO();

		if (empty($data))
			return false;

		if ($gidNumber === null && isset($data['gidNumber']))
			$gidNumber = $data['gidNumber'];
		
		$query = "UPDATE #__xgroups SET ";
		
		$classvars = get_class_vars(__CLASS__);
		
		$first = true;

		if (!is_numeric($gidNumber))
			return false;

		foreach ($classvars as $property=>$value) {
			if (($property{0} == '_') || in_array($property, self::$_list_keys)) {
				continue;
			}

			if (!isset($data[$property]))
				continue;
			
			if (!$first) {
				$query .= ',';
			}
			else {
				$first = false;
			}
			
			$value = $data[$property];
			
			if ($value === null) {
				$query .= "`$property`=NULL";
			}
			else {
				$query .= "`$property`=" . $db->Quote($value);
			}
		}

		$query .= " WHERE `gidNumber`=" . $db->Quote($gidNumber) . ";";

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
				return false;
			}
		}

		foreach (self::$_list_keys as $property) {
			if (!isset($data[$property]))
				continue;

			$aux_table = "#__xgroups_" . $property;

			$list = $data[$property];
			
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
				$tlist .= '(' . $db->Quote($gidNumber) . ',' . $db->Quote($value) . ')';
			}
			
			if (is_array($list) && count($list) > 0) {
				if (in_array($property, array('members', 'managers', 'applicants', 'invitees'))) {
					$query = "REPLACE INTO $aux_table (gidNumber,uidNumber) VALUES $tlist;";
				}
				
				$db->setQuery($query);
				if (!$db->query()) {
					return false;
				}
			
			}
			
			if (!is_array($list) || count($list) == 0) {
				if (in_array($property, array('members', 'managers', 'applicants', 'invitees'))) {
					$query = "DELETE FROM $aux_table WHERE gidNumber=" . $db->Quote($gidNumber) . ";";
				}
			}
			else {
				if (in_array($property, array('members', 'managers', 'applicants', 'invitees'))) {
					$query = "DELETE m FROM #__xgroups_$property AS m WHERE " . " m.gidNumber=" . $db->Quote($gidNumber) .
						 " AND m.uidNumber NOT IN (" . $ulist . ");";
				}
			}
			
			$db->setQuery($query);
			
			if (!$db->query()) {
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
		
		$legacy = $this->_ldapLegacy;

		$result = true;
		
		if ($storage == 'mysql' || $storage == 'all') {
			$data = $this->toArray('mysql',null,!$this->_updateAll);
			$result = self::_mysql_update($this->gidNumber, $data);
			
			if ($result === false) {
				$this->_error(__FUNCTION__ . ": MySQL update failed", E_USER_WARNING);
			}
		}
		
		if ($result === true && ($storage == 'ldap' || $storage == 'all')) {

			$result = self::_ldap_update($this->cn, $this->toArray('ldap',$legacy,!$this->_updateAll), $this->_ldapLegacy);
			
			if ($result === false) {
				$this->_error(__FUNCTION__ . ": LDAP update failed", E_USER_WARNING);
			}
		}
		
		$this->_updateAll = false;
		return $result;
	}

	private function _ldap_delete($cn = null, $legacy = false, $verbose = false, $dryrun = false)
	{
		$conn = &Hubzero_Factory::getPLDC();
		$xhub = &Hubzero_Factory::getHub();
		
		if (empty($conn) || empty($xhub)) {
			return false;
		}
		
		if (empty($cn)) {
			return false;
		}

		if ($legacy) {
			$dn = "gid=" . $cn . ",ou=groups," . $xhub->getCfg('hubLDAPBaseDN');
		}
		else {
			$dn = "cn=" . $cn . ",ou=groups," . $xhub->getCfg('hubLDAPBaseDN');
		}
		
		if ($verbose) {
			echo "DELETE $dn<br>";
		}

		if ($dryrun)
			return 0;

		@ldap_delete($conn, $dn);

		return @ldap_errno($conn);
	}

	private function _mysql_delete($group)
	{
		$db = JFactory::getDBO();
		
		if (empty($db)) {
			return false;
		}
		
		if (!is_numeric($group)) {
			$db->setQuery("SELECT gidNumber FROM #__xgroups WHERE cn=" . $db->Quote($group) . ";");
			
			$group = $db->loadResult();
		}
		
		if (!is_numeric($group)) {
			return false;
		}
		
		$db->setQuery("DELETE FROM #__xgroups WHERE gidNumber=" . $db->Quote($group) . ";");
		
		if (!$db->query()) {
			return false;
		}
		
		$db->setQuery("DELETE FROM #__xgroups_applicants WHERE gidNumber=" . $db->Quote($group) . ";");
		$db->query();
		$db->setQuery("DELETE FROM #__xgroups_invitees WHERE gidNumber=" . $db->Quote($group) . ";");
		$db->query();
		$db->setQuery("DELETE FROM #__xgroups_managers WHERE gidNumber=" . $db->Quote($group) . ";");
		$db->query();
		$db->setQuery("DELETE FROM #__xgroups_members WHERE gidNumber=" . $db->Quote($group) . ";");
		$db->query();
		
		return true;
	}

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
			$result = $this->_mysql_delete($this->gidNumber ? $this->gidNumber : $this->cn);
			
			if ($result === false) {
				$this->_error(__FUNCTION__ . ": MySQL deletion failed", E_USER_WARNING);
				return false;
			}
		}
		
		if ($result === true && ($storage == 'ldap' || $storage == 'all')) {
			$result = self::_ldap_delete($this->cn,$this->_ldapLegacy);
			
			if ($result === false) {
				$this->_error(__FUNCTION__ . ": LDAP deletion failed", E_USER_WARNING);
				return false;
			}
		}
		
		return true;
	}

	private function __get($property = null)
	{
		$xlog = &Hubzero_Factory::getLogger();
		
		if (!property_exists(__CLASS__, $property) || $property{0} == '_') {
			if (empty($property)) {
				$property = '(null)';
			}
			
			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}
		
		if (in_array($property, self::$_list_keys)) {
			if (!array_key_exists($property, get_object_vars($this))) {
				$db = &JFactory::getDBO();
				
				if (is_object($db)) {
					if (in_array($property, array('members', 'applicants', 'managers', 'invitees'))) {
						$aux_table = "#__xgroups_" . $property;
						$query = "SELECT uidNumber FROM $aux_table AS aux" . " WHERE " .
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

	private function __set($property = null, $value = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_') {
			if (empty($property)) {
				$property = '(null)';
			}
			
			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}
		
		if (in_array($property, self::$_list_keys)) {
			$value = array_diff((array) $value, array(''));

			if (in_array($property, array('managers','members','applicants','invitees'))) {
				$value = $this->_userids($value);
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

	private function __isset($property = null)
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

	private function __unset($property = null)
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

        $sql = "SELECT id FROM #__users WHERE username IN ( $set );";

        $db->setQuery($sql);

        $result = $db->loadResultArray();

        if (empty($result))
            $result = array();

        $result = array_merge($result,$userids);

        return $result;
    }

	public function add($key = null, $value = array())
	{
		$users = $this->_userids($value);

		$this->__set($key, array_merge($this->__get($key), $users));
	}

	public function remove($key = null, $value = array())
	{
		$users = $this->_userids($value);
		$this->__set($key, array_diff($this->__get($key), $users));
	}

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

	static public function exists($group)
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

	static function find($filters = array())
	{
		$db = &JFactory::getDBO();
		
		// Type 0 - System Group
		// Type 1 - HUB Group
		// Type 2 - Project Group
		// Type 3 - Partner "Special" Group
		$gTypes = array('all','system','hub','project','partner','0','1','2','3');
		
		$types = !empty($filters['type']) ? $filters['type'] : array('all');
		
 		foreach($types as $type) {
			if (!in_array($type, $gTypes))
				return false;
		}
		
		if(in_array('all',$types)) {
			$where_clause = '';
		} else {
			$t = implode(",",$types);
			if($t == 'hub') $t = 1;
			if($t == 'project') $t = 2;
			if($t == 'partner') $t = 3;
			if($t == 'system') $t = 0;
			$where_clause = 'WHERE type IN (' . $t . ')';
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
		
		if(isset($filters['authorized']) && $filters['authorized'] === 'admin') {
			$where_clause .= "";
		} else {
			if ($where_clause != '') {
				$where_clause .= " AND";
			}
			else {
				$where_clause .= "WHERE";
			}
			$where_clause .= " privacy=0";
		}
		
		if (isset($filters['policy']) && $filters['policy']) {
			if ($where_clause != '') {
				$where_clause .= " AND";
			}
			else {
				$where_clause .= "WHERE";
			}
			switch ($filters['policy']) 
			{
				case 'closed': $where_clause .= " join_policy=3"; break;
				case 'invite': $where_clause .= " join_policy=2"; break;
				case 'restricted': $where_clause .= " join_policy=1";  break;
				case 'open':
				default: $where_clause .= " join_policy=0"; break;
			}
		}
		
		if (empty($filters['fields']))
			$filters['fields'][] = 'cn';
		
		$field = implode(',', $filters['fields']);
		
		$query = "SELECT $field FROM #__xgroups $where_clause";
		if (isset($filters['sortby']) && $filters['sortby'] != '') {
			$query .= " ORDER BY ";
			switch ($filters['sortby']) 
			{
				case 'alias': $query .= 'cn ASC'; break;
				case 'title': $query .= 'description ASC'; break;
				default: $query .= $filters['sortby']; break;
			}
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
	
	//-----------
	
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

		$result = $db->loadResultArray();

		return !empty($result);
	}

	public function isMember($uid)
	{
		return $this->is_member_of('members',$uid);
	}

	public function isApplicant($uid)
	{
		return $this->is_member_of('applicants',$uid);
	}

	public function isManager($uid)
	{
		return $this->is_member_of('managers',$uid);
	}

	public function isInvitee($uid)
	{
		return $this->is_member_of('invitees',$uid);
	}
	
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

	private function _ldap_read($cn, $legacy)
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
			$filter = "(&(objectClass=posixGroup)(gidNumber=$cn))";
		else
			$filter = "(&(objectClass=posixGroup)(cn=$cn))";

		$dn = "ou=groups," . $hubLDAPBaseDN;

		$entry = @ldap_search($conn, $dn, $filter, array("*"), 0, 0, 0, 3);

		if (empty($entry)) {
			return false;
		}
		
		$count = ldap_count_entries($conn, $entry);
		
		if ($count <= 0) {
			return false;
		}

		$firstentry = ldap_first_entry($conn, $entry);
		$attr = ldap_get_attributes($conn, $firstentry);

		return self::_ldap_flatten_attributes($attr);
	}

	private function _ldap_exists($cn, $legacy = false)
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
		
		if ($legacy) {
			$oc = '(objectclass=hubGroup)';
		}
		else {
			$oc = '(objectclass=posixGroup)(!(objectclass=hubGroup))';
		}

		if (is_numeric($cn))
			$filter = "(&" . $oc . "(gidNumber=$cn))";
		else
			$filter = "(&" . $oc . "(cn=$cn))";


		$dn = "ou=groups," . $hubLDAPBaseDN;

		$entry = @ldap_search($conn, $dn, $filter, array("cn"), 0, 0, 0, 3);

		if (empty($entry)) {
			return false;
		}
		
		$count = ldap_count_entries($conn, $entry);
		
		if ($count <= 0) {
			return false;
		}

		return true;
	}
	

	private function _ldap_flatten_attributes($attr)
	{
		$info = array();

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

	public function importSQLfromLDAP($extended = false, $replace = false, $update = false, $legacy = false, $verbose = false, $dryrun = false)
	{
	    $xhub = &Hubzero_Factory::getHub();
        $conn = &Hubzero_Factory::getPLDC();

        if (empty($conn) || empty($xhub)) {
            return false;
        }

        $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

        $dn = 'ou=groups,' . $hubLDAPBaseDN;

		if ($legacy) {
			$filter = '(objectclass=hubGroup)';
		}
		else {
			$filter = '(&(objectclass=posixGroup)(!(objectclass=hubGroup)))';
		}

        $sr = ldap_search($conn, $dn, $filter, array('*'), 0, 0, 0);

        if ($sr === false) {
            return false;
        }
		
        if (ldap_count_entries($conn, $sr) == false) {
            return false;
        }

        $entry = ldap_first_entry($conn, $sr);

        while ($entry !== false) {

            $attributes = ldap_get_attributes($conn, $entry);

			$attributes = self::_ldap_flatten_attributes($attributes);

			if ($verbose)
				echo "PROCESSING GROUP: " . $attributes['cn'] . "<br>";

			$exists = self::exists($attributes['cn']);

			if ($replace && $exists) {
				if (!$dryrun)
					self::_mysql_delete($attributes['cn']);
			}

			if ($verbose && !$exists)
				echo "IMPORTING NEW GROUP: " . $attributes['cn'] . "<br>";

			if (!$dryrun && !$exists) {
				$result = self::_mysql_create($attributes['cn'],$attributes['gidNumber']);

				if ($verbose) {
					if ($result)
						echo "CREATED: " . $attributes['cn'] . "<br />";
					else
						echo "FAILED TO CREATE: " . $attributes['cn'] . "<br />";
				}
			}

			$data = self::_ldap_convert($attributes,$legacy);

			// if extended data is requested from a non-legacy schema
			// guess at some basic values based on cn and gidNumber
			// conventions

			if ($extended && !$legacy) {
				if (!$exists || $replace) {
					if (strpos($attributes['cn'],'app-') === 0) {
						$data['type'] = '2'; // project
						$data['published'] = '1';
						$data['access'] = '4';
						$data['join_policy'] = '1';
						$data['privacy'] = '0';
					}
					else if (strpos($attributes['cn'],'-') !== false) {
						$data['type'] = '0'; // system
						$data['published'] = '1';
						$data['access'] = '0';
						$data['join_policy'] = '1';
						$data['privacy'] = '0';
					}
					else if ($attributes['gidNumber'] < 1000) {
						$data['type'] = '0'; // system
						$data['published'] = '1';
						$data['access'] = '0';
						$data['join_policy'] = '1';
						$data['privacy'] = '0';
					}
					else {
						$data['type'] = '1'; // hub
						$data['published'] = '1';
						$data['access'] = '0';
						$data['join_policy'] = '1';
						$data['privacy'] = '0';
					}
				}
			}

			if (!$dryrun && ($update || $replace || !$exists)) {
				$result = self::_mysql_update($attributes['gidNumber'], $data);

				if ($verbose) {
					if ($result)
						echo "UPDATED: " . $attributes['cn'] . "<br />";
					else
						echo "FAILED TO UPDATE: " . $attributes['cn'] . "<br />";
				}
			}

            $entry = ldap_next_entry($conn, $entry);
        }
	}

	public function exportSQLtoLDAP($extended = false, $replace = false, $update = false, $legacy = false, $verbose = false, $dryrun = false)
	{
	    $xhub = &Hubzero_Factory::getHub();
        $conn = &Hubzero_Factory::getPLDC();

        if (empty($conn) || empty($xhub)) {
            return false;
        }

        $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		$db = &JFactory::getDBO();

        $query = "SELECT cn FROM #__xgroups;";

        $db->setQuery($query);

        $result = $db->query();

        if ($result === false) {
            return false;
        }

        while ($row = mysql_fetch_row($result)) {

			if ($verbose)
				echo "PROCESSING GROUP: " . $row[0] . "<br>";

    		$dhzg = self::getInstance($row[0],'mysql');

			if ($dhzg === false) {

				if ($verbose)
					echo "Unable to read SQL data for group: " . $row[0] . "<br>";

				continue;
			}

			if ($replace) {
            	self::_ldap_delete($row[0],false,$verbose,$dryrun);
	            self::_ldap_delete($row[0],true,$verbose,$dryrun);
			}

			$data = $dhzg->toArray('ldap',$legacy);

			if ($extended)
				$info = $data;
			else {

				$info['cn'] = $data['cn'];
				$info['gidNumber'] = $data['gidNumber'];
				$info['memberUid'] = $data['memberUid'];

				if ($legacy) {
					$info['gid'] = $data['cn'];
					$info['member'] = $data['member'];
				}
			}
				
			$exists = self::_ldap_exists($row[0],$legacy);

			if (!$exists)
				self::_ldap_create($row[0],$info,$legacy,$verbose,$dryrun);

			if (!$exists || $update)
			{
				$myresult = self::_ldap_update($row[0],$info,$legacy,$replace,$verbose,$dryrun);
			}
			else
				self::_ldap_create($row[0],$info,$legacy,$verbose,$dryrun);
        }

        mysql_free_result($result);
    }

	//----
	// New function for new groups (Chris)
	//----
	
	public function search_roles($role='') 
	{
		if ($role == '')
			return false;
		
		$roles = '#__xgroups_roles';
		$member_roles = '#__xgroups_member_roles';
		
		$db = & JFactory::getDBO();
		
		$query = "SELECT uidNumber FROM $roles as r, $member_roles as m WHERE r.id='".$role."' AND r.id=m.role AND r.gidNumber='".$this->gidNumber."'";

		$db->setQuery($query);
		$result = $db->loadResultArray();
		
		$result = array_intersect($result,$this->members);
	
		if(count($result) > 0) {
			return $result;
		}
	}
	
	//----
	// New function with new groups (Chris)
	//----
	
	public function getPluginAccess( $get_plugin = '' )
	{
		// Get plugins
		JPluginHelper::importPlugin( 'groups' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Trigger the functions that return the areas we'll be using
		//then add overview to array
		$hub_group_plugins = $dispatcher->trigger( 'onGroupAreas', array( ) );
		array_unshift($hub_group_plugins, array('name'=>'overview','title'=>'Overview','default_access'=>'anyone'));
		
		//array to store plugin preferences when after retrieved from db
		$active_group_plugins = array();
		
		//get the group plugin preferences
		//returns array of tabs and their access level (ex. [overview] => 'anyone', [messages] => 'registered')
		$group_plugins = $this->get('plugins');
		if($group_plugins) {
			$group_plugins = explode(',',$group_plugins);
			foreach($group_plugins as $plugin) {
				$temp = explode('=',trim($plugin));
				if($temp[0]) {
					$active_group_plugins[$temp[0]] = trim($temp[1]);
				}
			}
		}
		
		//array to store final group plugin preferences
		//array of acceptable access levels
		$group_plugin_access = array();
		$acceptable_levels = array('nobody','anyone','registered','members');
		
		//if we have already set some 
		if($active_group_plugins) {
			//for each plugin that is active on the hub
			foreach($hub_group_plugins as $hgp) {
				//if group defined access level is not an acceptable value or not set use default value that is set per plugin
				//else use group defined access level
				if(!in_array($active_group_plugins[$hgp['name']], $acceptable_levels)) {
					$value = $hgp['default_access'];
				} else {
					$value = $active_group_plugins[$hgp['name']];
				}

				//store final  access level in array of access levels
				$group_plugin_access[$hgp['name']] = $value;
			}
		} else {
			//for each plugin that is active on the hub
			foreach($hub_group_plugins as $hgp) {
				$value = $hgp['default_access'];
				
				//store final  access level in array of access levels
				$group_plugin_access[$hgp['name']] = $value;
			}
		}
		
		//if we wanted to return only a specific level return that otherwise return all access levels
		if($get_plugin != '') {
			return $group_plugin_access[$get_plugin];
		} else {
			return $group_plugin_access;
		}
	}


}

