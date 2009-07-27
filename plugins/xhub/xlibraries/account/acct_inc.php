<?php
/**
 * @package		HUBzero CMS
 * @author		Kevin Colby <colbykd@purdue.edu>
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

global $hub, $class, $url;

        $xhub =& XFactory::getHub();

/*
	$hub['name'] = "nanoHUB";
	$hub['url'] = "https://www.nanohub.org/";
	$hub['email'] = "support@nanohub.org";
	$hub['sysemail'] = "ncn-users@lists.nanohub.org";
	$hub['ldap_host'] = "ldaps://ldap.nanohub.org/ ldaps://ldap2.nanohub.org/";
	$hub['ldap_host_primary'] = "ldaps://ldap.nanohub.org/";
	$hub['ldap_base'] = "dc=nanohub,dc=org";
	$hub['home'] = "/home/nanohub";
*/
	$hub['name'] = $xhub->getCfg('hubShortName');
	$hub['url'] = $xhub->getCfg('hubLongURL');
	$hub['email'] = $xhub->getCfg('hubSupportEmail');
	$hub['sysemail'] = $xhub->getCfg('hubMonitorEmail');
	$hub['ldap_slaves'] = $xhub->getCfg('hubLDAPSlaveHosts');
	$hub['ldap_host_primary'] = $xhub->getCfg('hubLDAPMasterHost');
	$hub['ldap_host'] =  $hub['ldap_host_primary'];
	$hub['ldap_base'] = $xhub->getCfg('hubLDAPBaseDN');
	$hub['home'] = $xhub->getCfg('hubHomeDir');
	$class['table'] = "sectiontable";
	$class['th_center'] = "center-align";
	$class['th_right'] = "right-align";
	$class['div'] = "left";
	$class['text'] = "inputbox";
	$class['textarea'] = "inputbox";
	$class['submit'] = "submit";
	$class['checkbox'] = "checkbx";

	$url = array();
	$url['main']           = 'myaccount/'; //"/index.php?option=com_myaccount";
	$url['login']          = 'login/';
	$url['contact']        = "support/";
	$url['create']         = "/index.php?option=com_content&sectionid=5&task=view&id=";
	$url['info']           = "/index.php?option=com_content&sectionid=5&task=view&id=198";
	$url['pass_change']    = 'password/change';  // index.php?option=com_password&task=change
	$url['pass_lost']      = 'password/lost'; //index.php?option=com_content&sectionid=5&task=view&id=201;
	$url['courses']        = 'mynanohub/courses/';  // index.php?option=com_content&sectionid=5&task=view&id=199
	$url['course_request'] = 'mynanohub/courses/new/';  // index.php?option=com_content&sectionid=5&task=view&id=202
	$url['course_info']    = 'index.php?option=com_content&sectionid=5&task=view&id=203';  // mynanohub/courses/info/
	$url['course_users']   = 'index.php?option=com_content&sectionid=5&task=view&id=204';  // mynanohub/courses/users/
	$url['groups']         = 'groups/'; //"/index.php?option=com_content&sectionid=5&task=view&id=706";
	$url['group_request']  = "/index.php?option=com_content&sectionid=5&task=view&id=705";
	$url['group_info']     = "/index.php?option=com_content&sectionid=5&task=view&id=707";
	$url['group_users']    = "/index.php?option=com_content&sectionid=5&task=view&id=708";
	$url['confirm_email']  = '/email/confirm';
	$url['user_info']      = 'whois/'; //'index.php?option=com_whois&task=view';
	$url['user_edit']      = 'myaccount/edit';
	$url['user_delete']    = 'myaccount/delete';
	$url['user_lost']      = 'myaccount/resend';
	$url['user_limit']     = 'userlimit/';
	$url['feedback']       = 'feedback/';
	$url['admin']          = 'index.php?option=com_content&sectionid=5&task=view&id=';
	$url['usageAgreement'] = 'legal/terms/';
	$url['raceethnic']     = '/account/raceethnic.html';
	$url['confirmed']      = array();
	array_push($url['confirmed'], array('url' => '/', 'name' => $hub['name'] . ' Home Page'));
	array_push($url['confirmed'], array('url' => '/myhub', 'name' => 'my ' . $hub['name'] ));
	array_push($url['confirmed'], array('url' => '/search', 'name' => 'Search ' . $hub['name'] ));


function acc_extractfromdn($dn) {
	$id = null;
	$components = explode(',', $dn);
	if($components[0]) {
		$keyvalpair = explode('=', $components[0]);
		$id = $keyvalpair[1];
	}
	return($id);
}

?>
