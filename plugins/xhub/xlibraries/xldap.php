<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * xHUB LDAP Class
 *
 *     This code is a duplicate of the global functions found in the 
 *     nanoHUB account directory. This code is awaiting refactoring.
 *     This code should mostly just be used by the XUser class.
 *
 **/

jimport('joomla.client.ldap');
ximport("xfactory");

class XLDAP extends JLDAP {
    var $acctmgrDN;
	var $acctmgrPW;
	var $readonly;
	var $slavehosts;
	var $masterhost;

    function XLDAP() {
        $xhub =& XFactory::getHub();

        $this->masterhost = $xhub->getCfg('hubLDAPMasterHost','localhost');
		$this->slavehosts = $xhub->getCfg('hubLDAPSlaveHosts','');
		$this->auth_method = $xhub->getCfg('hubLDAPAuthMethod','bind');
		$this->port = $xhub->getCfg('hubLDAPPort','389');
		$this->base_dn = $xhub->getCfg('hubLDAPBaseDN','ou=People,dc=localhost');
		$this->search_string = $xhub->getCfg('hubLDAPSearchString','uid=[search],ou=People,dc=localhost');
		$this->use_ldapV3 = $xhub->getCfg('hubLDAPUseLDAP3','1');
		$this->no_referrals = $xhub->getCfg('hubLDAPNoReferrals','1');
		$this->negotiate_tls = $xhub->getCfg('hubLDAPNegotiateTLS','0');
		$this->username = $xhub->getCfg('hubLDAPSearchUserDN','uid=search,dc=localhost');
		$this->password = $xhub->getCfg('hubLDAPSearchUserPW','');
		$this->acctmgrDN = $xhub->getCfg('hubLDAPAcctMgrDN','uid=acctmgr,dc=localhost');
		$this->acctmgrPW = $xhub->getCfg('hubLDAPAcctMgrPW','');
        $readonly = false;
        $this->host = $this->masterhost . ' ' . $this->slavehosts;
		empty($this->port) && $this->port = '389';

		parent::__construct();
	}

	function is_readonly() {
	    return $this->readonly;
	}

	function admin_bind() {
		$this->host = $this->masterhost;
	    
		if ($this->connect()) {
		    if ($this->bind($this->acctmgrDN, $this->acctmgrPW))
			    return true;
		    $this->close();
		}

		$this->host = $this->slavehosts;

        if ($this->connect()) {
		    if ($this->bind($this->acctmgrDN, $this->acctmgrPW))
			{
			    $this->readonly = true;
			    return true;
			}
		}

		return false;
	}

    function attrsearch($filter, $attributes, $attrsonly, $sizelimit, $timelimit, $deref)
	{
	    $result = array();

	    $entries = ldap_search($this->_resource, $this->base_dn, $filter, $attributes, $attrsonly, $sizelimit, $timelimit, $deref);
        $count = ldap_count_entries($this->_resource, $entries);
        if ($count > 0) {
		    $entry = ldap_first_entry($this->_resource, $entries);
			$j = 0;
			while($entry) {
			    $attr = ldap_get_attributes($this->_resource, $entry);

				if ($attr[$attributes[0]][0]) {
				    for($i=0; $i < count($attributes); $i++)
					    $result[$j][$attributes[$i]] = $attr[$attributes[$i]][0];
					$j++;
				}

				$entry = ldap_next_entry($this->_resource, $entry);
			}
		}

		return $result;
	}


function is_positiveint($x) {
    if(is_numeric($x) && intval($x) == $x && $x >= 0) {
        return(true);
    }
    else {
        return(false);
    }
}
}