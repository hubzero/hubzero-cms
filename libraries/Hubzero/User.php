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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * xHUB User Class
 **/

ximport('Hubzero_User_Profile');

define('XUSER_EXISTS', 1);

/**
 * Description for ''XUSER_CREATE_ERROR''
 */
define('XUSER_CREATE_ERROR', 2);

/**
 * Short description for 'Hubzero_User'
 * 
 * Long description (if any) ...
 */
class Hubzero_User extends JObject
{

	/**
	 * Description for '_xuser'
	 * 
	 * @var array
	 */
	var $_xuser;

	/**
	 * Short description for 'logDebug'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $msg Parameter description (if any) ...
	 * @return     void
	 */
	public function logDebug($msg)
	{
		$xlog =& Hubzero_Factory::getLogger();
		$xlog->logDebug($msg);
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
		$this->_xuser = array();
		$this->normalize();
	}

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $login Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($login = null)
	{
		//$this->logDebug("Hubzero_User::__construct($login)");

		if (is_null($login))
			$this->clear();
		else
			$this->load($login);
	}

	/**
	 * Short description for 'getInstance'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $login Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getInstance($login = null)
	{
		$xhub =& Hubzero_Factory::getHub();
		$xlog =& Hubzero_Factory::getLogger();

		if (empty($login))
		{
			$juser =& JFactory::getUser();

			if (!$juser->get('guest'))
				$login = $juser->get('id');
		}

		if (empty($login))
		{
			//$xlog->logDebug("Hubzero_User::getInstance(guest)");
			return false;
		}

		$instance = new Hubzero_User($login);

		$uid = $instance->get('uid');

		if (empty($uid))
		{
			//$xlog->logDebug("Hubzero_User::getInstance($login) failed to find user.");
			return false;
		}

		//$xlog->logDebug("Hubzero_User::getInstance($login)");
		return $instance;
	}

	/**
	 * Short description for 'normalize'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function normalize()
	{
		//$this->logDebug("Hubzero_User::normalize()");

		if (!is_array($this->_xuser))
			$this->_xuser = array();

		$keys = array( 'login', 'uid', 'encrypt_password', 'email_confirmed', 'home',
						'jobs_allowed', 'reg_date', 'reg_ip', 'reg_host', 'name',
						'email', 'org', 'orgtype', 'countryresident', 'countryorigin',
						'sex', 'nativetribe','web','phone', 'reason','usageagreement',
						'mod_date', 'mailPreferenceOption', 'proxy_password',
						'proxy_uid', 'password', 'guest');

		foreach($keys as $key)
			if (!array_key_exists($key, $this->_xuser))
				$this->_xuser[$key] = null;

		$keys = array('disability','hispanic','race','admin','hosts','edulevel','role','groups');

		foreach($keys as $key)
			if (!array_key_exists($key, $this->_xuser))
				$this->_xuser[$key] = array();
	}

	/**
	 * Short description for 'get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $key Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function get($key)
	{
		//$this->logDebug("Hubzero_User::get($key)");

		if (!is_array($this->_xuser))
			$this->normalize();

		if (!array_key_exists($key, $this->_xuser))
			die("Hubzero_User::get() Unknown key: $key \n");

		return $this->_xuser[$key];
	}

	/**
	 * Short description for 'set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $key Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function set($key,$value)
	{
		//$this->logDebug("Hubzero_User::set($key,$value)");

		if (!is_array($this->_xuser))
			$this->normalize();

		if (!array_key_exists($key, $this->_xuser))
			die("Hubzero_User::set() Unknown key: $key \n");

		$this->_xuser[$key] = $value;

		if ($key == 'password')
			$this->_xuser['encrypt_password'] = Hubzero_User_Helper::encrypt_password($value);
		else if ($key == 'encrypt_password')
			$this->_xuser['password'] = '';
	}

	/**
	 * Short description for 'getuser'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function getuser()
	{
		//$this->logDebug("Hubzero_User::getuser()");

		return $this->_xuser;
	}

	/**
	 * Short description for 'reload'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function reload()
	{
		$login = $this->get('login');

		$this->clear();
		$this->load($login);
	}

	/**
	 * Short description for 'deactivate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function deactivate() // in the future this will mark the record inactive, maybe clear host/admin flags from ldap
	{
		$xhub =& Hubzero_Factory::getHub();
		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');
		$ldapconn =& Hubzero_Factory::getPLDC();

		if (!$ldapconn)
			return false;

		// if this record is in the initial transient xdomain state, go ahead and delete it

		$parts = explode(':', $this->get('login'));

		if (count($parts) == 3)
		{
			$realm_id = intval($parts[0]);

			if ($realm_id < 0)
			{
				$dn = 'uid=' . $this->get('login') . ',ou=users,' . $hubLDAPBaseDN;

				return ldap_delete($ldapconn, $dn);
			}
		}

		// otherwise do nothing

		// TODO: add flag to mark it inactive, maybe remove host/admin flags from ldap

		return true;
	}

	/**
	 * Short description for 'delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $login Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function delete($login)
	{
		$xhub =& Hubzero_Factory::getHub();
		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');
		$ldapconn =& Hubzero_Factory::getPLDC();

		if (!$ldapconn || $login === null || $login === false)
			return false;

		$dn = "uid=" . $login . ",ou=users," . $hubLDAPBaseDN;

		$result = @ldap_delete($ldapconn, $dn);

		return $result;

	}

	/**
	 * Short description for 'load'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $login Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function load($login = null)
	{
		//$this->logDebug("Hubzero_User::load($login)");
		$xlog =& Hubzero_Factory::getLogger();
		//$xlog->logDebug("Hubzero_User::load($login)");

		$xhub =& Hubzero_Factory::getHub();
		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');
		$ldapconn =& Hubzero_Factory::getPLDC();

		if (!$ldapconn) {
			//$xlog->logDebug("Hubzero_User::load($login) failed to get primary ldap connection trying secondary");

			$ldapconn =& Hubzero_Factory::getLDC();

			//if (!$ldapconn)
			//	$xlog->logDebug("Hubzero_User::load($login) failed to get secondary ldap connection");
			//else
			//	$xlog->logDebug("Hubzero_User:: got secondary ldap connection");
		}
		//else
			//$xlog->logDebug("Hubzero_User:: got primary ldap connection");

		if (!$ldapconn)
			return false;

		if ($login === null)
			$login = $this->get('login');

		if (strlen($login) == 0)
			return false;

		if (!is_numeric($login))
		{
				$base_dn = 'uid=' . $login . ',ou=users,' . $hubLDAPBaseDN;
			$filter = '(objectclass=*)';
		}
		else
		{
			$base_dn = 'ou=users,' . $hubLDAPBaseDN;
			$filter = '(uidNumber=' . $login . ')';
		}

		$attributes[] = 'uid';
		$attributes[] = 'uidNumber';
		$attributes[] = 'userPassword';
		$attributes[] = 'emailConfirmed';
		$attributes[] = 'homeDirectory';
		$attributes[] = 'jobsAllowed';
		$attributes[] = 'regDate';
		$attributes[] = 'regIp';
		$attributes[] = 'regHost';
		$attributes[] = 'cn';
		$attributes[] = 'mail';
		$attributes[] = 'o';
		$attributes[] = 'orgtype';
		$attributes[] = 'countryresident';
		$attributes[] = 'countryorigin';
		$attributes[] = 'sex';
		$attributes[] = 'disability';
		$attributes[] = 'hispanic';
		$attributes[] = 'race';
		$attributes[] = 'nativetribe';
		$attributes[] = 'url';
		$attributes[] = 'homePhone';
		$attributes[] = 'description';
		$attributes[] = 'usageAgreement';
		$attributes[] = 'admin';
		$attributes[] = 'edulevel';
		$attributes[] = 'role';
		$attributes[] = 'modDate';
		$attributes[] = 'mailPreferenceOption';
		$attributes[] = 'proxyUidNumber';
		$attributes[] = 'proxyPassword';

		$sr = @ldap_search($ldapconn, $base_dn, $filter, $attributes, 0, 1, 0, 3);

		if ($sr === false)
		{
			$err = ldap_error($ldapconn);
			//$xlog->logDebug("Hubzero_User::load($login) ldap search failed to find user: $err");

			return false;
		}

		$entry = @ldap_first_entry($ldapconn, $sr);

		if ($entry === false)
			return false;

		$attributes = ldap_get_attributes($ldapconn, $entry);

		$this->set('login', isset($attributes['uid'][0]) ? $attributes['uid'][0] : '');
		$this->set('uid', isset($attributes['uidNumber'][0]) ? $attributes['uidNumber'][0] : '');
		$this->set('encrypt_password', isset($attributes['userPassword'][0]) ? $attributes['userPassword'][0] : '');
		$this->set('email_confirmed', isset($attributes['emailConfirmed'][0]) ? $attributes['emailConfirmed'][0] : '');
		$this->set('home', isset($attributes['homeDirectory'][0]) ? $attributes['homeDirectory'][0] : '');
		$this->set('jobs_allowed', isset($attributes['jobsAllowed'][0]) ? $attributes['jobsAllowed'][0] : '');
		$this->set('reg_date', isset($attributes['regDate'][0]) ? $attributes['regDate'][0] : '');
		$this->set('reg_ip', isset($attributes['regIP'][0]) ? $attributes['regIP'][0] : '');
		$this->set('reg_host', isset($attributes['regHost'][0]) ? $attributes['regHost'][0] : '');
		$this->set('name', isset($attributes['cn'][0]) ? $attributes['cn'][0] : '');
		$this->set('email', isset($attributes['mail'][0]) ? $attributes['mail'][0] : '');
		$this->set('org', isset($attributes['o'][0]) ? $attributes['o'][0] : '');
		$this->set('orgtype', isset($attributes['orgtype'][0]) ? $attributes['orgtype'][0] : '');
		$this->set('countryresident', isset($attributes['countryresident'][0]) ? $attributes['countryresident'][0] : '');
		$this->set('countryorigin', isset($attributes['countryorigin'][0]) ? $attributes['countryorigin'][0] : '');
		$this->set('sex', isset($attributes['sex'][0]) ? $attributes['sex'][0] : '');

		if (isset($attributes['disability']) && $attributes['disability']['count'] > 0)
		{
			unset($attributes['disability']['count']);
			$this->set('disability', $attributes['disability']);
		}

		if (isset($attributes['hispanic']) && $attributes['hispanic']['count'] > 0)
		{
			unset($attributes['hispanic']['count']);
			$this->set('hispanic', $attributes['hispanic']);
		}

		if (isset($attributes['race']) && $attributes['race']['count'] > 0)
		{
			unset($attributes['race']['count']);
			$this->set('race', $attributes['race']);
		}

		$this->set('nativetribe', isset($attributes['nativeTribe'][0]) ? $attributes['nativeTribe'][0] : '');
		$this->set('web', isset($attributes['url'][0]) ? $attributes['url'][0] : '');
		$this->set('phone', isset($attributes['homePhone'][0]) ? $attributes['homePhone'][0] : '');
		$this->set('reason', isset($attributes['description'][0]) ? $attributes['description'][0] : '');

		if (isset($attributes['usageAgreement'][0]))
		{
			if (strcasecmp($attributes['usageAgreement'][0],'TRUE') == 0)
				$this->set('usageagreement', true);
			else
				$this->set('usageagreement', false);
		}

		if (isset($attributes['admin']) && $attributes['admin']['count'] > 0)
		{
			unset($attributes['admin']['count']);
			$this->set('admin', $attributes['admin']);
		}

		if (isset($attributes['edulevel']) && $attributes['edulevel']['count'] > 0)
		{
			unset($attributes['edulevel']['count']);
			$this->set('edulevel', $attributes['edulevel']);
		}

		if (isset($attributes['role']) && $attributes['role']['count'] > 0)
		{
			unset($attributes['role']['count']);
			$this->set('role', $attributes['role']);
		}

		$this->set('mod_date', isset($attributes['modDate'][0]) ? $attributes['modDate'][0] : '');
		$this->set('mailPreferenceOption', isset($attributes['mailPreferenceOption'][0]) ? $attributes['mailPreferenceOption'][0] : '');
		$this->set('proxy_uid', isset($attributes['proxyUidNumber'][0]) ? $attributes['proxyUidNumber'][0] : '');
		$this->set('proxy_password', isset($attributes['proxyPassword'][0]) ? $attributes['proxyPassword'][0] : '');

		$attributes = array();
		$attributes[] = 'gid';
		$attributes[] = 'groupName';
		$attributes[] = 'description';
		$attributes[] = 'public';

		foreach(array('applicant','member','owner') as $type)
		{
			$filter = '(&(objectclass=hubGroup)(' . $type . '=uid=' . $this->get('login') . ',ou=users,' . $hubLDAPBaseDN . '))';

			$dn = 'ou=groups,' . $hubLDAPBaseDN;
			$sr = ldap_search($ldapconn, $dn, $filter, $attributes, 0, 0, 0, 3);

			if (empty($sr))
				continue;

			$entry = ldap_first_entry($ldapconn, $sr);

			while($entry)
			{
			$attr = ldap_get_attributes($ldapconn, $entry);

				if (!empty($attr['count']))
				{
					$group = array();
				$group['gid'] = isset($attr['gid'][0]) ? $attr['gid'][0] : null;
				$group['name'] = isset($attr['groupName'][0]) ? $attr['groupName'][0] : null;
				$group['description'] = isset($attr['description'][0]) ? $attr['description'][0] : null;
				$group['confirmed'] = isset($attr['public'][0]) ? $attr['public'][0] : null;
				$group['manager'] = ($type == 'owner') ? 1 : 0;
				$group['regconfirmed'] = ($type == 'member') ? 1 : 0;
				$groups[] = $group;
				}
			$entry = ldap_next_entry($ldapconn, $entry);
			}
		}

		$this->set('groups', isset($groups) ? $groups : '');
	}

	/**
	 * Short description for 'create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function create()
	{
		//$this->logDebug("Hubzero_User::create()");

		$xhub =& Hubzero_Factory::getHub();
		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');
		$ldapconn =& Hubzero_Factory::getPLDC();
		$dn = 'uid=' . $this->get('login') . ',ou=users,' . $hubLDAPBaseDN;

		if ($this->get('uid') === false)
			return false;

		if ($this->get('uid') === null)
			return false;

		if ($this->get('uid') === '')
			return false;

		$entry['objectclass'][] = 'top';
		$entry['objectclass'][] = 'person';
		$entry['objectclass'][] = 'organizationalPerson';
		$entry['objectclass'][] = 'inetOrgPerson';
		$entry['objectclass'][] = 'posixAccount';
		$entry['objectclass'][] = 'shadowAccount';
		$entry['objectclass'][] = 'hubAccount';

		if (strlen($this->get('name')) > 0)
			$entry['cn'] = $this->get('name');

		if (strlen($this->get('login')) > 0)
			$entry['uid'] = $this->get('login');

		if (strlen($this->get('uid')) > 0)
			$entry['uidNumber'] = $this->get('uid');

		$entry['gidNumber'] = '3000';
		$entry['homeDirectory'] = $this->get('home');
		$entry['gid'] = 'public';

		if (strlen($this->get('password')) > 0)
			$entry['userPassword'] = Hubzero_User_Helper::encrypt_password($this->get('password'));

		if (strlen($this->get('name')) > 0)
			$entry['sn'] = $this->get('name');

		if (strlen($this->get('orgtype')) > 0)
			$entry['orgtype'] = $this->get('orgtype');

		if (strlen($this->get('countryresident')) > 0)
			$entry['countryresident'] = $this->get('countryresident');

		if (strlen($this->get('countryorigin')) > 0)
			$entry['countryorigin'] = $this->get('countryorigin');

		if (strlen($this->get('sex')) > 0)
			$entry['sex'] = $this->get('sex');

		$attributes = $this->get('disability');
		foreach($attributes as $attribute)
			$entry['disability'][] = $attribute;

		$attributes = $this->get('hispanic');
		foreach($attributes as $attribute)
			$entry['hispanic'][] = $attribute;

		$attributes = $this->get('race');
		foreach($attributes as $attribute)
			$entry['race'][] = $attribute;

		$attributes = $this->get('edulevel');
		foreach($attributes as $attribute)
			$entry['edulevel'][] = $attribute;

		$attributes = $this->get('role');
		foreach($attributes as $attribute)
			$entry['role'][] = $attribute;

		if (strlen($this->get('nativetribe')) > 0)
			$entry['nativeTribe'] = $this->get('nativetribe');

		if (strlen($this->get('email')) > 0)
			$entry['mail'] = $this->get('email');

		if (strlen($this->get('email_confirmed')) > 0)
			$entry['emailConfirmed'] = $this->get('email_confirmed');

		if (strlen($this->get('web')) > 0)
			$entry['url'] = $this->get('web');

		if (strlen($this->get('phone')) > 0)
			$entry['homePhone'] = $this->get('phone');

		if (strlen($this->get('reason')) > 0)
			$entry['description'] = $this->get('reason');

		if (strlen($this->get('mailPreferenceOption')) > 0)
			$entry['mailPreferenceOption'] = $this->get('mailPreferenceOption');

		if ($this->get('usageagreement') !== null)
			$entry['usageAgreement'] = $this->get('usageagreement') ? 'TRUE' : 'FALSE';

		if (strlen($this->get('reg_ip')) > 0)
			$entry['regIp'] = $this->get('reg_ip');

		if (strlen($this->get('reg_date')) > 0)
			$entry['regDate'] = $this->get('reg_date');

		if (strlen($this->get('reg_host')) > 0)
			$entry['regHost'] = $this->get('reg_host');

		if (strlen($this->get('jobs_allowed')) > 0)
			$entry['jobsAllowed'] = $this->get('jobs_allowed');

		$entry['loginShell'] = '/bin/bash';
		$entry['ftpShell'] = '/usr/lib/sftp-server';

		$juser = JFactory::getUser();

		if (!$juser->get('guest'))
		{
			if (strlen($juser->get('id')) > 0)
				$entry['proxyUidNumber'] = $juser->get('id');

			if (strlen($this->get('password')) > 0)
				$entry['proxyPassword'] = $this->get('password');
		}

		$result = @ldap_add($ldapconn,$dn,$entry);

		if (empty($result))
		{
			$errno = ldap_errno($ldapconn);

			if ($errno == 0x44)
				return(XUSER_EXISTS);

			return(XUSER_CREATE_ERROR);
		}

		$this->reload();

        $xprofile = new Hubzero_User_Profile();
		$xprofile->load($this->get('uid'), 'ldap');

		$bits = explode(' ', $xprofile->get('name'));
		$xprofile->set('surname', array_pop($bits));
		if (count($bits) >= 1) {
			$xprofile->set('givenName', array_shift($bits));
		}
		if (count($bits) >= 1) {
			$xprofile->set('middleName', implode(' ',$bits));
		}

		// Load the com_members config if available
		$component =& JComponentHelper::getComponent( 'com_members' );
		if (!trim($component->params)) {
			$path = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'config.xml';
			if (!is_file($path)) {
				$path = '';
			}
			$jconfig = new JParameter( $component->params, $path );
			$data = $jconfig->renderToArray();
			$c = array();
			foreach ($data as $d=>$info)
			{
				if ($d != '@spacer') {
					$c[] = $d.'='.$info[4];
				}
			}
			$g = implode(n,$c);
			$config = new JParameter( $g );
		} else {
			$config =& JComponentHelper::getParams( 'com_members' );
		}
		if ($config->get('privacy') == 1) {
			$xprofile->set('public', 1);
		}

		$xprofile->store(false,'mysql');

		return 0;
	}

	/**
	 * Short description for '_renameUid'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $olduid Parameter description (if any) ...
	 * @param      string $newuid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function _renameUid($olduid, $newuid)
	{
		$ldapconn =& Hubzero_Factory::getPLDC();
		$xhub =& Hubzero_Factory::getHub();

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		if (!$ldapconn)
			return false;

		$dn = 'uid=' . $olduid . ',ou=users,' . $hubLDAPBaseDN;
		$rdn = 'uid=' . $newuid;

		ldap_rename($ldapconn, $dn, $rdn, 'ou=users,' . $hubLDAPBaseDN,true);
	}

	/**
	 * Short description for 'update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function update()
	{
		//$this->logDebug("Hubzero_User::update()");

		$ldapconn =& Hubzero_Factory::getPLDC();
		$xhub =& Hubzero_Factory::getHub();

		if (!$ldapconn)
			return false;

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		$dn = 'uid=' . $this->get('login') . ',ou=users,' . $hubLDAPBaseDN;

		$xuser = new Hubzero_User( $this->get('uid') );

		if ($this->get('uid') !== $xuser->get('uid'))
		{
			// INDEX FIELD, CAN'T MODIFY WITH THIS PROCEDURE

			die('can\'t change ldap uidNumber with regular update procedure');
		}

		if ($this->get('login') !== $xuser->get('login'))
			$this->_renameUid($xuser->get('login'), $this->get('login') );

		if ($this->get('encrypt_password') !== $xuser->get('encrypt_password'))
		{
			// REQUIRED FIELD, CAN'T DELETE

			if (strlen($this->get('encrypt_password')) > 0)
				$attributes['userPassword'] = $this->get('encrypt_password');

		}

		if ($this->get('email_confirmed') !== $xuser->get('email_confirmed'))
			$attributes['emailConfirmed'] = strlen($this->get('email_confirmed')) > 0 ? $this->get('email_confirmed') : array();

		if ($this->get('home') !== $xuser->get('home'))
		{
			// REQUIRED FIELD, CAN'T DELETE

			if (strlen($this->get('home')) > 0)
				$attributes['homeDirectory'] = $this->get('home');
		}

		if ($this->get('jobs_allowed') !== $xuser->get('jobs_allowed'))
			$attributes['jobsAllowed'] = (strlen($this->get('jobs_allowed')) > 0) ? $this->get('jobs_allowed') : array();

		if ($this->get('reg_date') !== $xuser->get('reg_date'))
			$attributes['regDate'] = (strlen($this->get('reg_date')) > 0) ? $this->get('reg_date') : array();

		if ($this->get('reg_ip') !== $xuser->get('reg_ip'))
			$attributes['regIp'] = (strlen($this->get('reg_ip')) > 0) ? $this->get('reg_ip') : array();

		if ($this->get('reg_host') !== $xuser->get('reg_host'))
			$attributes['regHost'] = (strlen($this->get('reg_host')) > 0) ? $this->get('reg_host') : array();

		if ($this->get('name') !== $xuser->get('name'))
			$attributes['sn'] = $attributes['cn'] = (strlen($this->get('name')) > 0) ? $this->get('name') : array();

		if ($this->get('email') !== $xuser->get('email'))
			$attributes['mail'] = (strlen($this->get('email')) > 0) ? $this->get('email') : array();

		if ($this->get('org') !== $xuser->get('org'))
			$attributes['o'] = (strlen($this->get('org')) > 0) ? $this->get('org') : array();

		if ($this->get('orgtype') !== $xuser->get('orgtype'))
			$attributes['orgtype'] = (strlen($this->get('orgtype')) > 0) ? $this->get('orgtype') : array();

		if ($this->get('countryresident') !== $xuser->get('countryresident'))
			$attributes['countryresident'] = (strlen($this->get('countryresident')) > 0) ? $this->get('countryresident') : array();

		if ($this->get('countryorigin') !== $xuser->get('countryorigin'))
			$attributes['countryorigin'] = (strlen($this->get('countryorigin')) > 0) ? $this->get('countryorigin') : array();

		if ($this->get('sex') !== $xuser->get('sex'))
			$attributes['sex'] = (strlen($this->get('sex')) > 0) ? $this->get('sex') : array();

		if ($this->get('nativetribe') !== $xuser->get('nativetribe'))
			$attributes['nativetribe'] = (strlen($this->get('nativetribe')) > 0) ? $this->get('nativetribe') : array();

		if ($this->get('web') !== $xuser->get('web'))
			$attributes['url'] = (strlen($this->get('web')) > 0) ? $this->get('web') : array();

		if ($this->get('phone') !== $xuser->get('phone'))
			$attributes['homePhone'] = (strlen($this->get('phone')) > 0) ? $this->get('phone') : array();

		if ($this->get('reason') !== $xuser->get('reason'))
			$attributes['description'] = (strlen($this->get('reason')) > 0) ? $this->get('reason') : array();

		if ($this->get('usageagreement') !== $xuser->get('usageagreement'))
		{
			$attributes['usageAgreement'] = array();

			if (strlen($this->get('usageagreement')) > 0)
			{
				if ($this->get('usageagreement') === true)
					$attributes['usageAgreement'] = 'TRUE';
				else
					$attributes['usageAgreement'] = 'FALSE';
			}
		}

		if ($this->get('mod_date') !== $xuser->get('mod_date'))
			$attributes['modDate'] = (strlen($this->get('mod_date')) > 0) ? $this->get('mod_date') : array();

		if ($this->get('mailPreferenceOption') !== $xuser->get('mailPreferenceOption'))
			$attributes['mailPreferenceOption'] = (strlen($this->get('mailPreferenceOption')) > 0) ?
				$this->get('mailPreferenceOption') : array();

		if ($this->get('proxy_password') !== $xuser->get('proxy_password'))
			$attributes['proxyPassword'] = (strlen($this->get('proxy_password')) > 0) ? $this->get('proxy_password') : array();

		if ($this->get('proxy_uid') !== $xuser->get('proxy_uid'))
			$attributes['proxyUidNumber'] = (strlen($this->get('proxy_uid')) > 0) ? $this->get('proxy_uid') : array();

		$keys = array('disability','hispanic','race','admin','hosts','edulevel','role');

		foreach($keys as $key)
		{
			$values = $this->get($key);

			if ($values !== $xuser->get($key)) {
				if (!empty($values)) {
					if (is_array($values)) {
						foreach ($values as $value)
						{
							$attributes[$key][] = $value;
						}
					} else {
						$attributes[$key][] = $values;
					}
				} else {
					$attributes[$key] = array();
				}
			}
		}

		$groups = $this->get('groups');

		if ($groups !== $xuser->get('groups'))
		{
			//die('groups can not be changed through the xuser object');
		}

		if (empty($attributes))
			return 0;

		$result = ldap_modify($ldapconn, $dn, $attributes);

		if ($result == false) {
			$errno = @ldap_errno($conn);
			return $errno;
		}

		$this->reload();

		$xprofile = new Hubzero_User_Profile();
		$xprofile->load($this->get('uid'), 'mysql');
		$xprofile->set('emailConfirmed', $this->get('email_confirmed'));
		$xprofile->set('userPassword', $this->get('encrypt_password'));
		$xprofile->set('username', $this->get('login'));
		$xprofile->set('homeDirectory', $this->get('home'));
		$xprofile->set('jobsAllowed', $this->get('jobs_allowed'));
		$xprofile->set('registerDate', $this->get('reg_date'));
		$xprofile->set('regIP', $this->get('reg_ip'));
		$xprofile->set('regHost', $this->get('reg_host'));
		$xprofile->set('name', $this->get('name'));
		$xprofile->set('email', $this->get('email'));
		$xprofile->set('orgtype', $this->get('orgtype'));
		$xprofile->set('organization', $this->get('org'));
		$xprofile->set('countryresident', $this->get('countryresident'));
		$xprofile->set('countryorigin', $this->get('countryorigin'));
		$xprofile->set('gender', $this->get('sex'));
		$xprofile->set('nativeTribe', $this->get('nativetribe'));
		$xprofile->set('url', $this->get('web'));
		$xprofile->set('phone', $this->get('phone'));
		$xprofile->set('reason', $this->get('reason'));
		$xprofile->set('usageAgreement', $this->get('usageagreement'));
		$xprofile->set('modifiedDate', $this->get('mod_date'));
		$xprofile->set('mailPreferenceOption', $this->get('mailPreferenceOption'));
		$xprofile->set('proxyPassword', $this->get('proxy_password'));
		$xprofile->set('proxyUidNumber', $this->get('proxy_uid'));
		$xprofile->set('disability', $this->get('disability'));
		$xprofile->set('hispanic', $this->get('hispanic'));
		$xprofile->set('race', $this->get('race'));
		$xprofile->set('admin', $this->get('admin'));
		$xprofile->set('hosts', $this->get('hosts'));
		$xprofile->set('edulevel', $this->get('edulevel'));
		$xprofile->set('role', $this->get('role'));
		$xprofile->store(false,'mysql');

		return true;
	}

	/**
	 * Short description for 'loadRegistration'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object &$registration Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadRegistration(&$registration)
	{
		if (!is_object($registration))
			return false;

		$keys = array('login', 'password', 'email', 'name', 'org', 'orgtype',
				'countryresident', 'countryorigin',
				'sex', 'disability', 'hispanic', 'race', 'nativetribe',
				'web', 'phone', 'reason', 'edulevel',
				'role');

		foreach($keys as $key)
			if ($registration->get($key) !== null)
				$this->set($key, $registration->get($key));

		if ($registration->get('mailPreferenceOption') !== null)
			$this->set('mailPreferenceOption', $registration->get('mailPreferenceOption') ? '2' : '0');

		if ($registration->get('usageAgreement') !== null)
			$this->set('usageagreement', $registration->get('usageAgreement') ? true : false);

		return true;
	}

	/**
	 * Short description for 'hasTransientUsername'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function hasTransientUsername()
	{
		$parts = explode(':', $this->get('login'));

		if ( count($parts) == 3 && intval($parts[0]) < 0 )
			return true;
	}

	/**
	 * Short description for 'getTransientUsername'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function getTransientUsername()
	{
		$parts = explode(':', $this->_registration['login'] );

		if ( count($parts) == 3 && intval($parts[0]) < 0 )
			return pack("H*", $parts[1]);
	}

	/**
	 * Short description for 'hasTransientEmail'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function hasTransientEmail()
	{
		if (eregi( "\.localhost\.invalid$", $this->get('email')))
			return true;
	}

	/**
	 * Short description for 'getTransientEmail'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function getTransientEmail()
	{
		if (eregi( "\.localhost\.invalid$", $this->_registration['email']))
		{
			$parts = explode('@', $this->_registration['email']);
			$parts = explode('-', $parts[0]);
			return pack("H*", $parts[2]);
		}
	}
}

