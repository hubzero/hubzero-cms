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

function dv_list()
{
	global $com_name, $conf;

	Toolbar::title(Lang::txt('Database List'), 'databases');
	Toolbar::preferences(Request::getcmd('option'), '500');


	$base = $conf['dir_base'];
	$arry = array();
	$list = `cd $base; ls ./*/database.json`;

	$list = explode("\n", $list);
	array_pop($list);

?>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="50px">#</th>
				<th width="40%">Name</th>
				<th>Config</th>
				<th width="30%">Data Views</th>
			</tr>
		</thead>
		<tbody>
<?php
	$c = 0;

	foreach ($list as $item)
	{
		chdir($base);
		$id = explode('/', $item);
		$id = $id[1];
		$db = json_decode(file_get_contents($item), true);

		print '<tr>';
		print '<td >' . ++$c . '</td>';
		print '<td >' . $db['name'] . '</td>';
		print '<td><a href="/administrator/index.php?option=com_dataviewer&task=config&db=' . $id . '">Edit Config</a></td>';
		print '<td><a href="/administrator/index.php?option=com_dataviewer&task=dataview_list&db=' . $id . '" target="_blank">' . 'Dataviews' . '</a></td>';
		print '</tr>';
	}
?>
		<tbody>
	</table>
<?php
}
