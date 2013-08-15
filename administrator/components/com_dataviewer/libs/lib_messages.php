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

if(!isset($_SESSION['databases']['notifications'])) {
	$_SESSION['databases']['notifications'] = array();
}
	
function db_msg($msg, $type = 'error')
{
	$_SESSION['databases']['notifications'][] = array('message' => $msg, 'type' => $type);
}

function db_show_msg()
{
	foreach ($_SESSION['databases']['notifications'] as $notification) {
		print "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}

	$_SESSION['databases']['notifications'] = array();
}
?>
