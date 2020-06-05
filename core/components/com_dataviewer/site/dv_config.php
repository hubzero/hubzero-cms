<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();


global $html_path, $com_name, $dv_conf;

$html_path = str_replace(PATH_ROOT, '', __DIR__) . '/html';
$com_name = basename(dirname(__DIR__));
$com_name = str_replace('com_', '', $com_name);
$com_name = trim($com_name, DS);
$com_path = str_replace(PATH_ROOT, '', __DIR__);

$dv_conf['settings']['com_name'] = $com_name;

define('DV_COM', $com_name);
define('DV_COM_PATH', $com_path);
define('DV_COM_HTML', DV_COM_PATH . DS . 'html');
define('DV_PATH_HTML', __DIR__ . DS . 'html');

$params = Component::params('com_dataviewer');

$dv_conf['settings']['num_rows'] = array('labels'=>array(5, 10, 25, 50, 100), 'values'=>array(5, 10, 25, 50, 100));
$dv_conf['settings']['limit'] = $params->get('record_display_limit') == '' ? 10 : $params->get('record_display_limit');
$dv_conf['settings']['serverside'] = false;

/* Processing mode switching*/
$dv_conf['proc_mode_switch'] = ($params->get('processing_mode_switch') == '1') ? true : false;
$dv_conf['proc_switch_threshold'] = intval($params->get('proc_switch_threshold'));


$dv_conf['null_desc'] = $params->get('null_desc');
$dv_conf['help_file_base_path'] = '';
$dv_conf['db'] = array();


/* Access Control */
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
