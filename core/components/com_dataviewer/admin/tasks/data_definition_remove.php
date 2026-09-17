<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

function dv_data_definition_remove()
{
	check_rid();
	global $com_name, $conf;
	$base = $conf['dir_base'];

	$db_id = Request::getString('db', false);
	$dd_name = Request::getString('dd_name', false);

	// Validate the caller-supplied identifiers before they reach the shell,
	// a filesystem path or the generated PHP: they name a database directory and
	// a data-definition file, so restrict them to a safe identifier character set.
	foreach (array($db_id, $dd_name) as $__id)
	{
		if (!preg_match('/^[A-Za-z0-9_.-]+$/', (string) $__id) || strpos((string) $__id, '..') !== false)
		{
			App::abort(400, 'Invalid identifier');
		}
	}

	$author = User::get('name') . ' <' . User::get('email') . '>';


	$dd_file_php = "$base/$db_id/applications/$com_name/datadefinitions-php/$dd_name.php";
	system("rm $dd_file_php");
	$cmd = "cd $base/$db_id/applications/$com_name/datadefinitions-php/; git commit $dd_name.php --author=" . escapeshellarg($author) . " -m\"[DELETE] $dd_name.php.\"  > /dev/null";
	system($cmd);

	$dd_file_json = "$base/$db_id/applications/$com_name/datadefinitions/$dd_name.json";
	system("rm $dd_file_json");
	$cmd = "cd $base/$db_id/applications/$com_name/datadefinitions/; git commit $dd_name.json --author=" . escapeshellarg($author) . " -m\"[DELETE] $dd_name.json.\"  > /dev/null";
	system($cmd);

	db_msg('Dataview successfully removed', 'message');
	$url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']);
	$url .= "/administrator/index.php?option=com_$com_name&task=dataview_list&db=$db_id";
	header("Location: $url");
	exit();
}
