<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2010-2013 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2010-2013 by Purdue Research Foundation, West Lafayette, IN 47906.
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

defined('_JEXEC') or die('Restricted access');

global $dv_conf;
$params = &JComponentHelper::getParams('com_prj_dv');

$dv_conf['settings']['num_rows'] = array('labels'=>array(5, 10, 15, 20, 25, 50, 100), 'values'=>array(5, 10, 15, 20, 25, 50, 100));
$dv_conf['settings']['limit'] = $params->get('record_display_limit');
$dv_conf['settings']['serverside'] = false;

$dv_conf['null_desc'] = $params->get('null_desc');

$dv_conf['db'] = array(
	'host'=>$params->get('db_host'),
	'user'=>$params->get('db_user'),
	'pass' =>$params->get('db_pass'),
	'name' =>$params->get('db_name')
);

$dv_conf['dd_json'] = '/data/databases/data-definitions-prj';
$dv_conf['help_file_base_path'] = $params->get('help_file_path');


$acl_users = $params->get('acl_users');
if ($acl_users == 'registered') {
	$dv_conf['acl']['allowed_users'] = 'registered';
} elseif ($acl_users != 'registered' && $acl_users != '') {
	$dv_conf['acl']['allowed_users'] = array_map('trim', explode(',', $acl_users));
} else {
	$dv_conf['acl']['allowed_users'] = false;
}

$acl_groups = $params->get('acl_groups');
if ($acl_groups != '') {
	$dv_conf['acl']['allowed_groups'] = array_map('trim', explode(',', $acl_groups));
} else {
	$dv_conf['acl']['allowed_groups'] = false;
}
?>
