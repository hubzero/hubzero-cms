<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

function check_rid()
{
	if (isset($_POST[DB_RID]) && $_POST[DB_RID] == DB_RID) {
		return true;
	}

	exit;
}

function dv_identifier($value, $label = 'identifier')
{
	$value = (string) $value;

	if ($value === '' || !preg_match('/^[A-Za-z0-9_.-]+$/', $value) || strpos($value, '..') !== false)
	{
		App::abort(400, 'Invalid ' . $label);
	}

	return $value;
}
