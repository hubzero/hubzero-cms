<?php
/**
 * @package     hubzero.cms.admin
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

// Request ID/CSRF prevention
if (!isset($_SESSION['db'])) {
	$_SESSION['db'] = array();
}

if (!isset($_SESSION['db']['__rid'])) {
	$_SESSION['db']['__rid'] = sha1(uniqid('__rid', true));
}
define('DB_RID', $_SESSION['db']['__rid']);

global $conf, $com_name;
$document = App::get('document');
$com_name = Request::get('option'); //str_replace(PATH_CORE.'/components/', '', JPATH_COMPONENT);
$com_name = str_replace('com_', '' , $com_name);

$com_path = str_replace(PATH_ROOT, '', JPATH_COMPONENT);


/* Paths */
define('DB_COM', $com_name);
define('DB_PATH', $com_path);


$conf['com_name'] = $com_name;
$conf['com_path'] = $com_path;
$conf['app_title'] = 'Dataviewer';

// Base directory
$db_params = Component::params('com_databases');
$conf['dir_base'] = $db_params->get('base_dir');
if ($conf['dir_base'] == NULL || $conf['dir_base'] == '') {
	$conf['dir_base'] = '/db/databases';
}

$mode_db_enabled =  Component::params('com_dataviewer')->get('mode_db') == '1' ? true : false;
$conf['modes']['db'] = array('enabled' => $mode_db_enabled);

// ACL
$conf['access_limit_to_group'] = false;
if ($conf['modes']['db']['enabled']) {
	if ($db_params->get('access_limit_to_group') != '') {
		$conf['access_limit_to_group'] = $db_params->get('access_limit_to_group');
	}
}


// Makesure the files are not accessible by other
$conf['sys_umask'] = umask(0007);

?>
