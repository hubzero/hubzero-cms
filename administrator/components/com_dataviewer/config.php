<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2005-2011,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Request ID/CSRF prevention
if (!isset($_SESSION['db'])) {
	$_SESSION['db'] = array();
}

if (!isset($_SESSION['db']['__rid'])) {
	$_SESSION['db']['__rid'] = sha1(uniqid('__rid', true));
}
define('DB_RID', $_SESSION['db']['__rid']);

global $conf, $com_name;
$document = &JFactory::getDocument();
$com_name = str_replace(JPATH_BASE.'/components/', '', JPATH_COMPONENT);
$com_name = str_replace('com_', '' , $com_name);

$com_path = str_replace(JPATH_BASE, '', JPATH_COMPONENT);


/* Paths */
define('DB_COM', $com_name);
define('DB_PATH', '/administrator' . $com_path);


$conf['com_name'] = $com_name;
$conf['com_path'] = $com_path;
$conf['app_title'] = 'Dataviewer';

$conf['dir_base'] = '/data/db';

$mode_db_enabled = &JComponentHelper::getParams('com_dataviewer')->get('mode_db') == '1' ? true : false;
$conf['modes']['db'] = array('enabled' => $mode_db_enabled);

// ACL
$conf['access_limit_to_group'] = false;
if ($conf['modes']['db']['enabled']) {
	$db_params = &JComponentHelper::getParams('com_databases');

	if ($db_params->get('access_limit_to_group') != '') {
		$conf['access_limit_to_group'] = $db_params->get('access_limit_to_group');
	}
}
?>
