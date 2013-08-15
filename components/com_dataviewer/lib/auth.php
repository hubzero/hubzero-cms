<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2012,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


function dv_auth()
{
	global $dv_conf;
	$juser =& JFactory::getUser();
	ximport('Hubzero_Group');
	ximport('Hubzero_User_Helper');

	if (isset($dd['acl']['allowed_users']) && (is_array($dd['acl']['allowed_users']) || $dd['acl']['allowed_users'] === false || $dd['acl']['allowed_users'] == 'registered')) {
		$dv_conf['acl']['allowed_users'] = $dd['acl']['allowed_users'];
	}

	if (isset($dd['acl']['allowed_groups']) && (is_array($dd['acl']['allowed_groups']) || $dd['acl']['allowed_groups'] === false)) {
		$dv_conf['acl']['allowed_groups'] = $dd['acl']['allowed_groups'];
	}

	if ($dv_conf['acl']['allowed_users'] === false && $dv_conf['acl']['allowed_groups'] === false) {
		return true;
	} elseif ($juser->get('guest')) {
		$redir_url = '?return=' . base64_encode($_SERVER['REQUEST_URI']);
		$login_url = '/login';
		$url = $login_url . $redir_url;
		header('Location: ' . $url);
		return;
	}

	if ($dv_conf['acl']['allowed_users'] !== false && $dv_conf['acl']['allowed_users'] == 'registered' && !$juser->get('guest')) {
		return true;
	} elseif (isset($dv_conf['acl']['allowed_users']) && is_array($dv_conf['acl']['allowed_users']) && !$juser->get('guest')) {
		if(in_array($juser->get('username'), $dv_conf['acl']['allowed_users'])) {
			return true;
		}
	}
	
	if ($dv_conf['acl']['allowed_groups'] !== false && is_array($dv_conf['acl']['allowed_groups']) && !$juser->get('guest')) {
		$groups = Hubzero_User_Helper::getGroups($juser->get('id'));
		if ($groups && count($groups)) {
			foreach ($groups as $g) {
				if (in_array($g->cn, $dv_conf['acl']['allowed_groups'])) {
					return true;
				}
			}
		}
	}

	return false;
}
?>
