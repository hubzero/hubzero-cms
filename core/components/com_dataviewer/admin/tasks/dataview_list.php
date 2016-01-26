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

function dv_dataview_list()
{
	global $com_name, $conf;
	$base = $conf['dir_base'];

	$document = App::get('document');
	$document->addScript(DB_PATH . DS . 'html' . DS . 'ace/ace.js');

	$db_id = Request::getString('db', false);
	$db_conf_file = $base . DS . $db_id . DS . 'database.json';
	$db_conf = json_decode(file_get_contents($db_conf_file), true);

	$jdb =  JDatabase::getInstance($db_conf['database_ro']);

	Toolbar::title($db_conf['name'] . ' >> <small> The list of Dataviews</small>', 'databases');

	if (!$jdb->getErrorMsg()) {
		Toolbar::custom('new', 'new', 'new', 'New Dataview', false);
	}

	Toolbar::custom('back', 'back', 'back', 'Go back', false );

	$path = "$base/$db_id/applications/$com_name/datadefinitions/";

	// Check directories
	if (!file_exists($path)) {
		$cmd = "mkdir -p $path; cd $path; git init > /dev/null";
		system($cmd);
		system("chmod ug+Xrw -R $path");
	}

	$path_php = "$base/$db_id/applications/$com_name/datadefinitions-php/";

	if (!file_exists($path_php)) {
		$cmd = "mkdir -p $path_php; cd $path_php; git init > /dev/null";
		system($cmd);
		system("chmod ug+Xrw -R $path_php");
	}


	$files = array();
	if (is_dir($path_php)) {
		$files = scandir($path_php);
	}

	$back_link = "/administrator/index.php?option=com_databases";

	db_show_msg();
?>

	<script>
		var com_name = '<?php echo $com_name; ?>';
		var db_back_link = '<?php echo $back_link; ?>';
	</script>
	<style type="text/css"> .toolbar-box .header:before {content: " ";}</style>

	<table class="adminlist" summary="">
		<thead>
		 	<tr>
		 		<th>#</th>
				<th width="55%">Title</th>
				<th>Remove</th>
				<th>Last Updated</th>
				<th>Data View</th>
				<th>Data Definition</th>
			</tr>
		</thead>


		<tbody>
<?php

	if (count($files) < 1) {
		print "<h2>No Dataviews available</h2>";
	} else {

		asort($files);
		$c = 0;
		foreach ($files as $file) {
			if (substr($file, -4) === '.php') {
				$dd_name = substr($file, 0, -4);

				$json_file = $path . DS . $dd_name . '.json';
				$php_file =  $path_php . DS . $dd_name . '.php';

				// Create JSON data definition if unavailable
				if (!file_exists($json_file)) {
					$cmd = "cd " . JPATH_COMPONENT . "; php ./ddconvert.php -i$php_file -o$json_file";
					system($cmd);

					$author = User::get('name') . ' <' . User::get('email') . '>';
					$cmd = "cd $path; git add $dd_name.json; git commit $dd_name.json --author=\"$author\" -m\"[ADD] $dd_name.json Initial commit.\"  > /dev/null";
					system($cmd);
				}

				$dd = json_decode(file_get_contents($json_file), true);
				$last_mod = date("Y-m-d H:i:s", filemtime($php_file));

				print '<tr>';
				print '<td >' . ++$c . '</td>';
				print '<td >' . $dd['title'] . ' &nbsp;<small>[' . $dd_name . ']</small></td>';
				print '<td ><a class="db-dd-remove-link" style="color: red;" data-dd="' . $dd_name . '" href="#" />Remove</td>';
				print '<td>' . $last_mod . '</td>';
				print '<td align="center"><a target="_blank" href="/' . $com_name . "/view/$db_id:db/" . $dd_name . '/">View</a></td>';
				print '<td><a href="/administrator/index.php?option=com_dataviewer&task=data_definition&db=' . $db_id . '&dd=' . $dd_name . '">' . 'Edit &nbsp; ' . '</a>&nbsp;[<a target="_blank" href="/administrator/index.php?option=com_dataviewer&tmpl=component&task=data_definition&db=' . $db_id . '&dd=' . $dd_name . '">' . 'Full Screen' . '</a>]</td>';
				print '</tr>';
			}
		}
	}

?>
		<tbody>
	</table>


<?php

	if (get_class($jdb) === 'JException' || $jdb->getErrorMsg()) {
		print "<h3>Invalid Database connection information</h3>";
		return;
	} else {
		$sql = 'SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ' . $jdb->quote($db_conf['database_ro']['database']) . ' GROUP BY TABLE_NAME ORDER BY TABLE_NAME';
		$jdb->setQuery($sql);
		$list = $jdb->loadAssocList();
	}

?>

	<!-- Remove Table form -->
	<form id="db-dd-remove-frm" method="post" action="/administrator/index.php?option=com_<?php echo $com_name; ?>&task=data_definition_remove" style="display: none;">
			<input name="<?php echo DB_RID; ?>" type="hidden" value="<?php echo DB_RID; ?>" />
			<input name="db" type="hidden" value="<?php echo $db_id; ?>" />
			<input name="dd_name" type="hidden">
	</form>



	<div id="db-dd-new" style="display: none;" title="<?php echo $db_conf['name']; ?> Database : Add new Dataview">
		<form method="post" action="/administrator/index.php?option=com_<?php echo $com_name; ?>&task=data_definition_new">
			<input name="<?php echo DB_RID; ?>" type="hidden" value="<?php echo DB_RID; ?>" />
			<input name="db" type="hidden" value="<?php echo $db_id; ?>" />
			<label for="table">Select Table:</label>
			<br />
			<select name="table" id="table">
			<?php
				foreach ($list as $table) {
					print '<option value="' . $table['TABLE_NAME'] . '">' . $table['TABLE_NAME'] . '</option>';
				}
			?>
			</select>

			<br />
			<label for="name">Name:</label>
			<br />
			<input type="text" id="name" name="name" />

			<br />
			<label for="title">Title:</label>
			<br />
			<input type="text" id="title" name="title" />


			<input type="submit" value="Create" />
		</form>
	</div>
<?php
}
?>
