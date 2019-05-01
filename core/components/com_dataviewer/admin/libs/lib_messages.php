<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

if (!isset($_SESSION['databases']['notifications'])) {
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
