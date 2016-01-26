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

function dv_data_definition_new()
{
	global $com_name, $conf;
	$base = $conf['dir_base'];

	$document = App::get('document');

	$db_id = Request::getString('db', false);
	$table = Request::getString('table', false);
	$name = Request::getString('name', false);
	$title = Request::getString('title', false);

	$name = strtolower(preg_replace('/\W/', '_', $name));

	$db_conf_file = $base . DS . $db_id . DS . 'database.json';
	$db_conf = json_decode(file_get_contents($db_conf_file), true);

	$jdb =  JDatabase::getInstance($db_conf['database_ro']);


	$dd = array();
	$dd['table'] = $table;
	$dd['title'] = $title;

	$sql = "SHOW COLUMNS FROM $table";
	$jdb->setQuery($sql);
	$cols = $jdb->loadAssocList();

	$first_col = true;
	$pk = '';

	foreach ($cols as $col) {
		if ($col['Key'] == 'PRI') {
			$pk = $dd['table'] . '.' . $col['Field'];
		}

		$dd['cols'][$dd['table'] . '.' . $col['Field']] = array('label'=>ucwords(str_replace('_', ' ', $col['Field'])));
	}


	$dd_text = "<?php\ndefined('_HZEXEC_') or die();\n\n";
	$dd_text .= "function get_$name()\n{\n";
	$dd_text .= "\t" . '$dd[\'title\'] = \'' . $title . '\';' . "\n";
	$dd_text .= "\t" . '$dd[\'table\'] = \'' . $dd['table'] . '\';' . "\n";
	$dd_text .= "\t" . '$dd[\'pk\'] = \'' . $pk . '\';' . "\n\n";

	foreach ($dd['cols'] as $col=>$val) {
		$dd_text .= "\t" . '$dd[\'cols\'][\'' . $col . '\'] = ' . format_var(var_export($val, true)) . "\n";
	}

	$dd_text .= "\n\t" . 'return $dd;' . "\n\n}\n?>";

	// Check directories
	if (!file_exists("$base/$db_id/applications/$com_name/datadefinitions-php/")) {
		$dir = "$base/$db_id/applications/$com_name/datadefinitions-php/";
		$cmd = "mkdir -p $dir; cd $dir; git init > /dev/null";
		system($cmd);
	}

	if (!file_exists("$base/$db_id/applications/$com_name/datadefinitions/")) {
		$dir = "$base/$db_id/applications/$com_name/datadefinitions/";
		$cmd = "mkdir -p $dir; cd $dir; git init > /dev/null";
		system($cmd);
	}

	$dd_name = $name;

	$author = User::get('name') . ' <' . User::get('email') . '>';

	$dd_file_php = "$base/$db_id/applications/$com_name/datadefinitions-php/$dd_name.php";
	file_put_contents($dd_file_php, $dd_text);

	$cmd = "cd $base/$db_id/applications/$com_name/datadefinitions-php/; git add $dd_name.php; git commit $dd_name.php --author=\"$author\" -m\"[ADD] $dd_name.php Initial commit.\"  > /dev/null";
	system($cmd);


	$dd_file_json = "$base/$db_id/applications/$com_name/datadefinitions/$dd_name.json";
	$cmd = "cd " . JPATH_COMPONENT . "; php ./ddconvert.php -i$dd_file_php -o$dd_file_json";
	system($cmd);

	$cmd = "cd $base/$db_id/applications/$com_name/datadefinitions/; git add $dd_name.json; git commit $dd_name.json --author=\"$author\" -m\"[ADD] $dd_name.json Initial commit.\"  > /dev/null";
	system($cmd);

	db_msg('New Dataview Added', 'message');
	$url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']);
	$url .= "/administrator/index.php?option=com_$com_name&task=data_definition&db=$db_id&dd=$dd_name";
	header("Location: $url");
	exit();
}


function format_var($var)
{
	return str_replace(' => ', '=>', str_replace(',)', ');', str_replace(' (  ', '(', str_replace("\n", '', $var))));
}
?>
